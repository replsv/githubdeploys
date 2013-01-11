<?php

/*
 * This file is part of the Githubdeploys-Deploy Laravel Package.
 *
 * (c) Gabriel C. <lazycoder.ro@gmail.com>
 *
 */

use Githubdeploys\Model\Deploys,
    Githubdeploys\Model\Projects,
    Githubdeploys\Deploy,
    Orchestra\Messages,
    Orchestra\View;

class Githubdeploys_Release_Controller extends Controller {

    /**
     * 
     * Restful verb.
     * @var type 
     */
    public $restful = true;

    /**
     * 
     * Display all releases deployed from the project.
     * @param type $id
     * @return type
     */
    public function get_index($id = null) {

        $project = Projects::find($id);
        
        if (!$project) {
            $m = new Messages;
            $m->add('error', "Unknown project!");
            return Redirect::to(handles('orchestra::resources/githubdeploys'))
                            ->with('message', $m->serialize());
        }

        $rel = new \Githubdeploys\Release;
        $currentRelease = $rel->getCurrentRelease($project);

        $releases = Deploys::getDeploysByProject($id);

        View::share('_title_', 'Project Deploys');

        $data = array(
            'project' => $project,
            'releases' => $releases,
            'currentRelease' => $currentRelease,
        );

        return View::make('githubdeploys::githubdeploys.releases-index', $data);
    }

    /**
     * 
     * View release's details.
     * @param type $id
     * @return type
     */
    public function get_view($id = null) {

        $release = new Deploys;
        $release = $release->findWithProject($id);
        if (!$release) {
            $m = new Messages;
            $m->add('error', "Unknown release!");
            return Redirect::to(handles('orchestra::resources/githubdeploys'))
                            ->with('message', $m->serialize());
        }
        $project = (object) $release[0]->relationships['projects'];
        $release = (object) $release[0]->attributes;
        $rel = new \Githubdeploys\Release;
        $currentRelease = $rel->getCurrentRelease($project);

        $data = array(
            'release' => $release,
            'project' => $project,
            'currentRelease' => $currentRelease,
        );

        return View::make('githubdeploys::githubdeploys.releases-view', $data);
    }

    /**
     * 
     * Move release in production.
     * @param type $id
     * @return type
     */
    public function get_use($id = null) {

        $release = Deploys::find($id);
        
        if (!$release) {
            $m = new Messages;
            $m->add('error', "Unknown release!");
            return Redirect::to(handles('orchestra::resources/githubdeploys'))
                            ->with('message', $m->serialize());
        }
        $project = Projects::find($release->project_id);

        $rel = new \Githubdeploys\Release;
        $response = $rel->moveInProduction($project, $release);

        $m = new Messages;
        if (isset($response['error'])) {
            foreach ($response['error'] AS $r) {
                $m->add('error', $r);
            }
        } else {
            foreach ($response['success'] AS $r) {
                $m->add('success', $r);
            }
        }

        return Redirect::to(Request::referrer())
                        ->with('message', $m->serialize());
    }

    /**
     * 
     * Update the release to latest version. 
     * @param type $id
     * @return type
     */
    public function get_pull($id = null) {

        $release = Deploys::find($id);
        
        if (!$release) {
            $m = new Messages;
            $m->add('error', "Unknown release!");
            return Redirect::to(handles('orchestra::resources/githubdeploys'))
                            ->with('message', $m->serialize());
        }
        $project = Projects::find($release->project_id);

        $rel = new \Githubdeploys\Release;
        $response = $rel->pull($project, $release);

        $m = new Messages;
        foreach ($response AS $r) {
            $m->add('info', $r);
        }

        return Redirect::to(Request::referrer())
                        ->with('message', $m->serialize());
    }

    /**
     * 
     * Delete release.
     * @param type $id
     * @return type
     */
    public function get_delete($id = null) {

        $release = new Deploys;
        $release = $release->findWithProject($id);
        
        if (!$release) {
            $m = new Messages;
            $m->add('error', "Unknown release!");
            return Redirect::to(handles('orchestra::resources/githubdeploys'))
                            ->with('message', $m->serialize());
        }
        $project = (object) $release[0]->relationships['projects'];
        $release = (object) $release[0]->attributes;
        $rel = new \Githubdeploys\Release;
        $response = $rel->deleteRelease($project, $release);

        $m = new Messages;

        if (isset($response['error'])) {
            foreach ($response['error'] AS $r) {
                $m->add('error', $r);
            }
        } else {
            foreach ($response['success'] AS $r) {
                $m->add('success', $r);
            }
            
            /*
             * We have to reinitialise $release because it's not an instance
             * of the model \Githubdeploys\Deploys anymore. Read above the code^
             */
            if (Deploys::find($release->id)->delete()) {
                $m->add('success', sprintf("Release %i (%s) has been deleted from the database.", $release->id, $release->release));
            } else {
                $m->add('error', sprintf("Could not delete release %i from the database.", $release->id));
            }
            
        }

        return Redirect::to(Request::referrer())
                        ->with('message', $m->serialize());
    }

}