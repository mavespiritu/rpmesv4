<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@file' => dirname(__DIR__),
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
    'modules' => [
        'rpmes' => [
            'class' => 'common\modules\rpmes\Rpmes',
        ],
        'file' => [
            'class' => 'file\FileModule',
            'webDir' => 'files',
            'tempPath' => '@frontend/web/temp',
            'storePath' => '@frontend/web/store',
            'rules' => [
                'maxFiles' => 10,
                'maxSize' => 1024 * 1024 * 2 // 2 MB
            ],
        ],
        'user' => [
            'class' => 'markavespiritu\user\Module',
            'admins' => ['markavespiritu'],
            'enableRegistration' => true,
            'enableConfirmation' => false,
            'enablePasswordRecovery' => false,
            'controllerMap' => [
                'admin' => [
                    'class' => 'markavespiritu\user\controllers\AdminController',
                    'as access' => [
                        'class' => 'yii\filters\AccessControl',
                        'rules' => [
                            [
                                'allow' => true,
                                'roles' => ['SuperAdministrator','Administrator'],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'utility' => [
            'class' => 'c006\utility\migration\Module',
        ],
        'rbac' => [
            'class' => 'markavespiritu\rbac\RbacWebModule',
        ],
        'audit' => [
            'class' => 'bedezign\yii2\audit\Audit',
            'accessRoles' => ['SuperAdministrator'],
        ],
    ]
];
