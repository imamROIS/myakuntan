<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jurnalharians', function (Blueprint $table) {
            $table->id();

            
            // Field utama
            $table->date('jh_tanggal');
            $table->string('jh_nomor_jurnal')->nullable();
            $table->string('jh_nomor_dokumen')->nullable();
            $table->string('jh_code_account');
            $table->string('jh_nama_account');
            $table->string('jh_code_dept');
            $table->string('jh_departemen');
            $table->decimal('jh_dr', 15, 2)->default(0);
            $table->decimal('jh_cr', 15, 2)->default(0);
            $table->text('jh_keterangan')->nullable();
            $table->string('jh_pemohon');
            
            // Tracking user
            $table->foreignId('created_by')->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Timestamps dan soft delete
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnalharians');
    }
};
