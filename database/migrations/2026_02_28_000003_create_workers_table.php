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
        Schema::create('workers', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();

            $table->string('city');
            $table->string('state');
            $table->string('country')->default('India');

            $table->enum('status', ['active', 'inactive']);

            $table->integer('experience');
            $table->decimal('salary', 10, 2);

            // matched with DB (current_timestamp + on update)
            $table->timestamp('joined_at')
                ->useCurrent()
                ->useCurrentOnUpdate();

            $table->timestamps(); // created_at, updated_at

            // ✅ Indexes (as per DB)

            $table->index('name');
            $table->index('created_at');

            // ✅ Composite index (MOST IMPORTANT)
            $table->index(['status', 'city', 'created_at', 'id'], 'workers_status_city_created_at_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
