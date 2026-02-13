<?php
return [
    'routes' => [
        'users'          => true,
        'manual_actions' => true,
        'roles'          => true,
        'permissions'    => true,
        'verification'   => true,
    ],

    'features' => [
        // Adds status column support (active/suspended/banned). Requires migration.
        'user_status' => false,

        // Allow admins to impersonate other users for debugging.
        'impersonation' => true,
    ],

    'impersonation' => [
        // Permission required to impersonate users.
        'permission' => 'users.impersonate',

        // Where to redirect after starting/stopping impersonation.
        'redirect' => '/',
    ],

    'user_statuses' => ['active', 'suspended', 'banned'],
];
