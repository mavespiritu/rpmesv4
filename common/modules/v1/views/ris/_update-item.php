<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Url;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ris */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ris-form">
    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
        'id' => 'ris-update-form'
    ]); ?>

    <h5>RIS No. <?= $model->ris_no ?></h5>
    <h5><?= $activity->title ?> - <?= $model->fundSource->code ?> Funded</h5>
    <table class="table table-bordered">
        <tr>
            <th colspan=3><?= $item->title ?></th>
        </tr>
        <tr>
            <th>PAP</th>
            <th>Month</th>
            <th>Remaining</th>
            <th>Order</th>
        </tr>
        <?php if($items){ ?>
            <?php foreach($items as $i){ ?>
                <tr>
                    <td><?= $i->ppmpItem->subActivity->title ?></td>
                    <td><?= $i->month->month ?></td>
                    <td><?= number_format($i->ppmpItem->getRemainingQuantityPerMonth($i->month->id), 0) ?></td>
                    <td><?= $form->field($data[$i->id], "[$i->id]quantity")->textInput(['type' => 'number', 'maxlength' => true, 'min' => 1, 'max' => ($i->quantity + $i->ppmpItem->getRemainingQuantityPerMonth($i->month->id))])->label(false) ?></td>
                </tr>
            <?php } ?>
        <?php } ?>
    </table>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
