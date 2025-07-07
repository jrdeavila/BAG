<?php

use App\Models\Permission;
use App\Models\Role;

return [

    'models' => [
        'permission' => Permission::class,
        'role' => Role::class,
    ],

    'table_names' => [
        'roles' => env('DB_AUTHORIZATION_DATABASE') . '.roles',
        'permissions' => env('DB_AUTHORIZATION_DATABASE') . '.permissions',
        'model_has_permissions' => env('DB_AUTHORIZATION_DATABASE') . '.model_has_permissions',
        'model_has_roles' => env('DB_AUTHORIZATION_DATABASE') . '.model_has_roles',
        'role_has_permissions' =>  env('DB_AUTHORIZATION_DATABASE') . '.role_has_permissions',
    ],

    'column_names' => [
        'role_pivot_key' => 'role_id', // default 'role_id',
        'permission_pivot_key' => 'permission_id', // default 'permission_id',
        'model_morph_key' => 'model_id',
        'team_foreign_key' => 'team_id',
    ],

    'register_permission_check_method' => true,

    'register_octane_reset_listener' => false,

    'events_enabled' => false,

    'teams' => false,

    'team_resolver' => \Spatie\Permission\DefaultTeamResolver::class,

    'use_passport_client_credentials' => false,

    'display_permission_in_exception' => false,

    'display_role_in_exception' => false,

    'enable_wildcard_permission' => false,

    'cache' => [
        'expiration_time' => \DateInterval::createFromDateString('24 hours'),

        'key' => 'spatie.permission.cache',

        'store' => 'default',
    ],
];
