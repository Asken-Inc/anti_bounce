<?php

$config = [
    'AntiBounce' => [
        'settings' => [
            // true = stop mail sending, false = just write log
            'stopSending' => false,
            'bounceLimit' => 3,
            // email field
            'email' => [
                'table' => 'users',
                'key' => 'id',
                'mailField' => 'email',
            ],
            'updateFields' => [
                'model' => 'Users',
                'key' => 'id',
                'fields' => [
                    'mailmagazine' => 0,
                ]
            ]
        ]
    ]
];

return $config;
