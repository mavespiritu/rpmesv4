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

?>
<div class="acknowledgement-list">
    <table class="table table-condensed table-responsive table-hover table-striped table-bordered">
        <thead>
            <tr>
                <td align=center><b>#</b></td>
                <td align=center><b>Agency</b></td>
                <td align=center><b>Submitted By</b></td>
                <td align=center><b>Date Submitted</b></td>
                <td align=center><b>Acknowledged By</b></td>
                <td align=center><b>Date Acknowledged</b></td>
                <td align=center colspan=2><b>Actions</b></td>
            </tr>
        </thead>
        <tbody>
        <?php if($submissions){ ?>
            <?php $i = 1; ?>
            <?php foreach($submissions as $agency){ ?>
                <?= $this->render('_monitoring-plan-submission', [
                    'i' => $i,
                    'agency' => $agency,
                    'getData' => $getData,
                ]) ?>
                <?php $i++; ?>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php
    $script = '
        function printAcknowledgmentMonitoringPlan(id)
        {
            var printWindow = window.open(
                "'.Url::to(['/rpmes/acknowledgment/print-monitoring-plan']).'?id=" + id, 
                "Print",
                "left=200", 
                "top=200", 
                "width=650", 
                "height=500", 
                "toolbar=0", 
                "resizable=0"
                );
                printWindow.addEventListener("load", function() {
                    printWindow.print();
                    setTimeout(function() {
                    printWindow.close();
                }, 1);
                }, true);
        }
    ';

    $this->registerJs($script, View::POS_END);
?>