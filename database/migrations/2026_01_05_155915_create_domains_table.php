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
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hosting_account_id')->constrained()->cascadeOnDelete();
            $table->string('domain_name')->unique();
            $table->string('root_path');
            $table->boolean('ssl_enabled')->default(false);
            $table->string('php_version')->nullable();
            $table->boolean('enable_queue')->default(false);
            $table->boolean('enable_reverb')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
