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
            $table->string('file_as'); // e.g., "Doe, John A."
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
            $table->decimal('height_cm', 5, 2)->nullable(); // Normalized with unit specification
            $table->decimal('weight_kg', 5, 2)->nullable(); // Normalized with unit specification
            $table->foreignId('updated_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('badge_id')->nullable()->unique();
            $table->string('kiosk_pin')->nullable();
            $table->string('employee_image')->nullable();
            $table->foreignId('employee_type_id')->nullable()->constrained('employee_types')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('job_position_id')->nullable()->constrained('job_positions')->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->enum('employment_status', ['Active', 'Inactive', 'Suspended', 'On Leave'])->default('Inactive');
            $table->date('onboard_date')->nullable();
            $table->date('offboard_date')->nullable();
            $table->foreignId('departure_reason_id')->nullable()->constrained('departure_reasons')->nullOnDelete();
            $table->text('departure_remarks')->nullable(); // Renamed from duplicate departure_reason string
            $table->foreignId('updated_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Added for 201 File Compliance (e.g., SSS, PhilHealth, Pag-IBIG, TIN)
        Schema::create('employee_government_ids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('id_type'); // e.g., 'SSS', 'PhilHealth', 'Pag-IBIG', 'TIN'
            $table->string('id_number');
            $table->foreignId('updated_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
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
            $table->boolean('is_primary')->default(false); // Normalized to native boolean
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
            $table->boolean('is_dependent')->default(false); // Normalized to native boolean
            $table->foreignId('updated_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('document_type_id')->nullable()->constrained('document_types')->nullOnDelete();
            $table->string('document_name');
            $table->string('document_path'); // Renamed from document_file for storage convention clarity
            $table->foreignId('updated_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_educational_backgrounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('degree_type_id')->nullable()->constrained('degree_types')->nullOnDelete();
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
            $table->foreignId('employee_type_id')->nullable()->constrained('employee_types')->nullOnDelete();
            $table->string('employment_status')->nullable(); // Changed from strict enum to string to support diverse past separations (Resigned, Terminated, Retired, Contract Ended)
            $table->unsignedSmallInteger('month_started')->nullable();
            $table->unsignedSmallInteger('year_started')->nullable();
            $table->unsignedSmallInteger('month_ended')->nullable();
            $table->unsignedSmallInteger('year_ended')->nullable();
            $table->decimal('last_salary', 12, 2)->default(0); // Swapped double to decimal for financial accuracy
            $table->foreignId('updated_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
        Schema::dropIfExists('employee_disciplinary_records');
        Schema::dropIfExists('employee_trainings');
        Schema::dropIfExists('employee_employment_histories');
        Schema::dropIfExists('employee_educational_backgrounds');
        Schema::dropIfExists('employee_family_members');
        Schema::dropIfExists('employee_emergency_contacts');
        Schema::dropIfExists('employee_addresses');
        Schema::dropIfExists('employee_bank_accounts');
        Schema::dropIfExists('employee_government_ids');
        Schema::dropIfExists('employment_details');
        Schema::dropIfExists('employees');
    }
};