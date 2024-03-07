<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\components\helpers\HtmlHelper;
use yii\bootstrap\ButtonDropdown;
use yii\web\View;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\ResolutionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'RPMES Form 10: RPMC and RDC Resolutions Related to Implementation of RPMES';
$this->params['breadcrumbs'][] = $this->title;

$successMessage = \Yii::$app->getSession()->getFlash('success');
?>
<div class="resolution-index">
    <div class="flash-success" style="display: none;">
        <?= $successMessage ?>
    </div>
    <div class="box box-solid">
        <div class="box-header with-border"><h3 class="box-title">List of RPMC and RDC Resolutions</h3></div>
        <div class="box-body">
            <?= $this->render('_search', ['model' => $searchModel]) ?>

            <?= GridView::widget([
                'options' => [
                    'class' => 'table-responsive',
                ],
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => [
                            'style' => 'background-color: #002060; color: white; font-weight: normal;'
                        ],
                    ],
                    [
                        'attribute' => 'year',
                        'header' => 'Year',
                        'headerOptions' => [
                            'style' => 'width: 5%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'attribute' => 'resolution_number',
                        'header' => 'Resolution No.',
                        'headerOptions' => [
                            'style' => 'width: 10%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'attribute' => 'resolution_title',
                        'header' => 'Resolution Title',
                        'headerOptions' => [
                            'style' => 'width: 15%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'attribute' => 'date_approved',
                        'header' => 'Date Approved',
                        'headerOptions' => [
                            'style' => 'width: 10%; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'format' => 'raw',
                        'value' => function ($model){
                            return date("F j, Y", strtotime($model->date_approved)); 
                        }
                    ],
                    [
                        'attribute' => 'resolution',
                        'header' => 'Resolution (Specific actions done by the RPMC, or additional information if the title does not sufficiently describe the resolution)',
                        'headerOptions' => [
                            'style' => 'background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'attribute' => 'resolution_url',
                        'header' => 'Link to the Resolution',
                        'headerOptions' => [
                            'style' => 'width: 10%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'header' => 'Scanned Copy',
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