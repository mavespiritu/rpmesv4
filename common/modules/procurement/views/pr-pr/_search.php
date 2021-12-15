<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\procurement\models\PrPrSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pr-pr-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'entity_id') ?>

    <?= $form->field($model, 'dts_no') ?>

    <?= $form->field($model, 'rc_code') ?>

    <?= $form->field($model, 'date_requested') ?>

    <?php // echo $form->field($model, 'fund_cluster') ?>

    <?php // echo $form->field($model, 'purpose') ?>

    <?php // echo $form->field($model, 'requester') ?>

    <?php // echo $form->field($model, 'requester_designation') ?>

    <?php // echo $form->field($model, 'approver') ?>

    <?php // echo $form->field($model, 'approver_designation') ?>

    <?php // echo $form->field($model, 'source_of_fund') ?>

    <?php // echo $form->field($model, 'charge_to') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
