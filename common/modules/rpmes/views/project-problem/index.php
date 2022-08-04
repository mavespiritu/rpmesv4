<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;
use yii\bootstrap\ButtonDropdown;
use yii\widgets\LinkPager;
use common\components\helpers\HtmlHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\ProjectProblemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project Problems';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-problem-index">
    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Add New', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Project Problems/Issues</h3>
                </div>
                <div class="box-body">
                    <?= $this->render('_search', [
                        'searchModel' => $searchModel,
                        'years' => $years,
                        'agencies' =>$agencies
                    ]) ?>
                </div>
                <hr>
                <div class="pull-left">
                    <?= !Yii::$app->user->can('AgencyUser') ? ButtonDropdown::widget([
                        'label' => '<i class="fa fa-download"></i> Export',
                        'encodeLabel' => false,
                        'options' => ['class' => 'btn btn-success btn-sm'],
                        'dropdown' => [
                            'items' => [
                                ['label' => 'Excel', 'encodeLabel' => false, 'url' => Url::to(['/rpmes/project-problem/download-form-eleven', 'type' => 'excel', 'year' => $searchModel->year == null ? '2022' : $searchModel->year, 'quarter' => $searchModel->quarter == null ? '' : $searchModel->quarter, 'model' => json_encode($searchModel)])],
                                ['label' => 'PDF', 'encodeLabel' => false, 'url' => Url::to(['/rpmes/project-problem/download-form-eleven', 'type' => 'pdf', 'year' => $searchModel->year == null ? '2022' : $searchModel->year, 'quarter' => $searchModel->quarter == null ? '' : $searchModel->quarter, 'model' => json_encode($searchModel)])],
                            ],
                        ],
                    ]) : '' ?>
                        <?= Html::button('<i class="fa fa-print"></i> Print', ['onClick' => 'printFormElevenReport("'.$searchModel->year.'", "'.$searchModel->quarter.'")', 'class' => 'btn btn-danger btn-sm']) ?>
                </div>
                <div class="clearfix"></div>
                    <!-- <h5 class="text-center">REGIONAL PROJECT MONITORING AND EVALUATION SYSTEM (RPMES) <br>
                                                RPMES Form 11: LIST OF PROJECT PROBLEMS/ISSUES
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
                            'nature',
                            'detail:ntext',
                            'strategy:ntext',
                            'responsible_entity',
                            'lesson_learned',
                            'sectorTitle',
                            'subSectorTitle',
                            'projectBarangays',
                            'projectCitymuns',
                            'projectProvinces',
                            'projectRegions',
                            'agency',
                            [
                                'label' => 'Total Project Cost',
                                'value' => function ($model) {
                                return $model->getAllocationTotal();
                                }
                            ],
                            ['class' => 'yii\grid\ActionColumn', 'template' => '{update}{delete}'],
                        ],
                        ]); ?>
            </div>
        </div>
    </div>
</div>
<?php
    $script = '
        function printFormElevenReport(year,quarter)
        {
            var printWindow = window.open(
                "'.Url::to(['/rpmes/project-problem/print-form-eleven']).'?year=" + year + "&quarter=" + quarter, 
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
