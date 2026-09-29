<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        /* =============================================================================================
            TRIGGER
        ============================================================================================= */

        DB::unprepared('DROP TRIGGER IF EXISTS trg_departments_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_departments_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_departments_update
            AFTER UPDATE ON departments
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Department updated.<br/><br/>';
                DECLARE old_parent_name VARCHAR(255);
                DECLARE new_parent_name VARCHAR(255);

                SELECT name
                INTO old_parent_name
                FROM departments
                WHERE id = OLD.parent_id;

                SELECT name
                INTO new_parent_name
                FROM departments
                WHERE id = NEW.parent_id;

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

                IF NOT (NEW.parent_id <=> OLD.parent_id) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Parent Department: "',
                        COALESCE(old_parent_name, 'Not set'),
                        '" → "',
                        COALESCE(new_parent_name, 'Not set'),
                        '"<br/>'
                    );
                END IF;

                IF audit_log <> 'Department updated.<br/><br/>' THEN
                    INSERT INTO audit_log (
                        table_name,
                        reference_id,
                        log,
                        changed_by,
                        created_at
                    )
                    VALUES (
                        'departments',
                        NEW.id,
                        audit_log,
                        NEW.last_log_by,
                        NOW()
                    );
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_departments_insert
            AFTER INSERT ON departments
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;
                DECLARE parent_name VARCHAR(255);

                SELECT name
                INTO parent_name
                FROM departments
                WHERE id = NEW.parent_id;

                SET audit_log = CONCAT(
                    'Department created.<br/><br/>',
                    'Name: "', COALESCE(NEW.name, 'Not set'), '"<br/>',
                    'Parent Department: "', COALESCE(parent_name, 'Not set'), '"<br/>'
                );

                INSERT INTO audit_log (
                    table_name,
                    reference_id,
                    log,
                    changed_by,
                    created_at
                )
                VALUES (
                    'departments',
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
        Schema::dropIfExists('departments');
    }
};
