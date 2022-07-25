<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectProblem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-problem-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <?= $form->field($model, 'project_id')->textInput() ?>

    <?= $form->field($model, 'nature')->dropDownList([ 1 => '1', 2 => '2', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'detail')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'strategy')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'responsible_entity')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'lesson_learned')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'submitted_by')->textInput() ?>

    <?= $form->field($model, 'date_submitted')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
