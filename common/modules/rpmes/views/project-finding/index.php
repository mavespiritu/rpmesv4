<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\ProjectFindingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Form 7: Project Inspection Report';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-finding-index">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Project Inspection Report Form</h3>
                </div>
                <div class="box-body">
                    <?= $this->render('_search', [
                        'searchModel' => $searchModel,
                        'years' => $years,
                    ]) ?>
                </div>
                <hr>
                <div class="box-body">
                    <div class="pull-left"><?= Html::a('<i class="fa fa-plus"></i> Add New', ['create'], ['class' => 'btn btn-success']) ?></div>
                    <div class="pull-right">
                            <?= !Yii::$app->user->can('AgencyUser') ? ButtonDropdown::widget([
                            'label' => '<i class="fa fa-download"></i> Export',
                            'encodeLabel' => false,
                            'options' => ['class' => 'btn btn-success btn-sm'],
                            'dropdown' => [
                                'items' => [
                                    ['label' => 'Excel', 'encodeLabel' => false, 'url' => Url::to(['/rpmes/project-finding/download-form-seven', 'type' => 'excel', 'year' => $searchModel->year == null ? '' : $searchModel->year, 'quarter' => $searchModel->quarter == null ? '' : $searchModel->quarter, 'model' => json_encode($searchModel)])],
                                    ['label' => 'PDF', 'encodeLabel' => false, 'url' => Url::to(['/rpmes/project-finding/download-form-seven', 'type' => 'pdf', 'year' => $searchModel->year == null ? '' : $searchModel->year, 'quarter' => $searchModel->quarter == null ? '' : $searchModel->quarter, 'model' => json_encode($searchModel)])],
                                ],
                        ],
                        ]) : '' ?>
                        <?= Html::button('<i class="fa fa-print"></i> Print', ['onClick' => 'printFormSevenReport("'.$searchModel->year.'", "'.$searchModel->quarter.'")', 'class' => 'btn btn-danger btn-sm']) ?>
                    </div>
                    <div class="clearfix"></div>
                        <!-- <h5 class="text-center">REGIONAL PROJECT MONITORING AND EVALUATION SYSTEM (RPMES) <br>
                                                    RPMES Form 7: LIST OF PROJECT MMAJOR FINDINGS
                        </h5> --><br>
                        <?= GridView::widget([
                            'options' => [
                            'class' => 'table-responsive',
                            ],
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                                    'projectTitle',
                                    'sectorTitle',
                                    'agency',
                                    'projectRegions',
                                    'projectProvinces',
                                    'projectCitymuns',
                                    'projectBarangays',
                                    'inspection_date',
                                    'major_finding',
                                    'issues',
                                    'action',

                                ['class' => 'yii\grid\ActionColumn', 'template' => '{update}{delete}'],
                            ],
                        ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $script = '
        function printFormSevenReport(year,quarter)
        {
            var printWindow = window.open(
                "'.Url::to(['/rpmes/project-finding/print-form-seven']).'?year=" + year + "&quarter=" + quarter, 
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