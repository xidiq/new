<?php
/**
 * Manage and give access to lizmap configuration.
 *
 * @author    3liz
 * @copyright 2017 3liz
 *
 * @see      http://3liz.com
 *
 * @license Mozilla Public License : http://www.mozilla.org/MPL/
 */
class actionConfig
{
    private $status = false;
    private $errors = array();
    private $config;

    public function __construct($repository, $project)
    {
        $this->status = false;

        try {
            $lproj = lizmap::getProject($repository.'~'.$project);
            if (!$lproj) {
                $this->errors = array(
                    'title' => 'Invalid Query Parameter',
                    'detail' => 'The lizmap project '.strtoupper($project).' does not exist !',
                );

                return;
            }
        } catch (UnknownLizmapProjectException $e) {
            $this->errors = array(
                'title' => 'Invalid Query Parameter',
                'detail' => 'The lizmap project '.strtoupper($project).' does not exist !',
            );

            return;
        }

        // Check acl
        if (!$lproj->checkAcl()) {
            $this->errors = array(
                'title' => 'Access Denied',
                'detail' => jLocale::get('view~default.repository.access.denied'),
            );

            return;
        }

        // Test if action file is found
        $action_path = $lproj->getQgisPath().'.action';
        if (!file_exists($action_path)) {
            return;
        }

        // Parse config
        $config = jFile::read($action_path);
        $this->config = json_decode($config);
        if ($this->config === null) {
            return;
        }

        // Get config
        $this->status = true;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
