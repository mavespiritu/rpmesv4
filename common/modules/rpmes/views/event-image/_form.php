<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\EventImage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-image-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>

    <?= $form->field($model, 'title')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'image')->fileInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
