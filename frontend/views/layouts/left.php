<?php 

    $userRoles = [];
    $roles = \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
    if(!empty($roles))
    {
        foreach($roles as $role)
        {
            $userRoles[] = $role->name;
        }
    }  
?>
<aside class="main-sidebar">

    <section class="sidebar">
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => 'MAIN MENU', 'options' => ['class' => 'header']],
                    ['label' => 'Dashboard', 'icon' => 'bar-chart-o', 'url' => ['/rpmes/dashboard']],
                    ['label' => 'Add Project', 'icon' => 'plus', 'url' => ['/rpmes/project/create'], 'visible' => !Yii::$app->user->isGuest],
                    [
                        'label' => 'Projects',
                        'icon' => 'folder',
                        'url' => '#',
                        'visible' => !Yii::$app->user->isGuest,
                        'items' => [
                            ['label' => 'Saved Projects', 'icon' => 'folder', 'url' => ['/rpmes/project/draft'], 'visible' => !Yii::$app->user->isGuest],
                        ],
                        
                    ],
                    ['label' => 'Monitoring Plan', 'icon' => 'folder', 'url' => ['/rpmes/plan'], 'visible' => !Yii::$app->user->isGuest],
                    ['label' => 'Form 2: Accomplishment', 'icon' => 'folder', 'url' => ['/rpmes/accomplishment'], 'visible' => !Yii::$app->user->isGuest],
                    ['label' => 'Project Exception', 'icon' => 'folder', 'url' => ['/rpmes/project-exception'], 'visible' => !Yii::$app->user->isGuest],
                    [
                        'label' => 'Acknowledgment',
                        'icon' => 'folder',
                        'url' => '#',
                        'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles)),
                        'items' => [
                            ['label' => 'Monitoring Plans', 'icon' => 'folder', 'url' => ['/rpmes/acknowledgment/monitoring-plan'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                            ['label' => 'Monitoring Report', 'icon' => 'folder', 'url' => ['/rpmes/acknowledgment/monitoring-report'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                        ],
                    ],
                    [
                        'label' => 'Summary',
                        'icon' => 'folder',
                        'url' => '#',
                        'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles)),
                        'items' => [
                            ['label' => 'Monitoring Plans', 'icon' => 'folder', 'url' => ['/rpmes/summary/monitoring-plan'], 'visible' => !Yii::$app->user->isGuest],
                            ['label' => 'Monitoring Report', 'icon' => 'folder', 'url' => ['/rpmes/summary/monitoring-report'], 'visible' => !Yii::$app->user->isGuest],
                            ['label' => 'Accomplishment', 'icon' => 'folder', 'url' => ['/rpmes/summary/monitoring-report-sector'], 'visible' => !Yii::$app->user->isGuest],
                        ],
                    ],
                    ['label' => 'Form 7: Project Finding', 'icon' => 'folder', 'url' => ['/rpmes/project-finding'], 'visible' => !Yii::$app->user->isGuest],
                    ['label' => 'Form 9: Trainings', 'icon' => 'folder', 'url' => ['/rpmes/training'], 'visible' => !Yii::$app->user->isGuest],
                    ['label' => 'Form 10: Resolutions', 'icon' => 'folder', 'url' => ['/rpmes/resolution'], 'visible' => !Yii::$app->user->isGuest],
                    ['label' => 'Form 11: Key Lessons', 'icon' => 'folder', 'url' => ['/rpmes/project-problem'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                    ['label' => 'Administrator', 'options' => ['class' => 'header'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                    ['label' => 'Due Dates', 'icon' => 'clock-o', 'url' => ['/rpmes/due-date'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                    [
                        'label' => 'Presets',
                        'icon' => 'cog',
                        'url' => '#',
                        'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles)),
                        'items' => [
                            ['label' => 'Agencies', 'icon' => 'folder', 'url' => ['/rpmes/agency'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                            ['label' => 'Agency Types', 'icon' => 'folder', 'url' => ['/rpmes/agency-type'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                            ['label' => 'Programs', 'icon' => 'folder', 'url' => ['/rpmes/program'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                            ['label' => 'Sectors', 'icon' => 'folder', 'url' => ['/rpmes/sector'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                            ['label' => 'Sub-Sectors', 'icon' => 'folder', 'url' => ['/rpmes/sub-sector'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                            ['label' => 'Sub-Sectors By Sectors', 'icon' => 'folder', 'url' => ['/rpmes/sub-sector-per-sector'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                            ['label' => 'Fund Sources', 'icon' => 'folder', 'url' => ['/rpmes/fund-source'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                            [
                                'label' => 'RDP-related',
                                'icon' => 'folder',
                                'url' => '#',
                                'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles)),
                                'items' => [
                                    ['label' => 'SDG Goals', 'icon' => 'folder', 'url' => ['/rpmes/sdg-goal'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                                    ['label' => 'Chapters', 'icon' => 'folder', 'url' => ['/rpmes/rdp-chapter'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                                    ['label' => 'Chapter Outcomes', 'icon' => 'folder', 'url' => ['/rpmes/rdp-chapter-outcome'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                                    ['label' => 'Sub-Chapter Outcomes', 'icon' => 'folder', 'url' => ['/rpmes/rdp-sub-chapter-outcome'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                                    ['label' => 'Categories', 'icon' => 'folder', 'url' => ['/rpmes/category'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                                    ['label' => 'KRA/Clusters', 'icon' => 'folder', 'url' => ['/rpmes/key-result-area'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                                ],
                                
                            ],
                        ],
                        
                    ],
                    ['label' => 'User Management', 'icon' => 'users', 'url' => ['/user/admin'], 'visible' => !Yii::$app->user->isGuest && (in_array('SuperAdministrator', $userRoles) || in_array('Administrator', $userRoles))],
                ],
            ]
        ) ?>

    </section>

</aside>
