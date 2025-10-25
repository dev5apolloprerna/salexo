<?php
namespace App\Repositories\Lead;

interface LeadRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
}
