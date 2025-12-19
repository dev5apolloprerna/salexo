<?php

namespace App\Repositories\Lead;

use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
use App\Models\LeadMaster;

class LeadRepository implements LeadRepositoryInterface
{
    public function all()
    {
        return LeadMaster::with(['state', 'leadSource'])->paginate(env('PER_PAGE_COUNT'));
    }

    public function find($id)
    {
        return LeadMaster::findOrFail($id);
    }

    public function create(array $data)
    {
        return LeadMaster::create($data);
    }

    public function update($id, array $data)
    {
        $lead = LeadMaster::findOrFail($id);
        $lead->update($data);
        return $lead;
    }
}
