<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\widgets\MaskedInput;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Item */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="item-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
        'id' => 'item-form',
        'enableAjaxValidation' => true,
    ]); ?>

    <?= $form->field($model, 'procurement_mode_id')->widget(Select2::classname(), [
            'data' => $procurementModes,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'procurement-mode-select'],
            'pluginOptions' => [
                'allowClear' =>  false,
            ],
        ]);
    ?>

    <?= $form->field($model, 'category')->widget(Select2::classname(), [
            'data' => $categories,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class' => 'category-select'],
            'pluginOptions' => [
                'allowClear' =>  false,
            ],
        ]);
    ?>

    <?= $form->field($model, 'code')->textInput() ?>

    <?= $form->field($model, 'title')->textInput() ?>

    <?= $form->field($model, 'unit_of_measure')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cost_per_unit')->widget(MaskedInput::classname(), [
        'options' => [
            'autocomplete' => 'off',
        ],
        'clientOptions' => [
            'alias' =>  'decimal',
            'removeMaskOnSubmit' => true,
            'groupSeparator' => ',',
            'autoGroup' => true
        ],
    ]) ?>

    <?= $form->field($model, 'cse')->widget(Select2::classname(), [
            'data' => ['Yes' => 'Yes', 'No' => 'No'],
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'cse-select'],
            'pluginOptions' => [
                'allowClear' =>  false,
            ],
        ]);
    ?>

    <?= $form->field($model, 'classification')->widget(Select2::classname(), [
            'data' => $classifications,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'classification-select'],
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
