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
        Schema::create('hosting_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('disk_limit_mb')->nullable();
            $table->unsignedBigInteger('bandwidth_limit_mb')->nullable();
            $table->unsignedBigInteger('database_limit')->nullable();
            $table->unsignedBigInteger('email_limit')->nullable();
            $table->unsignedBigInteger('domain_limit')->nullable();
            $table->boolean('is_suspended')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hosting_accounts');
    }
};
