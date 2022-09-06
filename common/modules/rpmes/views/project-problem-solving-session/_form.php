<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectProblemSolvingSession */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-problem-solving-session-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <?= $form->field($model, 'year')->textInput() ?>

    <?= $form->field($model, 'quarter')->dropDownList([ 'Q1' => 'Q1', 'Q2' => 'Q2', 'Q3' => 'Q3', 'Q4' => 'Q4', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'project_id')->textInput() ?>

    <?= $form->field($model, 'pss_date')->textInput() ?>

    <?= $form->field($model, 'agreement_reached')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'next_step')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'submitted_by')->textInput() ?>

    <?= $form->field($model, 'submitted_date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
