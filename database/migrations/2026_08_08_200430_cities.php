<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('state_id')->constrained('states')->cascadeOnDelete();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();            
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        /* =============================================================================================
            TRIGGER
        ============================================================================================= */

        DB::unprepared('DROP TRIGGER IF EXISTS trg_cities_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_cities_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_cities_update
            AFTER UPDATE ON cities
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'City updated.<br/><br/>';
                DECLARE old_country_name VARCHAR(255);
                DECLARE new_country_name VARCHAR(255);
                DECLARE old_state_name VARCHAR(255);
                DECLARE new_state_name VARCHAR(255);

                SELECT name
                INTO old_country_name
                FROM countries
                WHERE id = OLD.country_id;

                SELECT name
                INTO new_country_name
                FROM countries
                WHERE id = NEW.country_id;

                SELECT name
                INTO old_state_name
                FROM states
                WHERE id = OLD.state_id;

                SELECT name
                INTO new_state_name
                FROM states
                WHERE id = NEW.state_id;

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

                IF NOT (NEW.state_id <=> OLD.state_id) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'State: "',
                        COALESCE(old_state_name, 'Not set'),
                        '" → "',
                        COALESCE(new_state_name, 'Not set'),
                        '"<br/>'
                    );
                END IF;

                IF NOT (NEW.country_id <=> OLD.country_id) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Country: "',
                        COALESCE(old_country_name, 'Not set'),
                        '" → "',
                        COALESCE(new_country_name, 'Not set'),
                        '"<br/>'
                    );
                END IF;

                IF audit_log <> 'City updated.<br/><br/>' THEN
                    INSERT INTO audit_log (
                        table_name,
                        reference_id,
                        log,
                        changed_by,
                        created_at
                    )
                    VALUES (
                        'cities',
                        NEW.id,
                        audit_log,
                        NEW.last_log_by,
                        NOW()
                    );
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_cities_insert
            AFTER INSERT ON cities
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;
                DECLARE country_name VARCHAR(255);
                DECLARE state_name VARCHAR(255);

                SELECT name
                INTO country_name
                FROM countries
                WHERE id = NEW.country_id;

                SELECT name
                INTO state_name
                FROM states
                WHERE id = NEW.state_id;

                SET audit_log = CONCAT(
                    'City created.<br/><br/>',
                    'Name: "', COALESCE(NEW.name, 'Not set'), '"<br/>',
                    'State: "', COALESCE(state_name, 'Not set'), '"<br/>',
                    'Country: "', COALESCE(country_name, 'Not set'), '"<br/>'
                );

                INSERT INTO audit_log (
                    table_name,
                    reference_id,
                    log,
                    changed_by,
                    created_at
                )
                VALUES (
                    'cities',
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
        Schema::dropIfExists('cities');
    }
};
