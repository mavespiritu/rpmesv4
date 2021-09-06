<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\procurement\models\PrSupplierList */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pr-supplier-list-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'service_type_id')->textInput() ?>

    <?= $form->field($model, 'type')->dropDownList([ 'Agency' => 'Agency', 'Private' => 'Private', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'business_name')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'business_address')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'contact_person')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'landline')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'philgeps_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bir_registration')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tin_no')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
