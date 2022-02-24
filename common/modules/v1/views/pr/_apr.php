<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Url;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
use yii\web\View;
/* @var $model common\modules\v1\models\Pr */
/* @var $form yii\widgets\ActiveForm */
?>

<table class="table table-bordered table-responsive table-hover table-condensed">
    <thead>
        <tr>
            <td rowspan=2 colspan=3>NAME & ADDRESS <br> OF REQUESTING <br> AGENCY <br><br></td>
            <td rowspan=2 colspan=2><b><?= $agency->value ?></b><br><?= $regionalOffice->value ?><br><?= $address->value ?> <br><br></td>
            <td colspan=3 style="vertical-align: bottom;">ACC. CODE: </td>
        </tr>
        <tr>
            <td colspan=3 style="vertical-align: bottom;">Agency Control No. <br><?= $model->pr_no ?></td>
        </tr>
        <tr>
            <td colspan=5 style="vertical-align: bottom;" colspan=2 align=center><b>AGENCY PROCUREMENT REQUEST</b></td>
            <td colspan=3 style="vertical-align: bottom;">PS APR No.</td>
        </tr>
        <tr>
            <td colspan=5 style="width: 75%;">
                <p>
                    TO: <br>
                    <?= $supplier->business_name ?> <br>
                    <?= $supplier->business_address ?> <br>
                </p>
                <p style="text-align: center;">ACTION REQUEST ON THE ITEM(S) LISTED BELOW</p>
                <p>
                    <?= Html::checkbox('checklist', false, ['id' => 'apr-check_1', 'class' => 'apr-checklist', 'checked' => 'checked', 'onclick' => '$(this).attr("value", this.checked ? 1 : 0)']) ?> Please furnish us with Price Estimate (for office equipment/furniture & supplementary items) <br>
                    <?= Html::checkbox('checklist', false, ['id' => 'apr-check_2', 'class' => 'apr-checklist', 'checked' => 'checked', 'onclick' => '$(this).attr("value", this.checked ? 1 : 0)']) ?> Please purchase for our agency the equipment/furniture/supplementary items per your Price Estimate <br>
                    &nbsp;&nbsp;&nbsp; (PS RAD No. <u><?= Html::input('text', 'rad_no', '', ['id' => 'apr-rad_no']) ?></u> attached) dated 
                    <?= Html::input('text', 'rad_month', '', ['id' => 'apr-rad_month', 'placeholder' => 'Month', 'style' => 'width: 100px;']) ?>-<?= Html::input('text', 'rad_year', '', ['id' => 'apr-rad_year', 'placeholder' => 'Year', 'style' => 'width: 50px;']) ?> <br>
                    <?= Html::checkbox('checklist', false, ['id' => 'apr-check_3', 'class' => 'apr-checklist', 'checked' => 'checked', 'onclick' => '$(this).attr("value", this.checked ? 1 : 0)']) ?> Please issue common-use supplies/materials per PS Price List as of <?= Html::input('text', 'pl_month', '', ['id' => 'apr-pl_month', 'placeholder' => 'Month', 'style' => 'width: 100px;']) ?>-<?= Html::input('text', 'pl_year', '', ['id' => 'apr-pl_year', 'placeholder' => 'Year', 'style' => 'width: 50px;']) ?> <br>
                    <?= Html::checkbox('checklist', false, ['id' => 'apr-check_4', 'class' => 'apr-checklist', 'checked' => 'checked', 'onclick' => '$(this).attr("value", this.checked ? 1 : 0)']) ?> Please issue Certificate of Price Reasonableness <br>
                    <?= Html::checkbox('checklist', false, ['id' => 'apr-check_5', 'class' => 'apr-checklist', 'checked' => 'checked', 'onclick' => '$(this).attr("value", this.checked ? 1 : 0)']) ?> Please furnish us with your latest/updated Price list <br>
                    <?= Html::checkbox('checklist', false, ['id' => 'apr-check_6', 'class' => 'apr-checklist', 'checked' => 'checked', 'onclick' => '$(this).attr("value", this.checked ? 1 : 0)']) ?> Others (specify) <?= Html::input('text', 'other', '', ['id' => 'apr-other', 'style' => 'width: 200px;']) ?>
                </p>
            </td>
            <td colspan=3 style="text-align: center; width: 25%;">
            <?= DatePicker::widget([
                'name' => 'date_generated',
                'id' => 'apr-date_generated',
                'template' => '{addon}{input}',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ]
            ]);?><br>
            <i>(Date Prepared)</i>
            </td>
        </tr>
        <tr>
            <td align=center colspan=8>IMPORTANT! PLEASE SEE INSTRUCTIONS/CONDITIONS AT THE BACK OF ORIGINAL COPY</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td align=center style="width: 5%;"><b>No.</b></td>
            <td align=center colspan=3 style="width: 50%;"><b>ITEM and DESCRIPTION/SPECIFICATIONS/STOCK No.</b></td>
            <td align=center style="width: 10%;"><b>QUANTITY</b></td>
            <td align=center style="width: 10%;"><b>UNIT</b></td>
            <td align=center style="width: 10%;"><b>UNIT PRICE</b></td>
            <td align=center style="width: 10%;"><b>AMOUNT</b></td>
        </tr>
        <?php if(!empty($aprItems)){ ?>
            <?php $i = 1; ?>
            <?php foreach($aprItems as $item){ ?>
                <tr>
                    <td align=center><?= $i ?></td>
                    <td colspan=3><?= $item['item'] ?></td>
                    <td align=center><?= number_format($item['total'], 0) ?></td>
                    <td align=center><?= $item['unit'] ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <?php $i++; ?>
            <?php } ?>
        <?php } ?>
        <?php if(!empty($specifications)){ ?>
        <tr>
            <td>&nbsp;</td>
            <td colspan=3 align=center>(Please see attached specifications for your reference.)</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <?php } ?>
        <tr>
            <td>&nbsp;</td>
            <td colspan=3 align=center>xxxxxxxxxxxxxx NOTHING FOLLOWS xxxxxxxxxxxxxxx</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan=4><?= $shortName->value ?> Office Telefax No: <?= Html::input('text', 'telefax', '', ['id' => 'apr-telefax']) ?></td>
            <td colspan=2 align=right>Total AMOUNT:</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan=8 align=center>NOTE: ALL SIGNATURES MUST BE OVER PRINTED NAME</td>
        </tr>
    </tbody>
</table>
<table class="table table-bordered table-responsive table-hover table-condensed">
    <tr>
        <td style="width: 30%">
            STOCKS REQUESTED ARE CERTIFIED <br>
            TO BE WITHIN APPROVED PROGRAM: <br>
            <br>
            <br>
            <p style="text-align: center"><b><?= strtoupper($apr->stockCertifierName) ?></b><br><?= $apr->stockCertifier ? $apr->stockCertifier->position.' (Supply Officer)' : '' ?></p>
        </td>
        <td style="width: 30%">
            FUNDS CERTIFIED AVAILABLE:
            <br>
            <br>
            <br>
            <br>
            <p style="text-align: center"><b><?= strtoupper($apr->fundsCertifierName) ?></b><br><?= $apr->fundsCertifier ? $apr->fundsCertifier->position : '' ?></p>
        </td>
        <td style="width: 30%">
            APPROVED:
            <br>
            <br>
            <br>
            <br>
            <p style="text-align: center"><b><?= strtoupper($apr->approverName) ?></b><br><?= $apr->approver ? $apr->approver->position : '' ?></p>
        </td>
    </tr>
</table>
<br>
<div class="pull-right">
<?= Html::button('<i class="fa fa-print"></i> Print APR', ['class' => 'btn btn-success', 'onclick' => 'printApr()']) ?>
</div>
<div class="clearfix"></div>
<?php
    $script = '
        function putValueInCheckbox()
        {
            $(".apr-checklist").val(0);
        }
        function printApr()
        {
            var date_generated = $("#apr-date_generated").val();
            var rad_no = $("#apr-rad_no").val();
            var rad_month = $("#apr-rad_month").val();
            var rad_year = $("#apr-rad_year").val();
            var pl_month = $("#apr-pl_month").val();
            var pl_year = $("#apr-pl_year").val();
            var telefax = $("#apr-telefax").val();
            var check_1 = $("#apr-check_1").val();
            var check_2 = $("#apr-check_2").val();
            var check_3 = $("#apr-check_3").val();
            var check_4 = $("#apr-check_4").val();
            var check_5 = $("#apr-check_5").val();
            var check_6 = $("#apr-check_6").val();
            var other = $("#apr-other").val();

            var printWindow = window.open(
            "'.Url::to(['/v1/pr/print-apr']).'?id='.$model->id.'&rad_no=" + rad_no + "&rad_month=" + rad_month + "&rad_year=" + rad_year + "&pl_month=" + pl_month + "&pl_year=" + pl_year + "&telefax=" + telefax + "&check_1=" + check_1 + "&check_2=" + check_2 + "&check_3=" + check_3 + "&check_4=" + check_4 + "&check_5=" + check_5 + "&check_6=" + check_6 + "&other=" + other + "&date_generated=" + date_generated, 
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

        $(document).ready(function(){
            $(".apr-checklist").removeAttr("checked");
            putValueInCheckbox();
        });
    ';

    $this->registerJs($script, View::POS_END);
?>