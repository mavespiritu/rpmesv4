<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\KeyResultArea */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="key-result-area-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <?= $form->field($model, 'category_id')->widget(Select2::classname(), [
            'data' => $categories,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'category-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
        ]);
    ?>

    <?= $form->field($model, 'kra_no')->textInput() ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
