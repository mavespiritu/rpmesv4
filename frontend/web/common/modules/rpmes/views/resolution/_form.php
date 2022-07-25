<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Resolution */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="resolution-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <?= $form->field($model, 'resolution_number')->textInput() ?>

    <?= $form->field($model, 'resolution')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'date_approved')->textInput() ?>

    <?= $form->field($model, 'rpmc_action')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'scanned_file')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
