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
        Schema::create('upload_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('max_file_size')->default(1);
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('upload_setting_extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upload_setting_id')->constrained('upload_settings')->cascadeOnDelete();
            $table->string('extension', 10);
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['upload_setting_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_settings');
        Schema::dropIfExists('upload_setting_extensions');
    }
};
