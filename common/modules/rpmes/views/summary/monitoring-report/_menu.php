<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;
use yii\bootstrap\ButtonDropdown;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\DueDateSearch */
/* @var $form yii\widgets\ActiveForm */

?>

<?= ButtonDropdown::widget([
        'label' => '<i class="fa fa-download"></i> Export',
        'encodeLabel' => false,
        'options' => ['class' => 'btn btn-success btn-sm'],
        'dropdown' => [
            'items' => [
                ['label' => 'Excel', 'encodeLabel' => false, 'url' => Url::to(['/rpmes/summary/download-monitoring-report', 'type' => 'excel', 'year' => $model->year, 'agency_id' => $model->agency_id, 'quarter' => $model->quarter, 'model' => json_encode($model)])],
                ['label' => 'PDF', 'encodeLabel' => false, 'url' => Url::to(['/rpmes/summary/download-monitoring-report', 'type' => 'pdf', 'year' => $model->year, 'agency_id' => $model->agency_id, 'quarter' => $model->quarter, 'model' => json_encode($model)])],
            ],
        ],
    ]); ?>
    <?= Html::button('<i class="fa fa-print"></i> Print', ['onClick' => 'printSummaryReport("'.$model->year.'","'.$model->quarter.'","'.$model->fund_source_id.'","'.$model->agency_id.'","'.$model->region_id.'","'.$model->province_id.'","'.$model->citymun_id.'","'.$model->sector_id.'","'.$model->grouping.'")', 'class' => 'btn btn-danger btn-sm']) ?>