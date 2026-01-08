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
        Schema::create('my_sql_dbs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hosting_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('domain_id')->nullable()->constrained()->nullOnDelete();
            $table->string('db_name');
            $table->string('engine')->default('mysql');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_sql_dbs');
    }
};
