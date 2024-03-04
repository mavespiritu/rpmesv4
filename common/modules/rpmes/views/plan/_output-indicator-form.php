<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use dosamigos\ckeditor\CKEditor;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Acknowledgment */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'options' => ['class' => 'disable-submit-buttons'],
]); ?>

<p><i class="fa fa-exclamation-circle"></i> You are <?= $model->isNewRecord ? 'adding' : 'editing' ?> output indicator of Project No. <?= $plan->project->project_no.': '.$plan->project->title ?> in Monitoring Plan <?= $submission->year ?></p>
<hr>

<?= $form->field($model, 'indicator')->textInput(['maxlength' => true])->label('Title of Output Indicator') ?>

<div class="pull-right">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
</div>
<div class="clearfix"></div>

<?php ActiveForm::end(); ?>
