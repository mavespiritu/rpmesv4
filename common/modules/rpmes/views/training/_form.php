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
        'layout' => 'horizontal',
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'wrapper' => 'col-sm-9',
            ],
        ],
    ]); ?>
    
    <?= $form->field($model, 'year')->textInput(['maxlength' => true, 'type' => 'number'])->label('Year') ?>

    <?= $form->field($model, 'title')->textarea(['rows' => 2]); ?>

    <?= $form->field($model, 'objective')->textarea(['rows' => 2]); ?>

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
    ]); ?>

    <?= $form->field($model, 'end_date')->widget(DatePicker::className(), [
        'type' => DatePicker::TYPE_COMPONENT_APPEND,
        'options' => ['placeholder' => 'Enter date', 'autocomplete' => 'off'],
        'pluginOptions' => [
            'autoclose'=>true,
            'format' => 'yyyy-mm-dd'
        ],
    ]); ?>

    <?= $form->field($model, 'action')->dropdownList(
        [
            'C' => 'Conducted',
            'F' => 'Facilitated',
            'A' => 'Attended',
        ]
    ); ?>

    <?= $form->field($model, 'office')->textarea(['rows' => 2]); ?>

    <?= $form->field($model, 'organization')->textarea(['rows' => 2]); ?>

    <?= $form->field($model, 'male_participant')->textInput(['maxlength' => true, 'type' => 'number']); ?>

    <?= $form->field($model, 'female_participant')->textInput(['maxlength' => true, 'type' => 'number']); ?>

    <?= $form->field($model, 'feedback')->textarea(['rows' => 2]); ?>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="pull-right">
                <?= Html::submitButton('Save Record', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<style>
    label.control-label{
        font-weight: bolder;
    }
    hr{
        opacity: 0.10;
    }
</style>