<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
/* @var $this yii\web\View */
/* @var $model common\modules\procurement\models\PrPr */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pr-pr-form">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <h4>Basic Information</h4>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <?= $form->field($model, 'dts_no')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $form->field($model, 'entity_name')->textInput() ?>
        </div>
        <div class="col-md-6 col-xs-12">
            <?= $form->field($model, 'date_requested')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Enter date', 'value' => date("Y-m-d"), 'disabled' => true],
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'startDate' => date("Y-m-d"),
                ],
            ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <?= $form->field($ppmpModel, 'description')->textarea(['rows' => 4]) ?>
        </div>
        <div class="col-md-12 col-xs-12">
            <?= $form->field($model, 'purpose')->textarea(['rows' => 4]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $form->field($model, 'requester')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'requester_designation')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6 col-xs-12">
            <?= $form->field($model, 'approver')->textInput(['maxlength' => true, 'value' => 'MARCELO NICOMEDES J. CASTILLO']) ?>

            <?= $form->field($model, 'approver_designation')->textInput(['maxlength' => true, 'value' => 'Regional Director']) ?>
        </div>
    </div>

    <h4>PPMP/SPPMP Information</h4>

    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $form->field($ppmpModel, 'source')->dropdownList(['' => 'SELECT ONE', 'PPMP' => 'PPMP', 'SPPMP' => 'SPPMP']) ?>

        </div>
        <div class="col-md-6 col-xs-12">
            <?= $form->field($ppmpModel, 'source_version')->textInput(['maxlength' => true, 'placeholder' => 'e.g. I, II, III, ....']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $form->field($ppmpModel, 'version_no')->textInput(['maxlength' => true, 'placeholder' => 'e.g. 0, 1, 2, ....']) ?>

        </div>
        <div class="col-md-6 col-xs-12">
            <?= $form->field($ppmpModel, 'item_no')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
