<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use common\components\helpers\HtmlHelper;
use yii\bootstrap\ButtonDropdown;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
use yii\web\View;
use yii\bootstrap\Modal;

$this->title = 'RPMES Form 4: Project Results Report';
$this->params['breadcrumbs'][] = $this->title;

$successMessage = \Yii::$app->getSession()->getFlash('success');
?>

<?php foreach ($dataProvider->models as $model): ?>
    <?php
    $modelID = $model->id;
    Modal::begin([
        'id' => 'update-modal-'.$modelID,
        'size' => "modal-md",
        'header' => '<div id="update-modal-'.$modelID.'-header"><h4>Update Project Results Report</h4></div>',
        'options' => ['tabindex' => false],
    ]);
    echo '<div id="update-modal-'.$modelID.'-content"></div>';
    Modal::end();
    ?>
<?php endforeach; ?>

<div class="accomplishment-index">
    <div class="flash-success" style="display: none;">
        <?= $successMessage ?>
    </div>

    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Project Result Reports</h3>
        </div>
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
                            ]
                        ],

                        //'id',
                        [
                            'attribute' => 'year',
                            'header' => 'Year',
                            'headerOptions' => [
                                'style' => 'width: 10%; background-color: #002060; color: white; font-weight: normal;'
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
                            'attribute' => 'status',
                            'header' => 'Status',
                            'headerOptions' => [
                                'style' => 'width: 10%; background-color: #002060; color: white; font-weight: normal;'
                            ],
                            'format' => 'raw',
                            'value' => function($model){
                                return $model->currentStatus;;
                            }
                        ],
                        [
                            'attribute' => 'submitted_by',
                            'header' => 'Submitted By',
                            'headerOptions' => [
                                'style' => 'width: 13%; background-color: #002060; color: white; font-weight: normal;'
                            ],
                            'format' => 'raw',
                            'value' => function($model){
                                return $model->currentStatus != 'Draft' || $model->currentStatus != 'For further validation' ? $model->submitted ? $model->submitted->actor.'<br>'.$model->submitted->actorPosition : '' : '';
                            }
                        ],
                        [
                            'attribute' => 'date_submitted',
                            'header' => 'Date Submitted',
                            'headerOptions' => [
                                'style' => 'width: 13%; background-color: #002060; color: white; font-weight: normal;'
                            ],
                            'format' => 'raw',
                            'value' => function($model){
                                return $model->currentStatus != 'Draft' || $model->currentStatus != 'For further validation' ? $model->submitted ? date("F j, Y H:i:s", strtotime($model->submitted->datetime)) : '' : '';
                            }
                        ],
                        [
                            'attribute' => 'acknowledged_by',
                            'header' => 'Acknowledged By',
                            'headerOptions' => [
                                'style' => 'width: 13%; background-color: #002060; color: white; font-weight: normal;'
                            ],
                            'format' => 'raw',
                            'value' => function($model){
                                return $model->currentStatus != 'Draft' || $model->currentStatus != 'For further validation' ? $model->acknowledged ? $model->acknowledged->actor.'<br>'.$model->acknowledged->actorPosition : '' : '';
                            }
                        ],
                        [
                            'attribute' => 'date_acknowledged',
                            'header' => 'Date Acknowledged',
                            'headerOptions' => [
                                'style' => 'width: 13%; background-color: #002060; color: white; font-weight: normal;'
                            ],
                            'format' => 'raw',
                            'value' => function($model){
                                return $model->currentStatus != 'Draft' || $model->currentStatus != 'For further validation' ? $model->acknowledged ? date("F j, Y H:i:s", strtotime($model->acknowledged->datetime)) : '' : '';
                            }
                        ],
                        [
                            'attribute' => 'remarks',
                            'header' => 'NEDA Remarks',
                            'headerOptions' => [
                                'style' => 'width: 20%; background-color: #002060; color: white; font-weight: normal;'
                            ],
                            'format' => 'raw',
                            'value' => function($model){
                                return $model->currentStatus == 'For further validation' ? $model->currentSubmissionLog->remarks : '';
                            }
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => 'Actions',
                            'headerOptions' => [
                                'style' => 'width: 5%; background-color: #002060; color: white; font-weight: normal;'
                            ],
                            'template' => '<center>{view} {update} {delete}</center>',
                            'buttons' => [
                                'update' => function($url, $model, $key){
                                    $modalID = $model->id;
                                    return Yii::$app->user->can('Administrator') ? 
                                                $model->currentStatus != 'Draft' || $model->currentStatus != 'For further validation' ?
                                                    count($model->plans) < 1 ? 
                                                        Html::a('<i class="fa fa-pencil"></i>', '#', [
                                                            'class' => 'update-button',
                                                            'data-toggle' => 'modal',
                                                            'data-target' => '#update-modal-'.$modalID,
                                                            'data-url' => Url::to(['update', 'id' => $model->id]),
                                                        ]) :
                                                    '' :
                                                '' :
                                            '';
                                },
                                'delete' => function($url, $model, $key){
                                    return Yii::$app->user->can('Administrator') ? 
                                                $model->currentStatus != 'Draft' || $model->currentStatus != 'For further validation' ?
                                                    count($model->plans) < 1 ?
                                                        Html::a('<i class="fa fa-trash"></i>', ['delete', 'id' => $model->id], [
                                                            'data' => [
                                                                'confirm' => 'Are you sure want to delete this item?',
                                                                'method' => 'post',
                                                            ],
                                                        ]) :
                                                    '' :
                                                '' :
                                            '';
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
                            'attribute' => 'year',
                            'header' => 'Year',
                            'headerOptions' => [
                                'style' => 'width: 10%; background-color: #002060; color: white; font-weight: normal;'
                            ],
                        ],
                        [
                            'attribute' => 'status',
                            'header' => 'Status',
                            'headerOptions' => [
                                'style' => 'width: 10%; background-color: #002060; color: white; font-weight: normal;'
                            ],
                            'format' => 'raw',
                            'value' => function($model){
                                return $model->currentStatus;
                            }
                        ],
                        [
                            'attribute' => 'submitted_by',
                            'header' => 'Submitted By',
                            'headerOptions' => [
                                'style' => 'width: 15%; background-color: #002060; color: white; font-weight: normal;'
                            ],
                            'format' => 'raw',
                            'value' => function($model){
                                return $model->currentStatus != 'Draft' || $model->currentStatus != 'For further validation' ? $model->submitted ? $model->submitted->actor.'<br>'.$model->submitted->actorPosition : '' : '';
                            }
                        ],
                        [
                            'attribute' => 'date_submitted',
                            'header' => 'Date Submitted',
                            'headerOptions' => [
                                'style' => 'width: 13%; background-color: #002060; color: white; font-weight: normal;'
                            ],
                            'format' => 'raw',
                            'value' => function($model){
                                return $model->currentStatus != 'Draft' || $model->currentStatus != 'For further validation' ? $model->submitted ? date("F j, Y H:i:s", strtotime($model->submitted->datetime)) : '' : '';
                            }
                        ],
                        [
                            'attribute' => 'acknowledged_by',
                            'header' => 'Acknowledged By',
                            'headerOptions' => [
                                'style' => 'width: 13%; background-color: #002060; color: white; font-weight: normal;'
                            ],
                            'format' => 'raw',
                            'value' => function($model){
                                return $model->currentStatus != 'Draft' || $model->currentStatus != 'For further validation' ? $model->acknowledged ? $model->acknowledged->actor.'<br>'.$model->acknowledged->actorPosition : '' : '';
                            }
                        ],
                        [
                            'attribute' => 'date_acknowledged',
                            'header' => 'Date Acknowledged',
                            'headerOptions' => [
                                'style' => 'width: 13%; background-color: #002060; color: white; font-weight: normal;'
                            ],
                            'format' => 'raw',
                            'value' => function($model){
                                return $model->currentStatus != 'Draft' || $model->currentStatus != 'For further validation' ? $model->acknowledged ? date("F j, Y H:i:s", strtotime($model->acknowledged->datetime)) : '' : '';
                            }
                        ],
                        [
                            'attribute' => 'remarks',
                            'header' => 'NEDA Remarks',
                            'headerOptions' => [
                                'style' => 'width: 20%; background-color: #002060; color: white; font-weight: normal;'
                            ],
                            'format' => 'raw',
                            'value' => function($model){
                                return $model->currentStatus == 'For further validation' ? $model->currentSubmissionLog->remarks : '';
                            }
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => 'Actions',
                            'headerOptions' => [
                                'style' => 'width: 5%; background-color: #002060; color: white; font-weight: normal;'
                            ],
                            'template' => '<center>{view} {update} {delete}</center>',
                            'buttons' => [
                                'update' => function($url, $model, $key){
                                    $modalID = $model->id;
                                    return Yii::$app->user->can('Administrator') ? 
                                                $model->currentStatus != 'Draft' || $model->currentStatus != 'For further validation' ?
                                                    count($model->plans) < 1 ? 
                                                        Html::a('<i class="fa fa-pencil"></i>', '#', [
                                                            'class' => 'update-button',
                                                            'data-toggle' => 'modal',
                                                            'data-target' => '#update-modal-'.$modalID,
                                                            'data-url' => Url::to(['update', 'id' => $model->id]),
                                                        ]) :
                                                    '' :
                                                '' :
                                            '';
                                },
                                'delete' => function($url, $model, $key){
                                    return Yii::$app->user->can('Administrator') ? 
                                                $model->currentStatus != 'Draft' || $model->currentStatus != 'For further validation' ?
                                                    count($model->plans) < 1 ?
                                                        Html::a('<i class="fa fa-trash"></i>', ['delete', 'id' => $model->id], [
                                                            'data' => [
                                                                'confirm' => 'Are you sure want to delete this item?',
                                                                'method' => 'post',
                                                            ],
                                                        ]) :
                                                    '' :
                                                '' :
                                            '';
                                },
                            ],
                        ],
                    ]
                ]); ?>

        </div>
    </div>
</div>
<?php
$this->registerJs('
    $(".update-button").click(function(e){
        e.preventDefault();

        var modalId = $(this).data("target");
        $(modalId).modal("show").find(modalId + "-content").load($(this).data("url"));
        
        return false;
    });
');
?>

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