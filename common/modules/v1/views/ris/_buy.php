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
    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
        'id' => 'buy-form',
    ]); ?>

    <div class="form-group pull-right">
        <?= Html::submitButton('<i class="fa fa-shopping-cart"></i> Add to RIS', ['class' => 'btn btn-success']) ?>
    </div>
    <div class="clearfix"></div>
    <?php ActiveForm::end(); ?>

</div>
<?php
  $script = '
    $(document).ready(function() {
        $("#activity-form").on("beforeSubmit", function(e) {
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