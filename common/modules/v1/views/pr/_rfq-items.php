
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Url;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
use yii\web\View;
use yii\bootstrap\Modal;
/* @var $model common\modules\v1\models\Pr */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'id' => 'rfq-items-form',
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
            <td align=center><b>Total Cost</b></td>
            <td align=center><input type=checkbox name="rfq-items" class="check-rfq-items" /></td>
        </tr>
    </thead>
    <tbody>
    <?php $i = 1; ?>
    <?php $total = 0; ?>
    <?php if(!empty($forRfqs)){ ?>
        <?php foreach($forRfqs as $item){ ?>
            <?php $id = $item['id'] ?>
            <?= $this->render('_rfq-item', [
                'i' => $i,
                'id' => $id,
                'model' => $model,
                'item' => $item,
                'rfqItems' => $rfqItems,
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

<div class="form-group pull-right"> 
    <?= !empty($forRfqs) ? Html::submitButton('Add to APR', ['class' => 'btn btn-success', 'id' => 'remove-rfq-button', 'data' => ['disabled-text' => 'Please Wait'], 'data' => [
        'method' => 'post',
    ], 'disabled' => true]) : '' ?>
</div>

<?php ActiveForm::end(); ?>

<?php
    $script = '
    function enableRfqRemoveButton()
    {
        $("#rfq-items-form input:checkbox:checked").length > 0 ? $("#remove-rfq-button").attr("disabled", false) : $("#remove-rfq-button").attr("disabled", true);
        $("#rfq-items-form input:checkbox:checked").length > 0 ? $("#add-rfq-button").attr("disabled", false) : $("#add-rfq-button").attr("disabled", true);
    }

    $(".check-rfq-items").click(function(){
        $(".check-rfq-item").not(this).prop("checked", this.checked);
        enableRfqRemoveButton();
    });

    $(".check-rfq-item").click(function(){
        enableRfqRemoveButton();
    });

    $(document).ready(function(){
        $(".check-rfq-item").removeAttr("checked");
        enableRfqRemoveButton();
    });

    $("#remove-rfq-button").on("click", function(e) {
        e.preventDefault();

        var con = confirm("Are you sure you want to add these items to APR?");
        if(con == true)
        {
            

            var form = $("#rfq-items-form");
            var formData = form.serialize();

            $.ajax({
                url: form.attr("action"),
                type: form.attr("method"),
                data: formData,
                success: function (data) {
                    form.enableSubmitButtons();
                    alert("Items added to APR");
                    aprItems('.$model->id.');
                    rfqItems('.$model->id.');
                },
                error: function (err) {
                    console.log(err);
                }
            }); 
        }

        return false;
    });
    ';

    $this->registerJs($script, View::POS_END);
?>