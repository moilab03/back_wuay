<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(
            [
                RolSeeder::class,
                CountrySeeder::class,
                DepartmentSeeder::class,
                CitySeeder::class,
                StatusSeeder::class,
                UserSeeder::class,
                TypeResourceSeeder::class,
                InterestSeeder::class
            ]
        );
    }
}
