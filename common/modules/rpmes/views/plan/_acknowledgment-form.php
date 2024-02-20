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


<p class="text-center">
    <b>CORRESPONDENCE ACKNOWLEDGMENT</b> <br>
    <b>ONLINE REGIONAL PROJECT MONITORING AND EVALUATION SYSTEM</b> <br>
    <b><?= $officeTitle ? $officeTitle->value : '' ?></b> <br>
    <?= $officeAddress ? $officeAddress->value : '' ?>
</p>
<table class="table table-bordered table-condensed table-responsive">
    <tbody>
        <tr>
            <td style="width: 50%;">
                <b>For/To: <?= $agency->salutation ?> <?= $agency->head ?></b> <br>
                <p style="margin-left: 54px;">
                    <?= $agency->head_designation ?> <br>
                    <?= $agency->title ?> <br>
                    <?= $agency->address ?>
                </p>
            </td>
            <td style="width: 50%;">
                <p>
                    <b>Control No:</b> <?= $model->control_no ?> <br>
                    <b>Date & Time Received:</b> <?= date("F j, Y H:i:s", strtotime($submission->date_submitted))?><br>
                    <b>Report Submitted by:</b> <?= $submission->submitter ?> <br>
                    <span style="margin-left: 29%;">
                        <?= $submission->submitterPosition ?>
                    </span>
                </p>
            </td>
        </tr>
        <tr><td colspan=2><b>Subject: Submission of CY <?= $submission->year ?> Regional Project Monitoring and Evaluation System (RPMES) Form 1 (Initial Project Report)</b></td></tr>
        <tr>
            <td colspan=2>
                <b>Findings:</b>
                <?= $form->field($model, 'findings')->widget(CKEditor::className(), [
                    'options' => ['id' => 'monitoring-plan-findings-'.$agency->id.'-'.$submission->quarter, 'rows' => 6, 'style' => 'resize: none;', 'value' => $model->isNewRecord ? '1. '.$submission->projectCount.' project/s enrolled '.$submission->deadlineStatus : $model->findings],
                    'preset' => 'basic'
                ])->label(false) ?>
                <b>Action Taken:</b>
                <?= $form->field($model, 'action_taken')->widget(CKEditor::className(), [
                    'options' => ['id' => 'monitoring-plan-action_taken-'.$agency->id.'-'.$submission->quarter, 'rows' => 6, 'style' => 'resize: none;', 'value' => $model->isNewRecord ? 'For consideration in the preparation of the CY '.$submission->year.' Regional Project Monitoring and Evaluation System (RPMES) Project Monitoring Plan.' : $model->action_taken],
                    'preset' => 'basic'
                ])->label(false) ?>
            </td>
        </tr>
        <tr>
            <td>
                <b>CA Prepared by:</b> <br><br>
                <?= $model->isNewRecord ? Yii::$app->user->identity->userinfo->fullName : $model->acknowledger ?> <br>
                <?= $model->isNewRecord ? Yii::$app->user->identity->userinfo->POSITION_C : $model->acknowledgerPosition ?>
            </td>
            <td>
                <b>Division:</b> <br><br>
                Monitoring and Evaluation Division
            </td>
        </tr>
        <tr>
            <td>
                <br>
                <p style="text-align: center;">
                    <u><?= date("F j, Y") ?></u> <br>
                    <b>Date</b>
                </p>
            </td>
            <td>
                <br>
                <p style="text-align: center;">
                    <u><b><?= $officeHead ? $officeHead->value : 'No set agency head' ?></b></u> <br>
                    <?= $officeTitleShort ? $officeTitleShort->value : 'No set agency title short' ?> Regional Director
                </p>
            </td>
        </tr>
    </tbody>
</table>
<div class="pull-right">
    <?= Html::submitButton('Acknowledge Report', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
</div>
<div class="clearfix"></div>
<?php ActiveForm::end(); ?>
