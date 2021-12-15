<?php 
    use yii\helpers\Html;
    $division = !is_null(Yii::$app->user->identity->userinfo->OFFICE_C) ? Yii::$app->user->identity->userinfo->office->abbreviation : '';
    $section = !is_null(Yii::$app->user->identity->userinfo->SECTION_C) ? Yii::$app->user->identity->userinfo->section->title : '';

    $office = $section == '' ? $division : $division.' - '.$section;
?>

<h4>Purchase Request Details <span class="pull-right" style="font-size: 14px;"><b>DTS No: </b><?= $model->dts_no ?></span></h4>
<table class="table table-responsive table-condensed table-bordered">
    <tbody>
        <tr>
            <th style="width: 10%;">Entity Name</th>
            <td colspan=3 style="width: 50%;"><?= $model->entity_name ?></td>
            <th>Fund Cluster</th>
            <td colspan=2><?= $model->prBudgetVerification ? $model->prBudgetVerification->fund_cluster : '<i>For Budget Verification</i>'  ?></td>
        </tr>
        <tr>
            <th rowspan=2>Office/Section</th>
            <td rowspan=2 style="width: 30%;"><?= $office ?></td>
            <th style="width: 10%;">PR No.:</th>
            <td colspan=2><?= $model->prProcVerification ? $model->prProcVerification->pr_no : '<i>For Procurement Verification</i>' ?></td>
            <th rowspan=2>Date</th>
            <td rowspan=2><?= date("j F Y", strtotime($model->date_requested)) ?></td>
        </tr>
        <tr>
            <th colspan=2>Responsibility Center Code</th>
            <td><?= $model->prBudgetVerification ? $model->prBudgetVerification->rc_code : '<i>For Budget Verification</i>'  ?></td>
        </tr>
    </tbody>
</table>
<div id="item-form"></div>
<div id="item-list"></div>
    
