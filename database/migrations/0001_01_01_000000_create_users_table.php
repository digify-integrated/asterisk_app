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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('profile_picture')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('status')->default('Inactive');
            $table->rememberToken();
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        /* =============================================================================================
            TRIGGER
        ============================================================================================= */

        DB::unprepared('DROP TRIGGER IF EXISTS trg_users_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_users_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_app_update
            AFTER UPDATE ON users
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'User changed.<br/><br/>';

                IF NEW.name <> OLD.name THEN
                    SET audit_log = CONCAT(audit_log, "Name: ", OLD.name, " -> ", NEW.name, "<br/>");
                END IF;

                IF NEW.email <> OLD.email THEN
                    SET audit_log = CONCAT(audit_log, "Email: ", OLD.email, " -> ", NEW.email, "<br/>");
                END IF;

                IF NEW.status <> OLD.status THEN
                    SET audit_log = CONCAT(audit_log, "Status: ", OLD.status, " -> ", NEW.status, "<br/>");
                END IF;
                
                IF audit_log <> 'User changed.<br/><br/>' THEN
                    INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at) 
                    VALUES ('users', NEW.id, audit_log, NEW.last_log_by, NOW());
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_app_insert
            AFTER INSERT ON users
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'User created.';

                INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at) 
                VALUES ('users', NEW.id, audit_log, NEW.last_log_by, NOW());
            END
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
