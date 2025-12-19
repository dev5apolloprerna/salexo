<?php
namespace App\Repositories\LeadPipeline;

interface LeadPipelineRepositoryInterface
{
    /**
     * Get's a post by it's ID
     *
     * @param int
     */
    public function find($id);

    /**
     * Get's all categories.
     *
     * @return mixed
     */
    public function all();

    /**
     * Update or create Documents
     *
     * @return mixed
     */
    public function createOrUpdate($request,$id=null);
    /**
     * Destory Documents
     *
     * @return mixed
     */
    public function destroy($id);
}