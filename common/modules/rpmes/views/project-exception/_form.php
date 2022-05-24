<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveField;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use kartik\typeahead\Typeahead;
use yii\web\View;
use yii\widgets\MaskedInput;
use kartik\daterange\DateRangePicker;
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\date\DatePicker;
use \file\components\AttachmentsInput;
use yii\web\JsExpression;
use yii\widgets\LinkPager;
use common\components\helpers\HtmlHelper;
use dosamigos\switchery\Switchery;
use dosamigos\ckeditor\CKEditor;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */   
/* @var $form yii\widgets\ActiveForm */
$HtmlHelper = new HtmlHelper();
function renderSummary($page)
{
    $firstNumber = $page->offset + 1;
    $lastNumber = $page->pageCount - 1 == $page->page ? $page->totalCount : ($page->page + 1) * $page->limit;
    $total = $page->totalCount;
    return 'Showing <b>'.$firstNumber.'-'.$lastNumber.'</b> of <b>'.$total.'</b> items.';
}
?>
    <?php $form = ActiveForm::begin([
        'options' => ['id' => 'project-exception-form', 'class' => 'disable-submit-buttons'],
    ]); ?>
        <div class="alert alert-<?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? 'info' : 'danger' : '' ?>"><i class="fa fa-exclamation-circle"></i> <?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? $HtmlHelper->time_elapsed_string($dueDate->due_date).' to go before the deadline of submission of '.$getData['quarter'].' Project Exceptions. Due date is '.date("F j, Y", strtotime($dueDate->due_date)) 
                : 'Submission of '.$getData['quarter'].' Project Exception has ended '.$HtmlHelper->time_elapsed_string($dueDate->due_date).' ago. Due date is '.date("F j, Y", strtotime($dueDate->due_date)) : 'No due date set' ?></div>
        <div class="summary"><?= renderSummary($projectsPages) ?></div>
        <div class="project-exception-form project-exception-table">
            <table class="table table-condensed table-responsive table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <td style="width: 2%">#</td>
                        <td style="width: 20%;">
                            <b>
                            (a) Project ID <br>
                            (b) Name of Project <br>
                            (c) Project Schedule <br>
                            (d) Location <br>
                            (e) Funding Source
                            </b>
                        </td>
                        <td align=center style="width: 10%;"><b>Implementation Status</b></td>
                        <td align=center style="width: 8%;"><b>% Slippage</b></td>
                        <td align=center style="width: 20%;"><b>Possible Reasons/Causes</b></td>
                        <td align=center style="width: 20%;"><b>Recommendations</b></td>
                    </tr>
                </thead>
                <tbody>
                <?php if($getData['status'] != ''){ ?>
                    <?php if($projectsModels){ ?>
                        <?php $i = 1; ?>
                        <?php foreach($projectsModels as $model){ ?>
                            <?php if($model->getImplementationStatus($getData['quarter']) == $getData['status']){ ?>
                            <tr>
                                <td align=center><?= $i ?></td>
                                <td>
                                    (a) <?= $model->project_no ?> <br>
                                    (b) <?= $model->title ?> <br>
                                    (c) <?= $model->startDate ?> to <?= $model->completionDate ?> <br>
                                    (d) <?= $model->location ?> <br>
                                    (e) <?= $model->fundSourceTitle ?> <br>
                                </td>
                                <td align=center><?= $model->getImplementationStatus($getData['quarter']) ?></td>
                                <td align=center><?= number_format($model->getPhysicalSlippage($getData['quarter']), 2) ?>%</td>
                                <td align=center>
                                    <?= $form->field($exceptions[$model->id], "[$model->id]causes")->widget(CKEditor::className(), [
                                        'options' => ['rows' => 6, 'style' => 'resize: none;'],
                                        'preset' => 'basic'
                                    ])->label(false) ?>
                                </td>
                                <td align=center>
                                    <?= $form->field($exceptions[$model->id], "[$model->id]recommendations")->widget(CKEditor::className(), [
                                        'options' => ['rows' => 6, 'style' => 'resize: none;'],
                                        'preset' => 'basic'
                                    ])->label(false) ?>
                                </td>
                            </tr>
                            <?php $i++ ?>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                <?php }else{ ?>
                    <?php if($projectsModels){ ?>
                        <?php $i = 1; ?>
                        <?php foreach($projectsModels as $model){ ?>
                            <?php if(($model->getPhysicalSlippage($getData['quarter']) <= -15) || ($model->getPhysicalSlippage($getData['quarter']) >= 15)){ ?>
                            <tr>
                                <td align=center><?= $i ?></td>
                                <td>
                                    (a) <?= $model->project_no ?> <br>
                                    (b) <?= $model->title ?> <br>
                                    (c) <?= $model->startDate ?> to <?= $model->completionDate ?> <br>
                                    (d) <?= $model->location ?> <br>
                                    (e) <?= $model->fundSourceTitle ?> <br>
                                </td>
                                <td align=center><?= $model->getImplementationStatus($getData['quarter']) ?></td>
                                <td align=center><?= number_format($model->getPhysicalSlippage($getData['quarter']), 2) ?>%</td>
                                <td align=center>
                                    <?= $form->field($exceptions[$model->id], "[$model->id]causes")->widget(CKEditor::className(), [
                                        'options' => ['id' => 'project-eception-causes-'.$model->id, 'rows' => 6, 'style' => 'resize: none;'],
                                        'preset' => 'basic'
                                    ])->label(false) ?>
                                </td>
                                <td align=center>
                                    <?= $form->field($exceptions[$model->id], "[$model->id]recommendations")->widget(CKEditor::className(), [
                                        'options' => ['id' => 'project-eception-recommendations-'.$model->id, 'rows' => 6, 'style' => 'resize: none;'],
                                        'preset' => 'basic'
                                    ])->label(false) ?>
                                </td>
                            </tr>
                            <?php $i++ ?>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div>
            <div class="pull-right"><?= LinkPager::widget(['pagination' => $projectsPages]); ?></div>
            <div class="pull-left">
                <?= !empty($exceptions) ? $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? Html::submitButton('Save Project Exceptions', ['class' => 'btn btn-primary', 'style' => 'margin-top: 20px;', 'data' => ['disabled-text' => 'Please Wait']]) : '' : '' : '' ?>
            </div>
            <div class="clearfix"></div>
        </div>

    <?php ActiveForm::end(); ?>
    <hr>
    <h4>Project Exceptions Submission for <?= $getData['quarter'] ?> <?= $getData['year'] ?></h4>
    <p><i class="fa fa-exclamation-circle"></i> Make sure to complete project exceptions for <?= $getData['quarter'] ?> <?= $getData['year'] ?> before clicking the button below. Once submitted, action cannot be reverted.</p>
    <?= !empty($exceptions) ? $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? !$submissionModel->isNewRecord ? 'Project Exceptions for '.$submissionModel->quarter.' '.$submissionModel->year.' has been submitted by '.$submissionModel->submitter.' last '.date("F j, Y H:i:s", strtotime($submissionModel->date_submitted)) : Html::button('Submit Project Exception '.$getData['quarter'].' '.$getData['year'],['class' => 'btn btn-success', 'id' => 'project-exception-submit-button']) : '' : '' : '' ?>
    <?php
        $script = '
        $("#project-exception-submit-button").on("click", function(e) {
            e.preventDefault();

            var con = confirm("The data I encoded had been duly approved by my agency head. I am providing my name and designation in the appropriate fields as an attestation of my submission\'s data integrity. Proceed?");
            if(con == true)
            {
                var form = $("#project-exception-form");
                var formData = form.serialize();

                $.ajax({
                    type: "POST",
                    url: "'.Url::to(['/rpmes/project-exception/submit']).'",
                    data: {
                        year: "'.$getData['year'].'",
                        quarter: "'.$getData['quarter'].'",
                        agency_id: "'.$agency_id.'"
                    },
                    success: function (data) {
                        console.log(data);
                        $.growl.notice({ title: "Success!", message: "Project Exception has been submitted" });
                    },
                    error: function (err) {
                        console.log(err);
                    }
                }); 
            }

            return false;
        });
        ';

        $this->registerJs($script, View::POS_END);
    ?>