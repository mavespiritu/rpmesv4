<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ppmp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gaa-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
        'id' => 'nep-form',
        'enableAjaxValidation' => true,
    ]); ?>

<?= $form->field($model, 'year')->textInput(['type' => 'number' , 'min' => date("Y"), 'autocomplete' => 'off'])->label('New GAA Year') ?>

    <hr style="opacity: 0.3">

    <?= $form->field($model, 'copy')->widget(Select2::classname(), [
        'data' => $neps,
        'options' => ['placeholder' => 'Select GAA','multiple' => false, 'class'=>'copy-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ]);
    ?>

<?= $form->field($model, 'data')->widget(Select2::classname(), [
        'data' => ['' => 'Select One', '1' => 'Structure Only (Programs and Objects)', '2' => 'Structure and Content (Programs, Objects, and Amount)'],
        'options' => ['placeholder' => 'Select One','multiple' => false, 'class'=>'data-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'name' => 'Copy', 'value' => 'Copy', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
