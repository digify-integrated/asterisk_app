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
        Schema::create('role', function (Blueprint $table) {
            $table->id();
            $table->string('role_name');
            $table->string('role_description');
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();

            $table->foreignId('role_id')
            ->constrained('role')
            ->cascadeOnDelete();

            $table->string('role_name');

            $table->foreignId('navigation_menu_id')
            ->constrained('navigation_menu')
            ->cascadeOnDelete();

            $table->string('navigation_menu_name');

            $table->boolean('read_access')->default(false);
            $table->boolean('write_access')->default(false);
            $table->boolean('create_access')->default(false);
            $table->boolean('delete_access')->default(false);
            $table->boolean('import_access')->default(false);
            $table->boolean('export_access')->default(false);
            $table->boolean('logs_access')->default(false);
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['role_id', 'navigation_menu_id']);

            $table->index(['role_id'], 'role_permission_role_id_idx');
            $table->index(['navigation_menu_id'], 'role_permission_navigation_menu_id_idx');
        });

        Schema::create('role_system_action_permission', function (Blueprint $table) {
            $table->id();

            $table->foreignId('role_id')
            ->constrained('role')
            ->cascadeOnDelete();

            $table->string('role_name');

            $table->foreignId('system_action_id')
            ->constrained('system_action')
            ->cascadeOnDelete();

            $table->string('system_action_name');

            $table->boolean('system_action_access')->default(false);
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['role_id', 'system_action_id']);

            $table->index(['role_id'], 'role_system_action_permission_role_id_idx');
            $table->index(['system_action_id'], 'role_system_action_permission_system_action_id_idx');
        });

        Schema::create('role_user_account', function (Blueprint $table) {
            $table->id();

            $table->foreignId('role_id')
            ->constrained('role')
            ->cascadeOnDelete();
                
            $table->string('role_name');

            $table->foreignId('user_account_id')
            ->constrained('users')
            ->cascadeOnDelete();

            $table->string('user_name');
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['role_id', 'user_account_id']);
            
            $table->index(['role_id'], 'role_user_account_role_id_idx');
            $table->index(['user_account_id'], 'role_user_account_user_account_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('role_system_action_permission');
        Schema::dropIfExists('role_user_account');
    }
};
