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
                'maxSize' => 1024 * 1024 * 5, // 2 MB
                'mimeTypes' => [
                    'application/pdf',
                    'image/jpeg',
                    'image/png',
                ],
            ],
        ],
        'user' => [
            'class' => 'markavespiritu\user\Module',
            'admins' => ['markavespiritu'],
            'enableRegistration' => true,
            'enableConfirmation' => true,
            'enablePasswordRecovery' => true,
            'mailer' => [
                    'sender'                => 'nro1.mailer@neda.gov.ph',
                    'welcomeSubject'        => 'Welcome to the NEDA RO1 eRPMES',
                    'confirmationSubject'   => 'Confirm your NEDA RO1 eRPMES account',
                    'reconfirmationSubject' => 'Reconfirm your NEDA RO1 eRPMES account',
                    'recoverySubject'       => 'Recover your NEDA RO1 eRPMES account',
            ],
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
                            [
                                'actions' => ['switch'],
                                'allow' => true,
                                'roles' => ['SuperAdministrator'],
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
        /* 'audit' => [
            'class' => 'bedezign\yii2\audit\Audit',
            'accessRoles' => ['SuperAdministrator'],
        ], */
    ]
];
