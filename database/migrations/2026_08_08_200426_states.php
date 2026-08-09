<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        /* =============================================================================================
            TRIGGER
        ============================================================================================= */

        DB::unprepared('DROP TRIGGER IF EXISTS trg_states_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_states_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_states_update
            AFTER UPDATE ON states
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'State updated.<br/><br/>';
                DECLARE old_country_name VARCHAR(255);
                DECLARE new_country_name VARCHAR(255);

                SELECT name
                INTO old_country_name
                FROM countries
                WHERE id = OLD.country_id;

                SELECT name
                INTO new_country_name
                FROM countries
                WHERE id = NEW.country_id;

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

                IF audit_log <> 'State updated.<br/><br/>' THEN
                    INSERT INTO audit_log (
                        table_name,
                        reference_id,
                        log,
                        changed_by,
                        created_at
                    )
                    VALUES (
                        'states',
                        NEW.id,
                        audit_log,
                        NEW.last_log_by,
                        NOW()
                    );
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_states_insert
            AFTER INSERT ON states
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;
                DECLARE country_name VARCHAR(255);

                SELECT name
                INTO country_name
                FROM countries
                WHERE id = NEW.country_id;

                SET audit_log = CONCAT(
                    'State created.<br/><br/>',
                    'Name: "', COALESCE(NEW.name, 'Not set'), '"<br/>',
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
                    'states',
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
        Schema::dropIfExists('states');
    }
};
