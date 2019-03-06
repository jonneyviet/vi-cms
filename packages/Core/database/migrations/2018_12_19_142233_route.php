<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Route extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Create Table User
         */
        Schema::create('route_link', function (Blueprint $table) {
           $table->increments("id");
           $table->string("link")->nullable();
           $table->string("title")->nullable();
           $table->unsignedInteger("category_id")->unsigned()->nullable();
           $table->unsignedInteger("post_id")->unsigned()->nullable();
           $table->string("desrciption")->nullable();
           $table->string("keyword")->nullable();
           $table->string("jsonld")->nullable();
           $table->unsignedInteger("add_by")->unsigned()->nullable();
           $table->timestamps();
           $table->softDeletes();
           $table->foreign("add_by")
                ->references("id")
                ->on("users")
                ->onDelete("cascade");
          $table->foreign("category_id")
                ->references("id")
                ->on("category")
                ->onDelete("cascade");
          $table->foreign("post_id")
                ->references("id")
                ->on("post")
                ->onDelete("cascade");
        });
        Schema::create('route_files', function (Blueprint $table) {
           $table->unsignedInteger("media_files_id")->unsigned()->nullable();
           $table->unsignedInteger("category_id")->unsigned()->nullable();
           $table->unsignedInteger("post_id")->unsigned()->nullable();
           $table->foreign("media_files_id")
                ->references("id")
                ->on("media_files")
                ->onDelete("cascade");
          $table->foreign("category_id")
                ->references("id")
                ->on("category")
                ->onDelete("cascade");
          $table->foreign("post_id")
                ->references("id")
                ->on("post")
                ->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("link");
        Schema::drop("route_files");
    }
}
