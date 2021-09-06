<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'view' => [
         'theme' => [
             'pathMap' => [
                '@frontend/views' => '@common/modules/procurement/views/layouts'
             ],
         ],
        ],
    ],
    'modules' => [
        'procurement' => [
            'class' => 'common\modules\procurement\Procurement',
        ],
        'v1' => [
            'class' => 'common\modules\v1\V1',
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
                                'roles' => ['Administrator'],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'rbac' => [
            'class' => 'markavespiritu\rbac\RbacWebModule',
        ],
    ]
];
