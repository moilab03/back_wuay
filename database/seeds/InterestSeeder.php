<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $active = \App\Status::byStatus(\App\Status::ENABLED)->value('id');
        $interests = [];
        foreach (\App\Interest::INTERESTS as $interest) {
            $interests[] = [
                "name" => $interest,
                "status_id" => $active,
            ];
        }
        DB::table('interests')->insert($interests);
    }
}
