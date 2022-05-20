<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Agency */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="agency-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <h4>Agency Information</h4>

    <?= $form->field($model, 'agency_type_id')->widget(Select2::classname(), [
            'data' => $agencyTypes,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'agency-type-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
        ]);
    ?>

    <div class="row">
        <div class="col-md-4 col-xs-12">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-8 col-xs-12">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>
    
    <hr>
    <h4>Agency Head</h4>

    <div class="row">
        <div class="col-md-4 col-xs-12">
            <?= $form->field($model, 'salutation')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-8 col-xs-12">
            <?= $form->field($model, 'head')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?= $form->field($model, 'head_designation')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
