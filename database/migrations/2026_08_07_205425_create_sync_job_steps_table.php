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
        Schema::create('sync_job_steps', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('sync_job_id')->constrained()->cascadeOnDelete();
            $table->integer('step_no');
            $table->string('name');
            $table->longText('sql');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique([
                'sync_job_id',
                'step_no'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_job_steps');
    }
};
