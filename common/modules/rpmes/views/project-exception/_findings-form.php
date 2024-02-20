<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\web\View;
use yii\web\JsExpression;
use kartik\select2\Select2;
use dosamigos\datepicker\DatePicker;
use yii\bootstrap\Dropdown;
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Project */

\yii\web\YiiAsset::register($this);

$exceptionCount = $exceptionModel->isNewRecord ? 0 : $exceptionModel->id;

?>

<div class="row">
    <div class="col-md-6 col-xs-12">
        Project No: <br><b><?= $project->project_no.': '.$project->title ?></b>
    </div>
    <div class="col-md-6 col-xs-12">
        Slippage (%): <br><b><?= number_format($project->getSlippage($model->year)[$model->quarter], 2) ?></b>
    </div>
</div>
<br>
<?php $form = ActiveForm::begin([
    'id' => $action.'-findings-'.$project->id.'-'.$exceptionCount.'-form',
    'options' => ['class' => 'disable-submit-buttons'],
    'enableAjaxValidation' => true,
]); ?>

<?= $form->field($exceptionModel, 'findings')->widget(CKEditor::className(), [
    'options' => ['id' => $action.'-findings-'.$project->id.'-'.$exceptionCount],
    'preset' => 'basic'
]) ?>

<?= $form->field($exceptionModel, 'typology_id')->widget(Select2::classname(), [
    'data' => $typologies,
    'options' => ['multiple' => false, 'placeholder' => 'Select one', 'id' => $action.'-typology-'.$project->id.'-'.$exceptionCount.'-select', 'class'=>'typology-select'],
    'pluginOptions' => [
        'allowClear' =>  true,
    ],
]) ?>

<?= $form->field($exceptionModel, 'issue_status')->widget(Select2::classname(), [
    'data' => [
        'Current' => 'Current',
        'Resolved' => 'Resolved',
    ],
    'options' => ['multiple' => false, 'placeholder' => 'Select one', 'id' => $action.'-issue-status-'.$project->id.'-'.$exceptionCount.'-select', 'class'=>'issue-status-select'],
    'pluginOptions' => [
        'allowClear' =>  true,
    ],
]) ?>

<?= $form->field($exceptionModel, 'causes')->widget(CKEditor::className(), [
    'options' => ['id' => $action.'-causes-'.$project->id.'-'.$exceptionCount],
    'preset' => 'basic'
]) ?>

<?= $form->field($exceptionModel, 'action_taken')->widget(CKEditor::className(), [
    'options' => ['id' => $action.'-action_taken-'.$project->id.'-'.$exceptionCount],
    'preset' => 'basic'
]) ?>

<?= $form->field($exceptionModel, 'recommendations')->widget(CKEditor::className(), [
    'options' => ['id' => $action.'-recommendations-'.$project->id.'-'.$exceptionCount],
    'preset' => 'basic'
]) ?>

<div class="form-group pull-right">
    <?= Yii::$app->user->can('AgencyUser') ? 
        $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
            count($model->plans) < 1 ? 
                Html::submitButton('Save Findings', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) : 
            '' : 
        '' : 
    '' ?>
</div>
<div class="clearfix"></div>

<?php ActiveForm::end(); ?>
