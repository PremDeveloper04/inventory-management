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
        Schema::create('export_files', function (Blueprint $table) {

            $table->id();

            $table->foreignId('export_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('file_name');

            $table->unsignedInteger('part_number');

            $table->unsignedBigInteger('records_count')->default(0);

            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed'
            ])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_files');
    }
};
