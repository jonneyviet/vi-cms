<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Folders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_folders', function (Blueprint $table) {
          $table->increments("id");
          $table->integer("parent_id")->unsigned()->nullable();
          $table->foreign("parent_id")
            ->references("id")
            ->on("media_folders")
            ->onDelete("cascade");
          $table->string("name")->nullable();
          $table->boolean("default")->nullable();
          $table->boolean("show")->nullable();
          $table->string("path")->nullable();
          $table->text("description")->nullable();
          $table->unsignedInteger("add_by")->unsigned()->nullable();
          $table->foreign("add_by")
            ->references("id")
            ->on("users")
            ->onDelete("cascade");
          $table->string("share")->nullable();
           $table->timestamps();
           $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::drop("media_folders");
    }
}
