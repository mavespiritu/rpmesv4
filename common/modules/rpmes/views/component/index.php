<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Component Projects';
$this->params['breadcrumbs'][] = $this->title;

$successMessage = \Yii::$app->getSession()->getFlash('success');
?>
<div class="project-index">

    <div class="box box-solid">
        <div class="box-header with-border"><h3 class="box-title">Component Projects</h3></div>
        <div class="box-body">
        
            <?= $this->render('_search', ['model' => $searchModel]); ?>

            <?= GridView::widget([
                'options' => [
                    'class' => 'table-responsive'
                ],
                'tableOptions' => [
                    'class' => 'table table-bordered table-striped table-hover',
                ],
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
                'columns' => Yii::$app->user->can('Administrator') ? [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => [
                            'style' => 'background-color: #002060; color: white; font-weight: normal;'
                        ],
                    ],

                    //'id',
                    [
                        'attribute' => 'project_no',
                        'header' => 'Project No.',
                        'headerOptions' => [
                            'style' => 'width: 10%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'attribute' => 'motherProject.title',
                        'header' => 'Mother Project',
                        'headerOptions' => [
                            'style' => 'width: 15%; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'value' => function($model){
                            return $model->motherProject->project_no.': '.$model->motherProject->title;
                        }
                    ],
                    [
                        'attribute' => 'title',
                        'header' => 'Component Project Title',
                        'headerOptions' => [
                            'style' => 'width: 15%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'attribute' => 'agency.code',
                        'header' => 'Agency',
                        'headerOptions' => [
                            'style' => 'width: 10%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'attribute' => 'sector.title',
                        'header' => 'Sector',
                        'headerOptions' => [
                            'style' => 'width: 15%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'attribute' => 'modeOfImplementation.title',
                        'header' => 'Mode of Implementation',
                        'headerOptions' => [
                            'style' => 'width: 15%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'header' => 'Total Project Cost',
                        'format' => 'raw',
                        'headerOptions' => [
                            'style' => 'width: 20%; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'value' => function($model){
                            return number_format($model->cost, 2);
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn', 
                        'header' => 'Actions',
                        'headerOptions' => [
                            'style' => 'background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'template' => '<center>{update} {delete}</center>'
                    ],
                ] : [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => [
                            'style' => 'background-color: #002060; color: white; font-weight: normal;'
                        ],
                    ],

                    //'id',
                    [
                        'attribute' => 'project_no',
                        'header' => 'Project No.',
                        'headerOptions' => [
                            'style' => 'width: 10%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'attribute' => 'motherProject.title',
                        'header' => 'Mother Project',
                        'headerOptions' => [
                            'style' => 'width: 15%; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'value' => function($model){
                            return $model->motherProject->project_no.': '.$model->motherProject->title;
                        }
                    ],
                    [
                        'attribute' => 'title',
                        'header' => 'Component Project Title',
                        'headerOptions' => [
                            'style' => 'width: 20%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'attribute' => 'sector.title',
                        'header' => 'Sector',
                        'headerOptions' => [
                            'style' => 'width: 15%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'attribute' => 'modeOfImplementation.title',
                        'header' => 'Mode of Implementation',
                        'headerOptions' => [
                            'style' => 'width: 15%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'header' => 'Total Project Cost',
                        'format' => 'raw',
                        'headerOptions' => [
                            'style' => 'width: 20%; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'value' => function($model){
                            return number_format($model->cost, 2);
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn', 
                        'header' => 'Actions',
                        'headerOptions' => [
                            'style' => 'background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'template' => '<center>{update} {delete}</center>'
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>

<?php
if ($successMessage) {
    $this->registerJs("
        $(document).ready(function() {
            // Display the flash message
            $('.alert-success').fadeIn();

            // Hide the flash message after 5 seconds
            setTimeout(function() {
                $('.alert-success').fadeOut();
            }, 5000);
        });
    ");
}
?>