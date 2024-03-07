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
/* @var $searchModel common\modules\rpmes\models\TrainingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'RPMES Form 9: Trainings/Workshops conducted/facilitated/attended by the RPMC';
$this->params['breadcrumbs'][] = $this->title;

$successMessage = \Yii::$app->getSession()->getFlash('success');
?>
<div class="training-index">
    <div class="flash-success" style="display: none;">
        <?= $successMessage ?>
    </div>
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">List of Trainings/Workshops Conducted/Facilitated/Attended</h3>
        </div>
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
                        'attribute' => 'title',
                        'header' => 'Title of Training/Workshop',
                        'headerOptions' => [
                            'style' => 'width: 15%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'attribute' => 'objective',
                        'header' => 'Objective of Training/Workshop',
                        'headerOptions' => [
                            'style' => 'width: 20%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'attribute' => 'start_date',
                        'header' => 'Date',
                        'headerOptions' => [
                            'style' => 'width: 15%; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'format' => 'raw',
                        'value' => function ($model){
                            return strtotime($model->start_date) == strtotime($model->end_date) ? date("F j, Y", strtotime($model->start_date)) : date("F j, Y", strtotime($model->start_date)).' to '.date("F j, Y", strtotime($model->end_date)); 
                        }
                    ],
                    [
                        'attribute' => 'action',
                        'header' => 'Conducted/<br>Facilitated/<br>Attended',
                        'headerOptions' => [
                            'style' => 'width: 5%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'attribute' => 'office',
                        'header' => 'Lead Office/Unit',
                        'headerOptions' => [
                            'style' => 'width: 10%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'attribute' => 'organization',
                        'header' => 'Participating Offices/Agencies/Organizations',
                        'headerOptions' => [
                            'style' => 'width: 10%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'header' => 'Total No. of Participants',
                        'headerOptions' => [
                            'style' => 'width: 5%; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'format' => 'raw',
                        'value' => function ($model){
                            return number_format($model->totalParticipants, 0); 
                        }
                    ],
                    [
                        'attribute' => 'feedback',
                        'header' => 'Results and Feedback',
                        'headerOptions' => [
                            'style' => 'width: 10%; background-color: #002060; color: white; font-weight: normal;'
                        ]
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
                ]
            ]); 
            ?>
        </div>
    </div>
</div>
<?php
    $script = '
        function printFormNineReport(year,quarter,title,objective,office,organization,startDate,end_date,maleParticipant,femaleParticipant)
        {
            var printWindow = window.open(
                "'.Url::to(['/rpmes/training/print-form-nine']).'?year=" + year + "&quarter=" + quarter + "&title=" + title + "&objective=" + objective + "&office=" + office + "&organization=" + organization + "&startDate=" + startDate + "&endDate=" + end_date + "&maleParticipant=" + maleParticipant + "&femaleParticipant=" + femaleParticipant,
                "Print",
                "left=200", 
                "top=200", 
                "width=650", 
                "height=500", 
                "toolbar=0", 
                "resizable=0"
                );
                printWindow.addEventListener("load", function() {
                    printWindow.print();
                    setTimeout(function() {
                    printWindow.close();
                }, 1);
                }, true);
        }
    ';

    $this->registerJs($script, View::POS_END);