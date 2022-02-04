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

<table class="table table-bordered table-responsive table-hover table-condensed">
    <thead>
        <tr>
            <td colspan=7 align=center><b>PR Items</b></td>
            <td colspan=2 align=center><b>Supplier Details</b></td>
        </tr>
        <tr>
            <th>#</th>
            <th>Unit</th>
            <th>Item</th>
            <th>Specifications</th>
            <th>Quantity</th>
            <th>Unit Cost</th>
            <td align=center><b>ABC</b></td>
            <th>Supplier</th>
            <td align=center><b>Unit Cost</b></td>
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
        <td colspan=6 align=right><b>Total ABC:</b></td>
        <td align=right><b><?= number_format($total, 2) ?></b></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    </tbody>
</table>

<?php ActiveForm::end(); ?>