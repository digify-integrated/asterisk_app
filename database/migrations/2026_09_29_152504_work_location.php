<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('location_type', ['Home', 'Office', 'Others'])->default('Office');
            
            $table->string('street_1')->nullable();
            $table->string('street_2')->nullable();
            $table->string('barangay')->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();

            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        /* =============================================================================================
            TRIGGERS
        ============================================================================================= */

        DB::unprepared('DROP TRIGGER IF EXISTS trg_work_locations_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_work_locations_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_work_locations_update
            AFTER UPDATE ON work_locations
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Work location updated.<br/><br/>';
                
                DECLARE old_city_name VARCHAR(255);
                DECLARE new_city_name VARCHAR(255);
                DECLARE old_state_name VARCHAR(255);
                DECLARE new_state_name VARCHAR(255);
                DECLARE old_country_name VARCHAR(255);
                DECLARE new_country_name VARCHAR(255);

                -- Fetch Foreign Key Labels
                SELECT name INTO old_city_name FROM cities WHERE id = OLD.city_id;
                SELECT name INTO new_city_name FROM cities WHERE id = NEW.city_id;
                SELECT name INTO old_state_name FROM states WHERE id = OLD.state_id;
                SELECT name INTO new_state_name FROM states WHERE id = NEW.state_id;
                SELECT name INTO old_country_name FROM countries WHERE id = OLD.country_id;
                SELECT name INTO new_country_name FROM countries WHERE id = NEW.country_id;

                IF NOT (NEW.name <=> OLD.name) THEN
                    SET audit_log = CONCAT(audit_log, 'Name: "', COALESCE(OLD.name, 'Not set'), '" → "', COALESCE(NEW.name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.location_type <=> OLD.location_type) THEN
                    SET audit_log = CONCAT(audit_log, 'Location Type: "', COALESCE(OLD.location_type, 'Not set'), '" → "', COALESCE(NEW.location_type, 'Not set'), '"<br/>');

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

                IF audit_log <> 'Work location updated.<br/><br/>' THEN
                    INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                    VALUES ('work_locations', NEW.id, audit_log, NEW.last_log_by, NOW());
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_work_locations_insert
            AFTER INSERT ON work_locations
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;
                DECLARE city_name VARCHAR(255);
                DECLARE state_name VARCHAR(255);
                DECLARE country_name VARCHAR(255);

                SELECT name INTO city_name FROM cities WHERE id = NEW.city_id;
                SELECT name INTO state_name FROM states WHERE id = NEW.state_id;
                SELECT name INTO country_name FROM countries WHERE id = NEW.country_id;

                SET audit_log = CONCAT(
                    'Work location created.<br/><br/>',
                    'Name: "', COALESCE(NEW.name, 'Not set'), '"<br/>',
                    'Location Type: "', COALESCE(NEW.location_type, 'Not set'), '"<br/>',
                    'Address: "', COALESCE(NEW.street_1, ''), ' ', COALESCE(NEW.street_2, ''), '"<br/>',
                    'Barangay: "', COALESCE(NEW.barangay, 'Not set'), '"<br/>',
                    'City: "', COALESCE(city_name, 'Not set'), '"<br/>',
                    'State/Province: "', COALESCE(state_name, 'Not set'), '"<br/>',
                    'Country: "', COALESCE(country_name, 'Not set'), '"<br/>'
                );

                INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                VALUES ('work_locations', NEW.id, audit_log, NEW.last_log_by, NOW());
            END
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('work_locations');
    }
};
