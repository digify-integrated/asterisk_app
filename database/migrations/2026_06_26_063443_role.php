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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('navigation_menu_id')->constrained('navigation_menus')->cascadeOnDelete();
            $table->boolean('read_access')->default(false);
            $table->boolean('write_access')->default(false);
            $table->boolean('create_access')->default(false);
            $table->boolean('delete_access')->default(false);
            $table->boolean('export_access')->default(false);
            $table->boolean('logs_access')->default(false);            
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['role_id', 'navigation_menu_id']);
        });

        Schema::create('role_system_action_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('system_action_id')->constrained('system_actions')->cascadeOnDelete();
            $table->boolean('access')->default(false);
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['role_id', 'system_action_id']);
            
            $table->index(['role_id', 'system_action_id']);
        });

        Schema::create('role_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['role_id', 'user_id']);
        });

        /* =============================================================================================
            TRIGGER
        ============================================================================================= */

        DB::unprepared('DROP TRIGGER IF EXISTS trg_role_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_role_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_role_update
            AFTER UPDATE ON roles
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Role updated.<br/><br/>';

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

                IF audit_log <> 'Role updated.<br/><br/>' THEN
                    INSERT INTO audit_log (
                        table_name,
                        reference_id,
                        log,
                        changed_by,
                        created_at
                    )
                    VALUES (
                        'roles',
                        NEW.id,
                        audit_log,
                        NEW.last_log_by,
                        NOW()
                    );
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_role_insert
            AFTER INSERT ON roles
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;

                SET audit_log = CONCAT(
                    'Role created.<br/><br/>',
                    'Name: "',
                    COALESCE(NEW.name, 'Not set'),
                    '"<br/>',
                    'Description: "',
                    COALESCE(NEW.description, 'Not set'),
                    '"'
                );

                INSERT INTO audit_log (
                    table_name,
                    reference_id,
                    log,
                    changed_by,
                    created_at
                )
                VALUES (
                    'roles',
                    NEW.id,
                    audit_log,
                    NEW.last_log_by,
                    NOW()
                );
            END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS trg_role_permissions_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_role_permissions_insert');

        DB::unprepared(<<<SQL
        CREATE TRIGGER trg_role_permissions_update
        AFTER UPDATE ON role_permissions
        FOR EACH ROW
        BEGIN
            DECLARE audit_log TEXT DEFAULT 'Role permission updated.<br/><br/>';

            IF NEW.read_access <> OLD.read_access THEN
                SET audit_log = CONCAT(
                    audit_log,
                    'Read Access: ',
                    IF(OLD.read_access, 'Granted', 'Revoked'),
                    ' → ',
                    IF(NEW.read_access, 'Granted', 'Revoked'),
                    '<br/>'
                );
            END IF;

            IF NEW.write_access <> OLD.write_access THEN
                SET audit_log = CONCAT(
                    audit_log,
                    'Write Access: ',
                    IF(OLD.write_access, 'Granted', 'Revoked'),
                    ' → ',
                    IF(NEW.write_access, 'Granted', 'Revoked'),
                    '<br/>'
                );
            END IF;

            IF NEW.create_access <> OLD.create_access THEN
                SET audit_log = CONCAT(
                    audit_log,
                    'Create Access: ',
                    IF(OLD.create_access, 'Granted', 'Revoked'),
                    ' → ',
                    IF(NEW.create_access, 'Granted', 'Revoked'),
                    '<br/>'
                );
            END IF;

            IF NEW.delete_access <> OLD.delete_access THEN
                SET audit_log = CONCAT(
                    audit_log,
                    'Delete Access: ',
                    IF(OLD.delete_access, 'Granted', 'Revoked'),
                    ' → ',
                    IF(NEW.delete_access, 'Granted', 'Revoked'),
                    '<br/>'
                );
            END IF;

            IF NEW.export_access <> OLD.export_access THEN
                SET audit_log = CONCAT(
                    audit_log,
                    'Export Access: ',
                    IF(OLD.export_access, 'Granted', 'Revoked'),
                    ' → ',
                    IF(NEW.export_access, 'Granted', 'Revoked'),
                    '<br/>'
                );
            END IF;

            IF NEW.logs_access <> OLD.logs_access THEN
                SET audit_log = CONCAT(
                    audit_log,
                    'Logs Access: ',
                    IF(OLD.logs_access, 'Granted', 'Revoked'),
                    ' → ',
                    IF(NEW.logs_access, 'Granted', 'Revoked'),
                    '<br/>'
                );
            END IF;

            IF audit_log <> 'Role permission updated.<br/><br/>' THEN
                INSERT INTO audit_log (
                    table_name,
                    reference_id,
                    log,
                    changed_by,
                    created_at
                )
                VALUES (
                    'role_permissions',
                    NEW.id,
                    audit_log,
                    NEW.last_log_by,
                    NOW()
                );
            END IF;
        END
        SQL);

        DB::unprepared(<<<SQL
        CREATE TRIGGER trg_role_permissions_insert
        AFTER INSERT ON role_permissions
        FOR EACH ROW
        BEGIN
            DECLARE audit_log TEXT;

            DECLARE role_name VARCHAR(255);
            DECLARE menu_name VARCHAR(255);

            SELECT name
            INTO role_name
            FROM roles
            WHERE id = NEW.role_id;

            SELECT name
            INTO menu_name
            FROM navigation_menus
            WHERE id = NEW.navigation_menu_id;

            SET audit_log = CONCAT(
                'Role permission created.<br/><br/>',
                'Role: "', COALESCE(role_name, 'Not set'), '"<br/>',
                'Navigation Menu: "', COALESCE(menu_name, 'Not set'), '"<br/>',
                'Read Access: ', IF(NEW.read_access, 'Granted', 'Revoked'), '<br/>',
                'Write Access: ', IF(NEW.write_access, 'Granted', 'Revoked'), '<br/>',
                'Create Access: ', IF(NEW.create_access, 'Granted', 'Revoked'), '<br/>',
                'Delete Access: ', IF(NEW.delete_access, 'Granted', 'Revoked'), '<br/>',
                'Export Access: ', IF(NEW.export_access, 'Granted', 'Revoked'), '<br/>',
                'Logs Access: ', IF(NEW.logs_access, 'Granted', 'Revoked')
            );

            INSERT INTO audit_log (
                table_name,
                reference_id,
                log,
                changed_by,
                created_at
            )
            VALUES (
                'role_permissions',
                NEW.id,
                audit_log,
                NEW.last_log_by,
                NOW()
            );
        END
        SQL);
    
        DB::unprepared('DROP TRIGGER IF EXISTS trg_role_system_action_permissions_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_role_system_action_permissions_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_role_system_action_permissions_update
            AFTER UPDATE ON role_system_action_permissions
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Role system action permission updated.<br/><br/>';

                IF NEW.access <> OLD.access THEN
                    SET audit_log = CONCAT(
                        audit_log,
                        'Access: ',
                        IF(OLD.access, 'Granted', 'Revoked'),
                        ' → ',
                        IF(NEW.access, 'Granted', 'Revoked'),
                        '<br/>'
                    );
                END IF;

                IF audit_log <> 'Role system action permission updated.<br/><br/>' THEN
                    INSERT INTO audit_log (
                        table_name,
                        reference_id,
                        log,
                        changed_by,
                        created_at
                    )
                    VALUES (
                        'role_system_action_permissions',
                        NEW.id,
                        audit_log,
                        NEW.last_log_by,
                        NOW()
                    );
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_role_system_action_permissions_insert
            AFTER INSERT ON role_system_action_permissions
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;

                DECLARE role_name VARCHAR(255);
                DECLARE action_name VARCHAR(255);

                SELECT name
                INTO role_name
                FROM roles
                WHERE id = NEW.role_id;

                SELECT name
                INTO action_name
                FROM system_actions
                WHERE id = NEW.system_action_id;

                SET audit_log = CONCAT(
                    'Role system action permission created.<br/><br/>',
                    'Role: "', COALESCE(role_name, 'Not set'), '"<br/>',
                    'System Action: "', COALESCE(action_name, 'Not set'), '"<br/>',
                    'Access: ', IF(NEW.access, 'Granted', 'Revoked')
                );

                INSERT INTO audit_log (
                    table_name,
                    reference_id,
                    log,
                    changed_by,
                    created_at
                )
                VALUES (
                    'role_system_action_permissions',
                    NEW.id,
                    audit_log,
                    NEW.last_log_by,
                    NOW()
                );
            END
        SQL);    
    
        DB::unprepared('DROP TRIGGER IF EXISTS trg_role_users_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_role_users_insert
            AFTER INSERT ON role_users
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;

                DECLARE role_name VARCHAR(255);
                DECLARE user_name VARCHAR(255);

                SELECT name
                INTO role_name
                FROM roles
                WHERE id = NEW.role_id;

                SELECT name
                INTO user_name
                FROM users
                WHERE id = NEW.user_id;

                SET audit_log = CONCAT(
                    'Role assigned.<br/><br/>',
                    'Role: "', COALESCE(role_name, 'Not set'), '"<br/>',
                    'User: "', COALESCE(user_name, 'Not set'), '"'
                );

                INSERT INTO audit_log (
                    table_name,
                    reference_id,
                    log,
                    changed_by,
                    created_at
                )
                VALUES (
                    'role_users',
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
        Schema::dropIfExists('roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('role_system_action_permissions');
        Schema::dropIfExists('role_user');
    }
};
