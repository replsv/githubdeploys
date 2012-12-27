<?php

/*
 * This file is part of the Github-Deploy Laravel Package.
 *
 * (c) Gabriel C. <lazycoder.ro@gmail.com>
 *
 */

namespace Github;

class Deploy {

    /**
     * 
     * Errors container encountered while processing requests.
     * @var type 
     */
    public $errors = array();
    /**
     * 
     * Response container.
     * @var type 
     */
    public $resp = array();
    /**
     * 
     * Current user for service. (ex. www-data)
     * @var type 
     */
    public $webuser;
    /**
     * 
     * Current project.
     * @var type 
     */
    public $project;
    /**
     * 
     * Branch to clone.
     * @var type 
     */
    public $branch;
    /**
     * 
     * Current branch hash.
     * @var type 
     */
    public $hash;
    /**
     * 
     * Current release path.
     * Symlink to the current cloned path.
     * @var type 
     */
    public $productionPath;
    /**
     * 
     * Deploy directory.
     * @var type 
     */
    public $deployDir;

    /**
     * 
     * Deploy from Github repo.
     * @param \Github\Model\Projects $project
     * @param string $branch Branch name to be deployed
     * @return \Github\Deploy
     */
    public function deploy(\Github\Model\Projects $project, $branch) {

        /*
         * Set few class variables.
         */
        $this->setWebuser();
        $this->setProject($project);
        $this->setBranch($branch);

        /*
         * Check if the project's path exists. If it doesn't, raise an error.
         */
        if (!is_dir($project->path) || !is_writable($project->path)) {
            $this->errors[] = sprintf("The project path doesn't exist or
                is not writable. (%s)", $project->path);
        }

        /*
         * Check for releases dir.
         * Create it if necessary.
         */
        if (!is_dir($project->path . 'releases')) {
            mkdir($project->path . 'releases/', 0777, true);
            $this->resp[] = "Created releases path for project.";
        }

        /*
         * Set release variables.
         */
        $this->setHash();
        $this->setProductionPath();
        $this->setDeployDir();

        /*
         * Check if the latest release is already cloned.
         */
        if (!count($this->errors) && $this->_checkHash() === false) {
            $this->errors[] = sprintf("The current hash already exists.", $this->hash);
        }

        /*
         * Begin deploy if there are no errors.
         */
        if (!count($this->errors)) {
            $this->cloneRelease();
            $this->moveInProduction();
            return $this;
        } else {
            return $this->errors;
        }
    }

    /**
     * 
     * Set webuser.
     */
    public function setWebuser() {

        $this->webuser = shell_exec('whoami');
    }

    /**
     * 
     * Set project.
     * @param \Github\Model\Projects $project
     */
    public function setProject(\Github\Model\Projects $project) {
        $this->project = $project;
    }

    /**
     * 
     * Set branch.
     * @param type $branch
     */
    public function setBranch($branch) {
        $this->branch = $branch;
    }

    /**
     * 
     * Retrieve latest hash from Github and set it.
     */
    public function setHash() {

        $this->hash = trim(
                        shell_exec(
                            sprintf('git ls-remote %s %s | awk -F "\t" \'{print $1}\'', 
                                    $this->project->repo, $this->branch
                            )
                        )
                      );
        if(!$this->hash) {
            $this->errors[] = 'Could not retrieve release hash.
                Unknown repo or wrong branch name';
        }
    }

    /**
     * 
     * Set deploy directory.
     */
    public function setDeployDir() {

        $this->deployDir = $this->project->path . 'releases/' . $this->hash;
    }

    /**
     * 
     * Set production path.
     */
    public function setProductionPath() {

        $this->productionPath = $this->project->path . 'current-release';
    }

    /**
     * 
     * Check if the release exists or not.
     * @return boolean
     */
    private function _checkHash() {
        
        if (is_dir($this->deployDir)) {
            return false;
        }

        $this->resp[] = sprintf("Created deploy directory %s", $this->deployDir);
        return true;
    }

    /**
     * 
     * Clone release from Github.
     */
    public function cloneRelease() {

        /*
         * Clone.
         */
        $command = sprintf("cd %s && git clone %s -b %s %s",
                dirname($this->deployDir) . '/', $this->project->repo, $this->branch, $this->hash
        );
        exec($command, $return);
        $this->resp[] = implode("\n", $return);
        /*
         * Set permissions to deploy dir.
         */
        exec(sprintf("chown %s %s", $this->webuser, $this->deployDir));
        
    }
    
    /**
     * 
     * Move the cloned release in production.
     */
    public function moveInProduction() {
        
        /*
         * Remove symlink if exists.
         */
        if (file_exists($this->productionPath)) {
            unlink($this->productionPath);
            $this->resp[] = 'Removed current release symlink.';
        }
        
        /*
         * Create symlink.
         */
        if (is_dir($this->deployDir)) {
            if(symlink($this->deployDir, $this->productionPath)) {
                $this->resp[] = sprintf("Created current release symlink under %s",
                        $this->productionPath);
            } else {
                $this->resp[] = 'Cannot create current release symlink';
            }            
        }
    }

}