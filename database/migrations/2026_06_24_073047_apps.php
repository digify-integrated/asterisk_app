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
        Schema::create('app', function (Blueprint $table) {
            $table->id();
            $table->string('app_name');

            $table->string('app_description')
            ->nullable();

            $table->string('app_version')
            ->default('1.0.0');

            $table->string('app_logo')
            ->nullable();

            $table->foreignId('navigation_menu_id')
            ->nullable();

            $table->string('navigation_menu_name')
            ->nullable();
            
            $table->integer('order_sequence')
            ->default(0);

            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['navigation_menu_id'], 'idx_app_navigation_menu_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app');
    }
};
