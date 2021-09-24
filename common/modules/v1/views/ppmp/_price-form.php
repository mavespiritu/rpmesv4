<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ppmp */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="price-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
        'id' => 'price-form',
    ]); ?>

    <div style="min-height: 200px; max-height: 500px; overflow-x: auto;">
        <table class="table table-bordered tab-responsive table-striped table-hover">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Unit of Measure</th>
                    <th>Current Price</th>
                    <th>Updated Price</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            <?php if(!empty($items)){ ?>
                <?php foreach($items as $item){ ?>
                    <tr>
                        <td><?= $item['title'] ?></td>
                        <td><?= $item['unit_of_measure'] ?></td>
                        <td><?= number_format($item['currentCost'], 2) ?></td>
                        <td><?= number_format($item['updatedCost'], 2) ?></td>
                        <td><?= $item['updatedCost'] > $item['currentCost'] ? '+'.abs($item['updatedCost'] - $item['currentCost']) : '-'.abs($item['updatedCost'] - $item['currentCost']) ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="form-group pull-right">
        <?= Html::submitButton('Update Price', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>
    <div class="clearfix"></div>

    <?php ActiveForm::end(); ?>

</div>
