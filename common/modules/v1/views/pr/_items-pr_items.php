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
<?php $form = ActiveForm::begin([
    'id' => 'pr-items-form',
    'options' => ['class' => 'disable-submit-buttons'],
]); ?>

<div class="pull-right">

</div>

<table class="table table-bordered table-responsive table-hover table-condensed">
    <thead>
        <tr>
            <th>#</th>
            <th>Unit</th>
            <th>Item</th>
            <th>Specifications</th>
            <th>Quantity</th>
            <th>Unit Cost</th>
            <td align=center><b>Total Cost</b></td>
            <td align=center><input type=checkbox name="pr-items" class="check-pr-items" /></td>
        </tr>
    </thead>
    <tbody>
    <?php $i = 1; ?>
    <?php $total = 0; ?>
    <?php if(!empty($items)){ ?>
        <?php foreach($items as $item){ ?>
            <?php $id = $item['id'] ?>
            <?= $this->render('_items-pr_item', [
                'i' => $i,
                'id' => $id,
                'model' => $model,
                'item' => $item,
                'prItems' => $prItems,
                'specifications' => $specifications,
                'form' => $form,
            ]) ?>
            <?php $total += $item['total'] * $item['cost'] ?>
            <?php $i++; ?>
        <?php } ?>
    <?php }else{ ?>
        <tr>
            <td colspan=9 align=center>No items included</td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan=6 align=right><b>ABC:</b></td>
        <td align=right><b><?= number_format($total, 2) ?></b></td>
    </tr>
    </tbody>
</table>

<div class="form-group">
    <?= Html::submitButton('Remove Selected', ['class' => 'btn btn-danger', 'id' => 'remove-pr-button', 'data' => ['disabled-text' => 'Please Wait'], 'disabled' => true]) ?>
</div>

<?php ActiveForm::end(); ?>
<?php
    $script = '
    function enableRemoveButton()
    {
        $("#pr-items-form input:checkbox:checked").length > 0 ? $("#remove-pr-button").attr("disabled", false) : $("#remove-pr-button").attr("disabled", true);
    }

    $(".check-pr-items").click(function(){
        $(".check-pr-item").not(this).prop("checked", this.checked);
        enableRemoveButton();
    });

    $(".check-pr-item").click(function(){
        enableRemoveButton();
    });

    $(document).ready(function(){
        $(".check-pr-item").removeAttr("checked");
        enableRemoveButton();
    });

    $("#pr-items-form").on("beforeSubmit", function(e) {
        e.preventDefault();
     
        var form = $(this);
        var formData = form.serialize();

        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            success: function (data) {
                form.enableSubmitButtons();
                alert("Items Removed");
                prItems('.$model->id.');
            },
            error: function (err) {
                console.log(err);
            }
        });

        return false;
    });
    ';

    $this->registerJs($script, View::POS_END);
?>