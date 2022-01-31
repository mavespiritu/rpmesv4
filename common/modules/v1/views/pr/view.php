<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use  yii\web\View;
/* @var $model common\modules\v1\models\Pr */

$this->title = $model->status ? $model->pr_no.' ['.$model->status->status.']' : $model->pr_no;
$this->params['breadcrumbs'][] = ['label' => 'PRs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="pr-view">
    <?= $this->render('_menu', [
        'model' => $model
    ]) ?>

    <div class="row">
        <div class="col-md-2 col-xs-12">
            <div class="box box-primary">
                <div class="box-header panel-title"><i class="fa fa-list"></i> Main Navigation</div>
                <div class="box-body">
                <?= $this->render('_pr-menu', [
                    'model' => $model
                ]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-8 col-xs-12">
            <div id="pr-main">
            </div>
        </div>
        <div class="col-md-2 col-xs-12">
            <div class="box box-primary">
                <div class="box-header panel-title"><i class="fa fa-edit"></i> To Dos</div>
                <div class="box-body">
                    <ul style="font-size: 12px;">
                        <li>Add Items</li>
                        <li>Set DBM Available Items</li>
                        <li>Generate RFQ</li>
                        <li>Retrieve RFQs</li>
                        <li>Set Suppliers</li>
                        <li>Set Purchase Order</li>
                        <li>Set Contract</li>
                        <li>Inspect Items</li>
                        <li>Issue Items</li>
                    </ul>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header panel-title"><i class="fa fa-file-o"></i> Reports</div>
                <div class="box-body">
                    <ul style="font-size: 12px;">
                        <li>Agency Purchase Request (APR)</li>
                        <li>Purchase Request (PR)</li>
                        <li>Request For Quotation (RFQ)</li>
                        <li>Abstract of Quotation (AOQ)</li>
                        <li>DBM Purchase Order (DBM-PO)</li>
                        <li>Purchase Order (PO)</li>
                        <li>Contract</li>
                        <li>Notice to Proceed (NTP)</li>
                        <li>Notice of Award (NOA)</li>
                        <li>Obligation Request Status (ORS)</li>
                        <li>Disbursement Voucher (DV)</li>
                        <li>Inspection and Acceptance Report (IAR)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $script = '
        $(document).ready(function(){
            home('.$model->id.');
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>