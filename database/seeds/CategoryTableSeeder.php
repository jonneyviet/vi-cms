<?php

use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $limit = 100;
        for ($i = 0; $i < $limit; $i++) {
            DB::table('category')->insert([
                'name' => $faker->name,
                'key' => str_random(10),
                'parent_id' =>null,
                'lang'=>$faker->randomElement(['vi','en']),
                'type'=>$faker->randomElement(['1','2']),
                'is_public'=>false,
                'text'=>$faker->realText(200,2),
                'add_by'=>2,
                'avatar'=>null,
                'created_at'=> Carbon::now(),
                'updated_at'=> Carbon::now(),
            ]);
        }
        for ($i = 0; $i < $limit; $i++) {
            DB::table('category')->insert([
                'name' => $faker->name,
                'key' => str_random(10),
                'parent_id' =>$faker->numberBetween(1,100),
                'lang'=>$faker->randomElement(['vi','en']),
                'type'=>$faker->randomElement(['1','2']),
                'is_public'=>false,
                'text'=>$faker->realText(200,2),
                'add_by'=>2,
                'avatar'=>null,
                'created_at'=> Carbon::now(),
                'updated_at'=> Carbon::now(),
            ]);
        }
        for ($i = 0; $i < 1000; $i++) {
            DB::table('post')->insert([
                'name' => $faker->sentence(10,true),
                'key' => str_random(10),
                'lang'=>$faker->randomElement(['vi','en']),
                'category_id'=>$faker->numberBetween(1,200),
                'is_public'=>false,
                'text'=> json_encode(
                    [
                        [
                            'key'=>'01',
                            'title'=>"Default",
                            'content'=> $faker->realText(500, 2)
                        ],
                        [
                            'key'=>'02',
                            'title'=>"Test",
                            'content'=> $faker->realText(500, 2)
                        ]
                    ]
                ),
                'add_by'=>2,
                'avatar'=>null,
                'created_at'=> Carbon::now(),
                'updated_at'=> Carbon::now(),
            ]);
        }
    }
}

