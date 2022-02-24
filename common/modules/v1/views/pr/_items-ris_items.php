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
    'id' => 'pr-item-form',
    'options' => ['class' => 'disable-submit-buttons'],
]); ?>

<table class="table table-bordered table-responsive table-hover table-condensed">
    <thead>
        <tr>
            <th>#</th>
            <th>Unit</th>
            <th>Item</th>
            <th>Quantity</th>
            <th>Unit Cost</th>
            <th>Total</th>
            <td align=center><input type=checkbox name="items" class="check-items" /></td>
        </tr>
    </thead>
    <tbody>
    <?php $i = 1; ?>
    <?php $total = 0; ?>
    <?php if(!empty($risItems)){ ?>
            <?php foreach($risItems as $idx => $items){ ?>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <th><?= $idx ?> - <?= $model->fundSource->code ?> Funded</th>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <?php if(!empty($items)){ ?>
                <?php foreach($items as $item){ ?>
                    <?php $id = $item['id'] ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= $item['unitOfMeasure'] ?></td>
                        <td style="width: 30%;"><?= $item['itemTitle'] ?><br><?= isset($specifications[$item['id']]) ? \file\components\AttachmentsTable::widget(['model' => $specifications[$item['id']]]) : '' ?></td>
                        <td align=center><?= number_format($item['total'], 0) ?></td>
                        <td align=right><?= number_format($item['cost'], 2) ?></td>
                        <td align=right><?= number_format($item['total'] * $item['cost'], 2) ?></td>
                        <td align=center>
                            <?= $form->field($prItems[$item['id']], "[$id]ris_item_id")->checkbox(['value' => $item['id'], 'class' => 'check-item', 'label' => '', 'id' => 'check-item-'.$item['id']]) ?>
                        </td>
                    </tr>
                    <?php $total += ($item['total'] * $item['cost']); ?>
                    <?php $i++; ?>
                <?php } ?>
            <?php } ?>
        <?php } ?>
        <tr>
            <td align=right colspan=5><b>Grand Total</b></td>
            <td align=right><b><?= number_format($total, 2) ?></b></td>
            <td>&nbsp;</td>
        </tr>
    <?php }else{ ?>
    <tr>
        <td colspan=7 align=center>No items available</td>
    </tr>
    <?php } ?>
    </tbody>
</table>

<div class="form-group">
    <?= !empty($risItems) ? Html::submitButton('Add to PR', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) : '' ?>
</div>

<?php ActiveForm::end(); ?>

<?php
    $script = '
    $(".check-items").click(function(){
        $(".check-item").not(this).prop("checked", this.checked);
    });

    $("#pr-item-form").on("beforeSubmit", function(e) {
        e.preventDefault();
     
        var form = $(this);
        var formData = form.serialize();

        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            success: function (data) {
                form.enableSubmitButtons();
                alert("Items Saved");
                prItems('.$model->id.');
                loadRisItems('.$model->id.','.$ris->id.');
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


