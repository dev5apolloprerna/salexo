<?php
// app/Repositories/CompanyClientRepository.php

namespace App\Repositories\Company;

use App\Models\CompanyClient;

class CompanyClientRepository implements CompanyClientRepositoryInterface
{
    public function all()
    {
        return CompanyClient::all();
    }

    public function find($id)
    {
        return CompanyClient::findOrFail($id);
    }

    public function create(array $data)
    {
        return CompanyClient::create($data);
    }

    public function update($id, array $data)
    {
        $client = CompanyClient::findOrFail($id);
        $client->update($data);
        return $client;
    }

    public function delete($id)
    {
        return CompanyClient::destroy($id);
    }
     public function query()
    {
        return CompanyClient::query();
    }
   public function updatePassword($id, $hashedPassword)
    {
        return CompanyClient::where('company_id', $id)->update([
            'password' => $hashedPassword,
        ]);
    }



}
