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
        Schema::create('navigation_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('navigation_menus')->nullOnDelete();
            $table->enum('page_type', ['folder', 'single_page', 'multi_page'])->default('folder');
            $table->integer('order_sequence')->default(0);
            $table->foreignId('last_log_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['app_id']);
        });

        Schema::create('navigation_menu_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('navigation_menu_id')->constrained('navigation_menus')->cascadeOnDelete();
            $table->enum('route_type', ['index', 'manage'])->default('index');            
            $table->string('view_file')->nullable();
            $table->string('js_file')->nullable();
            $table->foreignId('last_log_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['navigation_menu_id', 'route_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigation_menus');
        Schema::dropIfExists('navigation_menu_routes');
    }
};
