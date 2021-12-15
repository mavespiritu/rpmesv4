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

<div class="nep-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
        'id' => 'gaa-form',
        'enableAjaxValidation' => true,
    ]); ?>

    <?= $form->field($model, 'year')->textInput(['type' => 'number' , 'min' => date("Y") - 1, 'autocomplete' => 'off']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'name' => 'Submit', 'value' => 'Submit', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
