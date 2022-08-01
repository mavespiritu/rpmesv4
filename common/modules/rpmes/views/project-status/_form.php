<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Project */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <?= $form->field($model, 'source_id')->textInput() ?>

    <?= $form->field($model, 'project_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'year')->textInput() ?>

    <?= $form->field($model, 'agency_id')->textInput() ?>

    <?= $form->field($model, 'program_id')->textInput() ?>

    <?= $form->field($model, 'title')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'sector_id')->textInput() ?>

    <?= $form->field($model, 'sub_sector_id')->textInput() ?>

    <?= $form->field($model, 'location_scope_id')->textInput() ?>

    <?= $form->field($model, 'mode_of_implementation_id')->textInput() ?>

    <?= $form->field($model, 'other_mode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fund_source_id')->textInput() ?>

    <?= $form->field($model, 'typhoon')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'data_type')->dropDownList([ 'Default' => 'Default', 'Cumulative' => 'Cumulative', 'Maintained' => 'Maintained', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'period')->dropDownList([ 'Current Year' => 'Current Year', 'Carry-Over' => 'Carry-Over', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'start_date')->textInput() ?>

    <?= $form->field($model, 'completion_date')->textInput() ?>

    <?= $form->field($model, 'submitted_by')->textInput() ?>

    <?= $form->field($model, 'date_submitted')->textInput() ?>

    <?= $form->field($model, 'draft')->dropDownList([ 'Yes' => 'Yes', 'No' => 'No', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'complete')->dropDownList([ 'Yes' => 'Yes', 'No' => 'No', ], ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
