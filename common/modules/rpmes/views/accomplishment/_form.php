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
        'options' => ['id' => 'accomplishment-form', 'class' => 'disable-submit-buttons'],
    ]); ?>
        <div class="alert alert-<?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? 'info' : 'danger' : '' ?>"><i class="fa fa-exclamation-circle"></i> <?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? $HtmlHelper->time_elapsed_string($dueDate->due_date).' to go before the deadline of submission of '.$getData['quarter'].' Accomplishment. Due date is '.date("F j, Y", strtotime($dueDate->due_date)) 
                : 'Submission of '.$getData['quarter'].' Accomplishment has ended '.$HtmlHelper->time_elapsed_string($dueDate->due_date).' ago. Due date is '.date("F j, Y", strtotime($dueDate->due_date)) : 'No due date set' ?></div>
        <div class="summary"><?= renderSummary($projectsPages) ?></div>
        <div class="accomplishment-form accomplishment-table" style="height: 600px;">
            <table id="accomplishment-table" class="table table-bordered table-hover table-striped" cellspacing="0" style="min-width: 4000px;">
                <thead>
                    <tr>
                        <td rowspan=4 colspan=2 style="width: 10%;">
                            <b>
                            (a) Project ID <br>
                            (b) Name of Project <br>
                            (c) Project Schedule <br>
                            (d) Location <br>
                            (e) Funding Source
                            </b>
                        </td>
                        <td colspan=8 align=center><b>Financial Status (PhP)</b></td>
                        <td rowspan=3 align=center><b>Output Indicator</b></td>
                        <td colspan=4 align=center><b>Physical Status</b></td>
                        <td colspan=6 align=center><b>No. of Persons Employed</b></td>
                        <td colspan=7 align=center><b>No. of Beneficiaries</b></td>
                        <td rowspan=3 align=center><b>Remarks</b></td>
                        <td rowspan=3 align=center><b>Action</b></td>
                    </tr>
                    <tr>
                        <td colspan=2 align=center><b>Allocation</b></td>
                        <td colspan=2 align=center><b>Releases</b></td>
                        <td colspan=2 align=center><b>Obligation</b></td>
                        <td colspan=2 align=center><b>Disbursements</b></td>
                        <td rowspan=2 align=center><b>Target to Date</b></td>
                        <td rowspan=2 align=center><b>Target for the Qtr</b></td>
                        <td rowspan=2 align=center><b>Actual to Date</b></td>
                        <td rowspan=2 align=center><b>Actual for the Qtr</b></td>
                        <td colspan=3 align=center><b>Target</b></td>
                        <td colspan=3 align=center><b>Actual</b></td>
                        <td colspan=2 align=center><b>Target</b></td>
                        <td colspan=5 align=center><b>Actual</b></td>
                    </tr>
                    <tr>
                        <td align=center><b>As of Reporting Period</b></td>
                        <td align=center><b>For the Qtr</b></td>
                        <td align=center><b>As of Reporting Period</b></td>
                        <td align=center><b>For the Qtr</b></td>
                        <td align=center><b>As of Reporting Period</b></td>
                        <td align=center><b>For the Qtr</b></td>
                        <td align=center><b>As of Reporting Period</b></td>
                        <td align=center><b>For the Qtr</b></td>
                        <?php if(!empty($genders)){ ?>
                            <?php foreach($genders as $gender){ ?>
                                <td align=center><b><?= $gender ?></b></td>
                            <?php } ?>
                        <?php } ?>
                        <td align=center><b>Total</b></td>
                        <?php if(!empty($genders)){ ?>
                            <?php foreach($genders as $gender){ ?>
                                <td align=center><b><?= $gender ?></b></td>
                            <?php } ?>
                        <?php } ?>
                        <td align=center><b>Total</b></td>
                        <td align=center><b>Individual</b></td>
                        <td align=center><b>Group</b></td>
                        <?php if(!empty($genders)){ ?>
                            <?php foreach($genders as $gender){ ?>
                                <td align=center><b><?= $gender ?></b></td>
                            <?php } ?>
                        <?php } ?>
                        <td align=center><b>Total</b></td>
                        <td align=center><b>Group</b></td>
                        <td align=center><b>Total</b></td>
                    </tr>
                </thead>
                <tbody>
                <?php if(!empty($projectsModels)){ ?>
                    <?php if($projectsModels){ ?>
                        <?php $i = 1; ?>
                        <?php foreach($projectsModels as $model){ ?>
                            <?php $con = $model->isCompleted ? 'true' : 'false' ?>
                            <tr>
                                <td align=center><?= $i ?></td>
                                <td>
                                    (a) <?= $model->project_no ?> <br>
                                    (b) <?= $model->title ?> <br>
                                    (c) <?= $model->startDate ?> to <?= $model->completionDate ?> <br>
                                    (d) <?= $model->location ?> <br>
                                    (e) <?= $model->fundSourceTitle ?> <br>
                                </td>
                                <td align=right><?= number_format($model->getAllocationAsOfReportingPeriod($getData['quarter']), 2) ?></td>
                                <td align=right><b><?= number_format($model->getAllocationForQuarter($getData['quarter']), 2) ?></b></td>
                                <td align=right><?= number_format($model->getReleasesAsOfReportingPeriod($getData['quarter']), 2) ?></td>
                                <td align=center>
                                    <?= $form->field($financial[$model->id], "[$model->id]releases")->widget(MaskedInput::classname(), [
                                        'options' => [
                                            'autocomplete' => 'off',
                                            'value' => $financial[$model->id]['releases'] != '' ? $financial[$model->id]['releases'] : 0,
                                            'onKeyup' => 'updateAccomplishmentTable()',
                                            'disabled' => $model->isCompleted == true ? true : false
                                        ],
                                        'clientOptions' => [
                                            'alias' =>  'decimal',
                                            'removeMaskOnSubmit' => true,
                                            'groupSeparator' => ',',
                                            'autoGroup' => true
                                        ],
                                    ])->label(false) ?>
                                </td>
                                <td align=right><?= number_format($model->getObligationsAsOfReportingPeriod($getData['quarter']), 2) ?></td>
                                <td align=center>
                                    <?= $form->field($financial[$model->id], "[$model->id]obligation")->widget(MaskedInput::classname(), [
                                        'options' => [
                                            'autocomplete' => 'off',
                                            'value' => $financial[$model->id]['obligation'] != '' ? $financial[$model->id]['obligation'] : 0,
                                            'onKeyup' => 'updateAccomplishmentTable()',
                                            'disabled' => $model->isCompleted == true ? true : false
                                        ],
                                        'clientOptions' => [
                                            'alias' =>  'decimal',
                                            'removeMaskOnSubmit' => true,
                                            'groupSeparator' => ',',
                                            'autoGroup' => true
                                        ],
                                    ])->label(false) ?>
                                </td>
                                <td align=right><?= number_format($model->getExpendituresAsOfReportingPeriod($getData['quarter']), 2) ?></td>
                                <td align=center>
                                    <?= $form->field($financial[$model->id], "[$model->id]expenditures")->widget(MaskedInput::classname(), [
                                        'options' => [
                                            'autocomplete' => 'off',
                                            'value' => $financial[$model->id]['expenditures'] != '' ? $financial[$model->id]['expenditures'] : 0,
                                            'onKeyup' => 'updateAccomplishmentTable()',
                                            'disabled' => $model->isCompleted == true ? true : false
                                        ],
                                        'clientOptions' => [
                                            'alias' =>  'decimal',
                                            'removeMaskOnSubmit' => true,
                                            'groupSeparator' => ',',
                                            'autoGroup' => true
                                        ],
                                    ])->label(false) ?>
                                </td>
                                <td><?= $model->data_type != "" ? $model->unitOfMeasure.'<br>('.$model->data_type.')' : $model->unitOfMeasure.'<br>(No Data Type)' ?></td>
                                <td align=center><?= $model->indicatorUnitOfMeasure == true ? number_format($model->getPhysicalTargetAsOfReportingPeriod($getData['quarter']), 2).'%' : number_format($model->getPhysicalTargetAsOfReportingPeriod($getData['quarter']), 0) ?></td>
                                <td align=center><?= $model->indicatorUnitOfMeasure == true ? number_format($model->getPhysicalTargetForQuarter($getData['quarter']), 2).'%' : number_format($model->getPhysicalTargetForQuarter($getData['quarter']), 0) ?></td>
                                <td align=center><b><?= $model->indicatorUnitOfMeasure == true ? number_format($model->getPhysicalActualToDate($getData['quarter']), 2).'%' : number_format($model->getPhysicalActualToDate($getData['quarter']), 0) ?></b></td>
                                <td align=center>
                                    <?= $form->field($physical[$model->id], "[$model->id]value")->widget(MaskedInput::classname(), [
                                        'options' => [
                                            'autocomplete' => 'off',
                                            'value' => $physical[$model->id]['value'] != '' ? $physical[$model->id]['value'] : 0,
                                            'onKeyup' => 'updateAccomplishmentTable()',
                                            'disabled' => $model->isCompleted == true ? true : false
                                        ],
                                        'clientOptions' => [
                                            'alias' =>  'decimal',
                                            'removeMaskOnSubmit' => true,
                                            'groupSeparator' => ',',
                                            'autoGroup' => true
                                        ],
                                    ])->label(false) ?>
                                </td>
                                <td align=center><?= number_format($model->getMalesEmployedTarget($getData['quarter']), 0) ?></td>
                                <td align=center><?= number_format($model->getFemalesEmployedTarget($getData['quarter']), 0) ?></td>
                                <td align=center><b><?= number_format($model->getEmployedTarget($getData['quarter']), 0) ?></b></td>
                                <td align=center>
                                    <?= $form->field($personEmployed[$model->id], "[$model->id]male")->widget(MaskedInput::classname(), [
                                        'options' => [
                                            'autocomplete' => 'off',
                                            'value' => $personEmployed[$model->id]['male'] != '' ? $personEmployed[$model->id]['male'] : 0,
                                            'onKeyup' => 'updateAccomplishmentTable()',
                                            'disabled' => $model->isCompleted == true ? true : false
                                        ],
                                        'clientOptions' => [
                                            'alias' =>  'decimal',
                                            'removeMaskOnSubmit' => true,
                                            'groupSeparator' => ',',
                                            'autoGroup' => true
                                        ],
                                    ])->label(false) ?>
                                </td>
                                <td align=center>
                                    <?= $form->field($personEmployed[$model->id], "[$model->id]female")->widget(MaskedInput::classname(), [
                                        'options' => [
                                            'autocomplete' => 'off',
                                            'value' => $personEmployed[$model->id]['female'] != '' ? $personEmployed[$model->id]['female'] : 0,
                                            'onKeyup' => 'updateAccomplishmentTable()',
                                            'disabled' => $model->isCompleted == true ? true : false
                                        ],
                                        'clientOptions' => [
                                            'alias' =>  'decimal',
                                            'removeMaskOnSubmit' => true,
                                            'groupSeparator' => ',',
                                            'autoGroup' => true
                                        ],
                                    ])->label(false) ?>
                                </td>
                                <td align=center><b><?= number_format($model->getEmployedActual($getData['quarter']), 0) ?></b></td>
                                <td align=center><?= number_format($model->getBeneficiariesTarget($getData['quarter']), 0 ) ?></td>
                                <td align=center><?= number_format($model->getGroupsTarget($getData['quarter']), 0 ) ?></td>
                                <td align=center>
                                    <?= $form->field($beneficiaries[$model->id], "[$model->id]male")->widget(MaskedInput::classname(), [
                                        'options' => [
                                            'autocomplete' => 'off',
                                            'value' => $beneficiaries[$model->id]['male'] != '' ? $beneficiaries[$model->id]['male'] : 0,
                                            'onKeyup' => 'updateAccomplishmentTable()',
                                            'disabled' => $model->isCompleted == true ? true : false
                                        ],
                                        'clientOptions' => [
                                            'alias' =>  'decimal',
                                            'removeMaskOnSubmit' => true,
                                            'groupSeparator' => ',',
                                            'autoGroup' => true
                                        ],
                                    ])->label(false) ?>
                                </td>
                                <td align=center>
                                    <?= $form->field($beneficiaries[$model->id], "[$model->id]female")->widget(MaskedInput::classname(), [
                                        'options' => [
                                            'autocomplete' => 'off',
                                            'value' => $beneficiaries[$model->id]['female'] != '' ? $beneficiaries[$model->id]['female'] : 0,
                                            'onKeyup' => 'updateAccomplishmentTable()',
                                            'disabled' => $model->isCompleted == true ? true : false
                                        ],
                                        'clientOptions' => [
                                            'alias' =>  'decimal',
                                            'removeMaskOnSubmit' => true,
                                            'groupSeparator' => ',',
                                            'autoGroup' => true
                                        ],
                                    ])->label(false) ?>
                                </td>
                                <td align=center><b><?= number_format($model->getBeneficiariesActual($getData['quarter']), 0 ) ?></b></td>
                                <td align=center>
                                    <?= $form->field($groups[$model->id], "[$model->id]value")->widget(MaskedInput::classname(), [
                                        'options' => [
                                            'autocomplete' => 'off',
                                            'value' => $groups[$model->id]['value'] != '' ? $groups[$model->id]['value'] : 0,
                                            'onKeyup' => 'updateAccomplishmentTable()',
                                            'disabled' => $model->isCompleted == true ? true : false
                                        ],
                                        'clientOptions' => [
                                            'alias' =>  'decimal',
                                            'removeMaskOnSubmit' => true,
                                            'groupSeparator' => ',',
                                            'autoGroup' => true
                                        ],
                                    ])->label(false) ?>
                                </td>
                                <td align=center><b><?= number_format($model->getGroupsActual($getData['quarter']), 0 ) ?></b></td>
                                <td align=center>
                                    <?= $form->field($accomplishment[$model->id], "[$model->id]remarks")->textArea(['rows' => '3',  'onKeyup' => 'updateAccomplishmentTable()', 'style' => 'resize: none;',
                                            'disabled' => $model->isCompleted == true ? true : false])->label(false) ?>
                                </td>
                                <td align=center>
                                    <?= $form->field($accomplishment[$model->id], "[$model->id]action")->widget(Switchery::className(), [
                                        'options' => [
                                            'label' => false,
                                            'title' => 'Toggle if project is completed',
                                        ],
                                        'clientOptions' => [
                                            'color' => '#5fbeaa',
                                            'size' => 'small'
                                        ],
                                    'clientEvents' => [
                                            'change' => new JsExpression('function() {
                                                this.checked == true ? this.value = 1 : this.value = 0;
                                                updateAccomplishmentTable();
                                                enableInputFields(this.value, '.$model->id.');
                                            }'),
                                        ]
                                    ])->label('Project is completed?') ?>
                                </td>
                            </tr>
                            <?php $i++ ?>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div>
            <div class="pull-right"><?= LinkPager::widget(['pagination' => $projectsPages]); ?></div>
            <div class="pull-left"><?= $projectsModels ? $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? Html::submitButton('Save Accomplishment', ['class' => 'btn btn-primary', 'style' => 'margin-top: 20px;', 'data' => ['disabled-text' => 'Please Wait']]) : '' : '' : '' ?></div>
            <div class="clearfix"></div>
        </div>
    <?php ActiveForm::end(); ?>
    <hr>
    <h4>Accomplishment Submission for <?= $getData['quarter'] ?> <?= $getData['year'] ?></h4>
    <p><i class="fa fa-exclamation-circle"></i> Make sure to complete accomplishment for <?= $getData['quarter'] ?> <?= $getData['year'] ?> before clicking the button below. Once submitted, action cannot be reverted.</p>
    <?= $projectsModels ? $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? !$submissionModel->isNewRecord ? 'Accomplishment for '.$submissionModel->quarter.' '.$submissionModel->year.' has been submitted by '.$submissionModel->submitter.' last '.date("F j, Y H:i:s", strtotime($submissionModel->date_submitted)) : Html::button('Submit Accomplishment '.$getData['quarter'].' '.$getData['year'],['class' => 'btn btn-success', 'id' => 'accomplishment-submit-button']) : '' : '' : '' ?>
<?php
    $script = '
    function updateAccomplishmentTable(){
        $(".accomplishment-table").freezeTable("update");
    }

    function enableInputFields(toggle, id)
    {
        if(toggle == 1)
        {
            $("#financialaccomplishment-"+id+"-releases").prop("disabled", true);
            $("#financialaccomplishment-"+id+"-obligation").prop("disabled", true);
            $("#financialaccomplishment-"+id+"-expenditures").prop("disabled", true);
            $("#physicalaccomplishment-"+id+"-value").prop("disabled", true);
            $("#personemployedaccomplishment-"+id+"-male").prop("disabled", true);
            $("#personemployedaccomplishment-"+id+"-female").prop("disabled", true);
            $("#beneficiariesaccomplishment-"+id+"-male").prop("disabled", true);
            $("#beneficiariesaccomplishment-"+id+"-female").prop("disabled", true);
            $("#groupaccomplishment-"+id+"-value").prop("disabled", true);
            $("#accomplishment-"+id+"-remarks").prop("disabled", true);
        }else{
            $("#financialaccomplishment-"+id+"-releases").prop("disabled", false);
            $("#financialaccomplishment-"+id+"-obligation").prop("disabled", false);
            $("#financialaccomplishment-"+id+"-expenditures").prop("disabled", false);
            $("#physicalaccomplishment-"+id+"-value").prop("disabled", false);
            $("#personemployedaccomplishment-"+id+"-male").prop("disabled", false);
            $("#personemployedaccomplishment-"+id+"-female").prop("disabled", false);
            $("#beneficiariesaccomplishment-"+id+"-male").prop("disabled", false);
            $("#beneficiariesaccomplishment-"+id+"-female").prop("disabled", false);
            $("#groupaccomplishment-"+id+"-value").prop("disabled", false);
            $("#accomplishment-"+id+"-remarks").prop("disabled", false);
        }
    }
    $(document).ready(function(){
        $(".accomplishment-table").freezeTable({
            "scrollable": true,
        });
    });
    $("#accomplishment-submit-button").on("click", function(e) {
        e.preventDefault();

        var con = confirm("The data I encoded had been duly approved by my agency head. I am providing my name and designation in the appropriate fields as an attestation of my submission\'s data integrity. Proceed?");
        if(con == true)
        {
            $.ajax({
                type: "POST",
                url: "'.Url::to(['/rpmes/accomplishment/submit']).'",
                data: {
                    year: "'.$getData['year'].'",
                    quarter: "'.$getData['quarter'].'",
                    agency_id: "'.$agency_id.'"
                },
                success: function (data) {
                    console.log(data);
                    $.growl.notice({ title: "Success!", message: "Accomplishment has been submitted" });
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
