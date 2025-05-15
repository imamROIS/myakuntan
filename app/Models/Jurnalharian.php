<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

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
    }

    // format tanggal indonesia
    public function getJhTanggalFormattedAttribute()
{
    return $this->jh_tanggal->translatedFormat('d F Y');
}


//agar semua inputan yang dipilih menjadi kapital guna kesamaan data
    public function setJhNomorDokumenAttribute($value){
        $this->attributes['jh_nomor_dokumen'] = Str::upper($value);
    }
    public function setJhNomorJurnalAttribute($value){
        $this->attributes['jh_nomor_jurnal'] = Str::upper($value);
    }
    public function setJhNamaAccountAttribute($value){
        $this->attributes['jh_nama_account'] = Str::upper($value);
    }
    public function setJhCodeDeptAttribute($value){
        $this->attributes['jh_code_dept'] = Str::upper($value);
    }
    public function setJhDepartementtAttribute($value){
        $this->attributes['jh_departemen'] = Str::upper($value);
    }
    public function setJhPemohonAttribute($value){
        $this->attributes['jh_pemohon'] = Str::upper($value);
    }




}
