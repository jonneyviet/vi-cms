<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Categoy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category', function (Blueprint $table) {
           $table->increments("id");
           $table->string("key")->unique();
           $table->string("name")->nullable();
           $table->string("lang")->nullable();
           $table->integer('type')->default(1);
           $table->integer('parent_id')->unsigned()->nullable();
           $table->boolean("is_public")->default(1);
           $table->longText("text")->nullable();
           $table->timestamps();
           $table->softDeletes();
           $table->unsignedInteger("add_by")->unsigned()->nullable();
           $table->unsignedInteger("avatar")->unsigned()->nullable();
           $table->foreign("add_by")
                ->references("id")
                ->on("users")
                ->onDelete("cascade");
            $table->foreign("avatar")
                ->references("id")
                ->on("media_files")
                ->onDelete("cascade");
            $table->foreign('parent_id')
                ->references('id')
                ->on('category')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("category");
    }
}
