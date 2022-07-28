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
                ['label' => 'Excel', 'encodeLabel' => false, 'url' => Url::to(['/rpmes/summary/download-summary-accomplishment', 'type' => 'excel', 'model' => json_encode($model), 'year' => $model->year,'quarter' => $model->quarter, 'agency_id' => $model->agency_id])],
                ['label' => 'PDF', 'encodeLabel' => false, 'url' => Url::to(['/rpmes/summary/download-summary-accomplishment', 'type' => 'pdf', 'model' => json_encode($model), 'year' => $model->year, 'quarter' => $model->quarter, 'agency_id' => $model->agency_id])],
            ],
        ],
    ]); ?>
<?= Html::button('<i class="fa fa-print"></i> Print', ['onClick' => 'printSummary("'.str_replace('"', '\'', json_encode($model)).'","'.$model->year.'","'.$model->quarter.'","'.$model->agency_id.'")', 'class' => 'btn btn-danger btn-sm']) ?>