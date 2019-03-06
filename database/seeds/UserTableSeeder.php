<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Packages\Core\Models\Users;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        factory(\Packages\Core\Models\Users::class,100)->create();
    }
}
