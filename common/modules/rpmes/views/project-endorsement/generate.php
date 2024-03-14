<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Url;
use yii\web\View;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ris */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-endorsement-form">
    <?php $form = ActiveForm::begin([
        'id' => 'project-endorsement-generate-form',
    ]); ?>

    <?= $form->field($model, 'year')->widget(Select2::classname(), [
            'data' => $years,
            'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'year-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
        ])->label('Select year') ?>

    <?= $form->field($model, 'quarter')->widget(Select2::classname(), [
        'data' => [
            'Q1' => 'Q1',
            'Q2' => 'Q2',
            'Q3' => 'Q3',
            'Q4' => 'Q4',
        ],
        'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'quarter-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ])->label('Select quarter') ?>

    <div class="form-group pull-right">
        <?= Html::submitButton('<i class="fa fa-excel-o"></i> Export to Excel', ['class' => 'btn btn-success', 'id' => 'generate-excel-button']) ?>

        <?= Html::button('<i class="fa fa-print"></i> Print Report', ['onClick' => 'printSummary()', 'class' => 'btn btn-danger', 'id' => 'print-button']) ?>
    </div>

    <div class="clearfix"></div>

    <?php ActiveForm::end(); ?>

</div>

<?php
    $script = '
        function printSummary(id)
        {
            var printWindow = window.open(
                "'.Url::to(['/rpmes/project-endorsement/print']).'?year=" + $(".year-select").val() + "&quarter=" + $(".quarter-select").val(), 
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
?>