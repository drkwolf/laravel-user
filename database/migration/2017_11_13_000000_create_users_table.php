<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
        Schema::create('users', function(Blueprint $table) {
            $table->increments('id');

            $table->string('username')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable()->unique();
            $table->string('password')->nullable();
            $table->tinyInteger('active')->default(0);

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            
            $table->enum('sex', ['M', 'F'])->nullable();
            $table->date('birthdate')->nullable();

            $table->longText('options')->nullable();
            $table->longText('contacts')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tutor_user', function(Blueprint $table) {
            $table->integer('user_id');
            $table->integer('tutor_id');
            $table->longText('options')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
        Schema::drop('tutor_user');
    }

}
