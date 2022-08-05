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

$this->title = 'Trainings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="training-index">
    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Add New', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Trainings</h3>
                </div>
                <div class="box-body">
                    <?= $this->render('_search', [
                            'searchModel' => $searchModel,
                            'years' => $years,
                    ]) ?>
                    <br><br>
                    <hr>
                <div class="pull-left">
                        <?= !Yii::$app->user->can('AgencyUser') ? ButtonDropdown::widget([
                        'label' => '<i class="fa fa-download"></i> Export',
                        'encodeLabel' => false,
                        'options' => ['class' => 'btn btn-success btn-sm'],
                        'dropdown' => [
                            'items' => [
                                ['label' => 'Excel', 'encodeLabel' => false, 'url' => Url::to(['/rpmes/training/download-form-nine', 'type' => 'excel', 'year' => $searchModel->year == null ? '' : $searchModel->year, 'quarter' => $searchModel->quarter == null ? '' : $searchModel->quarter, 'title' => $searchModel->title == null ? '2022' : $searchModel->title, 'objective' => $searchModel->objective == null ? '' : $searchModel->objective, 'office' => $searchModel->office == null ? '' : $searchModel->office, 'organization' => $searchModel->organization == null ? '' : $searchModel->organization, 'startDate' => $searchModel->start_date == null ? '' : $searchModel->start_date, 'endDate' => $searchModel->end_date == null ? '' : $searchModel->end_date, 'maleParticipant' => $searchModel->male_participant == null ? '' : $searchModel->male_participant, 'femaleParticipant' => $searchModel->female_participant == null ? '' : $searchModel->female_participant, 'model' => json_encode($searchModel)])],
                                
                                ['label' => 'PDF', 'encodeLabel' => false, 'url' => Url::to(['/rpmes/training/download-form-nine', 'type' => 'pdf', 'year' => $searchModel->year == null ? '2022' : $searchModel->year, 'quarter' => $searchModel->quarter == null ? '' : $searchModel->quarter, 'title' => $searchModel->title == null ? '2022' : $searchModel->title, 'objective' => $searchModel->objective == null ? '' : $searchModel->objective, 'office' => $searchModel->office == null ? '' : $searchModel->office, 'organization' => $searchModel->organization == null ? '' : $searchModel->organization, 'startDate' => $searchModel->start_date == null ? '' : $searchModel->start_date, 'endDate' => $searchModel->end_date == null ? '' : $searchModel->end_date, 'maleParticipant' => $searchModel->male_participant == null ? '' : $searchModel->male_participant, 'femaleParticipant' => $searchModel->female_participant == null ? '' : $searchModel->female_participant, 'model' => json_encode($searchModel)])],
                            ],
                    ],
                    ]) : '' ?>
                    <?= Html::button('<i class="fa fa-print"></i> Print', ['onClick' => 'printFormNineReport("'.$searchModel->year.'", "'.$searchModel->quarter.'", "'.$searchModel->title.'", "'.$searchModel->objective.'", "'.$searchModel->office.'", "'.$searchModel->organization.'", "'.$searchModel->start_date.'", "'.$searchModel->end_date.'", "'.$searchModel->male_participant.'", "'.$searchModel->female_participant.'")', 'class' => 'btn btn-danger btn-sm']) ?>
                </div><br><br>
                    <!-- <h5 class="text-center">REGIONAL PROJECT MONITORING AND EVALUATION SYSTEM (RPMES) <br>
                                    RPMES Form 9: LIST OF TRAININGS AND WORKSHOPS
                    </h5> -->
                    <?= GridView::widget(['options' => ['class' => 'table-responsive',],
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'columns' => [
                                    ['class' => 'yii\grid\SerialColumn'],

                                                //'id',
                                                'title:ntext',
                                                'objective:ntext',
                                                'office:ntext',
                                                'organization:ntext',
                                                'start_date',
                                                'end_date',
                                                'male_participant',
                                                'female_participant',
                                                [
                                                'label' => 'Total Participants',
                                                'value' => function ($model) {
                                                return $model->getTotalParticipant();
                                                            }
                                                ],
                                                ['class' => 'yii\grid\ActionColumn', 'template' => '{update}{delete}'],
                                         ],
                                        ]); 
                    ?>
                </div>
            </div>
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