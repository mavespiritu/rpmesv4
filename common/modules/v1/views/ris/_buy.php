<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
use yii\web\View;
/* @var $model common\modules\v1\models\Ppmp */
/* @var $form yii\widgets\ActiveForm */
$maxValueUrl = \yii\helpers\Url::to(['/v1/ris/max-value']);
?>
<br>
<div class="panel panel-default">
    <div class="panel-body">
        <p class="panel-title"><i class="fa fa-shopping-cart"></i> Add to RIS: <b><?= $item->item->title ?></b></p><br>
        <div class="buy-form">
            <table class="table table-responsive table-condensed">
                <tr>
                    <td align=right style="width: 20%;">Title:</td>
                    <td><b><?= $item->item->title ?></b></td>
                    <td align=right style="width: 20%;">Object:</td>
                    <td><b><?= $item->obj->objTitle ?></b></td>
                </tr>
                <tr>
                    <td align=right style="width: 20%;">Unit of Measure:</td>
                    <td><b><?= $item->item->unit_of_measure ?></b></td>
                    <td align=right style="width: 20%;">Type:</td>
                    <td><b><?= $item->type ?></b></td>
                </tr>
                <tr>
                    <td align=right style="width: 20%;">Remaining Qty:</td>
                    <td><b><?= $item->remainingQuantity ?></b></td>
                    <td rowspan=2 align=right style="width: 20%;">Remarks:</td>
                    <td rowspan=2><b><?= $item->remarks ?></b></td>
                </tr>
                <tr>
                    <td align=right style="width: 20%;">Cost Per Unit:</td>
                    <td><b><?= number_format($item->cost, 2) ?></b></td>
                </tr>
            </table>

            <table class="table table-bordered table-responsive table-condensed">
                <tbody>
                    <tr>
                    <?php if($item->itemBreakdowns){ ?>
                        <?php foreach($item->itemBreakdowns as $breakdown){ ?>
                            <th><?= $breakdown->month->abbreviation ?></th>
                        <?php } ?>
                    <?php } ?>
                    </tr>
                    <tr>
                    <?php if($item->itemBreakdowns){ ?>
                        <?php foreach($item->itemBreakdowns as $breakdown){ ?>
                            <td><?= $breakdown->quantity ?></td>
                        <?php } ?>
                    <?php } ?>
                    </tr>
                </tbody>
            </table>

            <?php $form = ActiveForm::begin([
                'options' => ['class' => 'disable-submit-buttons'],
                'id' => 'buy-item-form',
            ]); ?>

            <div class="row">
                <div class="col-md-6 col-xs-12">
                    <?= $form->field($risItemModel, 'month_id')->widget(Select2::classname(), [
                        'data' => $months,
                        'options' => ['placeholder' => 'Select Month','multiple' => false, 'class'=>'month-select'],
                        'pluginOptions' => [
                            'allowClear' =>  true,
                        ],
                        'pluginEvents'=>[
                            'select2:select'=>'
                                function(){
                                    $.ajax({
                                        url: "'.$maxValueUrl.'",
                                        data: {
                                                id: '.$item->id.',
                                                month_id: this.value
                                            }
                                        
                                    }).done(function(result) {
                                        $("#risitem-quantity").val("");
                                        $("#risitem-quantity").attr({
                                            "max" : result,
                                            "min" : 1
                                        });
                                    });
                                }'

                        ]
                        ]);
                    ?>
                </div>
                <div class="col-md-6 col-xs-12">
                    <?= $form->field($risItemModel, 'quantity')->textInput(['type' => 'number', 'maxlength' => true, 'onkeyup' => 'getTotal()']) ?>
                </div>
            </div>

            <span class="pull-right">Total</span><br>
            <p class="panel-title pull-right" style="font-size: 35px !important;" id="total"></p>
            <p class="clearfix"></p>
            
            <div class="form-group pull-right">
                <?= Html::submitButton('<i class="fa fa-shopping-cart"></i> Add to RIS', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
            </div>
            <div class="clearfix"></div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
<?php
  $script = '
  function getTotal()
    {
        var cost_per_unit = parseFloat('.$item->cost.');
        var quantity = parseInt($("#risitem-quantity").val());

        var total = quantity * cost_per_unit;

        $("#total").empty();
        $("#total").html(number_format(total, 2, ".", ","));       
    }
    
  function number_format (number, decimals, dec_point, thousands_sep) {
    number = (number + "").replace(/[^0-9+\-Ee.]/g, "");
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === "undefined") ? "," : thousands_sep,
        dec = (typeof dec_point === "undefined") ? "." : dec_point,
        s = "",
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return "" + Math.round(n * k) / k;
        };
    s = (prec ? toFixedFix(n, prec) : "" + Math.round(n)).split(".");
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || "").length < prec) {
        s[1] = s[1] || "";
        s[1] += new Array(prec - s[1].length + 1).join("0");
    }
    return s.join(dec);
}

function loadRisItems(id)
{
    $.ajax({
        url: "'.Url::to(['/v1/ris/load-items']).'",
        data: {
            id: id,
            activity_id: '.$item->activity_id.',
            sub_activity_id: '.$item->sub_activity_id.',
            fund_source_id: '.$item->fund_source_id.'
        },
        success: function (data) {
            $("#ris-item-list").empty();
            $("#ris-item-list").hide();
            $("#ris-item-list").fadeIn("slow");
            $("#ris-item-list").html(data);
        },
        error: function (err) {
            console.log(err);
        }
    });
}

function loadRisItemsTotal(id)
{
    $.ajax({
        url: "'.Url::to(['/v1/ris/load-ris-items-total']).'",
        data: {
            id: id,
        },
        success: function (data) {
            $("#badge-ris").empty();
            $("#badge-ris").hide();
            $("#badge-ris").fadeIn("slow");
            $("#badge-ris").html(data);
        },
        error: function (err) {
            console.log(err);
        }
    });
}

function alertRis()
{
    $("#alert-ris").empty();
    $("#alert-ris").hide();
    $("#alert-ris").fadeIn("slow");
    $("#alert-ris").html("<div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\">Item has been included.<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button></div>");
}

    $(document).ready(function() {
        getTotal();

        $("#buy-item-form").on("beforeSubmit", function(e) {
            var form = $(this);
            var formData = form.serialize();

            e.preventDefault();
            $.ajax({
                url: form.attr("action"),
                type: form.attr("method"),
                data: formData,
                beforeSend: function(){
                    $("#ris-item-list").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
                },
                success: function (data) {
                    loadRisItems('.$model->id.');
                    loadRisItemsTotal('.$model->id.');
                    alertRis();
                },
                error: function (err) {
                    console.log(err);
                }
            });

            return false;
        });
    });
  ';
  $this->registerJs($script, View::POS_END);
?>