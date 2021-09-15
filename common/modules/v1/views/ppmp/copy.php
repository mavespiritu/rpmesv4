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

<div class="ppmp-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
        'id' => 'ppmp-copy-form',
        'enableAjaxValidation' => true,
        'type' => 'POST',
    ]); ?>

    <?php if(Yii::$app->user->can('Administrator')){ ?>
        <?= $form->field($model, 'office_id')->widget(Select2::classname(), [
            'data' => $offices,
            'options' => ['placeholder' => 'Select Division','multiple' => false, 'class'=>'office-select', 'id' => 'ppmp-office-copy'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
            ]);
        ?>
    <?php } ?>

    <?= $form->field($model, 'year')->textInput(['type' => 'number' , 'min' => date("Y") - 1, 'autocomplete' => 'off'])->label('New PPMP Year') ?>

    <?= $form->field($model, 'stage')->widget(Select2::classname(), [
        'data' => [ 'Indicative' => 'Indicative', 'Adjusted' => 'Adjusted', 'Final' => 'Final', ],
        'options' => ['placeholder' => 'Select Stage','multiple' => false, 'class'=>'stage-select', 'id' => 'ppmp-stage-copy'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ]);
    ?>

    <hr style="opacity: 0.3">

    <?= $form->field($model, 'copy')->widget(Select2::classname(), [
        'data' => $ppmps,
        'options' => ['placeholder' => 'Select PPMP','multiple' => false, 'class'=>'copy-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ]);
    ?>

    <?= $form->field($model, 'data')->widget(Select2::classname(), [
        'data' => ['' => 'Select One', '1' => 'Items Only', '2' => 'Items and Quantity'],
        'options' => ['placeholder' => 'Select One','multiple' => false, 'class'=>'data-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'name' => 'Copy', 'value' => 'Copy', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
