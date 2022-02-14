<?php

use Illuminate\Database\Seeder;

class TypeResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('type_resources')
            ->insert(\App\TypeResource::TYPES_RESOURCES);
    }
}
