<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('value');
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        /* =============================================================================================
            TRIGGER
        ============================================================================================= */

        DB::unprepared('DROP TRIGGER IF EXISTS trg_system_parameter_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_system_parameter_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_system_parameter_update
            AFTER UPDATE ON system_parameters
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'System parameter updated.<br/><br/>';

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

                IF NOT (NEW.description <=> OLD.description) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Description: "',
                        COALESCE(OLD.description, 'Not set'),
                        '" → "',
                        COALESCE(NEW.description, 'Not set'),
                        '"<br/>'
                    );
                END IF;

                IF NOT (NEW.value <=> OLD.value) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Value: "',
                        COALESCE(OLD.value, 'Not set'),
                        '" → "',
                        COALESCE(NEW.value, 'Not set'),
                        '"<br/>'
                    );
                END IF;

                IF audit_log <> 'System parameter updated.<br/><br/>' THEN
                    INSERT INTO audit_log (
                        table_name,
                        reference_id,
                        log,
                        changed_by,
                        created_at
                    )
                    VALUES (
                        'system_parameters',
                        NEW.id,
                        audit_log,
                        NEW.last_log_by,
                        NOW()
                    );
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_system_parameter_insert
            AFTER INSERT ON system_parameters
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;

                SET audit_log = CONCAT(
                    'System parameter created.<br/><br/>',
                    'Name: "', COALESCE(NEW.name, 'Not set'), '"<br/>',
                    'Description: "', COALESCE(NEW.description, 'Not set'), '"<br/>',
                    'Value: "', COALESCE(NEW.value, 'Not set'), '"'
                );

                INSERT INTO audit_log (
                    table_name,
                    reference_id,
                    log,
                    changed_by,
                    created_at
                )
                VALUES (
                    'system_parameters',
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
        Schema::dropIfExists('system_parameters');
    }
};
