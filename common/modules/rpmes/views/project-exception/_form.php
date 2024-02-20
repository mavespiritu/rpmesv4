<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Url;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ris */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-exception-form">
    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
        'id' => 'project-exception-form',
        'enableAjaxValidation' => true,
    ]); ?>

    <?= $form->field($model, 'year')->textInput(['type' => 'number', 'min' => date("Y") - 1])->label('Year') ?>

    <?= $form->field($model, 'quarter')->widget(Select2::classname(), [
            'data' => $quarters,
            'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'quarter-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
        ])->label('Quarter') ?>

    <?= Yii::$app->user->can('Administrator') ? $form->field($model, 'agency_id')->widget(Select2::classname(), [
            'data' => $agencies,
            'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'agency-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
        ])->label('Agency') : ''
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save Project Exception Report', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
