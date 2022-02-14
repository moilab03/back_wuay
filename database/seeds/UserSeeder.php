<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('users')
            ->insert([
                [
                    'name' => 'Administrador Wuay',
                    'email' => 'wuay.center.admin@gmail.com',
                    'password' => \Illuminate\Support\Facades\Hash::make('Wuay3Man'),
                    'status_id' => \App\Status::byStatus(\App\Status::ENABLED)->value('id'),
                    'rol_id' => \App\Rol::byRol(\App\Rol::ADMINISTRATOR)->value('id')
                ]
            ]);
    }
}
