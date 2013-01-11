<?php

/*
 * This file is part of the Githubdeploys-Deploy Laravel Package.
 *
 * (c) Gabriel C. <lazycoder.ro@gmail.com>
 *
 */

/*
 * Use autoloader for bundle's declared namespaces.
 */
Autoloader::namespaces(array(
	'Githubdeploys\Model' => Bundle::path('githubdeploys').'models'.DS,
	'Githubdeploys'       => Bundle::path('githubdeploys').'libraries'.DS,
));

/*
 * Start engine - vruuuum
 */
Githubdeploys\Core::start();
