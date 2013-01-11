<?php

/*
 * This file is part of the Githubdeploys-Deploy Laravel Package.
 *
 * (c) Gabriel C. <lazycoder.ro@gmail.com>
 *
 */

Event::listen('orchestra.started', function () {
            $githubdeploys = Orchestra\Resources::make('githubdeploys', array(
                        'name' => 'Githubdeploys Deploys',
                        'uses' => 'githubdeploys::index',
                    ));

            $githubdeploys->releases = 'githubdeploys::release';
        });