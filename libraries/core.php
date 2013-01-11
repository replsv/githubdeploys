<?php 

/*
 * This file is part of the Githubdeploys-Deploy Laravel Package.
 *
 * (c) Gabriel C. <lazycoder.ro@gmail.com>
 *
 */

namespace Githubdeploys;

use Orchestra\Core AS O,
    Orchestra\Acl;

class Core {
    
    /**
     * 
     * Start Githubdeploys's Orchestra heart
     */
    public static function start() {
        Acl::make('githubdeploys')->attach(O::memory());
    }

}