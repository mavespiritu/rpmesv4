<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <p style="color: white;">Howdy, <?= Yii::$app->user->identity->userinfo->FIRST_M ?>!</p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
            <div class="pull-left info">
                
            </div>
        </div>

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => 'MAIN MENU', 'options' => ['class' => 'header']],
                    ['label' => 'Dashboard', 'icon' => 'bar-chart-o', 'url' => ['/gii']],
                    ['label' => 'Budget Monitoring', 'icon' => 'bar-chart-o', 'url' => ['/gii']],
                    ['label' => 'Procurement Planning', 'options' => ['class' => 'header']],
                    ['label' => 'NEP', 'icon' => 'file-code-o', 'url' => ['/v1/nep']],
                    ['label' => 'GAA', 'icon' => 'file-code-o', 'url' => ['/v1/gaa']],
                    ['label' => 'PPMP', 'icon' => 'file-code-o', 'url' => ['/v1/ppmp']],
                    ['label' => 'Actual Procurement', 'options' => ['class' => 'header']],
                    ['label' => 'RIS', 'icon' => 'file-code-o', 'url' => ['/gii']],
                    ['label' => 'PR', 'icon' => 'file-code-o', 'url' => ['/gii']],
                    ['label' => 'Reports', 'icon' => 'file-code-o', 'url' => ['/gii']],

                    ['label' => 'Inventory', 'options' => ['class' => 'header']],
                    ['label' => 'Inventory', 'icon' => 'file-code-o', 'url' => ['/gii']],
                    ['label' => 'Administrator', 'options' => ['class' => 'header']],
                    [
                        'label' => 'Libraries',
                        'icon' => 'cog',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => 'Activities', 
                                'icon' => 'file-code-o', 
                                'url' => '#', 
                                'items' => [
                                    ['label' => 'Level 1', 'icon' => 'file-code-o', 'url' => ['/v1/activity'],],
                                    ['label' => 'Level 2', 'icon' => 'file-code-o', 'url' => ['/v1/sub-activity'],],                                ]
                            ],
                            ['label' => 'Fund Clusters', 'icon' => 'file-code-o', 'url' => ['/v1/fund-cluster'],],
                            ['label' => 'Fund Sources', 'icon' => 'file-code-o', 'url' => ['/v1/fund-source'],],
                            [
                                'label' => 'PREXC', 
                                'icon' => 'file-code-o', 
                                'url' => '#', 
                                'items' => [
                                    ['label' => 'Cost Structures', 'icon' => 'file-code-o', 'url' => ['/v1/cost-structure'],],
                                    ['label' => 'Org Outcomes', 'icon' => 'file-code-o', 'url' => ['/v1/organizational-outcome'],],
                                    ['label' => 'Programs', 'icon' => 'file-code-o', 'url' => ['/v1/program'],],
                                    ['label' => 'Sub-Programs', 'icon' => 'file-code-o', 'url' => ['/v1/sub-program'],],
                                    ['label' => 'Identifiers', 'icon' => 'file-code-o', 'url' => ['/v1/identifier'],],
                                    ['label' => 'PAPs', 'icon' => 'file-code-o', 'url' => ['/v1/pap'],],
                                    
                                ]
                            ],
                            ['label' => 'Objects', 'icon' => 'file-code-o', 'url' => ['/v1/obj'],],
                            ['label' => 'Procurement Modes', 'icon' => 'file-code-o', 'url' => ['/v1/procurement-mode'],],
                        ],
                    ],
                    ['label' => 'User Management', 'icon' => 'users', 'url' => ['/user/admin']],
                ],
            ]
        ) ?>

    </section>

</aside>
