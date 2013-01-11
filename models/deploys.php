<?php

/*
 * This file is part of the Githubdeploys-Deploy Laravel Package.
 *
 * (c) Gabriel C. <lazycoder.ro@gmail.com>
 *
 */

namespace Githubdeploys\Model;

use \Config,
    \Eloquent;

class Deploys extends Eloquent {
    
    public static $table = 'gc_deploys';
    public static $timestamps = false;

    /**
     * 
     * Load releases for project.
     * 
     * @param type $projectId
     * @return type
     */
    public static function getDeploysByProject($projectId) {
        
        return static::with('users')
                        ->where('project_id', '=', $projectId)
                        ->order_by('id', 'DESC')
                        ->get();
    }
    
    /**
     * 
     * Method used to retrieve owners for releases.
     * 
     * @return type
     */
    
    public function users() {
        return $this->belongs_to(Config::get('auth.model'), 'user_id');
    }
    
    /**
     * 
     * Retrieve a release alongside with it's project.
     * 
     * @param type $id
     * @return type
     */
    
    public function findWithProject($id) {
        
        return static::with('projects')
                        ->where('id', '=', $id)
                        ->get();
    }
    
    /**
     * 
     * Method used to retrieve project for release.
     * 
     * @return type
     */    
    public function projects() {
        return $this->belongs_to('Githubdeploys\Model\Projects', 'project_id');
    }

}
