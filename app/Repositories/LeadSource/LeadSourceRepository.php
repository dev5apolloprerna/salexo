<?php

namespace App\Repositories\LeadSource;



use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;

//use Your Model



/**

 * Class CategoryRepository.

 */

use App\Models\LeadSource;
use Illuminate\Support\Facades\Auth;


class LeadSourceRepository implements LeadSourceRepositoryInterface

{

    /**

     * @return string

     *  Return the model

     */

    public function find($id)

    {

        return LeadSource::findOrFail($id)->toArray(); 

    }



    public function all()

    {

        return LeadSource::get()->toArray();

    }




    public function createOrUpdate($request,$id=null)

    {
       $data = $request->all();
    $user = Auth::user();

    try {
        if ($id) {
            // Update existing LeadSource
            $leadSource = LeadSource::findOrFail($id);
            $leadSource->update($data);
        } else {
            // Create new LeadSource
            $data['company_id'] = $user->company_id;
            $leadSource = LeadSource::create($data);
        }

        return $leadSource;
    } catch (\Exception $e) {
        // Handle any exceptions, e.g., log or rethrow as needed
        throw new \Exception("Error processing LeadSource: " . $e->getMessage());
    }
}


public function changeStatus($request, $id)
    {
        $LeadSource = LeadSource::find($id);

        if (!$LeadSource) {

            throw new \Exception("LeadSource with ID {$id} not found.");

        }

        $LeadSource->isDelete = $request['isDelete'] ?? 0;
        $LeadSource->save();
        return $LeadSource;
    }


    public function destroy($id){

        LeadSource::where('lead_source_id',$id)->delete();

    }

    public function query()
    {
        return LeadSource::query();
    }

}

