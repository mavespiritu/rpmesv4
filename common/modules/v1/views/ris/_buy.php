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
            <td><b><?= $item->cost ?></b></td>
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
        'id' => 'buy-form',
        'enableAjaxValidation' => true,
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
                                $("#quantity-select").val("");
                                $("#quantity-select").attr({
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
            <?= $form->field($risItemModel, 'quantity')->textInput(['type' => 'number', 'maxlength' => true, 'id' => 'quantity-select']) ?>
        </div>
    </div>
    
    <div class="form-group pull-right">
        <?= Html::submitButton('<i class="fa fa-shopping-cart"></i> Add to RIS', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>
    <div class="clearfix"></div>

    <?php ActiveForm::end(); ?>

</div>
<?php
  $script = '
    $(document).ready(function() {
        $("#buy-form").on("beforeSubmit", function(e) {
            var form = $(this);
            e.preventDefault();
            $.ajax({
                url: "'.Url::to(['/v1/ris/load-items']).'",
                data: {
                    id: '.$model->id.',
                    activity_id: $("#appropriationitem-activity_id").val(),
                    sub_activity_id: $("#appropriationitem-sub_activity_id").val(),
                    fund_source_id: $("#appropriationitem-fund_source_id").val(),
                },
                beforeSend: function(){
                    $("#items").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
                },
                success: function (data) {
                    console.log(this.data);
                    $("#ris-items").empty();
                    $("#ris-items").hide();
                    $("#ris-items").fadeIn("slow");
                    $("#ris-items").html(data);
                    form.enableSubmitButtons();
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