<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadSource extends Model
{
    use HasFactory;
    public $table = 'lead_source_master';
    protected $primaryKey = 'lead_source_id'; // Define the primary key
    public $timestamps = false; // Disable timestamps
    protected $fillable = [
        'company_id',
        'lead_source_name',
    ];
}
