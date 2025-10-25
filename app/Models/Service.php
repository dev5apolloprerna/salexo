<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    public $table = 'service_master';
    protected $primaryKey = 'service_id'; // Define the primary key

    protected $fillable = [
        'service_id',
        'company_id',
        'service_name',
        'service_description',
        'service_image',
    ];

     public function Company()
    {
        return $this->belongsTo(CompanyClient::class, 'company_id');
    }
}
