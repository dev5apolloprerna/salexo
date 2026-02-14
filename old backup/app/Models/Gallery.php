<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;
    public $table = 'gallery';
    protected $fillable = [
        'galleryId',
        'serviceId',
        'strPhoto',
    ];
}
