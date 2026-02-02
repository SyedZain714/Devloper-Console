<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

// Developer Console UI
$entry_point_registry['DevConsole'] = [
    'file' => 'custom/devconsole/index.php',
    'auth' => true
];

// Developer Console API
$entry_point_registry['DevConsoleAPI'] = [
    'file' => 'custom/devconsole/api.php',
    'auth' => true
];

