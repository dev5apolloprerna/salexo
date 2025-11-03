<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    protected $table = 'party';
    protected $primaryKey = 'partyId';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'strPartyName',
        'iCompanyId',
        'address1','city','state_id',
        'strGST','iMobile','strEmail','strIP',
        'strEntryDate','iStatus','isDelete'
    ];

    protected $casts = [
        'iCompanyId'   => 'integer',
        'iStatus'      => 'integer',
        'isDelete'     => 'integer',
        'strEntryDate' => 'date:Y-m-d',
    ];

    /* ---- Scopes ---- */
    public function scopeNotDeleted(Builder $q): Builder
    {
        return $q->where('isDelete', 0);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('iStatus', 1);
    }

    public function scopeForCompany(Builder $q, int $companyId): Builder
    {
        return $q->where('iCompanyId', $companyId);
    }

    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!$term) return $q;
        return $q->where(function ($w) use ($term) {
            $w->where('strPartyName', 'like', "%{$term}%")
              ->orWhere('strGST', 'like', "%{$term}%")
              ->orWhere('strEmail', 'like', "%{$term}%")
              ->orWhere('iMobile', 'like', "%{$term}%");
        });
    }

    /* ---- Mutators (normalize) ---- */
    public function setStrPartyNameAttribute($v): void
    {
        $this->attributes['strPartyName'] = trim((string)$v);
    }

    public function setStrGstAttribute($v): void
    {
        $this->attributes['strGST'] = $v ? strtoupper(trim((string)$v)) : null;
    }
    public function company()     { return $this->belongsTo(CompanyClient::class, 'iCompanyId','company_id'); }
    public function state()     { return $this->belongsTo(State::class, 'state_id','stateId'); }

}
