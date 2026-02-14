<?php

namespace App\Repositories\LeadPipeline;



use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;

//use Your Model



/**

 * Class CategoryRepository.

 */

use App\Models\LeadPipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LeadPipelineRepository implements LeadPipelineRepositoryInterface

{

    /**

     * @return string

     *  Return the model

     */

    public function find($id)

    {

        return LeadPipeline::findOrFail($id)->toArray();
    }



    public function all()

    {
        $company_id = Auth::user();

        return LeadPipeline::where('')->get()->toArray();
    }




    public function createOrUpdate($request, $id = null)
    {
        $data = $request->all();

        $user = Auth::user(); // Get the currently logged-in user

        try {
            if ($id) {
                $leadPipeline = LeadPipeline::find($id);

                if (!$leadPipeline) {
                    throw new \Exception("LeadPipeline with ID {$id} not found.");
                }

                $data['slugname'] = Str::slug($data['pipeline_name']);
                $leadPipeline->update($data);
            } else {
                $data['company_id'] = $user->company_id;
                $data['slugname'] = Str::slug($data['pipeline_name']);
                $leadPipeline = LeadPipeline::create($data);
            }

            return $leadPipeline;
        } catch (\Exception $e) {
            Log::error('Error in createOrUpdate LeadPipeline: ' . $e->getMessage());
            throw $e;
        }
    }


    public function changeStatus($request, $id)
    {
        $LeadPipeline = LeadPipeline::find($id);

        if (!$LeadPipeline) {

            throw new \Exception("LeadPipeline with ID {$id} not found.");
        }

        $LeadPipeline->isDelete = $request['isDelete'] ?? 0;
        $LeadPipeline->save();
        return $LeadPipeline;
    }


    public function destroy($id)
    {

        LeadPipeline::where('pipeline_id', $id)->delete();
    }

    public function query()
    {
        return LeadPipeline::query();
    }
}
