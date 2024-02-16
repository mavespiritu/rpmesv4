<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\DueDateSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="due-date-search">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <?= $form->field($model, 'year')->widget(Select2::classname(), [
            'data' => $years,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'year-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
            'pluginEvents'=>[
                'select2:select'=>'
                    function(){
                        loadMonitoringPlanDueDate(this.value);
                        loadAccomplishmentDueDate(this.value,"Q1");
                        loadAccomplishmentDueDate(this.value,"Q2");
                        loadAccomplishmentDueDate(this.value,"Q3");
                        loadAccomplishmentDueDate(this.value,"Q4");
                        loadProjectExceptionDueDate(this.value,"Q1");
                        loadProjectExceptionDueDate(this.value,"Q2");
                        loadProjectExceptionDueDate(this.value,"Q3");
                        loadProjectExceptionDueDate(this.value,"Q4");
                    }'

            ]
        ])->label('Select year');
    ?>

    <?php ActiveForm::end(); ?>

</div>
