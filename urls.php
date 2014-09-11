<?php

return [
    '/{id:\d+}' => [
        'name' => 'view',
        'params' => [
            'id' => '(\d+)',
        ],
        'callback' => '\Modules\User\Controllers\UserController:view'
    ],

//    'edit/{:id}' => [
//        'name' => 'edit',
//        'callback' => 'UserController:actionUpdate'
//    ],
//
//    'edit' => 'user/user/update',
    '/recover' => [
        'name' => 'recoverform',
        'callback' => '\Modules\User\Controllers\RegistrationController:recoverform'
    ],
//    'recover-{:key}' => 'user/recovery/recovery',
//    'changepassword/{:id}' => 'user/changepassword/changepassword',
    '/profile' => [
        'name' => 'profile',
        'callback' => '\Modules\User\Controllers\UserController:profile',
    ],
//    'activation/{:key}' => 'user/activation/activation',
    '/registration' => [
        'name' => 'registration',
        'callback' => '\Modules\User\Controllers\RegistrationController:registration'
    ],
//    'registration/success' => 'user/registration/success',

//    '' => 'user/user/index',
    '/logout' => [
        'name' => 'logout',
        'callback' => '\Modules\User\Controllers\AuthController:logout'
    ],
    '/login' => [
        'name' => 'login',
        'callback' => '\Modules\User\Controllers\AuthController:login'
    ],

//    'group/change' => 'user/group/change',
//    'group/select/{:id}' => 'user/group/select',
];
