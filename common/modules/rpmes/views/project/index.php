<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Projects';
$this->params['breadcrumbs'][] = $this->title;

$successMessage = \Yii::$app->getSession()->getFlash('success');
?>
<div class="project-index">

    <div class="box box-solid">
        <div class="box-header with-border"><h3 class="box-title">Projects</h3></div>
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
                        'header' => '
                                    (a) Program/Project Title <br>
                                    (b) Province <br>
                                    (c) City/Municipality <br>
                                    (d) Barangay
                                    ',
                        'headerOptions' => [
                            'style' => 'width: 30%; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'format' => 'raw',
                        'value' => function($model){
                            return 
                                '(a) '.$model->title.'<br>'.
                                '(b) '.$model->provinceTitle.'<br>'.
                                '(c) '.$model->citymunTitle.'<br>'.
                                '(e) '.$model->barangayTitle
                            ;
                        }
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
                        'header' => 'Project Profile',
                        'format' => 'raw',
                        'headerOptions' => [
                            'style' => 'width: 20%; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'value' => function($model){
                            $str = '';
                            if($model->files){
                                foreach($model->files as $file){
                                    $str.= Html::a($file->name.'.'.$file->type, ['/file/file/download', 'id' => $file->id]).'<br>';
                                }
                            }
                            return $str;
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn', 
                        'header' => 'Actions',
                        'headerOptions' => [
                            'style' => 'background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'template' => '<center>{update} {delete}</center>',
                        'buttons' => [
                            'update' => function($url, $model, $key){
                                $modalID = $model->id;
                                return Html::a('Update', ['update', 'id' => $model->id], [
                                    'class' => 'btn btn-warning btn-block btn-xs'
                                ]);
                            },
                            'delete' => function($url, $model, $key){
                                return Html::a('Delete', ['delete', 'id' => $model->id], [
                                                    'class' => 'btn btn-danger btn-block btn-xs',
                                                    'data' => [
                                                        'confirm' => 'Are you sure want to delete this item?',
                                                        'method' => 'post',
                                                    ],
                                                ]);
                            },
                        ],
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
                        'header' => '
                                    (a) Program/Project Title <br>
                                    (b) Province <br>
                                    (c) City/Municipality <br>
                                    (d) Barangay
                                    ',
                        'headerOptions' => [
                            'style' => 'width: 30%; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'format' => 'raw',
                        'value' => function($model){
                            return 
                                '(a) '.$model->title.'<br>'.
                                '(b) '.$model->provinceTitle.'<br>'.
                                '(c) '.$model->citymunTitle.'<br>'.
                                '(e) '.$model->barangayTitle
                            ;
                        }
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
                        'header' => 'Project Profile',
                        'format' => 'raw',
                        'headerOptions' => [
                            'style' => 'width: 20%; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'value' => function($model){
                            $str = '';
                            if($model->files){
                                foreach($model->files as $file){
                                    $str.= '<table style="width: 100%">';
                                    $str.= '<tr>';
                                    $str.= '<td>'.Html::a($file->name.'.'.$file->type, ['/file/file/download', 'id' => $file->id]).'</td>';
                                    $str.= '<td style="vertical-align: top;" align=right>'.Html::a('<i class="fa fa-trash"></i>',['/file/file/delete', 'id' => $file->id],[
                                        'data' => [
                                            'confirm' => 'Are you sure want to delete this file?',
                                            'method' => 'post',
                                        ],
                                    ]).'</td>';
                                    $str.= '</tr>';
                                    $str.= '</table>';
                                }
                            }
                            return $str;
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn', 
                        'header' => 'Actions',
                        'headerOptions' => [
                            'style' => 'background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'template' => '<center>{update} {delete}</center>',
                        'buttons' => [
                            'update' => function($url, $model, $key){
                                $modalID = $model->id;
                                return Html::a('Update', ['update', 'id' => $model->id], [
                                    'class' => 'btn btn-warning btn-block btn-xs'
                                ]);
                            },
                            'delete' => function($url, $model, $key){
                                return Html::a('Delete', ['delete', 'id' => $model->id], [
                                                    'class' => 'btn btn-danger btn-block btn-xs',
                                                    'data' => [
                                                        'confirm' => 'Are you sure want to delete this item?',
                                                        'method' => 'post',
                                                    ],
                                                ]);
                            },
                        ],
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