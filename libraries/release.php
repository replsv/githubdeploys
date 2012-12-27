<?php

/*
 * This file is part of the Github-Deploy Laravel Package.
 *
 * (c) Gabriel C. <lazycoder.ro@gmail.com>
 *
 */

namespace Github;

class Release {
    
    /**
     * 
     * Get current release from project.
     * @param $project
     * @return string
     */
    public function getCurrentRelease(\Github\Model\Projects $project) {
        
        if(!file_exists($project->path)) {            
            return '';
        }
        
        if(!file_exists($project->path . 'current-release')) {
            return '';
        }
        
        $path = explode('/', readlink($project->path . 'current-release'));
        return end($path);
        
    }
    
    /**
     * 
     * Move the chosen release in production.
     * @param \Github\Model\Projects $project
     * @param \Github\Model\Deploys $release
     * @return string
     */
    public function moveInProduction(\Github\Model\Projects $project, \Github\Model\Deploys $release) {
        
        $productionPath = $project->path . 'current-release';
        $releasePath = $project->path . 'releases/' . $release->release;
        $return = array();
        
        /*
         * Remove symlink if exists.
         */
        if (file_exists($productionPath)) {
            unlink($productionPath);
            $return['success'][] = 'Removed current release symlink.';
        }
        
        /*
         * Create symlink.
         */
        if (is_dir($releasePath)) {
            if(symlink($releasePath, $productionPath)) {
                $return['success'][] = sprintf("Created current release symlink under %s",
                        $productionPath);
            } else {
                $return['error'][] = 'Cannot create current release symlink';
            }            
        } else {
            $return['error'][] = 'Unknown folder for the current release';
        }
        
        return $return;
        
    }
    
    /**
     * 
     * Make a simple 'git pull' for the initialised repo.
     * @param \Github\Model\Projects $project
     * @param \Github\Model\Deploys $release
     * @return type
     */
    public function pull(\Github\Model\Projects $project, \Github\Model\Deploys $release) {
        
        $releasePath = $project->path . 'releases/' . $release->release;
        $command = sprintf("cd %s && git pull", $releasePath);
        exec($command, $output);
        
        return $output;
        
    }
    
    /**
     * 
     * Delete the release.
     * @param \Github\Model\Projects $project
     * @param type $release
     * @return type
     */
    public function deleteRelease(\Github\Model\Projects $project, $release) {
        
        $return = array();
        $releasePath = $project->path . 'releases/' . $release->release;
        
        /*
         * Check if the current release is used in production.
         * In this case, the symlink will be removed.
         */
        if($this->getCurrentRelease($project) === $release->release) {
            $productionPath = $project->path . 'current-release';
            if(unlink($productionPath)) {
                $return['success'][] = 'Removed the release from production.';
            } else {
                $return['error'][] = 'There was a problem trying to remove the release from production.';
            }
        }
        
        /*
         * Check $releasePath and delete it.
         */
        if(is_dir($releasePath)) {
            exec(sprintf("rm -rf %s", $releasePath));
            $return['success'][] = sprintf("Deleted release's dir. (%s)", $releasePath);
        } else {
            $return['error'][] = sprintf("Could not find release's path. (%s)", $releasePath);
        }
        
        return $return;
        
    }
    
    /**
     * 
     * Delete project's path.
     * We filter to get absolute path. exec used to remove the whole dir recursive.
     * @param \Github\Model\Projects $project
     */
    public function deleteProject(\Github\Model\Projects $project) {
        
        $path = $project->path;
        if(substr($path, strlen($path)-1) === '/') {
            $path = substr($path, 0, strlen($path)-1);
        }
        $command = sprintf("rm -rf %s", $project->path);
        exec($command);
    }
    
}
