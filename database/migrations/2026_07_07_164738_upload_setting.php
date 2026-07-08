<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('upload_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('max_file_size')->default(1);
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('upload_setting_extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upload_setting_id')->constrained('upload_settings')->cascadeOnDelete();
            $table->string('extension', 10);
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['upload_setting_id']);
        });

        /* =============================================================================================
            TRIGGER
        ============================================================================================= */

        DB::unprepared('DROP TRIGGER IF EXISTS trg_upload_settings_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_upload_settings_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_upload_settings_update
            AFTER UPDATE ON upload_settings
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Upload setting updated.<br/><br/>';

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

                IF NEW.max_file_size <> OLD.max_file_size THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Maximum File Size: ',
                        OLD.max_file_size,
                        ' Mb → ',
                        NEW.max_file_size,
                        ' Mb<br/>'
                    );
                END IF;

                IF audit_log <> 'Upload setting updated.<br/><br/>' THEN
                    INSERT INTO audit_log (
                        table_name,
                        reference_id,
                        log,
                        changed_by,
                        created_at
                    )
                    VALUES (
                        'upload_settings',
                        NEW.id,
                        audit_log,
                        NEW.last_log_by,
                        NOW()
                    );
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_upload_settings_insert
            AFTER INSERT ON upload_settings
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;

                SET audit_log = CONCAT(
                    'Upload setting created.<br/><br/>',
                    'Name: "', COALESCE(NEW.name, 'Not set'), '"<br/>',
                    'Maximum File Size: ', NEW.max_file_size, ' Mb'
                );

                INSERT INTO audit_log (
                    table_name,
                    reference_id,
                    log,
                    changed_by,
                    created_at
                )
                VALUES (
                    'upload_settings',
                    NEW.id,
                    audit_log,
                    NEW.last_log_by,
                    NOW()
                );
            END
        SQL);
    
        DB::unprepared('DROP TRIGGER IF EXISTS trg_upload_setting_extensions_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_upload_setting_extensions_insert
            AFTER INSERT ON upload_setting_extensions
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;

                SET audit_log = CONCAT(
                    'Upload setting extension created.<br/><br/>',
                    'Extension: "', COALESCE(NEW.extension, 'Not set'), '"'
                );

                INSERT INTO audit_log (
                    table_name,
                    reference_id,
                    log,
                    changed_by,
                    created_at
                )
                VALUES (
                    'upload_setting_extensions',
                    NEW.id,
                    audit_log,
                    NEW.last_log_by,
                    NOW()
                );
            END
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_settings');
        Schema::dropIfExists('upload_setting_extensions');
    }
};
