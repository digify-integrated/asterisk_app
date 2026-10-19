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
            $table->float('height')->default(0);
            $table->float('weight')->default(0);
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employement_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('badge_id')->nullable();
            $table->string('kiosk_pin')->nullable();
            $table->string('employee_image')->nullable();
            $table->foreignId('employee_type_id')->constrained('employee_types')->nullOnDelete();
            $table->foreignId('department_id')->constrained('departments')->nullOnDelete();
            $table->foreignId('job_position_id')->constrained('job_positions')->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->enum('employement_status', ['Active', 'Inactive'])->default('Inactive');
            $table->date('onboard_date')->nullable();
            $table->date('offboard_date')->nullable();
            $table->foreignId('departure_reason_id')->constrained('departure_reasons')->nullOnDelete();
            $table->string('departure_reason')->nullable();
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('address_type_id')->constrained('address_types')->nullOnDelete();
            $table->string('street_1')->nullable();
            $table->string('street_2')->nullable();
            $table->string('barangay')->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->enum('is_primary', ['Yes', 'No'])->default('No');
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('relation_id')->constrained('relations')->nullOnDelete();
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('relation_id')->constrained('relations')->nullOnDelete();
            $table->date('birthday')->nullable();
            $table->string('occupation')->nullable();
            $table->enum('is_dependent', ['Yes', 'No'])->default('No');
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained('document_types')->nullOnDelete();
            $table->string('document_name');
            $table->string('document_file');
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_educational_backgrounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('degree_type_id')->constrained('degree_types')->nullOnDelete();
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
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
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
            $table->enum('employment_status', ['Resigned', 'Terminated']);
            $table->unsignedSmallInteger('month_started')->nullable();
            $table->unsignedSmallInteger('year_started')->nullable();
            $table->unsignedSmallInteger('month_ended')->nullable();
            $table->unsignedSmallInteger('year_ended')->nullable();
            $table->double('last_salary')->default(0);
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        /* =============================================================================================
            TRIGGER
        ============================================================================================= */

        DB::unprepared('DROP TRIGGER IF EXISTS trg_degree_types_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_degree_types_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_degree_types_update
            AFTER UPDATE ON degree_types
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Degree type updated.<br/><br/>';

                IF NOT (NEW.name <=> OLD.name) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Name: "',
                        COALESCE(OLD.name, 'Not set'),
                        '" → "',
                        COALESCE(NEW.name, 'Not set'),
                        '"<br/>'
                    );
                END IF;

                IF audit_log <> 'Degree type updated.<br/><br/>' THEN
                    INSERT INTO audit_log (
                        table_name,
                        reference_id,
                        log,
                        changed_by,
                        created_at
                    )
                    VALUES (
                        'degree_types',
                        NEW.id,
                        audit_log,
                        NEW.last_log_by,
                        NOW()
                    );
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_degree_types_insert
            AFTER INSERT ON degree_types
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;

                SET audit_log = CONCAT(
                    'Degree type created.<br/><br/>',
                    'Name: "', COALESCE(NEW.name, 'Not set'), '"<br/>'
                );

                INSERT INTO audit_log (
                    table_name,
                    reference_id,
                    log,
                    changed_by,
                    created_at
                )
                VALUES (
                    'degree_types',
                    NEW.id,
                    audit_log,
                    NEW.last_log_by,
                    NOW()
                );
            END
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};
