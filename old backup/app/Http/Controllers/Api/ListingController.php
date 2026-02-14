<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Year;
use App\Models\State;

class ListingController extends Controller
{
    public function dropdown_list(Request $request)
    {
        try {
            
            // Fetch active Years / States
            $years  = Year::where(['iStatus' => 1, 'isDelete' => 0])->orderBy('strYear', 'asc')
                ->get(['year_id', 'strYear']);

            $states = State::where(['iStatus' => 1, 'isDelete' => 0])->orderBy('stateName', 'asc')->get(['stateId', 'stateName']);

            // Map to lightweight lists
            $yearList = $years->map(fn ($y) => [
                'year_id' => $y->year_id,
                'year'    => $y->strYear,
            ])->values();

            $stateList = $states->map(fn ($s) => [
                'state_id'   => $s->stateId,
                'state_name' => $s->stateName,
            ])->values();

            return response()->json([
                'success'     => true,
                'message'    => 'Dropdown list',
                'year_list'  => $yearList,
                'state_list' => $stateList,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success'  => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}

