<?php

$productName = 'InMaFSS';
$productVersion = '2.0';
$steps = Array(
    Array(
        'title' => 'Introduction',
        'template' => 'introduction'
    ),
    Array(
        'title' => 'Licence',
        'template' => 'licence'
    ),
    Array(
        'title' => 'Server requirements',
        'template' => 'requirements'
    ),
    Array(
        'title' => 'File permissions',
        'template' => 'permissions'
    ),
    Array(
        'title' => 'Database',
        'template' => 'database'
    ),
    Array(
        'title' => 'Database import',
        'template' => 'db_import'
    ),
    Array(
        'title' => 'Settings',
        'template' => 'settings'
    ),
    Array(
        'title'=>'Done',
        'template'=>'done'
    )
);

$licence = file_get_contents('config/licence.txt');

$requirements = Array(
    'php' => Array(
        'version' => '5.2.3',
        'extensions' => Array(
            Array(
                'name' => 'reflection'
            ),
            Array(
                'name' => 'spl',
                'version' => '0.1'
            ),
            Array(
                'name' => 'simplexml',
                'version' => '0.1'
            ),
            Array(
                'name' => 'ctype'
            ),
            Array(
                'name' => 'date',
            ),
            Array(
                'name' => 'hash',
                'version' => '1.0'
            ),
            Array(
                'name' => 'json',
                'version' => '1.2.1'
            ),
            Array(
                'name' => 'curl',
            ),
            Array(
                'name' => 'ldap'
            ),
            Array(
                'name' => 'mbstring'
            ),
            Array(
                'name' => 'openssl',
            ),
            Array(
                'name' => 'pcre',
            ),
            Array(
                'name' => 'session',
            ),
            Array(
                'name' => 'xml',
            ),
            Array(
                'name' => 'xmlreader',
                'version' => '0.1'
            ),
            Array(
                'name' => 'zip',
                'version' => '1.0.0'
            ),
        ),
        'optionalExt' => Array(
        ),
        'exchangeableExt' => Array(
            Array(
                'name' => 'Database',
                'ext' => Array(
                    Array(
                        'name' => 'mysqli',
                        'version' => '0.1'
                    ),
                    Array(
                        'name' => 'mysql',
                        'version' => '0.1'
                    )
                )
            )
        )
    ),
    // Works only with apache
    'server' => Array(
        'modules' => Array(
            'mod_rewrite'
        )
    )
);

$files = Array(
    'dirs'=>Array(
        'tmp'
    ),
    'files'=>Array(
        'inc/config.php'
    )
);

$configFile = "inc/config.php";
$configContent = file_get_contents('config/config.txt');
?>
