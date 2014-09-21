<?php

return [
    '/{id:\d+}' => [
        'name'   => 'view',
        'params' => [
            'id' => '(\d+)',
        ],
        'callback' => '\Modules\User\Controllers\UserController:view'
    ],
    '/recover' => [
        'name'     => 'recover',
        'callback' => '\Modules\User\Controllers\RegistrationController:recoverform'
    ],
    '/profile' => [
        'name'     => 'profile',
        'callback' => '\Modules\User\Controllers\UserController:profile',
    ],

    '/registration' => [
        'name'     => 'registration',
        'callback' => '\Modules\User\Controllers\RegistrationController:index'
    ],
    '/registration/success' => [
        'name'     => 'registration_success',
        'callback' => '\Modules\User\Controllers\RegistrationController:success'
    ],
    '/registration/activation/{key}' => [
        'name'     => 'registration_activation',
        'callback' => '\Modules\User\Controllers\RegistrationController:activate'
    ],

    '/logout' => [
        'name'     => 'logout',
        'callback' => '\Modules\User\Controllers\AuthController:logout'
    ],
    '/login' => [
        'name'     => 'login',
        'callback' => '\Modules\User\Controllers\AuthController:login'
    ]
];
