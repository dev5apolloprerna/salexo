<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePartyRequest;
use App\Http\Requests\UpdatePartyRequest;
use App\Models\Party;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartyApiController extends Controller
{
    /**
     * List parties
     * Accepts GET or POST; reads filters from query/body.
     * Params: company_id?, q?, per_page?, page?
     */
    public function index(Request $request)
    {
         try {
            $employee = Auth::guard('employee_api')->user();
            // dd($employee);
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

        $companyId = (int) $request->input('company_id', 0);
        $q         = $request->input('q');
        $perPage   = max(1, (int) $request->input('per_page', 15));

        $list = Party::with('company')
            ->when($companyId > 0, fn($x) => $x->where('iCompanyId', $companyId))
            ->where('isDelete', 0)
            ->when($q, function ($x) use ($q) {
                $x->where(function ($w) use ($q) {
                    $w->where('strPartyName', 'like', "%$q%")
                      ->orWhere('strGST', 'like', "%$q%")
                      ->orWhere('strEmail', 'like', "%$q%")
                      ->orWhere('iMobile', 'like', "%$q%");
                });
            })
            ->orderBy('strPartyName')
            ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Party List fetched successfully',
                'party_list' => $list->items(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch lead pipeline',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Party detail
     * Body/Query: party_id (int, required)
     */
    public function show(Request $request)
    {
        $partyId = (int) $request->input('party_id');
        
        if (!$partyId) {
            return response()->json(['message' => 'party_id is required'], 422);
        }

        $row = Party::with('company')->where('isDelete', 0)->find($partyId);
        if (!$row) return response()->json(['message' => 'Party not found'], 404);

        return response()->json(['party' => $row]);
    }

    /**
     * Create party
     * Uses StorePartyRequest validation.
     * Body: strPartyName (req), strGST?, iMobile?, strEmail?, address1?, address2?, address3?, strEntryDate?
     * iCompanyId is taken from auth employee; fallback to provided iCompanyId if necessary.
     */
    public function store(StorePartyRequest $request)
    {
        $authUser  = auth('employee_api')->user();
        $companyId = $authUser->company_id ?? $request->input('iCompanyId');

        $data              = $request->validated();
        $data['iCompanyId'] = $companyId;
        $data['strIP']      = $request->ip();

        try {
            $row = Party::create($data);
            // return response()->json(['message' => 'Party created successfully', 'party' => $row], 201);
            return response()->json(['message' => 'Party created successfully'], 201);
        } catch (QueryException $e) {
            if ((int) $e->getCode() === 23000) {
                return response()->json(['message' => 'This GST is already registered for your company.'], 422);
            }
            throw $e;
        }
    }

    /**
     * Update party
     * Uses UpdatePartyRequest validation.
     * Body: party_id (req) + updatable fields
     */
    public function update(UpdatePartyRequest $request)
    {
        $partyId = (int) $request->input('party_id');
        if (!$partyId) {
            return response()->json(['message' => 'party_id is required'], 422);
        }

        $row = Party::find($partyId);
        if (!$row) return response()->json(['message' => 'Party not found'], 404);

        $authUser  = auth('employee_api')->user();
        $data               = $request->validated();
        $data['iCompanyId'] = $authUser->company_id ?? $row->iCompanyId;
        $data['strIP']      = $request->ip();

        try {
            $row->update($data);
            // return response()->json(['message' => 'Party updated successfully', 'party' => $row->fresh()]);
            return response()->json(['message' => 'Party updated successfully']);
        } catch (QueryException $e) {
            if ((int) $e->getCode() === 23000) {
                return response()->json(['message' => 'This GST is already registered for your company.'], 422);
            }
            throw $e;
        }
    }

    /**
     * Delete party
     * Body: party_id (req)
     */
    public function destroy(Request $request)
    {
        $partyId = (int) $request->input('party_id');
        if (!$partyId) {
            return response()->json(['message' => 'party_id is required'], 422);
        }

        $row = Party::find($partyId);
        if (!$row) return response()->json(['message' => 'Party not found'], 404);

        $row->delete();
        return response()->json(['message' => 'Party deleted']);
    }

    /**
     * Bulk delete
     * Body: ids[] (req, array<int>)
     */
    public function bulkDestroy(Request $request)
    {
        $ids = collect($request->input('ids', []))->map('intval')->filter()->all();
        if (!$ids) {
            return response()->json(['message' => 'No records selected'], 422);
        }

        Party::whereIn('partyId', $ids)->delete();
        return response()->json(['message' => 'Selected parties deleted', 'deleted_ids' => $ids]);
    }

    /**
     * Toggle status
     * Body: party_id (req)
     */
    public function toggleStatus(Request $request)
    {
        $partyId = (int) $request->input('party_id');
        if (!$partyId) {
            return response()->json(['message' => 'party_id is required'], 422);
        }

        $row = Party::find($partyId);
        if (!$row) return response()->json(['message' => 'Party not found'], 404);

        $row->update(['iStatus' => $row->iStatus ? 0 : 1]);
        return response()->json(['message' => 'Status updated', 'party' => $row->fresh()]);
    }
}
