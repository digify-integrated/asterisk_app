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
        Schema::create('navigation_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('navigation_menus')->nullOnDelete();
            $table->enum('page_type', ['menu', 'single_page', 'multi_page'])->default('menu');
            $table->integer('order_sequence')->default(0);
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['parent_id', 'page_type']);
        });

        Schema::create('navigation_menu_apps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('navigation_menu_id')->constrained('navigation_menus')->cascadeOnDelete();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['navigation_menu_id', 'app_id']);
        });

        Schema::create('navigation_menu_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('navigation_menu_id')->constrained('navigation_menus')->cascadeOnDelete();
            $table->enum('route_type', ['index', 'manage'])->default('index');            
            $table->string('view_file')->nullable();
            $table->string('js_file')->nullable();
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->unique(['navigation_menu_id', 'route_type']);
        });

        /* =============================================================================================
            TRIGGER
        ============================================================================================= */

        DB::unprepared('DROP TRIGGER IF EXISTS trg_navigation_menu_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_navigation_menu_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_navigation_menu_update
            AFTER UPDATE ON navigation_menus
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Navigation menu updated.<br/><br/>';

                DECLARE old_app_name VARCHAR(255);
                DECLARE new_app_name VARCHAR(255);

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

                IF NOT (NEW.icon <=> OLD.icon) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Icon: "',
                        COALESCE(OLD.icon, 'Not set'),
                        '" → "',
                        COALESCE(NEW.icon, 'Not set'),
                        '"<br/>'
                    );
                END IF;

                IF NOT (NEW.page_type <=> OLD.page_type) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Page Type: "',
                        
                        OLD.page_type,
                        '" → "',
                        NEW.page_type,
                        '"<br/>'
                    );
                END IF;

                IF NEW.order_sequence <> OLD.order_sequence THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Order Sequence: ',
                        OLD.order_sequence,
                        ' → ',
                        NEW.order_sequence,
                        '<br/>'
                    );
                END IF;

                IF audit_log <> 'Navigation menu updated.<br/><br/>' THEN
                    INSERT INTO audit_log (
                        table_name,
                        reference_id,
                        log,
                        changed_by,
                        created_at
                    )
                    VALUES (
                        'navigation_menus',
                        NEW.id,
                        audit_log,
                        NEW.last_log_by,
                        NOW()
                    );
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_navigation_menu_insert
            AFTER INSERT ON navigation_menus
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;

                SET audit_log = CONCAT(
                    'Navigation menu created.<br/><br/>',
                    'Name: "', COALESCE(NEW.name, 'Not set'), '"<br/>',
                    'Icon: "', COALESCE(NEW.icon, 'Not set'), '"<br/>',
                    'Page Type: "', NEW.page_type, '"<br/>',
                    'Order Sequence: ', NEW.order_sequence
                );

                INSERT INTO audit_log (
                    table_name,
                    reference_id,
                    log,
                    changed_by,
                    created_at
                )
                VALUES (
                    'navigation_menus',
                    NEW.id,
                    audit_log,
                    NEW.last_log_by,
                    NOW()
                );
            END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS trg_navigation_menu_app_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_navigation_menu_app_insert
            AFTER INSERT ON navigation_menu_apps
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;
                DECLARE app_name VARCHAR(255);
                DECLARE navigation_menu_name VARCHAR(255);

                SELECT name
                INTO app_name
                FROM apps
                WHERE id = NEW.app_id;

                SELECT name
                INTO navigation_menu_name
                FROM navigation_menus
                WHERE id = NEW.navigation_menu_id;

                SET audit_log = CONCAT(
                    'Navigation menu app created.<br/><br/>',
                    'Navigation Menu: "', COALESCE(navigation_menu_name, 'Not set'), '"<br/>',
                    'App: "', COALESCE(app_name, 'Not set'), '"<br/>',
                );

                INSERT INTO audit_log (
                    table_name,
                    reference_id,
                    log,
                    changed_by,
                    created_at
                )
                VALUES (
                    'navigation_menu_apps',
                    NEW.id,
                    audit_log,
                    NEW.last_log_by,
                    NOW()
                );
            END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS trg_navigation_menu_route_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_navigation_menu_route_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_navigation_menu_route_update
            AFTER UPDATE ON navigation_menu_routes
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Navigation menu route updated.<br/><br/>';

                IF NOT (NEW.route_type <=> OLD.route_type) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Route Type: "',
                        OLD.route_type,
                        '" → "',
                        NEW.route_type,
                        '"<br/>'
                    );
                END IF;

                IF NOT (NEW.view_file <=> OLD.view_file) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'View File: "',
                        COALESCE(OLD.view_file, 'Not set'),
                        '" → "',
                        COALESCE(NEW.view_file, 'Not set'),
                        '"<br/>'
                    );
                END IF;

                IF NOT (NEW.js_file <=> OLD.js_file) THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'JavaScript File: "',
                        COALESCE(OLD.js_file, 'Not set'),
                        '" → "',
                        COALESCE(NEW.js_file, 'Not set'),
                        '"<br/>'
                    );
                END IF;

                IF audit_log <> 'Navigation menu route updated.<br/><br/>' THEN
                    INSERT INTO audit_log (
                        table_name,
                        reference_id,
                        log,
                        changed_by,
                        created_at
                    )
                    VALUES (
                        'navigation_menu_routes',
                        NEW.id,
                        audit_log,
                        NEW.last_log_by,
                        NOW()
                    );
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_navigation_menu_route_insert
            AFTER INSERT ON navigation_menu_routes
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;
                DECLARE menu_name VARCHAR(255);

                SELECT name
                INTO menu_name
                FROM navigation_menus
                WHERE id = NEW.navigation_menu_id;

                SET audit_log = CONCAT(
                    'Navigation menu route created.<br/><br/>',
                    'Navigation Menu: "', COALESCE(menu_name, 'Not set'), '"<br/>',
                    'Route Type: "', NEW.route_type, '"<br/>',
                    'View File: "', COALESCE(NEW.view_file, 'Not set'), '"<br/>',
                    'JavaScript File: "', COALESCE(NEW.js_file, 'Not set'), '"'
                );

                INSERT INTO audit_log (
                    table_name,
                    reference_id,
                    log,
                    changed_by,
                    created_at
                )
                VALUES (
                    'navigation_menu_routes',
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
        Schema::dropIfExists('navigation_menus');
        Schema::dropIfExists('navigation_menu_routes');
    }
};
