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
            $table->foreignId('gender_id')->nullable()->constrained('genders')->nullOnDelete();
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
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
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

        /* =============================================================================================
            TRIGGER
        ============================================================================================= */

        DB::unprepared('DROP TRIGGER IF EXISTS trg_employees_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employees_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employees_update
            AFTER UPDATE ON employees
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Employee updated.<br/><br/>';
                
                DECLARE old_marital_status VARCHAR(255);
                DECLARE new_marital_status VARCHAR(255);
                DECLARE old_gender VARCHAR(255);
                DECLARE new_gender VARCHAR(255);
                DECLARE old_religion VARCHAR(255);
                DECLARE new_religion VARCHAR(255);
                DECLARE old_blood_type VARCHAR(255);
                DECLARE new_blood_type VARCHAR(255);

                -- Fetch Foreign Key Labels
                SELECT name INTO old_marital_status FROM marital_statuses WHERE id = OLD.marital_status_id;
                SELECT name INTO new_marital_status FROM marital_statuses WHERE id = NEW.marital_status_id;
                SELECT name INTO old_gender FROM genders WHERE id = OLD.marital_status_id;
                SELECT name INTO new_gender FROM genders WHERE id = NEW.marital_status_id;
                SELECT name INTO old_religion FROM religions WHERE id = OLD.religion_id;
                SELECT name INTO new_religion FROM religions WHERE id = NEW.religion_id;
                SELECT name INTO old_blood_type FROM blood_types WHERE id = OLD.blood_type_id;
                SELECT name INTO new_blood_type FROM blood_types WHERE id = NEW.blood_type_id;

                IF NOT (NEW.file_as <=> OLD.file_as) THEN
                    SET audit_log = CONCAT(audit_log, 'File As: "', COALESCE(OLD.file_as, 'Not set'), '" → "', COALESCE(NEW.file_as, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.first_name <=> OLD.first_name) THEN
                    SET audit_log = CONCAT(audit_log, 'First Name: "', COALESCE(OLD.first_name, 'Not set'), '" → "', COALESCE(NEW.first_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.middle_name <=> OLD.middle_name) THEN
                    SET audit_log = CONCAT(audit_log, 'Middle Name: "', COALESCE(OLD.middle_name, 'Not set'), '" → "', COALESCE(NEW.middle_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.last_name <=> OLD.last_name) THEN
                    SET audit_log = CONCAT(audit_log, 'Last Name: "', COALESCE(OLD.last_name, 'Not set'), '" → "', COALESCE(NEW.last_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.suffix <=> OLD.suffix) THEN
                    SET audit_log = CONCAT(audit_log, 'Suffix: "', COALESCE(OLD.suffix, 'Not set'), '" → "', COALESCE(NEW.suffix, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.nickname <=> OLD.nickname) THEN
                    SET audit_log = CONCAT(audit_log, 'Nickname: "', COALESCE(OLD.nickname, 'Not set'), '" → "', COALESCE(NEW.nickname, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.phone <=> OLD.phone) THEN
                    SET audit_log = CONCAT(audit_log, 'Phone: "', COALESCE(OLD.phone, 'Not set'), '" → "', COALESCE(NEW.phone, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.email <=> OLD.email) THEN
                    SET audit_log = CONCAT(audit_log, 'Email: "', COALESCE(OLD.email, 'Not set'), '" → "', COALESCE(NEW.email, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.birthday <=> OLD.birthday) THEN
                    SET audit_log = CONCAT(audit_log, 'Birthday: "', COALESCE(OLD.birthday, 'Not set'), '" → "', COALESCE(NEW.birthday, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.place_of_birth <=> OLD.place_of_birth) THEN
                    SET audit_log = CONCAT(audit_log, 'Place of Birth: "', COALESCE(OLD.place_of_birth, 'Not set'), '" → "', COALESCE(NEW.place_of_birth, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.marital_status_id <=> OLD.marital_status_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Marital Status: "', COALESCE(old_marital_status, 'Not set'), '" → "', COALESCE(new_marital_status, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.gender_id <=> OLD.gender_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Gender: "', COALESCE(old_gender, 'Not set'), '" → "', COALESCE(new_gender, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.religion_id <=> OLD.religion_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Religion: "', COALESCE(old_religion, 'Not set'), '" → "', COALESCE(new_religion, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.blood_type_id <=> OLD.blood_type_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Blood Type: "', COALESCE(old_blood_type, 'Not set'), '" → "', COALESCE(new_blood_type, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.height <=> OLD.height) THEN
                    SET audit_log = CONCAT(audit_log, 'Height: "', COALESCE(OLD.height, 'Not set'), '" → "', COALESCE(NEW.height, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.weight <=> OLD.weight) THEN
                    SET audit_log = CONCAT(audit_log, 'Weight: "', COALESCE(OLD.weight, 'Not set'), '" → "', COALESCE(NEW.weight, 'Not set'), '"<br/>');
                END IF;

                IF audit_log <> 'Employee updated.<br/><br/>' THEN
                    INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                    VALUES ('employees', NEW.id, audit_log, NEW.last_log_by, NOW());
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employees_insert
            AFTER INSERT ON employees
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;
                DECLARE marital_status VARCHAR(255);
                DECLARE gender VARCHAR(255);
                DECLARE religion VARCHAR(255);
                DECLARE blood_type VARCHAR(255);

                SELECT name INTO marital_status FROM marital_statuses WHERE id = NEW.marital_status_id;
                SELECT name INTO gender FROM genders WHERE id = NEW.gender_id;
                SELECT name INTO religion FROM religions WHERE id = NEW.religion_id;
                SELECT name INTO blood_type FROM blood_types WHERE id = NEW.blood_type_id;

                SET audit_log = CONCAT(
                    'Employee created.<br/><br/>',
                    'File As: "', COALESCE(NEW.file_as, 'Not set'), '"<br/>',
                    'First Name: "', COALESCE(NEW.first_name, 'Not set'), '"<br/>',
                    'Middle Name: "', COALESCE(NEW.middle_name, 'Not set'), '"<br/>',
                    'Last Name: "', COALESCE(NEW.last_name, 'Not set'), '"<br/>',
                    'Suffix: "', COALESCE(NEW.suffix, 'Not set'), '"<br/>',
                    'Nickname: "', COALESCE(NEW.nickname, 'Not set'), '"<br/>',
                    'Phone: "', COALESCE(NEW.phone, 'Not set'), '"<br/>',
                    'Email: "', COALESCE(NEW.email, 'Not set'), '"<br/>',
                    'Birthday: "', COALESCE(NEW.birthday, 'Not set'), '"<br/>',
                    'Place of Birth: "', COALESCE(NEW.place_of_birth, 'Not set'), '"<br/>',
                    'Marital Status: "', COALESCE(marital_status, 'Not set'), '"<br/>',
                    'Gender: "', COALESCE(gender, 'Not set'), '"<br/>',
                    'Religion: "', COALESCE(religion, 'Not set'), '"<br/>',
                    'Blood Type: "', COALESCE(blood_type, 'Not set'), '"<br/>',
                    'Height: "', COALESCE(NEW.height, 'Not set'), '"<br/>',
                    'Weight: "', COALESCE(NEW.weight, 'Not set'), '"<br/>'
                );

                INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                VALUES ('employees', NEW.id, audit_log, NEW.last_log_by, NOW());
            END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS trg_employment_details_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employment_details_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employment_details_update
            AFTER UPDATE ON employment_details
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Employment details updated.<br/><br/>';
                
                DECLARE old_company VARCHAR(255);
                DECLARE new_company VARCHAR(255);
                DECLARE old_emp_type VARCHAR(255);
                DECLARE new_emp_type VARCHAR(255);
                DECLARE old_dept VARCHAR(255);
                DECLARE new_dept VARCHAR(255);
                DECLARE old_job_pos VARCHAR(255);
                DECLARE new_job_pos VARCHAR(255);
                DECLARE old_manager VARCHAR(255);
                DECLARE new_manager VARCHAR(255);
                DECLARE old_emp_status VARCHAR(255);
                DECLARE new_emp_status VARCHAR(255);
                DECLARE old_dep_reason VARCHAR(255);
                DECLARE new_dep_reason VARCHAR(255);

                SELECT legal_name INTO old_company FROM companies WHERE id = OLD.company_id;
                SELECT legal_name INTO new_company FROM companies WHERE id = NEW.company_id;
                SELECT name INTO old_emp_type FROM employment_types WHERE id = OLD.employment_type_id;
                SELECT name INTO new_emp_type FROM employment_types WHERE id = NEW.employment_type_id;
                SELECT name INTO old_dept FROM departments WHERE id = OLD.department_id;
                SELECT name INTO new_dept FROM departments WHERE id = NEW.department_id;
                SELECT name INTO old_job_pos FROM job_positions WHERE id = OLD.job_position_id;
                SELECT name INTO new_job_pos FROM job_positions WHERE id = NEW.job_position_id;
                SELECT file_as INTO old_manager FROM employees WHERE id = OLD.manager_id;
                SELECT file_as INTO new_manager FROM employees WHERE id = NEW.manager_id;
                SELECT name INTO old_emp_status FROM employment_statuses WHERE id = OLD.employment_status_id;
                SELECT name INTO new_emp_status FROM employment_statuses WHERE id = NEW.employment_status_id;
                SELECT name INTO old_dep_reason FROM departure_reasons WHERE id = OLD.departure_reason_id;
                SELECT name INTO new_dep_reason FROM departure_reasons WHERE id = NEW.departure_reason_id;

                IF NOT (NEW.badge_id <=> OLD.badge_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Badge ID: "', COALESCE(OLD.badge_id, 'Not set'), '" → "', COALESCE(NEW.badge_id, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.kiosk_pin <=> OLD.kiosk_pin) THEN
                    SET audit_log = CONCAT(audit_log, 'Kiosk PIN updated.<br/>');
                END IF;

                IF NOT (NEW.employment_type_id <=> OLD.employment_type_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Employment Type: "', COALESCE(old_emp_type, 'Not set'), '" → "', COALESCE(new_emp_type, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.department_id <=> OLD.department_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Department: "', COALESCE(old_dept, 'Not set'), '" → "', COALESCE(new_dept, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.job_position_id <=> OLD.job_position_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Job Position: "', COALESCE(old_job_pos, 'Not set'), '" → "', COALESCE(new_job_pos, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.manager_id <=> OLD.manager_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Manager: "', COALESCE(old_manager, 'Not set'), '" → "', COALESCE(new_manager, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.company_id <=> OLD.company_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Company: "', COALESCE(old_company, 'Not set'), '" → "', COALESCE(new_company, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.employment_status_id <=> OLD.employment_status_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Employment Status: "', COALESCE(old_emp_status, 'Not set'), '" → "', COALESCE(new_emp_status, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.onboard_date <=> OLD.onboard_date) THEN
                    SET audit_log = CONCAT(audit_log, 'Onboard Date: "', COALESCE(OLD.onboard_date, 'Not set'), '" → "', COALESCE(NEW.onboard_date, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.offboard_date <=> OLD.offboard_date) THEN
                    SET audit_log = CONCAT(audit_log, 'Offboard Date: "', COALESCE(OLD.offboard_date, 'Not set'), '" → "', COALESCE(NEW.offboard_date, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.departure_reason_id <=> OLD.departure_reason_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Departure Reason: "', COALESCE(old_dep_reason, 'Not set'), '" → "', COALESCE(new_dep_reason, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.departure_remarks <=> OLD.departure_remarks) THEN
                    SET audit_log = CONCAT(audit_log, 'Departure Remarks: "', COALESCE(OLD.departure_remarks, 'Not set'), '" → "', COALESCE(NEW.departure_remarks, 'Not set'), '"<br/>');
                END IF;

                IF audit_log <> 'Employment details updated.<br/><br/>' THEN
                    INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                    VALUES ('employment_details', NEW.employee_id, audit_log, NEW.last_log_by, NOW());
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employment_details_insert
            AFTER INSERT ON employment_details
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;
                DECLARE company VARCHAR(255);
                DECLARE emp_type VARCHAR(255);
                DECLARE dept VARCHAR(255);
                DECLARE job_pos VARCHAR(255);
                DECLARE manager VARCHAR(255);
                DECLARE emp_status VARCHAR(255);
                DECLARE dep_reason VARCHAR(255);

                SELECT legal_name INTO company FROM companies WHERE id = NEW.company_id;
                SELECT name INTO emp_type FROM employment_types WHERE id = NEW.employment_type_id;
                SELECT name INTO dept FROM departments WHERE id = NEW.department_id;
                SELECT name INTO job_pos FROM job_positions WHERE id = NEW.job_position_id;
                SELECT file_as INTO manager FROM employees WHERE id = NEW.manager_id;
                SELECT name INTO emp_status FROM employment_statuses WHERE id = NEW.employment_status_id;
                SELECT name INTO dep_reason FROM departure_reasons WHERE id = NEW.departure_reason_id;

                SET audit_log = CONCAT(
                    'Employment details created.<br/><br/>',
                    'Badge ID: "', COALESCE(NEW.badge_id, 'Not set'), '"<br/>',
                    'Employment Type: "', COALESCE(emp_type, 'Not set'), '"<br/>',
                    'Department: "', COALESCE(dept, 'Not set'), '"<br/>',
                    'Job Position: "', COALESCE(job_pos, 'Not set'), '"<br/>',
                    'Manager: "', COALESCE(manager, 'Not set'), '"<br/>',
                    'Company: "', COALESCE(company, 'Not set'), '"<br/>',
                    'Employment Status: "', COALESCE(emp_status, 'Not set'), '"<br/>',
                    'Onboard Date: "', COALESCE(NEW.onboard_date, 'Not set'), '"<br/>'
                );

                INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                VALUES ('employment_details', NEW.employee_id, audit_log, NEW.last_log_by, NOW());
            END
        SQL);
    
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employee_identifications_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employee_identifications_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employee_identifications_update
            AFTER UPDATE ON employee_identifications
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Employee identification updated.<br/><br/>';
                DECLARE old_id_type VARCHAR(255);
                DECLARE new_id_type VARCHAR(255);

                SELECT name INTO old_id_type FROM id_types WHERE id = OLD.id_type_id;
                SELECT name INTO new_id_type FROM id_types WHERE id = NEW.id_type_id;

                IF NOT (NEW.id_type_id <=> OLD.id_type_id) THEN
                    SET audit_log = CONCAT(audit_log, 'ID Type: "', COALESCE(old_id_type, 'Not set'), '" → "', COALESCE(new_id_type, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.id_number <=> OLD.id_number) THEN
                    SET audit_log = CONCAT(audit_log, 'ID Number: "', COALESCE(OLD.id_number, 'Not set'), '" → "', COALESCE(NEW.id_number, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.issued_at <=> OLD.issued_at) THEN
                    SET audit_log = CONCAT(audit_log, 'Issued At: "', COALESCE(OLD.issued_at, 'Not set'), '" → "', COALESCE(NEW.issued_at, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.expires_at <=> OLD.expires_at) THEN
                    SET audit_log = CONCAT(audit_log, 'Expires At: "', COALESCE(OLD.expires_at, 'Not set'), '" → "', COALESCE(NEW.expires_at, 'Not set'), '"<br/>');
                END IF;

                IF audit_log <> 'Employee identification updated.<br/><br/>' THEN
                    INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                    VALUES ('employee_identifications', NEW.employee_id, audit_log, NEW.last_log_by, NOW());
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employee_identifications_insert
            AFTER INSERT ON employee_identifications
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;
                DECLARE id_type_name VARCHAR(255);

                SELECT name INTO id_type_name FROM id_types WHERE id = NEW.id_type_id;

                SET audit_log = CONCAT(
                    'Employee identification created.<br/><br/>',
                    'ID Type: "', COALESCE(id_type_name, 'Not set'), '"<br/>',
                    'ID Number: "', COALESCE(NEW.id_number, 'Not set'), '"<br/>',
                    'Issued At: "', COALESCE(NEW.issued_at, 'Not set'), '"<br/>',
                    'Expires At: "', COALESCE(NEW.expires_at, 'Not set'), '"<br/>'
                );

                INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                VALUES ('employee_identifications', NEW.employee_id, audit_log, NEW.last_log_by, NOW());
            END
        SQL);
        
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employee_addresses_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employee_addresses_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employee_addresses_update
            AFTER UPDATE ON employee_addresses
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Employee address updated.<br/><br/>';
                
                DECLARE old_address_type VARCHAR(255);
                DECLARE new_address_type VARCHAR(255);
                DECLARE old_city_name VARCHAR(255);
                DECLARE new_city_name VARCHAR(255);
                DECLARE old_state_name VARCHAR(255);
                DECLARE new_state_name VARCHAR(255);
                DECLARE old_country_name VARCHAR(255);
                DECLARE new_country_name VARCHAR(255);

                SELECT name INTO old_address_type FROM address_types WHERE id = OLD.address_type_id;
                SELECT name INTO new_address_type FROM address_types WHERE id = NEW.address_type_id;
                SELECT name INTO old_city_name FROM cities WHERE id = OLD.city_id;
                SELECT name INTO new_city_name FROM cities WHERE id = NEW.city_id;
                SELECT name INTO old_state_name FROM states WHERE id = OLD.state_id;
                SELECT name INTO new_state_name FROM states WHERE id = NEW.state_id;
                SELECT name INTO old_country_name FROM countries WHERE id = OLD.country_id;
                SELECT name INTO new_country_name FROM countries WHERE id = NEW.country_id;

                IF NOT (NEW.address_type_id <=> OLD.address_type_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Address Type: "', COALESCE(old_address_type, 'Not set'), '" → "', COALESCE(new_address_type, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.street_1 <=> OLD.street_1) THEN
                    SET audit_log = CONCAT(audit_log, 'Street 1: "', COALESCE(OLD.street_1, 'Not set'), '" → "', COALESCE(NEW.street_1, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.street_2 <=> OLD.street_2) THEN
                    SET audit_log = CONCAT(audit_log, 'Street 2: "', COALESCE(OLD.street_2, 'Not set'), '" → "', COALESCE(NEW.street_2, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.barangay <=> OLD.barangay) THEN
                    SET audit_log = CONCAT(audit_log, 'Barangay: "', COALESCE(OLD.barangay, 'Not set'), '" → "', COALESCE(NEW.barangay, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.city_id <=> OLD.city_id) THEN
                    SET audit_log = CONCAT(audit_log, 'City: "', COALESCE(old_city_name, 'Not set'), '" → "', COALESCE(new_city_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.state_id <=> OLD.state_id) THEN
                    SET audit_log = CONCAT(audit_log, 'State/Province: "', COALESCE(old_state_name, 'Not set'), '" → "', COALESCE(new_state_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.country_id <=> OLD.country_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Country: "', COALESCE(old_country_name, 'Not set'), '" → "', COALESCE(new_country_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.is_primary <=> OLD.is_primary) THEN
                    SET audit_log = CONCAT(audit_log, 'Is Primary: "', COALESCE(OLD.is_primary, 0), '" → "', COALESCE(NEW.is_primary, 0), '"<br/>');
                END IF;

                IF audit_log <> 'Employee address updated.<br/><br/>' THEN
                    INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                    VALUES ('employee_addresses', NEW.employee_id, audit_log, NEW.updated_by, NOW());
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employee_addresses_insert
            AFTER INSERT ON employee_addresses
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;
                DECLARE address_type_name VARCHAR(255);
                DECLARE city_name VARCHAR(255);
                DECLARE state_name VARCHAR(255);
                DECLARE country_name VARCHAR(255);

                SELECT name INTO address_type_name FROM address_types WHERE id = NEW.address_type_id;
                SELECT name INTO city_name FROM cities WHERE id = NEW.city_id;
                SELECT name INTO state_name FROM states WHERE id = NEW.state_id;
                SELECT name INTO country_name FROM countries WHERE id = NEW.country_id;

                SET audit_log = CONCAT(
                    'Employee address created.<br/><br/>',
                    'Address Type: "', COALESCE(address_type_name, 'Not set'), '"<br/>',
                    'Address: "', COALESCE(NEW.street_1, ''), ' ', COALESCE(NEW.street_2, ''), '"<br/>',
                    'Barangay: "', COALESCE(NEW.barangay, 'Not set'), '"<br/>',
                    'City: "', COALESCE(city_name, 'Not set'), '"<br/>',
                    'State/Province: "', COALESCE(state_name, 'Not set'), '"<br/>',
                    'Country: "', COALESCE(country_name, 'Not set'), '"<br/>'
                );

                INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                VALUES ('employee_addresses', NEW.employee_id, audit_log, NEW.updated_by, NOW());
            END
        SQL);
        
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employee_emergency_contacts_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employee_emergency_contacts_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employee_emergency_contacts_update
            AFTER UPDATE ON employee_emergency_contacts
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Emergency contact updated.<br/><br/>';
                DECLARE old_relation VARCHAR(255);
                DECLARE new_relation VARCHAR(255);

                SELECT name INTO old_relation FROM relations WHERE id = OLD.relation_id;
                SELECT name INTO new_relation FROM relations WHERE id = NEW.relation_id;

                IF NOT (NEW.name <=> OLD.name) THEN
                    SET audit_log = CONCAT(audit_log, 'Name: "', COALESCE(OLD.name, 'Not set'), '" → "', COALESCE(NEW.name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.phone <=> OLD.phone) THEN
                    SET audit_log = CONCAT(audit_log, 'Phone: "', COALESCE(OLD.phone, 'Not set'), '" → "', COALESCE(NEW.phone, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.email <=> OLD.email) THEN
                    SET audit_log = CONCAT(audit_log, 'Email: "', COALESCE(OLD.email, 'Not set'), '" → "', COALESCE(NEW.email, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.relation_id <=> OLD.relation_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Relation: "', COALESCE(old_relation, 'Not set'), '" → "', COALESCE(new_relation, 'Not set'), '"<br/>');
                END IF;

                IF audit_log <> 'Emergency contact updated.<br/><br/>' THEN
                    INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                    VALUES ('employee_emergency_contacts', NEW.employee_id, audit_log, NEW.updated_by, NOW());
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employee_emergency_contacts_insert
            AFTER INSERT ON employee_emergency_contacts
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;
                DECLARE relation_name VARCHAR(255);

                SELECT name INTO relation_name FROM relations WHERE id = NEW.relation_id;

                SET audit_log = CONCAT(
                    'Emergency contact created.<br/><br/>',
                    'Name: "', COALESCE(NEW.name, 'Not set'), '"<br/>',
                    'Phone: "', COALESCE(NEW.phone, 'Not set'), '"<br/>',
                    'Email: "', COALESCE(NEW.email, 'Not set'), '"<br/>',
                    'Relation: "', COALESCE(relation_name, 'Not set'), '"<br/>'
                );

                INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                VALUES ('employee_emergency_contacts', NEW.employee_id, audit_log, NEW.updated_by, NOW());
            END
        SQL);
        
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employee_family_members_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employee_family_members_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employee_family_members_update
            AFTER UPDATE ON employee_family_members
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Family member updated.<br/><br/>';
                DECLARE old_relation VARCHAR(255);
                DECLARE new_relation VARCHAR(255);

                SELECT name INTO old_relation FROM relations WHERE id = OLD.relation_id;
                SELECT name INTO new_relation FROM relations WHERE id = NEW.relation_id;

                IF NOT (NEW.name <=> OLD.name) THEN
                    SET audit_log = CONCAT(audit_log, 'Name: "', COALESCE(OLD.name, 'Not set'), '" → "', COALESCE(NEW.name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.relation_id <=> OLD.relation_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Relation: "', COALESCE(old_relation, 'Not set'), '" → "', COALESCE(new_relation, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.birthday <=> OLD.birthday) THEN
                    SET audit_log = CONCAT(audit_log, 'Birthday: "', COALESCE(OLD.birthday, 'Not set'), '" → "', COALESCE(NEW.birthday, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.occupation <=> OLD.occupation) THEN
                    SET audit_log = CONCAT(audit_log, 'Occupation: "', COALESCE(OLD.occupation, 'Not set'), '" → "', COALESCE(NEW.occupation, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.is_dependent <=> OLD.is_dependent) THEN
                    SET audit_log = CONCAT(audit_log, 'Is Dependent: "', COALESCE(OLD.is_dependent, 0), '" → "', COALESCE(NEW.is_dependent, 0), '"<br/>');
                END IF;

                IF audit_log <> 'Family member updated.<br/><br/>' THEN
                    INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                    VALUES ('employee_family_members', NEW.employee_id, audit_log, NEW.updated_by, NOW());
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employee_family_members_insert
            AFTER INSERT ON employee_family_members
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;
                DECLARE relation_name VARCHAR(255);

                SELECT name INTO relation_name FROM relations WHERE id = NEW.relation_id;

                SET audit_log = CONCAT(
                    'Family member created.<br/><br/>',
                    'Name: "', COALESCE(NEW.name, 'Not set'), '"<br/>',
                    'Relation: "', COALESCE(relation_name, 'Not set'), '"<br/>',
                    'Birthday: "', COALESCE(NEW.birthday, 'Not set'), '"<br/>',
                    'Occupation: "', COALESCE(NEW.occupation, 'Not set'), '"<br/>',
                    'Is Dependent: "', COALESCE(NEW.is_dependent, 0), '"<br/>'
                );

                INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                VALUES ('employee_family_members', NEW.employee_id, audit_log, NEW.updated_by, NOW());
            END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS trg_employee_documents_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employee_documents_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employee_documents_update
            AFTER UPDATE ON employee_documents
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Employee document updated.<br/><br/>';
                DECLARE old_doc_type VARCHAR(255);
                DECLARE new_doc_type VARCHAR(255);

                SELECT name INTO old_doc_type FROM document_types WHERE id = OLD.document_type_id;
                SELECT name INTO new_doc_type FROM document_types WHERE id = NEW.document_type_id;

                IF NOT (NEW.document_type_id <=> OLD.document_type_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Document Type: "', COALESCE(old_doc_type, 'Not set'), '" → "', COALESCE(new_doc_type, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.document_name <=> OLD.document_name) THEN
                    SET audit_log = CONCAT(audit_log, 'Document Name: "', COALESCE(OLD.document_name, 'Not set'), '" → "', COALESCE(NEW.document_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.document_path <=> OLD.document_path) THEN
                    SET audit_log = CONCAT(audit_log, 'Document Path updated.<br/>');
                END IF;

                IF audit_log <> 'Employee document updated.<br/><br/>' THEN
                    INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                    VALUES ('employee_documents', NEW.employee_id, audit_log, NEW.updated_by, NOW());
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employee_documents_insert
            AFTER INSERT ON employee_documents
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;
                DECLARE doc_type_name VARCHAR(255);

                SELECT name INTO doc_type_name FROM document_types WHERE id = NEW.document_type_id;

                SET audit_log = CONCAT(
                    'Employee document created.<br/><br/>',
                    'Document Type: "', COALESCE(doc_type_name, 'Not set'), '"<br/>',
                    'Document Name: "', COALESCE(NEW.document_name, 'Not set'), '"<br/>'
                );

                INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                VALUES ('employee_documents', NEW.employee_id, audit_log, NEW.updated_by, NOW());
            END
        SQL);
        
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employee_educational_backgrounds_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employee_educational_backgrounds_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employee_educational_backgrounds_update
            AFTER UPDATE ON employee_educational_backgrounds
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Educational background updated.<br/><br/>';
                
                DECLARE old_edu_level VARCHAR(255);
                DECLARE new_edu_level VARCHAR(255);
                DECLARE old_city_name VARCHAR(255);
                DECLARE new_city_name VARCHAR(255);
                DECLARE old_state_name VARCHAR(255);
                DECLARE new_state_name VARCHAR(255);
                DECLARE old_country_name VARCHAR(255);
                DECLARE new_country_name VARCHAR(255);

                SELECT name INTO old_edu_level FROM educational_levels WHERE id = OLD.educational_level_id;
                SELECT name INTO new_edu_level FROM educational_levels WHERE id = NEW.educational_level_id;
                SELECT name INTO old_city_name FROM cities WHERE id = OLD.city_id;
                SELECT name INTO new_city_name FROM cities WHERE id = NEW.city_id;
                SELECT name INTO old_state_name FROM states WHERE id = OLD.state_id;
                SELECT name INTO new_state_name FROM states WHERE id = NEW.state_id;
                SELECT name INTO old_country_name FROM countries WHERE id = OLD.country_id;
                SELECT name INTO new_country_name FROM countries WHERE id = NEW.country_id;

                IF NOT (NEW.educational_level_id <=> OLD.educational_level_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Educational Level: "', COALESCE(old_edu_level, 'Not set'), '" → "', COALESCE(new_edu_level, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.school_name <=> OLD.school_name) THEN
                    SET audit_log = CONCAT(audit_log, 'School Name: "', COALESCE(OLD.school_name, 'Not set'), '" → "', COALESCE(NEW.school_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.street_1 <=> OLD.street_1) THEN
                    SET audit_log = CONCAT(audit_log, 'Street 1: "', COALESCE(OLD.street_1, 'Not set'), '" → "', COALESCE(NEW.street_1, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.street_2 <=> OLD.street_2) THEN
                    SET audit_log = CONCAT(audit_log, 'Street 2: "', COALESCE(OLD.street_2, 'Not set'), '" → "', COALESCE(NEW.street_2, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.barangay <=> OLD.barangay) THEN
                    SET audit_log = CONCAT(audit_log, 'Barangay: "', COALESCE(OLD.barangay, 'Not set'), '" → "', COALESCE(NEW.barangay, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.city_id <=> OLD.city_id) THEN
                    SET audit_log = CONCAT(audit_log, 'City: "', COALESCE(old_city_name, 'Not set'), '" → "', COALESCE(new_city_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.state_id <=> OLD.state_id) THEN
                    SET audit_log = CONCAT(audit_log, 'State/Province: "', COALESCE(old_state_name, 'Not set'), '" → "', COALESCE(new_state_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.country_id <=> OLD.country_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Country: "', COALESCE(old_country_name, 'Not set'), '" → "', COALESCE(new_country_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.degree_earned <=> OLD.degree_earned) THEN
                    SET audit_log = CONCAT(audit_log, 'Degree Earned: "', COALESCE(OLD.degree_earned, 'Not set'), '" → "', COALESCE(NEW.degree_earned, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.year_started <=> OLD.year_started) THEN
                    SET audit_log = CONCAT(audit_log, 'Year Started: "', COALESCE(OLD.year_started, 'Not set'), '" → "', COALESCE(NEW.year_started, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.year_graduated <=> OLD.year_graduated) THEN
                    SET audit_log = CONCAT(audit_log, 'Year Graduated: "', COALESCE(OLD.year_graduated, 'Not set'), '" → "', COALESCE(NEW.year_graduated, 'Not set'), '"<br/>');
                END IF;

                IF audit_log <> 'Educational background updated.<br/><br/>' THEN
                    INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                    VALUES ('employee_educational_backgrounds', NEW.employee_id, audit_log, NEW.updated_by, NOW());
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employee_educational_backgrounds_insert
            AFTER INSERT ON employee_educational_backgrounds
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;
                DECLARE edu_level VARCHAR(255);
                DECLARE city_name VARCHAR(255);
                DECLARE state_name VARCHAR(255);
                DECLARE country_name VARCHAR(255);

                SELECT name INTO edu_level FROM educational_levels WHERE id = NEW.educational_level_id;
                SELECT name INTO city_name FROM cities WHERE id = NEW.city_id;
                SELECT name INTO state_name FROM states WHERE id = NEW.state_id;
                SELECT name INTO country_name FROM countries WHERE id = NEW.country_id;

                SET audit_log = CONCAT(
                    'Educational background created.<br/><br/>',
                    'Educational Level: "', COALESCE(edu_level, 'Not set'), '"<br/>',
                    'School Name: "', COALESCE(NEW.school_name, 'Not set'), '"<br/>',
                    'Degree Earned: "', COALESCE(NEW.degree_earned, 'Not set'), '"<br/>',
                    'City: "', COALESCE(city_name, 'Not set'), '"<br/>',
                    'Country: "', COALESCE(country_name, 'Not set'), '"<br/>'
                );

                INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                VALUES ('employee_educational_backgrounds', NEW.employee_id, audit_log, NEW.updated_by, NOW());
            END
        SQL);
        
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employee_employment_histories_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employee_employment_histories_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employee_employment_histories_update
            AFTER UPDATE ON employee_employment_histories
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Employment history updated.<br/><br/>';
                
                DECLARE old_city_name VARCHAR(255);
                DECLARE new_city_name VARCHAR(255);
                DECLARE old_state_name VARCHAR(255);
                DECLARE new_state_name VARCHAR(255);
                DECLARE old_country_name VARCHAR(255);
                DECLARE new_country_name VARCHAR(255);
                DECLARE old_emp_type VARCHAR(255);
                DECLARE new_emp_type VARCHAR(255);
                DECLARE old_emp_status VARCHAR(255);
                DECLARE new_emp_status VARCHAR(255);

                SELECT name INTO old_city_name FROM cities WHERE id = OLD.city_id;
                SELECT name INTO new_city_name FROM cities WHERE id = NEW.city_id;
                SELECT name INTO old_state_name FROM states WHERE id = OLD.state_id;
                SELECT name INTO new_state_name FROM states WHERE id = NEW.state_id;
                SELECT name INTO old_country_name FROM countries WHERE id = OLD.country_id;
                SELECT name INTO new_country_name FROM countries WHERE id = NEW.country_id;
                SELECT name INTO old_emp_type FROM employment_types WHERE id = OLD.employment_type_id;
                SELECT name INTO new_emp_type FROM employment_types WHERE id = NEW.employment_type_id;
                SELECT name INTO old_emp_status FROM employment_statuses WHERE id = OLD.employment_status_id;
                SELECT name INTO new_emp_status FROM employment_statuses WHERE id = NEW.employment_status_id;

                IF NOT (NEW.company_name <=> OLD.company_name) THEN
                    SET audit_log = CONCAT(audit_log, 'Company Name: "', COALESCE(OLD.company_name, 'Not set'), '" → "', COALESCE(NEW.company_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.street_1 <=> OLD.street_1) THEN
                    SET audit_log = CONCAT(audit_log, 'Street 1: "', COALESCE(OLD.street_1, 'Not set'), '" → "', COALESCE(NEW.street_1, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.street_2 <=> OLD.street_2) THEN
                    SET audit_log = CONCAT(audit_log, 'Street 2: "', COALESCE(OLD.street_2, 'Not set'), '" → "', COALESCE(NEW.street_2, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.barangay <=> OLD.barangay) THEN
                    SET audit_log = CONCAT(audit_log, 'Barangay: "', COALESCE(OLD.barangay, 'Not set'), '" → "', COALESCE(NEW.barangay, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.city_id <=> OLD.city_id) THEN
                    SET audit_log = CONCAT(audit_log, 'City: "', COALESCE(old_city_name, 'Not set'), '" → "', COALESCE(new_city_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.state_id <=> OLD.state_id) THEN
                    SET audit_log = CONCAT(audit_log, 'State/Province: "', COALESCE(old_state_name, 'Not set'), '" → "', COALESCE(new_state_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.country_id <=> OLD.country_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Country: "', COALESCE(old_country_name, 'Not set'), '" → "', COALESCE(new_country_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.position_title <=> OLD.position_title) THEN
                    SET audit_log = CONCAT(audit_log, 'Position Title: "', COALESCE(OLD.position_title, 'Not set'), '" → "', COALESCE(NEW.position_title, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.department <=> OLD.department) THEN
                    SET audit_log = CONCAT(audit_log, 'Department: "', COALESCE(OLD.department, 'Not set'), '" → "', COALESCE(NEW.department, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.employment_type_id <=> OLD.employment_type_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Employment Type: "', COALESCE(old_emp_type, 'Not set'), '" → "', COALESCE(new_emp_type, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.employment_status_id <=> OLD.employment_status_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Employment Status: "', COALESCE(old_emp_status, 'Not set'), '" → "', COALESCE(new_emp_status, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.last_salary <=> OLD.last_salary) THEN
                    SET audit_log = CONCAT(audit_log, 'Last Salary: "', COALESCE(OLD.last_salary, 0), '" → "', COALESCE(NEW.last_salary, 0), '"<br/>');
                END IF;

                IF audit_log <> 'Employment history updated.<br/><br/>' THEN
                    INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                    VALUES ('employee_employment_histories', NEW.employee_id, audit_log, NEW.updated_by, NOW());
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employee_employment_histories_insert
            AFTER INSERT ON employee_employment_histories
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;
                DECLARE city_name VARCHAR(255);
                DECLARE country_name VARCHAR(255);
                DECLARE emp_type VARCHAR(255);

                SELECT name INTO city_name FROM cities WHERE id = NEW.city_id;
                SELECT name INTO country_name FROM countries WHERE id = NEW.country_id;
                SELECT name INTO emp_type FROM employment_types WHERE id = NEW.employment_type_id;

                SET audit_log = CONCAT(
                    'Employment history created.<br/><br/>',
                    'Company Name: "', COALESCE(NEW.company_name, 'Not set'), '"<br/>',
                    'Position Title: "', COALESCE(NEW.position_title, 'Not set'), '"<br/>',
                    'Department: "', COALESCE(NEW.department, 'Not set'), '"<br/>',
                    'Employment Type: "', COALESCE(emp_type, 'Not set'), '"<br/>',
                    'Last Salary: "', COALESCE(NEW.last_salary, 0), '"<br/>'
                );

                INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                VALUES ('employee_employment_histories', NEW.employee_id, audit_log, NEW.updated_by, NOW());
            END
        SQL);
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