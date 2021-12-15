<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use dosamigos\datepicker\DatePicker;
use yii\web\View;
use dosamigos\ckeditor\CKEditor;
use yii\widgets\MaskedInput;
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="device-type-form">
	<h4>Manual Addition/Updating of Item</h4>

    <?php $form = ActiveForm::begin([
    	'id' => 'add-item', 
    	'enableClientValidation' => true,
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <?= $form->field($itemModel, 'item')->textInput(['maxlength' => true]) ?>

    <?= $form->field($itemModel, 'description')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'basic'
    ]) ?>

    <?= $form->field($itemModel, 'unit')->textInput(['maxlength' => true]) ?>

    <?= $form->field($itemModel, 'quantity')->textInput(['type' => 'number', 'min' => 1, 'placeholder' => 'Enter quantity']) ?>

    <?= $form->field($itemModel, 'unit_cost')->widget(MaskedInput::classname(), [
        'options' => [
            'placeholder' => 'Enter amount',
        ],
        'clientOptions' => [
            'alias' =>  'decimal',
            'autoGroup' => true,
        ],
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php
	  	$script = '
	    	$("#add-item").on("beforeSubmit", function(e) {
	      		e.preventDefault();
	      		var form = $(this);
	      		var formData = form.serialize();
	      		$.ajax({
	        		url: form.attr("action"),
	        		type: form.attr("method"),
	        		data: formData,
	        		success: function (data) {
	          			alert("Item has been saved");
	          			location.reload();
	        		},
		      	});
		      	return false;
		    });
	  	';
	  	$this->registerJs($script, View::POS_END);
	?>
</div>
