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
        Schema::create('sync_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('database');
            $table->longText('sql');
            $table->integer('interval');
            $table->integer('timeout')->default(300);
            $table->integer('retry')->default(3);
            $table->timestamp('last_execute')->nullable();
            $table->boolean('active')->default(true);
            $table->string('status')->default('idle');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_jobs');
    }
};
