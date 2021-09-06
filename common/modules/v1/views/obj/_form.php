<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Obj */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="obj-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <?= $form->field($model, 'obj_id')->widget(Select2::classname(), [
        'data' => $objects,
        'options' => ['placeholder' => 'Select Object','multiple' => false, 'class'=>'object-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ])->label('Parent Object');
    ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'active')->widget(Select2::classname(), [
        'data' => ['1' => '1', '0' => '0'],
        'options' => ['multiple' => false, 'class'=>'active-select'],
        'pluginOptions' => [
            'allowClear' =>  false,
        ],
        ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
