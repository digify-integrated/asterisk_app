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
        Schema::create('navigation_menu', function (Blueprint $table) {
            $table->id();
            $table->string('navigation_menu_name');

            $table->string('navigation_menu_icon')
            ->nullable();

            $table->foreignId('app_id')
            ->nullable();

            $table->string('app_name')
            ->nullable();

            $table->bigInteger('parent_navigation_menu_id')
            ->nullable();

            $table->string('parent_navigation_menu_name')
            ->nullable();

            $table->enum('page_type', ['modal', 'route'])
                ->nullable();

            $table->string('database_table')
            ->nullable();

            $table->integer('order_sequence')
            ->default(0);

            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['app_id'], 'navigation_menu_app_id_idx');
            $table->index(['parent_navigation_menu_id'], 'navigation_menu_parent_navigation_menu_id_idx');
        });

        Schema::create('navigation_menu_route', function (Blueprint $table) {
            $table->id();

            $table->foreignId('navigation_menu_id')
            ->constrained('navigation_menu')
            ->cascadeOnDelete();

            $table->enum('route_type', ['index', 'details', 'new', 'import'])
            ->default('index');
            
            $table->string('view_file')->nullable();
            $table->string('js_file')->nullable();
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['navigation_menu_id', 'route_type'], 'navigation_menu_route_app_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigation_menu');
        Schema::dropIfExists('navigation_menu_route');
    }
};
