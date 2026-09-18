<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('legal_name');
            $table->string('trade_name')->nullable();
            $table->string('logo')->nullable();
            
            $table->string('tin', 20)->nullable();
            $table->string('branch_code', 10)->default('0000');
            $table->string('rdo_code', 10)->nullable();
            $table->enum('entity_type', ['Corporation', 'Partnership', 'Sole Proprietorship', 'Cooperative', 'One Person Corporation (OPC)'])->default('Corporation');
            $table->string('sec_dti_registration_no')->nullable();
            $table->date('date_registered')->nullable();
            $table->string('psic_code')->nullable();
            $table->string('line_of_business')->nullable();
            $table->enum('vat_status', ['VAT-Registered', 'Non-VAT', 'VAT-Exempt'])->default('VAT-Registered');
            
            $table->string('street_1')->nullable();
            $table->string('street_2')->nullable();
            $table->string('barangay')->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->unsignedTinyInteger('fiscal_year_start_month')->default(1);
            
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('contact_person')->nullable();

            $table->foreignId('last_log_by')->nullable()->default(1)->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        /* =============================================================================================
            TRIGGERS
        ============================================================================================= */

        DB::unprepared('DROP TRIGGER IF EXISTS trg_companies_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_companies_insert');

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_companies_update
            AFTER UPDATE ON companies
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT DEFAULT 'Company updated.<br/><br/>';
                
                DECLARE old_city_name VARCHAR(255);
                DECLARE new_city_name VARCHAR(255);
                DECLARE old_state_name VARCHAR(255);
                DECLARE new_state_name VARCHAR(255);
                DECLARE old_country_name VARCHAR(255);
                DECLARE new_country_name VARCHAR(255);
                DECLARE old_currency_name VARCHAR(255);
                DECLARE new_currency_name VARCHAR(255);

                -- Fetch Foreign Key Labels
                SELECT name INTO old_city_name FROM cities WHERE id = OLD.city_id;
                SELECT name INTO new_city_name FROM cities WHERE id = NEW.city_id;
                SELECT name INTO old_state_name FROM states WHERE id = OLD.state_id;
                SELECT name INTO new_state_name FROM states WHERE id = NEW.state_id;
                SELECT name INTO old_country_name FROM countries WHERE id = OLD.country_id;
                SELECT name INTO new_country_name FROM countries WHERE id = NEW.country_id;
                SELECT name INTO old_currency_name FROM currencies WHERE id = OLD.currency_id;
                SELECT name INTO new_currency_name FROM currencies WHERE id = NEW.currency_id;

                IF NOT (NEW.legal_name <=> OLD.legal_name) THEN
                    SET audit_log = CONCAT(audit_log, 'Legal Name: "', COALESCE(OLD.legal_name, 'Not set'), '" → "', COALESCE(NEW.legal_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.trade_name <=> OLD.trade_name) THEN
                    SET audit_log = CONCAT(audit_log, 'Trade Name: "', COALESCE(OLD.trade_name, 'Not set'), '" → "', COALESCE(NEW.trade_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.tin <=> OLD.tin) THEN
                    SET audit_log = CONCAT(audit_log, 'TIN: "', COALESCE(OLD.tin, 'Not set'), '" → "', COALESCE(NEW.tin, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.branch_code <=> OLD.branch_code) THEN
                    SET audit_log = CONCAT(audit_log, 'Branch Code: "', COALESCE(OLD.branch_code, 'Not set'), '" → "', COALESCE(NEW.branch_code, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.rdo_code <=> OLD.rdo_code) THEN
                    SET audit_log = CONCAT(audit_log, 'RDO Code: "', COALESCE(OLD.rdo_code, 'Not set'), '" → "', COALESCE(NEW.rdo_code, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.entity_type <=> OLD.entity_type) THEN
                    SET audit_log = CONCAT(audit_log, 'Entity Type: "', COALESCE(OLD.entity_type, 'Not set'), '" → "', COALESCE(NEW.entity_type, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.sec_dti_registration_no <=> OLD.sec_dti_registration_no) THEN
                    SET audit_log = CONCAT(audit_log, 'SEC/DTI Reg No: "', COALESCE(OLD.sec_dti_registration_no, 'Not set'), '" → "', COALESCE(NEW.sec_dti_registration_no, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.date_registered <=> OLD.date_registered) THEN
                    SET audit_log = CONCAT(audit_log, 'Date Registered: "', COALESCE(OLD.date_registered, 'Not set'), '" → "', COALESCE(NEW.date_registered, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.psic_code <=> OLD.psic_code) THEN
                    SET audit_log = CONCAT(audit_log, 'PSIC Code: "', COALESCE(OLD.psic_code, 'Not set'), '" → "', COALESCE(NEW.psic_code, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.line_of_business <=> OLD.line_of_business) THEN
                    SET audit_log = CONCAT(audit_log, 'Line of Business: "', COALESCE(OLD.line_of_business, 'Not set'), '" → "', COALESCE(NEW.line_of_business, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.vat_status <=> OLD.vat_status) THEN
                    SET audit_log = CONCAT(audit_log, 'VAT Status: "', COALESCE(OLD.vat_status, 'Not set'), '" → "', COALESCE(NEW.vat_status, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.street_1 <=> OLD.street_1) THEN
                    SET audit_log = CONCAT(audit_log, 'Street 1: "', COALESCE(OLD.street_1, 'Not set'), '" → "', COALESCE(NEW.street_1, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.street_2 <=> OLD.street_2) THEN
                    SET audit_log = CONCAT(audit_log, 'Street 2: "', COALESCE(OLD.street_2, 'Not set'), '" → "', COALESCE(NEW.street_2, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.barangay <=> OLD.barangay) THEN
                    SET audit_log = CONCAT(audit_log, 'Barangay: "', COALESCE(OLD.barangay, 'Not set'), '" → "', COALESCE(NEW.barangay, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.city_id <=> OLD.city_id) THEN
                    SET audit_log = CONCAT(audit_log, 'City: "', COALESCE(old_city_name, 'Not set'), '" → "', COALESCE(new_city_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.state_id <=> OLD.state_id) THEN
                    SET audit_log = CONCAT(audit_log, 'State/Province: "', COALESCE(old_state_name, 'Not set'), '" → "', COALESCE(new_state_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.country_id <=> OLD.country_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Country: "', COALESCE(old_country_name, 'Not set'), '" → "', COALESCE(new_country_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.currency_id <=> OLD.currency_id) THEN
                    SET audit_log = CONCAT(audit_log, 'Currency: "', COALESCE(old_currency_name, 'Not set'), '" → "', COALESCE(new_currency_name, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.fiscal_year_start_month <=> OLD.fiscal_year_start_month) THEN
                    SET audit_log = CONCAT(audit_log, 'Fiscal Year Start Month: "', COALESCE(OLD.fiscal_year_start_month, 'Not set'), '" → "', COALESCE(NEW.fiscal_year_start_month, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.phone <=> OLD.phone) THEN
                    SET audit_log = CONCAT(audit_log, 'Phone: "', COALESCE(OLD.phone, 'Not set'), '" → "', COALESCE(NEW.phone, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.email <=> OLD.email) THEN
                    SET audit_log = CONCAT(audit_log, 'Email: "', COALESCE(OLD.email, 'Not set'), '" → "', COALESCE(NEW.email, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.website <=> OLD.website) THEN
                    SET audit_log = CONCAT(audit_log, 'Website: "', COALESCE(OLD.website, 'Not set'), '" → "', COALESCE(NEW.website, 'Not set'), '"<br/>');
                END IF;

                IF NOT (NEW.contact_person <=> OLD.contact_person) THEN
                    SET audit_log = CONCAT(audit_log, 'Contact Person: "', COALESCE(OLD.contact_person, 'Not set'), '" → "', COALESCE(NEW.contact_person, 'Not set'), '"<br/>');
                END IF;

                IF audit_log <> 'Company updated.<br/><br/>' THEN
                    INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                    VALUES ('companies', NEW.id, audit_log, NEW.last_log_by, NOW());
                END IF;
            END
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_companies_insert
            AFTER INSERT ON companies
            FOR EACH ROW
            BEGIN
                DECLARE audit_log TEXT;
                DECLARE city_name VARCHAR(255);
                DECLARE state_name VARCHAR(255);
                DECLARE country_name VARCHAR(255);
                DECLARE currency_name VARCHAR(255);

                SELECT name INTO city_name FROM cities WHERE id = NEW.city_id;
                SELECT name INTO state_name FROM states WHERE id = NEW.state_id;
                SELECT name INTO country_name FROM countries WHERE id = NEW.country_id;
                SELECT name INTO currency_name FROM currencies WHERE id = NEW.currency_id;

                SET audit_log = CONCAT(
                    'Company created.<br/><br/>',
                    'Legal Name: "', COALESCE(NEW.legal_name, 'Not set'), '"<br/>',
                    'Trade Name: "', COALESCE(NEW.trade_name, 'Not set'), '"<br/>',
                    'TIN: "', COALESCE(NEW.tin, 'Not set'), '"<br/>',
                    'Branch Code: "', COALESCE(NEW.branch_code, 'Not set'), '"<br/>',
                    'RDO Code: "', COALESCE(NEW.rdo_code, 'Not set'), '"<br/>',
                    'Entity Type: "', COALESCE(NEW.entity_type, 'Not set'), '"<br/>',
                    'SEC/DTI Reg No: "', COALESCE(NEW.sec_dti_registration_no, 'Not set'), '"<br/>',
                    'Date Registered: "', COALESCE(NEW.date_registered, 'Not set'), '"<br/>',
                    'PSIC Code: "', COALESCE(NEW.psic_code, 'Not set'), '"<br/>',
                    'Line of Business: "', COALESCE(NEW.line_of_business, 'Not set'), '"<br/>',
                    'VAT Status: "', COALESCE(NEW.vat_status, 'Not set'), '"<br/>',
                    'Address: "', COALESCE(NEW.street_1, ''), ' ', COALESCE(NEW.street_2, ''), '"<br/>',
                    'Barangay: "', COALESCE(NEW.barangay, 'Not set'), '"<br/>',
                    'City: "', COALESCE(city_name, 'Not set'), '"<br/>',
                    'State/Province: "', COALESCE(state_name, 'Not set'), '"<br/>',
                    'Country: "', COALESCE(country_name, 'Not set'), '"<br/>',
                    'Currency: "', COALESCE(currency_name, 'Not set'), '"<br/>',
                    'Fiscal Year Start Month: "', COALESCE(NEW.fiscal_year_start_month, 'Not set'), '"<br/>',
                    'Phone: "', COALESCE(NEW.phone, 'Not set'), '"<br/>',
                    'Email: "', COALESCE(NEW.email, 'Not set'), '"<br/>',
                    'Website: "', COALESCE(NEW.website, 'Not set'), '"<br/>',
                    'Contact Person: "', COALESCE(NEW.contact_person, 'Not set'), '"<br/>'
                );

                INSERT INTO audit_log (table_name, reference_id, log, changed_by, created_at)
                VALUES ('companies', NEW.id, audit_log, NEW.last_log_by, NOW());
            END
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
