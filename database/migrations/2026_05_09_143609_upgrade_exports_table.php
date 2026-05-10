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
        Schema::table('exports', function (Blueprint $table) {

            $table->unsignedBigInteger('total_records')->default(0);

            $table->unsignedBigInteger('processed_records')->default(0);

            $table->unsignedInteger('total_parts')->default(0);

            $table->unsignedInteger('completed_parts')->default(0);

            $table->string('export_name')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exports', function (Blueprint $table) {

            $table->dropColumn([
                'total_records',
                'processed_records',
                'total_parts',
                'completed_parts',
                'export_name'
            ]);

        });
    }
};
