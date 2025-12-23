<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadPipeline extends Model
{
    use HasFactory;
    public $table = 'lead_pipeline_master';
    protected $primaryKey = 'pipeline_id'; // Define the primary key
    public $timestamps = false; // Disable timestamps
    protected $fillable = [
        'company_id',
        'pipeline_name',
        'slugname',
        'admin',
        'followup_needed',
        'followup_date',
        'color',
        'icon'
    ];
}
