<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationDiscountApiController extends Controller
{
    public function apply(Request $request, $quotationId)
    {
        $data = $request->validate([
            'mode'  => ['required', 'in:percent,amount'],
            'value' => ['required', 'numeric', 'min:0'],
        ]);

        $mode  = $data['mode'];
        $value = (float) $data['value'];

        // Load items
        $items = DB::table('quotationdetails')
            ->where([
                'quotationID' => $quotationId,
                'iStatus'     => 1,
                'isDelete'    => 0,
            ])
            ->orderBy('quotationdetailsId')
            ->get([
                'quotationdetailsId',
                'quantity',
                'rate',
                'iGstPercentage',
                'amount',
                'discount',
                'netAmount',
                'totalAmount',
            ]);

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No items found for this quotation.',
            ], 404);
        }

        // Build working lines
        $lines = $items->map(function ($r) {
            $qty  = (float) ($r->quantity ?? 0);
            $rate = (float) ($r->rate ?? 0);
            $base = $qty * $rate;              // taxable base BEFORE discount
            $gstp = (float) ($r->iGstPercentage ?? 0);

            return [
                'id'    => $r->quotationdetailsId,
                'base'  => $base,
                'gstp'  => $gstp,
            ];
        });

        // If total base is zero, nothing to discount
        $totalBase = $lines->sum('base');
        if ($totalBase <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Total base amount is zero; cannot apply discount.',
            ], 422);
        }

        // Decide effective percent & per-line discounts
        $effectivePercent = 0.0;
        $discounts = [];

        if ($mode === 'percent') {
            $effectivePercent = (float) $value;
            // Initial raw per-line discount and fractional parts for rounding fix
            $raws = [];
            $sumRounded = 0.0;

            foreach ($lines as $ln) {
                $raw = ($ln['base'] * ($effectivePercent / 100.0));
                $raws[] = [
                    'id'   => $ln['id'],
                    'raw'  => $raw,
                    'round'=> floor($raw * 100) / 100, // truncate to 2 decimals
                    'frac' => $raw - (floor($raw * 100) / 100),
                ];
                $sumRounded += floor($raw * 100) / 100;
            }

            // Total target discount when % mode is simply recomputed by %.
            $targetTotal = round($totalBase * ($effectivePercent / 100.0), 2);
            $left = round($targetTotal - $sumRounded, 2);

            usort($raws, fn($a,$b) => $b['frac'] <=> $a['frac']);
            foreach ($raws as &$row) {
                $alloc = $row['round'];
                if ($left > 0) {
                    $bump = min($left, 0.01);
                    $alloc = round($alloc + $bump, 2);
                    $left  = round($left - $bump, 2);
                }
                $discounts[$row['id']] = $alloc;
            }
            unset($row);
        } else { // mode === 'amount'
            // Convert amount to one common percentage of total base
            $effectivePercent = ($value / $totalBase) * 100.0;

            // Compute per-line by that percent; then fix rounding to match requested amount
            $raws = [];
            $sumRounded = 0.0;

            foreach ($lines as $ln) {
                $raw = ($ln['base'] * ($effectivePercent / 100.0));
                $raws[] = [
                    'id'   => $ln['id'],
                    'raw'  => $raw,
                    'round'=> floor($raw * 100) / 100, // truncate to 2 decimals
                    'frac' => $raw - (floor($raw * 100) / 100),
                ];
                $sumRounded += floor($raw * 100) / 100;
            }

            // Adjust to hit EXACT requested amount
            $left = round($value - $sumRounded, 2);
            usort($raws, fn($a,$b) => $b['frac'] <=> $a['frac']);

            foreach ($raws as &$row) {
                $alloc = $row['round'];
                if ($left > 0) {
                    $bump = min($left, 0.01);
                    $alloc = round($alloc + $bump, 2);
                    $left  = round($left - $bump, 2);
                }
                $discounts[$row['id']] = $alloc;
            }
            unset($row);
        }

        // Recompute line net, gst, total and write back
        $totalDiscount = 0.0;
        $subTotal      = 0.0;
        $gstTotal      = 0.0;
        $grandTotal    = 0.0;

        DB::beginTransaction();
        try {
            foreach ($lines as $ln) {
                $disc = (float) ($discounts[$ln['id']] ?? 0);
                // Ensure discount cannot exceed base
                $disc = min($disc, $ln['base']);
                $totalDiscount += $disc;

                $net = max($ln['base'] - $disc, 0.0);             // taxable after discount
                $gst = round($net * ($ln['gstp'] / 100.0), 2);     // GST on net
                $tot = round($net + $gst, 2);

                DB::table('quotationdetails')
                    ->where('quotationdetailsId', $ln['id'])
                    ->update([
                        'amount'     => round($ln['base'], 2),
                        'discount'   => round($disc, 2),
                        'netAmount'  => round($net, 2),
                        'totalAmount'=> round($tot, 2),
                        'updated_at' => now(),
                    ]);

                $subTotal   += $ln['base'];
                $gstTotal   += $gst;
                $grandTotal += $tot;
            }

        DB::table('quotation')
            ->where('quotationId', $quotationId)
            ->update([
                'discount_type' => $mode,         // "percent" or "amount"
                'discount'      => round($value, 2),  // the raw value user entered
                'updated_at'    => now(),
            ]);
            
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply discount.',
                'error'   => $e->getMessage(),
            ], 500);
        }

        // For info: are GST% all same? (no restrictions now, just a hint)
        $uniquePcts = collect($lines)->pluck('gstp')->map(fn($x) => round((float)$x, 4))->unique()->values();
        $allSameGST = $uniquePcts->count() === 1;

        return response()->json([
            'success' => true,
            'message' => 'Discount applied successfully.',
            'quotation_id' => (int) $quotationId,
            'request' => [
                'mode'  => $mode,
                'value' => $value,
            ],
            'gst_uniform'      => $allSameGST,
            'effective_percent'=> round($effectivePercent, 6), // % that actually got applied
            'summary' => [
                'sub_total_before_discount' => round($subTotal, 2),
                'total_discount'            => round($totalDiscount, 2),
                'taxable_after_discount'    => round($subTotal - $totalDiscount, 2),
                'gst_total'                 => round($gstTotal, 2),
                'grand_total'               => round($grandTotal, 2),
            ],
        ]);
    }
}
