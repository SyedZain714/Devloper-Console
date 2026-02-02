<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('modules/Administration/controller.php');

class CustomAdministrationController extends AdministrationController
{
    public function action_devconsole()
    {
        global $current_user;
        
        // Only admins can access
        if (!is_admin($current_user)) {
            sugar_die('Unauthorized access');
        }
        
        $this->view = 'devconsole';
    }
}

