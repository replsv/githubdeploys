<?php

/*
 * This file is part of the Github-Deploy Laravel Package.
 *
 * (c) Gabriel C. <lazycoder.ro@gmail.com>
 *
 */

namespace Github\Model;

use \Config,
    \Eloquent;

class Projects extends Eloquent {

    public static $table = 'gc_projects';

    /**
     * 
     * Load the project and join the owner.
     * 
     * @param type $id
     * @return Github\Model\Projects
     */
    public static function identity($id) {
        return static::with('users')->where('id', '=', $id)->get();
    }

    /**
     * 
     * Get a collection of projects.
     * 
     * @return Github\Model\Projects
     */
    public function getProjects() {
        return static::with('users')
                        ->order_by('id', 'DESC')
                        ->get();
    }

    /**
     * 
     * Method used to retrieve owners for projects.
     * 
     * @return type
     */
    public function users() {
        return $this->belongs_to(Config::get('auth.model'), 'user_id');
    }

}
