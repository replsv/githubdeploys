<?php 

/*
 * This file is part of the Github-Deploy Laravel Package.
 *
 * (c) Gabriel C. <lazycoder.ro@gmail.com>
 *
 */

namespace Github;

use Orchestra\Core AS O,
    Orchestra\Acl;

class Core {
    
    /**
     * 
     * Start Github's Orchestra heart
     */
    public static function start() {
        Acl::make('github')->attach(O::memory());
    }

}