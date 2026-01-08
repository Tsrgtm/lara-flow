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
        Schema::create('database_user_my_sql_db', function (Blueprint $table) {
            $table->id();
            $table->foreignId('database_user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('my_sql_db_id')->constrained()->cascadeOnDelete();

            $table->string('privileges')->default('ALL PRIVILEGES');
            $table->timestamps();

            $table->unique(['database_user_id', 'my_sql_db_id'], 'db_user_link_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_user_my_sql_db');
    }
};
