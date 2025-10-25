<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
    public $table = 'state';
    protected $primaryKey = 'stateId'; // Define the primary key
    public $timestamps = false; // Disable timestamps
    protected $fillable = [
        'stateName',
    ];
}
