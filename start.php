<?php

/*
 * This file is part of the Github-Deploy Laravel Package.
 *
 * (c) Gabriel C. <lazycoder.ro@gmail.com>
 *
 */

/*
 * Use autoloader for bundle's declared namespaces.
 */
Autoloader::namespaces(array(
	'Github\Model' => Bundle::path('github').'models'.DS,
	'Github'       => Bundle::path('github').'libraries'.DS,
));

/*
 * Start engine - vruuuum
 */
Github\Core::start();
