<?php

/*
 * This file is part of the Github-Deploy Laravel Package.
 *
 * (c) Gabriel C. <lazycoder.ro@gmail.com>
 *
 */

Route::any('(:bundle)/release/(:any)', 'github::release@view');