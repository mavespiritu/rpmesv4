<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\web\View;
use yii\bootstrap\Modal;
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
                    <ul class="todos" style="font-size: 13px; line-height: 2rem;" type="none" >
                        <li><a href="javascript:void(0);" onclick="items(<?= $model->id?>);">Add Items</a></li>
                        <li>Set DBM Items</li>
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
                    <ul class="reports" style="font-size: 13px; line-height: 2rem;" type="none">
                        <li><?= Html::button('Purchase Request (PR)', ['value' => Url::to(['/v1/pr/pr', 'id' => $model->id]), 'class' => 'button-link', 'id' => 'pr-button']) ?></li>
                        <li>Agency Purchase Request (APR)</li>
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
<style>
    .button-link {
        background: none!important;
        border: none;
        padding: 0!important;
        color: #6192CD;
        text-decoration: none;
        cursor: pointer;
    }
</style>
<?php
  Modal::begin([
    'id' => 'pr-modal',
    'size' => "modal-lg",
    'header' => '<div id="pr-modal-header"><h4>Preview Report</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="pr-modal-content"></div>';
  Modal::end();
?>
<?php
    $script = '
        $(document).ready(function(){
            $("#pr-button").click(function(){
              $("#pr-modal").modal("show").find("#pr-modal-content").load($(this).attr("value"));
            });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>