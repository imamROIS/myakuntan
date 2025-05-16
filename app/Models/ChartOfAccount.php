<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// Suggested code may be subject to a license. Learn more: ~LicenseLog:1089347142.
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Jurnalharian;


class ChartOfAccount extends Model
{
    //'jh_code_account', 'coa_code'
    use HasFactory, SoftDeletes;

    protected $table = 'chart_of_accounts';

    protected $fillable = [
        'coa_code',
        'coa_name',
        'coa_type',
        'coa_category',
        'increase_on_debit',
        'opening_balance',
        'current_balance',
        'is_active',
        'description'
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'increase_on_debit' => 'boolean'
    ];

    public function jurnals()
    {
        return $this->hasMany(JurnalHarian::class, 'jh_code_account', 'coa_code');
    }

    public function getNormalBalanceAttribute()
    {
        return $this->increase_on_debit ? 'debit' : 'credit';
    }

    public function calculateCurrentBalance()
    {
        $totals = $this->jurnals()
            ->selectRaw('SUM(jh_dr) as total_debit, SUM(jh_cr) as total_credit')
            ->first();
        
        if ($this->increase_on_debit) {
            $this->current_balance = $this->opening_balance 
                + ($totals->total_debit ?? 0) 
                - ($totals->total_credit ?? 0);
        } else {
            $this->current_balance = $this->opening_balance 
                + ($totals->total_credit ?? 0) 
                - ($totals->total_debit ?? 0);
        }
        
        $this->save();
    }


// function di bawah berfungsi untuk kapitalisasi semua inputan yang dipilih
    public function setJhNomorDokumenAttribute($value){
        $this->attributes['coa_code'] = strtoupper($value);
    }
    public function setCoaNameAttribute($value){
        $this->attributes['coa_name'] = strtoupper($value);
    }
    public function setCoaTypeAttribute($value){
        $this->attributes['coa_type'] = strtoupper($value);
    }
    public function setCoaCategoryAttribute($value){
        $this->attributes['coa_category'] = strtoupper($value);
    }

}
