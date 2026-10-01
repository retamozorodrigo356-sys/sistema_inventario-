<?php

/*
|--------------------------------------------------------------------------
| Permisos por rol
|--------------------------------------------------------------------------
|
| Cada rol posee una lista de habilidades. La habilidad "*" otorga
| acceso total. Estos permisos se usan en el middleware "permission:..."
| y en las directivas @can de las vistas Blade.
|
*/

return [
    'roles' => [
        'admin' => [
            'name' => 'Administrador',
            'abilities' => ['*'],
        ],

        'encargado' => [
            'name' => 'Encargado de almacén',
            'abilities' => [
                'dashboard.view',
                'products.view',
                'products.create',
                'products.update',
                'categories.view',
                'categories.create',
                'categories.update',
                'categories.delete',
                'entries.view',
                'entries.create',
                'exits.view',
                'exits.create',
                'movements.view',
                'reports.view',
            ],
        ],

        'consultor' => [
            'name' => 'Consultor',
            'abilities' => [
                'dashboard.view',
                'products.view',
                'categories.view',
                'movements.view',
                'reports.view',
            ],
        ],
    ],
];
