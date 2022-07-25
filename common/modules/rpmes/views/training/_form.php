<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveField;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use kartik\typeahead\Typeahead;
use yii\web\View;
use yii\widgets\MaskedInput;
use kartik\daterange\DateRangePicker;
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\date\DatePicker;
use \file\components\AttachmentsInput;
use yii\web\JsExpression;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Training */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="training-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <?= $form->field($model, 'title')->textarea(['rows' => 2])->label('Title of Training *'); ?>

    <?= $form->field($model, 'objective')->textarea(['rows' => 2])->label('Objective of Training *'); ?>

    <?= $form->field($model, 'office')->textarea(['rows' => 2])->label('Lead Office *'); ?>

    <?= $form->field($model, 'organization')->textInput()->label('Participating Offices/ Agencies/ Organizations *'); ?>

            <?= $form->field($model, 'start_date')->widget(DatePicker::className(), [
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'options' => ['placeholder' => 'Enter date', 'autocomplete' => 'off'],
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'yyyy-mm-dd'
                ],
                'pluginEvents' => [
                    'changeDate' => "function(e) {
                        const dateReceived = $('#training-start_date');
                        const dateActed = $('#training-end_date-kvdate');
                        dateActed.val('');
                        dateActed.kvDatepicker('update', '');
                        dateActed.kvDatepicker('setStartDate', dateReceived.val());
                    }",
                ]
            ])->label('Start Date *'); ?>

            <?= $form->field($model, 'end_date')->widget(DatePicker::className(), [
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'options' => ['placeholder' => 'Enter date', 'autocomplete' => 'off'],
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'yyyy-mm-dd'
                ],
            ])->label('Completion Date *'); ?>

    <?= $form->field($model, 'male_participant')->textInput(['type'=>'number'])->label('Male Participant *'); ?>

    <?= $form->field($model, 'female_participant')->textInput(['type'=>'number'])->label('Female Participant *'); ?>

    <?= $form->field($model, 'quarter')->dropDownList(['' => '', 'Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'])->label('Quarter *'); ?>

    <?= $form->field($model, 'year')->dropDownList(['' => '', $model->getYearsList()])->label('Year *'); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
