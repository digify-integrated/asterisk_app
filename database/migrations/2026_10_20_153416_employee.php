<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('file_as');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->string('nickname')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->date('birthday')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->foreignId('marital_status_id')->nullable()->constrained('marital_statuses')->nullOnDelete();
            $table->foreignId('religion_id')->nullable()->constrained('religions')->nullOnDelete();
            $table->foreignId('blood_type_id')->nullable()->constrained('blood_types')->nullOnDelete();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('badge_id')->nullable()->unique();
            $table->string('kiosk_pin')->nullable();
            $table->string('employee_image')->nullable();
            $table->foreignId('employment_type_id')->nullable()->constrained('employment_types')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('job_position_id')->nullable()->constrained('job_positions')->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('employment_status_id')->nullable()->constrained('employment_statuses')->nullOnDelete();
            $table->date('onboard_date')->nullable();
            $table->date('offboard_date')->nullable();
            $table->foreignId('departure_reason_id')->nullable()->constrained('departure_reasons')->nullOnDelete();
            $table->text('departure_remarks')->nullable();
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_identifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('id_type_id')->nullable()->constrained('id_types')->nullOnDelete();
            $table->string('id_number');
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('file_path')->nullable();            
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();            
            $table->timestamps();

            $table->unique(['employee_id', 'id_type_id'], 'emp_id_type_unique');
            $table->index('id_number');
        });

        Schema::create('employee_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('address_type_id')->nullable()->constrained('address_types')->nullOnDelete();
            $table->string('street_1')->nullable();
            $table->string('street_2')->nullable();
            $table->string('barangay')->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->foreignId('updated_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('relation_id')->nullable()->constrained('relations')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('relation_id')->nullable()->constrained('relations')->nullOnDelete();
            $table->date('birthday')->nullable();
            $table->string('occupation')->nullable();
            $table->boolean('is_dependent')->default(false);
            $table->foreignId('updated_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('document_type_id')->nullable()->constrained('document_types')->nullOnDelete();
            $table->string('document_name');
            $table->string('document_path');
            $table->foreignId('updated_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_educational_backgrounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('educational_level_id')->nullable()->constrained('educational_levels')->nullOnDelete();
            $table->string('school_name');
            $table->string('street_1')->nullable();
            $table->string('street_2')->nullable();
            $table->string('barangay')->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('degree_earned')->nullable();
            $table->unsignedSmallInteger('year_started')->nullable();
            $table->unsignedSmallInteger('year_graduated')->nullable();
            $table->foreignId('updated_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_employment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('street_1')->nullable();
            $table->string('street_2')->nullable();
            $table->string('barangay')->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('position_title')->nullable();
            $table->string('department')->nullable();
            $table->foreignId('employment_type_id')->nullable()->constrained('employment_types')->nullOnDelete();
            $table->foreignId('employment_status_id')->nullable()->constrained('employment_statuses')->nullOnDelete();
            $table->unsignedSmallInteger('month_started')->nullable();
            $table->unsignedSmallInteger('year_started')->nullable();
            $table->unsignedSmallInteger('month_ended')->nullable();
            $table->unsignedSmallInteger('year_ended')->nullable();
            $table->decimal('last_salary', 12, 2)->default(0);
            $table->foreignId('updated_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_employment_histories');
        Schema::dropIfExists('employee_educational_backgrounds');
        Schema::dropIfExists('employee_documents');
        Schema::dropIfExists('employee_family_members');
        Schema::dropIfExists('employee_emergency_contacts');
        Schema::dropIfExists('employee_addresses');
        Schema::dropIfExists('employee_identifications');
        Schema::dropIfExists('employment_details');
        Schema::dropIfExists('employees');
    }
};