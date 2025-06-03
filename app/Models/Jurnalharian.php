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

    // Gunakan method updateDebitCreditOnly yang sudah diperbaiki
    static::created(function ($jurnal) {
        if ($jurnal->coa) {
            $jurnal->coa->updateDebitCreditOnly($jurnal->jh_dr, $jurnal->jh_cr);
        }
    });

    static::updated(function ($jurnal) {
        if ($jurnal->coa) {
            $drChange = $jurnal->jh_dr - ($jurnal->getOriginal('jh_dr') ?? 0);
            $crChange = $jurnal->jh_cr - ($jurnal->getOriginal('jh_cr') ?? 0);
            $jurnal->coa->updateDebitCreditOnly($drChange, $crChange);
        }
    });

    static::deleted(function ($jurnal) {
        if ($jurnal->coa) {
            $jurnal->coa->updateDebitCreditOnly(-$jurnal->jh_dr, -$jurnal->jh_cr);
        }
    });

    static::restored(function ($jurnal) {
        if ($jurnal->coa) {
            $jurnal->coa->updateDebitCreditOnly($jurnal->jh_dr, $jurnal->jh_cr);
        }
    });
}
//    protected static function boot()
// {
//     parent::boot();

//     static::creating(function ($model) {
//         if (auth()->check()) {
//             $model->created_by = auth()->id();
//         }
//     });

//     static::updating(function ($model) {
//         if (auth()->check()) {
//             $model->updated_by = auth()->id();
//         }
//     });

//     // Perubahan di sini - ganti semua pemanggilan updateDebitCreditBalances()
//     static::created(function ($jurnal) {
//         $jurnal->coa?->updateDebitCreditOnly($jurnal->jh_dr, $jurnal->jh_cr);
//     });

//     static::updated(function ($jurnal) {
//         $drChange = $jurnal->jh_dr - ($jurnal->getOriginal('jh_dr') ?? 0);
//         $crChange = $jurnal->jh_cr - ($jurnal->getOriginal('jh_cr') ?? 0);
//         $jurnal->coa?->updateDebitCreditOnly($drChange, $crChange);
//     });

//     static::deleted(function ($jurnal) {
//         $jurnal->coa?->updateDebitCreditOnly(-$jurnal->jh_dr, -$jurnal->jh_cr);
//     });

//     static::restored(function ($jurnal) {
//         $jurnal->coa?->updateDebitCreditOnly($jurnal->jh_dr, $jurnal->jh_cr);
//     });
// }
    
    // format tanggal indonesia
    public function getJhTanggalFormattedAttribute()
    {
        return $this->jh_tanggal->translatedFormat('d F Y');
    }

    // Mutators untuk kapitalisasi
    public function setJhNomorDokumenAttribute($value) {
        $this->attributes['jh_nomor_dokumen'] = strtoupper($value);
    }
    public function setJhNomorJurnalAttribute($value) {
        $this->attributes['jh_nomor_jurnal'] = strtoupper($value);
    }
    public function setJhNamaAccountAttribute($value) {
        $this->attributes['jh_nama_account'] = strtoupper($value);
    }
    public function setJhCodeDeptAttribute($value) {
        $this->attributes['jh_code_dept'] = strtoupper($value);
    }
    public function setJhDepartemenAttribute($value) {
        $this->attributes['jh_departemen'] = strtoupper($value);
    }
    public function setJhPemohonAttribute($value) {
        $this->attributes['jh_pemohon'] = strtoupper($value);
    }

    public function coa(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'jh_code_account', 'coa_code');
    }

    public static function getAvailableYears()
    {
        $driver = config('database.default');
        
        if ($driver === 'sqlite') {
            return self::query()
                ->selectRaw("strftime('%Y', jh_tanggal) as year")
                ->distinct()
                ->pluck('year', 'year');
        } else {
            return self::query()
                ->selectRaw('YEAR(jh_tanggal) as year')
                ->distinct()
                ->pluck('year', 'year');
        }
    }
}
// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Support\Str;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use App\Models\ChartOfAccount;
// use User;

// class Jurnalharian extends Model
// {
//     //

//     use HasFactory, SoftDeletes;

//     protected $table = 'jurnalharians';

//     protected $fillable = [
//         'jh_tanggal',
//         'jh_nomor_jurnal',
//         'jh_nomor_dokumen',
//         'jh_code_account',
//         'jh_nama_account',
//         'jh_code_dept',
//         'jh_departemen',
//         'jh_dr',
//         'jh_cr',
//         'jh_keterangan',
//         'jh_pemohon',
//         'created_by',
//         'updated_by',
//     ];

//     protected $casts = [
//         'jh_tanggal' => 'date',
//         'jh_dr' => 'decimal:2',
//         'jh_cr' => 'decimal:2',
//     ];

//     // Relationship dengan user creator
//     public function creator()
//     {
//         return $this->belongsTo(User::class, 'created_by');
//     }

//     // Relationship dengan user updater
//     public function updater()
//     {
//         return $this->belongsTo(User::class, 'updated_by');
//     }

//     // UPDATE credit dan debit // Boot method untuk auto-fill created_by dan updated_by
//     protected static function boot()
//     {
//         parent::boot();
    
//         static::creating(function ($model) {
//             if (auth()->check()) {
//                 $model->created_by = auth()->id();
//             }
//         });
    
//         static::updating(function ($model) {
//             if (auth()->check()) {
//                 $model->updated_by = auth()->id();
//             }
//         });
    
//         static::created(function ($jurnal) {
//             $jurnal->updateCoaBalance();
//             $jurnal->coa?->updateDebitCreditBalances();
//         });
    
//         static::updated(function ($jurnal) {
//             $jurnal->updateCoaBalance();
//             $jurnal->coa?->updateDebitCreditBalances();
//         });
    
//         static::deleted(function ($jurnal) {
//             $jurnal->reverseCoaBalance();
//             $jurnal->coa?->updateDebitCreditBalances();
//         });
    
//         static::restored(function ($jurnal) {
//             $jurnal->updateCoaBalance();
//             $jurnal->coa?->updateDebitCreditBalances();
//         });
//     }
    

//     // Boot method untuk auto-fill created_by dan updated_by
    

//     // format tanggal indonesia
//     public function getJhTanggalFormattedAttribute()
// {
//     return $this->jh_tanggal->translatedFormat('d F Y');
// }


// //agar semua inputan yang dipilih menjadi kapital guna kesamaan data
// public function setJhNomorDokumenAttribute($value){
//     $this->attributes['jh_nomor_dokumen'] = strtoupper($value);
// }
// public function setJhNomorJurnalAttribute($value){
//     $this->attributes['jh_nomor_jurnal'] = strtoupper($value);
// }
// public function setJhNamaAccountAttribute($value){
//     $this->attributes['jh_nama_account'] = strtoupper($value);
// }
// public function setJhCodeDeptAttribute($value){
//     $this->attributes['jh_code_dept'] = strtoupper($value);
// }
//     public function setJhDepartemenAttribute($value){
//         $this->attributes['jh_departemen'] = strtoupper($value);
//     }
//     public function setJhPemohonAttribute($value){
//         $this->attributes['jh_pemohon'] = strtoupper($value);
//     }

//     public function coa(): BelongsTo
//     {
//         return $this->belongsTo(ChartOfAccount::class, 'jh_code_account', 'coa_code');
//     }


//     public function updateCoaBalance()
//     {
//         if ($this->coa) {
//             $this->coa->calculateCurrentBalance();
//         }
//     }

//     public function reverseCoaBalance()
//     {
//         if ($this->coa) {
//             $this->coa->calculateCurrentBalance();
//         }
//     }

//     public static function getAvailableYears()
// {
//     $driver = config('database.default');
    
//     if ($driver === 'sqlite') {
//         return self::query()
//             ->selectRaw("strftime('%Y', jh_tanggal) as year")
//             ->distinct()
//             ->pluck('year', 'year');
//     } else { // MySQL/MariaDB/PostgreSQL
//         return self::query()
//             ->selectRaw('YEAR(jh_tanggal) as year')
//             ->distinct()
//             ->pluck('year', 'year');
//     }
// }




// }
