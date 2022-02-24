
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
    'id' => 'apr-items-form',
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
            <td align=center><input type=checkbox name="apr-items" class="check-apr-items" /></td>
        </tr>
    </thead>
    <tbody>
    <?php $i = 1; ?>
    <?php $total = 0; ?>
    <?php if(!empty($forAprs)){ ?>
        <?php foreach($forAprs as $item){ ?>
            <?php $id = $item['id'] ?>
            <?= $this->render('_apr-item', [
                'i' => $i,
                'id' => $id,
                'model' => $model,
                'item' => $item,
                'aprItems' => $aprItems,
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
    <?= !empty($forAprs) ? Html::submitButton('Add to RFQ', ['class' => 'btn btn-success', 'id' => 'remove-apr-button', 'data' => ['disabled-text' => 'Please Wait'], 'data' => [
        'method' => 'post',
    ], 'disabled' => true]) : '' ?>
</div>

<?php ActiveForm::end(); ?>

<?php
    $script = '
    function enableAprRemoveButton()
    {
        $("#apr-items-form input:checkbox:checked").length > 0 ? $("#remove-apr-button").attr("disabled", false) : $("#remove-apr-button").attr("disabled", true);
        $("#apr-items-form input:checkbox:checked").length > 0 ? $("#add-apr-button").attr("disabled", false) : $("#add-apr-button").attr("disabled", true);
    }

    $(".check-apr-items").click(function(){
        $(".check-apr-item").not(this).prop("checked", this.checked);
        enableAprRemoveButton();
    });

    $(".check-apr-item").click(function(){
        enableAprRemoveButton();
    });

    $(document).ready(function(){
        $(".check-apr-item").removeAttr("checked");
        enableAprRemoveButton();
    });

    $("#remove-apr-button").on("click", function(e) {
        e.preventDefault();

        var con = confirm("Are you sure you want to add these items to RFQ?");
        if(con == true)
        {
            

            var form = $("#apr-items-form");
            var formData = form.serialize();

            $.ajax({
                url: form.attr("action"),
                type: form.attr("method"),
                data: formData,
                success: function (data) {
                    form.enableSubmitButtons();
                    alert("Items added to RFQ");
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