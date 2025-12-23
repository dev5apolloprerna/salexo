<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadCancelReason extends Model
{
    use HasFactory;
    public $table = 'lead_cancel_reason';
    protected $primaryKey = 'lead_cancel_reason_id'; // Define the primary key
    public $timestamps = false; // Disable timestamps
    protected $fillable = [
        'company_id',
        'reason',
    ];
}
