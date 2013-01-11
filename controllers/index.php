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

class Githubdeploys_Index_Controller extends Controller {

    /**
     * 
     * Restful verb.
     * @var type 
     */
    public $restful = true;

    /**
     * 
     * Index action.
     * List all available projects. 
     * @return type
     */
    public function get_index() {

        View::share('_title_', 'Githubdeploys Deploys Projects');

        $projects = new Projects();
        $data = array(
            'projects' => $projects->getProjects(),
        );

        return View::make('githubdeploys::githubdeploys.index-index', $data);
    }

    /**
     * 
     * Manage projects.
     * @param type $id
     * @return type
     */
    public function get_manage($id = null) {

        if ($id) {
            $project = Projects::find($id);
            if (!$project) {
                $m = new Messages;
                $m->add('error', "Unknown project!");
                return Redirect::to(handles('orchestra::resources/githubdeploys'))
                                ->with('message', $m->serialize());
            }
            View::share('_title_', "Edit project");
            $data = array(
                'project' => $project,
                'type' => 'update'
            );
        } else {
            $project = new Projects();
            View::share('_title_', 'Add project');
            $data = array(
                'project' => $project,
                'type' => 'create'
            );
        }

        return View::make('githubdeploys::githubdeploys.index-manage', $data);
    }

    /**
     * 
     * Process the add/edit project form.
     * @return type
     */
    public function post_manage() {

        $input = Input::all();
        if (!$input['id']) {
            $input['id'] = null;
        }

        $rules = array(
            'name' => array(
                'required',
                'match:/[a-z0-9\-]+/',
                "unique:gc_projects,name,{$input['id']}"
            ),
            'repo' => array(
                'required',
                'min:2',
                'match:/[a-z0-9\-]+/',
                "unique:gc_projects,repo,{$input['id']}",
            ),
            'path' => 'required',
        );

        $m = new Messages;
        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to(handles('orchestra::resources/githubdeploys/manage/' . $input['id']))
                            ->with_input()
                            ->with_errors($v);
        }

        $project = Projects::find($input['id']);

        if (!$project) {
            $project = new Projects();
            $project->user_id = Auth::user()->id;
            if(!is_dir($input['path'])) {
                if(mkdir($input['path'], 0777, true)) {
                    $m->add('success', "Created project root dir!");
                }
            }
        }

        foreach ($input AS $property => $value) {
            $project->$property = $value;
        }

        $project->save();

        $m->add('success', "Project modified!");

        return Redirect::to(handles('orchestra::resources/githubdeploys'))
                        ->with('message', $m->serialize());
    }

    /**
     * 
     * Deploy the current project to the latest version.
     * @param type $id
     */
    public function post_deploy() {

        $id = Input::get('id');
        $branch = Input::get('branch');
        
        if(!$branch) {
            $branch = 'master';
        }
        
        $projects = new Projects();
        $project = $projects->find($id);
        $m = new Messages;

        if (!$project) {
            $m->add('error', "Unknown project!");
            return Redirect::to(handles('orchestra::resources/githubdeploys'))
                            ->with('message', $m->serialize());
        }

        $response = new Deploy();
        $response = $response->deploy($project, $branch);

        if (is_array($response)) {

            foreach ($response AS $error) {
                $m->add('error', $error);
            }

            return Redirect::to(handles('orchestra::resources/githubdeploys'))
                            ->with('message', $m->serialize());
        } else {
            $hash = $response->hash;
        }

        $deploy = new Deploys();
        $deploy->project_id = $project->id;
        $deploy->release = $hash;
        $deploy->user_id = Auth::user()->id;
        $deploy->added_on = date('Y-m-d h:i:s');
        $deploy->response = serialize($response->resp);
        $deploy->save();

        $m->add('success', 'Deploy succedeed!');

        return Redirect::to(handles('orchestra::resources/githubdeploys.releases/view/' . $deploy->id))
                        ->with('message', $m->serialize());
    }

    /**
     * 
     * Remove the project alongside deploy dir.
     * @param type $id
     * @return type
     */
    public function get_delete($id) {
        
        $project = Projects::find($id);
        $m = new Messages;
        if (!$project) {
            $m->add('error', "Unknown project!");
            return Redirect::to(handles('orchestra::resources/githubdeploys'))
                            ->with('message', $m->serialize());
        }
        
        $proj = new Githubdeploys\Release;
        $proj->deleteProject($project);
        
        if($project->delete()) {
            
            Deploys::where('project_id', '=', $id)->delete();
            $m->add('success', "Project deleted alongside deploy path!");
            
        } else {
            $m->add('error', "Could not remove project from the database!");
        }
        
        return Redirect::to(handles('orchestra::resources/githubdeploys'))
                        ->with('message', $m->serialize());
        
    }

}