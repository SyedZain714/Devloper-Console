<?php
/**
 * SuiteCRM Developer Console
 * Module Installer Manifest
 */

$manifest = [
    'name' => 'Developer Console',
    'description' => 'An integrated code editor and developer console for SuiteCRM. Browse and edit files directly from the admin panel.',
    'version' => '1.0.0',
    'author' => 'SuiteCRM Developer',
    'readme' => 'README.txt',
    'acceptable_sugar_flavors' => ['CE'],
    'acceptable_sugar_versions' => [
        'exact_matches' => [],
        'regex_matches' => ['.*'],
    ],
    'type' => 'module',
    'is_uninstallable' => true,
    'published_date' => '2024-12-03',
    'icon' => '',
];

$installdefs = [
    'id' => 'developer_console',
    'copy' => [
        // Main console files
        [
            'from' => '<basepath>/Files/custom/devconsole',
            'to' => 'custom/devconsole',
        ],
        // Controller file
        [
            'from' => '<basepath>/Files/custom/modules/Administration/controller.php',
            'to' => 'custom/modules/Administration/controller.php',
        ],
        // View file
        [
            'from' => '<basepath>/Files/custom/modules/Administration/views',
            'to' => 'custom/modules/Administration/views',
        ],
    ],
    'language' => [
        [
            'from' => '<basepath>/Files/custom/Extension/modules/Administration/Ext/Language/en_us.dev_console.php',
            'to_module' => 'Administration',
            'language' => 'en_us',
        ],
    ],
    'administration' => [
        [
            'from' => '<basepath>/Files/custom/Extension/modules/Administration/Ext/Administration/DevConsole.php',
        ],
    ],
    'entrypoints' => [
        [
            'from' => '<basepath>/Files/custom/Extension/application/Ext/EntryPointRegistry/DevConsole.php',
        ],
    ],
    'custom_fields' => [],
    'beans' => [],
    'layoutdefs' => [],
    'relationships' => [],
    'vardefs' => [],
    'menu' => [],
    'dashlets' => [],
    'logic_hooks' => [],
];

