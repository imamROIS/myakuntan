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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('coa_code');
            $table->string('coa_name');
             // Ubah enum menjadi string biasa untuk kustomisasi user
            $table->string('coa_type'); // Bukan enum lagi
            $table->string('coa_category'); // Bukan enum lagi
    
            // Tambahkan field untuk menentukan sifat akun
            $table->boolean('increase_on_debit')->default(true);
            $table->decimal('coa_debit', 15, 2)->default(0);
            $table->decimal('coa_credit', 15, 2)->default(0);
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
