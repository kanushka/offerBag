<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
    'visa_config' => [
        'url' => 'https://sandbox.api.visa.com/',
        'username' => 'MXQ4H8NR802VVQIXNR5621A05pcmYEEd0d2OaP082vfB15hxA',
        'password' => 'yz1kXO4outKqNtCinm6C0C5qw1KeZHrD77',
        'file_name' => [
            'certificate' => '\cert.pem',
            'private_key' => '\key_2d724912-2c02-45ef-b2c8-accb2983ad08.pem',
        ],
    ],
];
