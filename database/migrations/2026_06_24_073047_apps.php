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
        Schema::create('apps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('logo')->nullable();
            $table->integer('order_sequence')->default(0);
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        /* =============================================================================================
            TRIGGER
        ============================================================================================= */

        DB::unprepared('DROP TRIGGER IF EXISTS trg_app_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_app_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_app_update
            AFTER UPDATE ON apps
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'App changed.<br/><br/>';

                IF NEW.name <> OLD.name THEN
                    SET audit_log = CONCAT(audit_log, "Name: ", OLD.name, " -> ", NEW.name, "<br/>");
                END IF;

                IF NEW.description <> OLD.description THEN
                    SET audit_log = CONCAT(audit_log, "Description: ", OLD.description, " -> ", NEW.description, "<br/>");
                END IF;

                IF NEW.order_sequence <> OLD.order_sequence THEN
                    SET audit_log = CONCAT(audit_log, "Order Sequence: ", OLD.order_sequence, " -> ", NEW.order_sequence, "<br/>");
                END IF;
                
                IF audit_log <> 'App changed.<br/><br/>' THEN
                    INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at) 
                    VALUES ('apps', NEW.id, audit_log, NEW.last_log_by, NOW());
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_app_insert
            AFTER INSERT ON apps
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'App created.';

                INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at) 
                VALUES ('apps', NEW.id, audit_log, NEW.last_log_by, NOW());
            END
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps');
    }
};
