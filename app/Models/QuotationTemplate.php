<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationTemplate extends Model
{
    protected $fillable = [
        'company_id','name','version','engine','file_path','inline_html','is_active','meta'
    ];
    protected $casts = ['is_active'=>'bool','meta'=>'array'];
}
?>