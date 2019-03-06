<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TranslatorTranslationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data=[
            [
                "locale"=>"vi",
                "group"=>"translation",
                "item"=>"title",
                "text"=>"Ngon ngu",
            ],
            [
                "locale"=>"en",
                "group"=>"translation",
                "item"=>"title",
                "text"=>"Language",
            ],
            [
                "locale"=>"vi",
                "group"=>"translation",
                "item"=>"group",
                "text"=>"Nhom",
            ],
            [
                "locale"=>"en",
                "group"=>"translation",
                "item"=>"group",
                "text"=>"Group",
            ],
        ];
        foreach ($data as $value) {
            DB::table('translator_translations')->insert([
                'locale' => $value["locale"],
                "group"=>$value["group"],
                "item"=>$value["item"],
                "text"=>$value["text"],
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
