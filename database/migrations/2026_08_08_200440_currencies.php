<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol');
            $table->string('shorthand');
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        /* =============================================================================================
            TRIGGER
        ============================================================================================= */

        DB::unprepared('DROP TRIGGER IF EXISTS trg_currencies_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_currencies_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_currencies_update
            AFTER UPDATE ON currencies
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Currency updated.<br/><br/>';

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

                IF NOT (NEW.symbol <=> OLD.symbol) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Symbol: "',
                        COALESCE(OLD.symbol, 'Not set'),
                        '" → "',
                        COALESCE(NEW.symbol, 'Not set'),
                        '"<br/>'
                    );
                END IF;

                IF NOT (NEW.shorthand <=> OLD.shorthand) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Shorthand: "',
                        COALESCE(OLD.shorthand, 'Not set'),
                        '" → "',
                        COALESCE(NEW.shorthand, 'Not set'),
                        '"<br/>'
                    );
                END IF;

                IF audit_log <> 'Currency updated.<br/><br/>' THEN
                    INSERT INTO audit_log (
                        table_name,
                        reference_id,
                        log,
                        changed_by,
                        created_at
                    )
                    VALUES (
                        'currencies',
                        NEW.id,
                        audit_log,
                        NEW.last_log_by,
                        NOW()
                    );
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_currencies_insert
            AFTER INSERT ON currencies
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;

                SET audit_log = CONCAT(
                    'Currency created.<br/><br/>',
                    'Name: "', COALESCE(NEW.name, 'Not set'), '"<br/>',
                    'Symbol: "', COALESCE(NEW.symbol, 'Not set'), '"<br/>',
                    'Shorthand: "', COALESCE(NEW.shorthand, 'Not set'), '"'
                );

                INSERT INTO audit_log (
                    table_name,
                    reference_id,
                    log,
                    changed_by,
                    created_at
                )
                VALUES (
                    'currencies',
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
        Schema::dropIfExists('currencies');
    }
};
