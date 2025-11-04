<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationTemplate extends Model
{
    protected $fillable = [
         'id', 'company_id', 'name', 'file_path', 'guid', 'is_active', 'created_at', 'updated_at'
    ];
    protected $casts = ['is_active'=>'bool'];
}
?>