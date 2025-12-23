<?php

namespace App\Repositories\LeadCancelReason;



use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;

//use Your Model



/**

 * Class CategoryRepository.

 */

use App\Models\LeadCancelReason;
use Illuminate\Support\Facades\Auth;


class LeadCancelReasonRepository implements LeadCancelReasonRepositoryInterface

{

    /**

     * @return string

     *  Return the model

     */

    public function find($id)

    {

        return LeadCancelReason::findOrFail($id)->toArray(); 

    }



    public function all()

    {

        return LeadCancelReason::get()->toArray();

    }




    public function createOrUpdate($request,$id=null)

    {
        $data = $request->all();
        $user = Auth::user(); // Get the currently authenticated user

        try {
            if ($id) {
                $leadCancelReason = LeadCancelReason::find($id);
    
                if (!$leadCancelReason) {
                    throw new \Exception("LeadCancelReason with ID {$id} not found.");
                }
    
                $leadCancelReason->update($data);
            } else {
                $data['company_id'] = $user->company_id; // Add company_id for new records
                $leadCancelReason = LeadCancelReason::create($data);
            }
    
            return $leadCancelReason;
    
        } catch (\Exception $e) {
            \Log::error('Error in createOrUpdate LeadCancelReason: ' . $e->getMessage());
            throw $e;
        }    
    }


public function changeStatus($request, $id)
    {
        $LeadCancelReason = LeadCancelReason::find($id);

        if (!$LeadCancelReason) {

            throw new \Exception("LeadCancelReason with ID {$id} not found.");

        }

        $LeadCancelReason->isDelete = $request['isDelete'] ?? 0;
        $LeadCancelReason->save();
        return $LeadCancelReason;
    }


    public function destroy($id){

        LeadCancelReason::where('lead_cancel_reason_id',$id)->delete();

    }

    public function query()
    {
        return LeadCancelReason::query();
    }

}

