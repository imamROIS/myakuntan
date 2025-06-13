<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;

class ChartOfAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'chart_of_accounts';

    protected $fillable = [
        'coa_code',
        'coa_name',
        'coa_type',
        'coa_category',
        'increase_on_debit',
        'coa_debit',
        'coa_credit',
        'opening_balance',
        'current_balance',
        'is_active',
        'description'
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'increase_on_debit' => 'boolean',
        'coa_debit' => 'decimal:2',
        'coa_credit' => 'decimal:2'
    ];

    public function jurnals()
    {
        return $this->hasMany(Jurnalharian::class, 'jh_code_account', 'coa_code');
    }


    public function getNormalBalanceAttribute()
    {
        return $this->increase_on_debit ? 'debit' : 'credit';
    }

  /**
 * Update kolom debit, credit DAN current_balance secara real-time
 */
public function updateDebitCreditOnly($drChange = 0, $crChange = 0)
{
    // Update debit dan credit
    $this->increment('coa_debit', $drChange);
    $this->increment('coa_credit', $crChange);
    
    // Hitung ulang current_balance
    if ($this->increase_on_debit) {
        $this->current_balance = $this->opening_balance 
            + $this->coa_debit 
            - $this->coa_credit;
    } else {
        $this->current_balance = $this->opening_balance 
            + $this->coa_credit 
            - $this->coa_debit;
    }
    
    // Simpan perubahan tanpa memicu event/observer
    $this->saveQuietly();
}

public function updateDebitCreditBalances()
{
    $this->recalculateAllBalances(); // Sekarang panggil recalculateAllBalances()
}

// public function updateDebitCreditBalances()
// {
//     $this->updateDebitCreditOnly(); // Panggil method baru
// }


    /**
     * Hitung ulang semua balance dari awal
     * Untuk dijalankan secara terjadwal atau manual
     */
    public function recalculateAllBalances()
    {
        $totals = $this->jurnals()
            ->selectRaw('SUM(jh_dr) as total_debit, SUM(jh_cr) as total_credit')
            ->first();
        
        $this->coa_debit = $totals->total_debit ?? 0;
        $this->coa_credit = $totals->total_credit ?? 0;
        
        if ($this->increase_on_debit) {
            $this->current_balance = $this->opening_balance 
                + ($this->coa_debit) 
                - ($this->coa_credit);
        } else {
            $this->current_balance = $this->opening_balance 
                + ($this->coa_credit) 
                - ($this->coa_debit);
        }
        
        $this->save();
    }

    // Mutators untuk kapitalisasi
    public function setCoaCodeAttribute($value) {
        $this->attributes['coa_code'] = strtoupper($value);
    }
    public function setCoaNameAttribute($value) {
        $this->attributes['coa_name'] = strtoupper($value);
    }
    public function setCoaTypeAttribute($value) {
        $this->attributes['coa_type'] = strtoupper($value);
    }
    public function setCoaCategoryAttribute($value) {
        $this->attributes['coa_category'] = strtoupper($value);
    }

    public function getBalanceDifferenceAttribute()
    {
        return abs($this->coa_debit - $this->coa_credit);
    }

    public function getBalanceStatusAttribute()
    {
        return $this->coa_debit == $this->coa_credit 
            ? 'balanced' 
            : ($this->coa_debit > $this->coa_credit ? 'debit_larger' : 'credit_larger');
    }
}

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// // Suggested code may be subject to a license. Learn more: ~LicenseLog:1089347142.
// use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use App\Models\Jurnalharian;


// class ChartOfAccount extends Model
// {
//     //'jh_code_account', 'coa_code'
//     use HasFactory, SoftDeletes;

//     protected $table = 'chart_of_accounts';

//     protected $fillable = [
//         'coa_code',
//         'coa_name',
//         'coa_type',
//         'coa_category',
//         'increase_on_debit',
//         'coa_debit',
//         'coa_credit',
//         'opening_balance',
//         'current_balance',
//         'is_active',
//         'description'
//     ];

//     protected $casts = [
//         'opening_balance' => 'decimal:2',
//         'current_balance' => 'decimal:2',
//         'is_active' => 'boolean',
//         'increase_on_debit' => 'boolean',
//         'coa_debit' => 'decimal:2',
//         'coa_credit' => 'decimal:2'
//     ];

//     public function jurnals()
//     {
//         return $this->hasMany(JurnalHarian::class, 'jh_code_account', 'coa_code');
//     }

//     public function getNormalBalanceAttribute()
//     {
//         return $this->increase_on_debit ? 'debit' : 'credit';
//     }

//     public function calculateCurrentBalance()
//     {
//         $totals = $this->jurnals()
//             ->selectRaw('SUM(jh_dr) as total_debit, SUM(jh_cr) as total_credit')
//             ->first();
        
//         if ($this->increase_on_debit) {
//             $this->current_balance = $this->opening_balance 
//                 + ($totals->total_debit ?? 0) 
//                 - ($totals->total_credit ?? 0);
//         } else {
//             $this->current_balance = $this->opening_balance 
//                 + ($totals->total_credit ?? 0) 
//                 - ($totals->total_debit ?? 0);
//         }
        
//         $this->save();
//     }


// // function di bawah berfungsi untuk kapitalisasi semua inputan yang dipilih
//     public function setJhNomorDokumenAttribute($value){
//         $this->attributes['coa_code'] = strtoupper($value);
//     }
//     public function setCoaNameAttribute($value){
//         $this->attributes['coa_name'] = strtoupper($value);
//     }
//     public function setCoaTypeAttribute($value){
//         $this->attributes['coa_type'] = strtoupper($value);
//     }
//     public function setCoaCategoryAttribute($value){
//         $this->attributes['coa_category'] = strtoupper($value);
//     }


// // Tambahkan method untuk update coa_debit dan coa_credit
//     public function updateDebitCreditBalances()
//     {
//         $totals = $this->jurnals()
//             ->selectRaw('SUM(jh_dr) as total_debit, SUM(jh_cr) as total_credit')
//             ->first();

//         $this->coa_debit = $totals->total_debit ?? 0;
//         $this->coa_credit = $totals->total_credit ?? 0;
    
//         $this->save();
//     }

//     // Di model ChartOfAccount.php
// public function getBalanceDifferenceAttribute()
// {
//     return abs($this->coa_debit - $this->coa_credit);
// }

// public function getBalanceStatusAttribute()
// {
//     return $this->coa_debit == $this->coa_credit 
//         ? 'balanced' 
//         : ($this->coa_debit > $this->coa_credit ? 'debit_larger' : 'credit_larger');
// }



// }
