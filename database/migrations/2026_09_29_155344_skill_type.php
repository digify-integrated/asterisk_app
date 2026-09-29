<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_certification')->default(false);
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('skill_type_id')->constrained('skill_types')->cascadeOnDelete();
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('skill_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('progress')->default(0);
            $table->foreignId('skill_type_id')->constrained('skill_types')->cascadeOnDelete();
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        /* =============================================================================================
            TRIGGER
        ============================================================================================= */

        DB::unprepared('DROP TRIGGER IF EXISTS trg_skill_types_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_skill_types_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_skill_types_update
            AFTER UPDATE ON skill_types
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Skill type updated.<br/><br/>';

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

                IF NEW.is_certification <> OLD.is_certification THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Is Certification?: ',
                        IF(OLD.is_certification, 'Yes', 'No'),
                        ' → ',
                        IF(NEW.is_certification, 'Yes', 'No'),
                        '<br/>'
                    );
                END IF;

                IF audit_log <> 'Skill type updated.<br/><br/>' THEN
                    INSERT INTO audit_log (
                        table_name,
                        reference_id,
                        log,
                        changed_by,
                        created_at
                    )
                    VALUES (
                        'skill_types',
                        NEW.id,
                        audit_log,
                        NEW.last_log_by,
                        NOW()
                    );
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_skill_types_insert
            AFTER INSERT ON skill_types
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;

                SET audit_log = CONCAT(
                    'Skill type created.<br/><br/>',
                    'Name: "', COALESCE(NEW.name, 'Not set'), '"<br/>',
                    'Is Certification?: ', IF(NEW.is_certification, 'Yes', 'No'), '<br/>'
                );

                INSERT INTO audit_log (
                    table_name,
                    reference_id,
                    log,
                    changed_by,
                    created_at
                )
                VALUES (
                    'skill_types',
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
        Schema::dropIfExists('skill_types');
    }
};
