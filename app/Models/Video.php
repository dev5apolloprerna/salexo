<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    public $table = 'video_gallery';
    protected $fillable = [
        'videoId',
        'serviceId',
        'video',
    ];
}
