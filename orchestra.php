<?php

/*
 * This file is part of the Github-Deploy Laravel Package.
 *
 * (c) Gabriel C. <lazycoder.ro@gmail.com>
 *
 */

Event::listen('orchestra.started', function () {
            $github = Orchestra\Resources::make('github', array(
                        'name' => 'Github Deploys',
                        'uses' => 'github::index',
                    ));

            $github->releases = 'github::release';
        });