<?php

use Illuminate\Database\Seeder;

class FolderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $limit = 10;
        $userIds = DB::table('media_folders')->get();
        $data=[];
        foreach ($userIds as $value) {
           array_push($data,$value->id);
        }
       // dd(array_rand($data));
        for ($i = 0; $i < $limit; $i++) {
            DB::table('media_folders')->insert([
                'name' => $faker->name,
                'parent_id' =>7,
                'path' => str_random(config("media.length_name")),
                'add_by'=>1,
                'share'=>str_random(config("media.length_share")),
                'created_at'=> Carbon::now(),
                'updated_at'=> Carbon::now(),
            ]);
        }
        for ($i = 0; $i < $limit; $i++) {
            DB::table('media_folders')->insert([
                'name' => $faker->name,
                'parent_id' =>8,
                'path' => str_random(config("media.length_name")),
                'add_by'=>1,
                'share'=>str_random(config("media.length_share")),
                'created_at'=> Carbon::now(),
                'updated_at'=> Carbon::now(),
            ]);
        }
        for ($i = 0; $i < $limit; $i++) {
            DB::table('media_folders')->insert([
                'name' => $faker->name,
                'parent_id' =>9,
                'path' => str_random(config("media.length_name")),
                'add_by'=>1,
                'share'=>str_random(config("media.length_share")),
                'created_at'=> Carbon::now(),
                'updated_at'=> Carbon::now(),
            ]);
        }
        for ($i = 0; $i < $limit; $i++) {
            DB::table('media_folders')->insert([
                'name' => $faker->name,
                'parent_id' =>10,
                'path' => str_random(config("media.length_name")),
                'add_by'=>1,
                'share'=>str_random(config("media.length_share")),
                'created_at'=> Carbon::now(),
                'updated_at'=> Carbon::now(),
            ]);
        }
    }
}
