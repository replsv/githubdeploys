<?php

/*
 * This file is part of the Github-Deploy Laravel Package.
 *
 * (c) Gabriel C. <lazycoder.ro@gmail.com>
 *
 */

class Github_Create_Schema {

    /**
     * Make changes to the database.
     *
     * @return void
     */
    public function up() {
        Schema::create('gc_projects', function ($table) {
                    $table->increments('id');
                    $table->integer('user_id')->unsigned();
                    $table->string('name', 60)->unique();
                    $table->string('path', 100);
                    $table->string('repo', 100);                    
                    $table->timestamps();
                    $table->index('name');
                    $table->engine = 'InnoDB';
                });

        Schema::create('gc_deploys', function ($table) {
                    $table->increments('id');
                    $table->integer('project_id')->unsigned();
                    $table->string('release', 100);
                    $table->timestamp('added_on');
                    $table->integer('user_id')->unsigned();
                    $table->index('user_id');
                    $table->text('response');
                    $table->engine = 'InnoDB';
                });
    }

    /**
     * Revert the changes to the database.
     *
     * @return void
     */
    public function down() {
        Schema::drop('gc_projects');
        Schema::drop('gc_deploys');
    }

}