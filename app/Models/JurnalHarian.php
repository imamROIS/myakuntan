<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\ChartOfAccount;
use User;

class Jurnalharian extends Model
{
    //

    use HasFactory, SoftDeletes;

    protected $table = 'jurnalharians';

    protected $fillable = [
        'jh_tanggal',
        'jh_nomor_jurnal',
        'jh_nomor_dokumen',
        'jh_code_account',
        'jh_nama_account',
        'jh_code_dept',
        'jh_departemen',
        'jh_dr',
        'jh_cr',
        'jh_keterangan',
        'jh_pemohon',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'jh_tanggal' => 'date',
        'jh_dr' => 'decimal:2',
        'jh_cr' => 'decimal:2',
    ];

    // Relationship dengan user creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship dengan user updater
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Boot method untuk auto-fill created_by dan updated_by
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
        static::created(function ($jurnal) {
            $jurnal->updateCoaBalance();
        });

        static::updated(function ($jurnal) {
            $jurnal->updateCoaBalance();
        });

        static::deleted(function ($jurnal) {
            $jurnal->reverseCoaBalance();
        });

        static::restored(function ($jurnal) {
            $jurnal->updateCoaBalance();
        });
    }

    // format tanggal indonesia
    public function getJhTanggalFormattedAttribute()
{
    return $this->jh_tanggal->translatedFormat('d F Y');
}


//agar semua inputan yang dipilih menjadi kapital guna kesamaan data
public function setJhNomorDokumenAttribute($value){
    $this->attributes['jh_nomor_dokumen'] = strtoupper($value);
}
public function setJhNomorJurnalAttribute($value){
    $this->attributes['jh_nomor_jurnal'] = strtoupper($value);
}
public function setJhNamaAccountAttribute($value){
    $this->attributes['jh_nama_account'] = strtoupper($value);
}
public function setJhCodeDeptAttribute($value){
    $this->attributes['jh_code_dept'] = strtoupper($value);
}
    public function setJhDepartemenAttribute($value){
        $this->attributes['jh_departemen'] = strtoupper($value);
    }
    public function setJhPemohonAttribute($value){
        $this->attributes['jh_pemohon'] = strtoupper($value);
    }

    public function coa(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'jh_code_account', 'coa_code');
    }


    public function updateCoaBalance()
    {
        if ($this->coa) {
            $this->coa->calculateCurrentBalance();
        }
    }

    public function reverseCoaBalance()
    {
        if ($this->coa) {
            $this->coa->calculateCurrentBalance();
        }
    }




}
