<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

// Developer Tools section with Developer Console
$admin_option_defs = [];
$admin_option_defs['Administration']['dev_console'] = [
    'Studio',
    'LBL_DEV_CONSOLE_TITLE',
    'LBL_DEV_CONSOLE_DESC',
    './index.php?module=Administration&action=devconsole',
    'developer-console'
];

$admin_group_header[] = [
    'LBL_DEV_TOOLS_TITLE',
    '',
    false,
    $admin_option_defs,
    'LBL_DEV_TOOLS_DESC'
];

