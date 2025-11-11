<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePartyRequest;
use App\Http\Requests\UpdatePartyRequest;
use App\Models\Party;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


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
             ->get();

        if(sizeof($list) != 0)
            {
                foreach($list as $val)
                {
                    $PartyList[] = array(
                        "partyId" => $val->partyId,
                        "strPartyName" => $val->strPartyName,
                        "strContactPersonName" => $val->strContactPersonName,
                        "iCompanyId" => $val->iCompanyId,
                        "company_name" => $val->company->company_name,
                        "address1" => $val->address1,
                        "address2" => $val->address2,
                        "strGST" => $val->strGST,
                        "iMobile" => $val->iMobile,
                        "strEmail" => $val->strEmail,
                        "city" => $val->city,
                        "state_id" => $val->state_id,
                        "state_name" => $val->state->stateName ?? null,
                        "pincode"=>$val->pincode ?? null,
                        "strEntryDate"=> date('d-m-Y',strtotime($val->strEntryDate)) ?? null,
                    );
                }
                    return response()->json([
                        'success' => true,
                        'message' => 'Party List fetched successfully',
                        'party_list' => $PartyList
                    ]);

            } else 
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No Data Found!',
                    'party_list' => []
                ]);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch lead pipeline',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
     public function search(Request $request)
    {
        $q         = trim($request->query('q', ''));
        $companyId = (int) $request->query('company_id', 0);

        $rows = Party::query()
            ->when($companyId > 0, fn($w) => $w->where('iCompanyId', $companyId))
            ->where('isDelete', 0)
            ->when($q !== '', fn($w) => $w->where('strPartyName', 'like', "%{$q}%"))
            ->orderBy('strPartyName')
            ->limit(20)
            ->get([
                'partyId as id',
                'strPartyName as text',
            ]);

        return response()->json(['results' => $rows]);
    }

    // GET /api/parties/lookup/name?q=&company_id=
    public function lookupByName(Request $request)
    {


            $q = trim((string) $request->input('name', $request->input('q', '')));
            if (mb_strlen($q) < 3) {
                return response()->json([
                    'success'=>false,
                    'message' => 'Please type at least 3 characters.',
                ], 422);
            }


        $companyId = (int) $request->query('company_id', 0);

        $query = DB::table('lead_master')
            ->select([
                'lead_id',
                'company_name',
                'customer_name',
                'mobile',
                'email',
                'address',
                'GST_No',
                'remarks',
                'iCustomerId',
                'isDelete',
                'created_at',
            ])
            ->where('isDelete', 0)
            ->when($companyId > 0, fn($w) => $w->where('iCustomerId', $companyId))
            ->where(function ($w) use ($q) {
                $like = "%{$q}%";
                $w->where('company_name', 'like', $like)
                  ->orWhere('customer_name', 'like', $like);
            })
            ->orderByRaw(
                "CASE 
                    WHEN company_name = ? OR customer_name = ? THEN 0
                    WHEN company_name LIKE ? OR customer_name LIKE ? THEN 1
                    ELSE 2
                 END",
                [$q, $q, "%{$q}%", "%{$q}%"]
            )
            ->orderByDesc('lead_id');

        $hits = $query->limit(1)->get();

        if ($hits->isEmpty()) {
            return response()->json([
                'success'=>false,
                'message' => 'No lead found for this name.',
            ], 404);
        }

        $lead = $hits->first();

        $prefill = [
            'strPartyName'         => $lead->company_name ?: ($lead->customer_name ?: ''),
            'strContactPersonName' => $lead->customer_name ?: '',
            'iMobile'              => $lead->mobile ?: '',
            'strEmail'             => $lead->email ?: '',
            'address1'             => $lead->address ?: '',
            'strGST'               => $lead->GST_No ?: '',
            'remarks'              => $lead->remarks ?: '',
        ];

        return response()->json([
            'success' => true,
            'message'    => 'Get Data From Lead Master',
            // 'count' => $hits->count(),
            // 'lead'  => $lead,
            'lead_data'  => $prefill,
        ]);
    }

    // GET /api/parties/lookup/mobile?mobile=&company_id=
    public function lookupByMobile(Request $request)
    {
        $mobile = preg_replace('/\D+/', '', (string) $request->query('mobile', ''));

        if (strlen($mobile) < 6) {
            return response()->json([
                'success'=>false,
                'message' => 'Please provide a valid mobile number.',
            ], 422);
        }

        $companyId = (int) $request->query('company_id', 0);

        $query = DB::table('lead_master')
            ->where('isDelete', 0)
            ->where('mobile', $mobile);

        if ($companyId > 0) {
            $query->where('iCustomerId', $companyId);
        }

        $lead = $query->orderByDesc('lead_id')->first();

        if (!$lead) {
            return response()->json([
                'success'=>false,
                'message' => 'No lead found for this mobile.',
            ], 404);
        }

        $prefill = [
            'strPartyName'     => $lead->company_name ?: ($lead->customer_name ?: ''),
            'strContactPerson' => $lead->customer_name ?: '',
            'iMobile'          => $lead->mobile ?: '',
            'strEmail'         => $lead->email ?: '',
            'address1'         => $lead->address ?: '',
            'strGST'           => $lead->GST_No ?: '',
            'remarks'          => $lead->remarks ?: '',
        ];

        return response()->json([
            'success' => true,
            'lead' => $lead,
            'data' => $prefill,
        ]);
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
            return response()->json(['success' => true,'message' => 'Party created successfully'], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
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
            return response()->json(['success' => true,'message' => 'Party updated successfully']);
        } catch (QueryException $e) {
            if ((int) $e->getCode() === 23000) {
                return response()->json(['status'=>'error','message' => 'This GST is already registered for your company.'], 422);
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
            return response()->json(['success'=>false,'message' => 'party_id is required'], 422);
        }

        $row = Party::find($partyId);
        if (!$row) return response()->json(['success'=>false,'message' => 'Party not found'], 404);

        $row->delete();
        return response()->json(['success'=>true,'message' => 'Party deleted']);
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
