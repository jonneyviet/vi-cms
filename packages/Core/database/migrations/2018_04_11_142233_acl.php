<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Acl extends Migration
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
        
        Schema::create('users', function (Blueprint $table) {
           $table->increments("id");
           $table->string("name");
           $table->string("email")->unique();
           $table->string("phone")->unique();
           $table->string("password");
           $table->boolean("is_active")->default(1);
           $table->boolean("is_ban")->default(0);
           $table->boolean("is_status")->default(0);
           $table->rememberToken();
           $table->timestamps();
           $table->softDeletes();
        });

        /**
         * Create Table Roles
         */
         Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('guard_name');
            $table->integer("parent");    
            $table->timestamps();
        });

        /**
         * Create Table Permission
         */
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->softDeletes();
        });
        /**
         * Roles has Permission
         */
        Schema::create('permission_role', function (Blueprint $table) {
            $table->unsignedInteger('permission_id');
            $table->unsignedInteger('role_id');
            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
            $table->primary(['permission_id', 'role_id']);
        });
        /**
         * Permission User
         */
         Schema::create('permission_user', function (Blueprint $table){
         	$table->unsignedInteger("permission_id");
         	$table->unsignedInteger("user_id");
         	$table->foreign("permission_id")
         		->references("id")
         		->on("permissions")
         		->onDelete("cascade");
         	$table->foreign("user_id")
         		->references("id")
         		->on("users")
         		->onDelete("cascade");
         	$table->primary(["permission_id","user_id"]);
         });
         /**
          * User Role
          */
         Schema::create('user_role', function (Blueprint $table){
         	$table->unsignedInteger("role_id");
         	$table->unsignedInteger("user_id");
         	$table->foreign("role_id")
         		->references("id")
         		->on("roles")
         		->onDelete("cascade");
         	$table->foreign("user_id")
         		->references("id")
         		->on("users")
         		->onDelete("cascade");
         	$table->primary(["role_id","user_id"]);
         });
    }   

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("users");
        Schema::drop("roles");
        Schema::drop("permissions");
        Schema::drop("permission_role");
        Schema::drop("psermision_user");
        Schema::drop("user_role");        
    }
}
