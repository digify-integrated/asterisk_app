<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
        
        Schema::create('work_schedule_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_schedule_id')->constrained('work_schedules')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');             
            $table->boolean('is_rest_day')->default(false);            
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->time('break_start')->nullable();
            $table->time('break_end')->nullable();
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();            
            $table->timestamps();
        });

        /* =============================================================================================
            TRIGGER
        ============================================================================================= */

        DB::unprepared('DROP TRIGGER IF EXISTS trg_work_schedules_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_work_schedules_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_work_schedules_update
            AFTER UPDATE ON work_schedules
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Work schedule updated.<br/><br/>';

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

                IF audit_log <> 'Work schedule updated.<br/><br/>' THEN
                    INSERT INTO audit_log (
                        table_name,
                        reference_id,
                        log,
                        changed_by,
                        created_at
                    )
                    VALUES (
                        'work_schedules',
                        NEW.id,
                        audit_log,
                        NEW.last_log_by,
                        NOW()
                    );
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_work_schedules_insert
            AFTER INSERT ON work_schedules
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;

                SET audit_log = CONCAT(
                    'Work schedule created.<br/><br/>',
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
                    'work_schedules',
                    NEW.id,
                    audit_log,
                    NEW.last_log_by,
                    NOW()
                );
            END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS trg_work_schedule_days_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_work_schedule_days_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_work_schedule_days_update
            AFTER UPDATE ON work_schedule_days
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Work schedule day updated.<br/><br/>';
                
                DECLARE old_rest_day VARCHAR(3);
                DECLARE new_rest_day VARCHAR(3);
                DECLARE old_day_name VARCHAR(15);
                DECLARE new_day_name VARCHAR(15);

                SET old_rest_day = IF(OLD.is_rest_day, 'Yes', 'No');
                SET new_rest_day = IF(NEW.is_rest_day, 'Yes', 'No');

                SET old_day_name = CASE OLD.day_of_week
                    WHEN 1 THEN 'Monday'
                    WHEN 2 THEN 'Tuesday'
                    WHEN 3 THEN 'Wednesday'
                    WHEN 4 THEN 'Thursday'
                    WHEN 5 THEN 'Friday'
                    WHEN 6 THEN 'Saturday'
                    WHEN 7 THEN 'Sunday'
                    ELSE OLD.day_of_week
                END;

                SET new_day_name = CASE NEW.day_of_week
                    WHEN 1 THEN 'Monday'
                    WHEN 2 THEN 'Tuesday'
                    WHEN 3 THEN 'Wednesday'
                    WHEN 4 THEN 'Thursday'
                    WHEN 5 THEN 'Friday'
                    WHEN 6 THEN 'Saturday'
                    WHEN 7 THEN 'Sunday'
                    ELSE NEW.day_of_week
                END;

                IF NOT (NEW.day_of_week <=> OLD.day_of_week) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Day of Week: "',
                        COALESCE(old_day_name, 'Not set'),
                        '" → "',
                        COALESCE(new_day_name, 'Not set'),
                        '"<br/>'
                    );
                END IF;

                IF NOT (NEW.is_rest_day <=> OLD.is_rest_day) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Is Rest Day: "',
                        old_rest_day,
                        '" → "',
                        new_rest_day,
                        '"<br/>'
                    );
                END IF;

                IF NOT (NEW.start_time <=> OLD.start_time) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Start Time: "',
                        COALESCE(OLD.start_time, 'Not set'),
                        '" → "',
                        COALESCE(NEW.start_time, 'Not set'),
                        '"<br/>'
                    );
                END IF;

                IF NOT (NEW.end_time <=> OLD.end_time) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'End Time: "',
                        COALESCE(OLD.end_time, 'Not set'),
                        '" → "',
                        COALESCE(NEW.end_time, 'Not set'),
                        '"<br/>'
                    );
                END IF;

                IF NOT (NEW.break_start <=> OLD.break_start) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Break Start: "',
                        COALESCE(OLD.break_start, 'Not set'),
                        '" → "',
                        COALESCE(NEW.break_start, 'Not set'),
                        '"<br/>'
                    );
                END IF;

                IF NOT (NEW.break_end <=> OLD.break_end) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Break End: "',
                        COALESCE(OLD.break_end, 'Not set'),
                        '" → "',
                        COALESCE(NEW.break_end, 'Not set'),
                        '"<br/>'
                    );
                END IF;

                IF audit_log <> 'Work schedule day updated.<br/><br/>' THEN
                    INSERT INTO audit_log (
                        table_name,
                        reference_id,
                        log,
                        changed_by,
                        created_at
                    )
                    VALUES (
                        'work_schedule_days',
                        NEW.id,
                        audit_log,
                        NEW.last_log_by,
                        NOW()
                    );
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_work_schedule_days_insert
            AFTER INSERT ON work_schedule_days
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;
                DECLARE rest_day_text VARCHAR(3);
                DECLARE day_name VARCHAR(15);

                SET rest_day_text = IF(NEW.is_rest_day, 'Yes', 'No');

                SET day_name = CASE NEW.day_of_week
                    WHEN 1 THEN 'Monday'
                    WHEN 2 THEN 'Tuesday'
                    WHEN 3 THEN 'Wednesday'
                    WHEN 4 THEN 'Thursday'
                    WHEN 5 THEN 'Friday'
                    WHEN 6 THEN 'Saturday'
                    WHEN 7 THEN 'Sunday'
                    ELSE NEW.day_of_week
                END;

                SET audit_log = CONCAT(
                    'Work schedule day created.<br/><br/>',
                    'Day of Week: "', COALESCE(day_name, 'Not set'), '"<br/>',
                    'Is Rest Day: "', rest_day_text, '"<br/>',
                    'Start Time: "', COALESCE(NEW.start_time, 'Not set'), '"<br/>',
                    'End Time: "', COALESCE(NEW.end_time, 'Not set'), '"<br/>',
                    'Break Start: "', COALESCE(NEW.break_start, 'Not set'), '"<br/>',
                    'Break End: "', COALESCE(NEW.break_end, 'Not set'), '"<br/>'
                );

                INSERT INTO audit_log (
                    table_name,
                    reference_id,
                    log,
                    changed_by,
                    created_at
                )
                VALUES (
                    'work_schedule_days',
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
        Schema::dropIfExists('work_schedule_days');
        Schema::dropIfExists('work_schedules');
    }
};
