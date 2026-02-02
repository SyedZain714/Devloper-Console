<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class AdministrationViewDevconsole extends SugarView
{
    public function display()
    {
        global $current_user;
        
        if (!is_admin($current_user)) {
            sugar_die('Unauthorized access');
        }
        
        // Load the Developer Console via entry point in an iframe
        echo '<iframe src="index.php?entryPoint=DevConsole" style="width:100%; height:calc(100vh - 100px); border:none; margin:0; padding:0;"></iframe>';
    }
}

