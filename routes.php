<?php

/*
 * This file is part of the Githubdeploys-Deploy Laravel Package.
 *
 * (c) Gabriel C. <lazycoder.ro@gmail.com>
 *
 */

Route::any('(:bundle)/release/(:any)', 'githubdeploys::release@view');