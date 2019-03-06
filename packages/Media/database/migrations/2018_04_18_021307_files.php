<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Files extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_files', function (Blueprint $table) {
          $table->increments("id");
          $table->string("name")->nullable();
          $table->string("type")->nullable();
          $table->string("path")->nullable();
          $table->string("size")->nullable();
          $table->string("thumbnail")->nullable();
          $table->boolean("show")->nullable();
          $table->text("description")->nullable();
          $table->string("share")->nullable();
          $table->unsignedInteger("add_by")->unsigned()->nullable();
          $table->unsignedInteger("folders_id")->unsigned()->nullable();
          $table->timestamps();
          $table->softDeletes();
        });
        Schema::table('media_files', function($table) {
           $table->foreign('folders_id')
            ->references('id')
            ->on('media_folders')
            ->onDelete("cascade");
           $table->foreign("add_by")
            ->references("id")
            ->on("users")
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
       Schema::drop("media_files");
    }
}
