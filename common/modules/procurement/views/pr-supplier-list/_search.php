<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\procurement\models\PrSupplierListSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pr-supplier-list-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'service_type_id') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'business_name') ?>

    <?= $form->field($model, 'business_address') ?>

    <?php // echo $form->field($model, 'contact_person') ?>

    <?php // echo $form->field($model, 'landline') ?>

    <?php // echo $form->field($model, 'mobile') ?>

    <?php // echo $form->field($model, 'email_address') ?>

    <?php // echo $form->field($model, 'philgeps_no') ?>

    <?php // echo $form->field($model, 'bir_registration') ?>

    <?php // echo $form->field($model, 'tin_no') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
