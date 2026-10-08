<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AddressTypeSeeder::class,
            AppSeeder::class,
            BloodTypeSeeder::class,
            DegreeTypeSeeder::class,
            DepartureReasonSeeder::class,
            GenderSeeder::class,
            HolidayTypeSeeder::class,
            MaritalStatusSeeder::class,
            NavigationMenuSeeder::class,
            NavigationMenuAppSeeder::class,
            NavigationMenuRouteSeeder::class,
            NavigationMenuDatabaseTableSeeder::class,
            SystemActionSeeder::class,
            RelationSeeder::class,
            RoleSeeder::class,
            RolePermissionSeeder::class,
            RoleSystemActionPermissionSeeder::class,
            RoleUserAccountSeeder::class,
            UploadSettingSeeder::class,
            UploadSettingExtensionSeeder::class,
        ]);
    }
}
