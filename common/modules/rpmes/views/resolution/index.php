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

$this->title = 'Resolutions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resolution-index">
    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Add New', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Resolutions</h3>
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
                                ['label' => 'Excel', 'encodeLabel' => false, 'url' => Url::to(['/rpmes/resolution/download-form-ten', 'type' => 'excel', 'year' => $searchModel->year == null ? '' : $searchModel->year, 'quarter' => $searchModel->quarter == null ? '' : $searchModel->quarter, 'model' => json_encode($searchModel)])],
                                ['label' => 'PDF', 'encodeLabel' => false, 'url' => Url::to(['/rpmes/resolution/download-form-ten', 'type' => 'pdf', 'year' => $searchModel->year == null ? '' : $searchModel->year, 'quarter' => $searchModel->quarter == null ? '' : $searchModel->quarter, 'model' => json_encode($searchModel)])],
                            ],
                        ],
                    ]) : '' ?>
                    <?= Html::button('<i class="fa fa-print"></i> Print', ['onClick' => 'printFormTenReport("'.$searchModel->year.'", "'.$searchModel->quarter.'", "'.$searchModel->resolution_number.'", "'.$searchModel->resolution.'", "'.$searchModel->date_approved.'", "'.$searchModel->rpmc_action.'")', 'class' => 'btn btn-danger btn-sm']) ?>
                </div>
                <div class="clearfix"></div><br>
                    <!-- <h5 class="text-center">REGIONAL PROJECT MONITORING AND EVALUATION SYSTEM (RPMES) <br>
                                                    RPMES Form 10: LIST OF RESOLUTIONS PASSED
                    </h5> -->
                    <?= GridView::widget([
                        'options' => [
                            'class' => 'table-responsive',
                        ],
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                            //'id',
                            'resolution_number',
                            'resolution:ntext',
                            'date_approved',
                            'rpmc_action:ntext',
                        [
                            'label' => 'Attached Scanned File of the Resolution',
                            'format' => 'raw',
                            'value' => function ($model) {
                                    $string = '';
                            if ($model->files){
                                    foreach($model->files as $file){ 
                                        $string .= Html::a($file->name.'.'.$file->type, ['/file/file/download', 'id' => $file->id]).'<br>';
                                    }
                            }
                            return $string;
                            }
                        ],
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
        function printFormTenReport(year,quarter,resolutionNumber,resolution,dateApproved,rpmcAction)
        {
            var printWindow = window.open(
                "'.Url::to(['/rpmes/resolution/print-form-ten']).'?year=" + year + "&quarter=" + quarter+ "&resolutionNumber=" + resolutionNumber + "&resolution=" + resolution + "&dateApproved=" + dateApproved + "&rpmcAction=" + rpmcAction, 
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