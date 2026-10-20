<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employment_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        /* =============================================================================================
            TRIGGER
        ============================================================================================= */

        DB::unprepared('DROP TRIGGER IF EXISTS trg_employment_statuses_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_employment_statuses_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employment_statuses_update
            AFTER UPDATE ON employment_statuses
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Employment status updated.<br/><br/>';

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

                IF audit_log <> 'Employment status updated.<br/><br/>' THEN
                    INSERT INTO audit_log (
                        table_name,
                        reference_id,
                        log,
                        changed_by,
                        created_at
                    )
                    VALUES (
                        'employment_statuses',
                        NEW.id,
                        audit_log,
                        NEW.last_log_by,
                        NOW()
                    );
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_employment_statuses_insert
            AFTER INSERT ON employment_statuses
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;

                SET audit_log = CONCAT(
                    'Employment status created.<br/><br/>',
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
                    'employment_statuses',
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
        Schema::dropIfExists('employment_statuses');
    }
};
