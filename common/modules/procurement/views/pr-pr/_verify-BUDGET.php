<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use dosamigos\datepicker\DatePicker;
use yii\web\View;
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="device-type-form">
	<h4>Budget Verification Form</h4>
	<p>By filling-up this form, you are hereby approving the purchase request being made.</p>

    <?php $form = ActiveForm::begin(['id' => 'verify-form', 'enableClientValidation' => true]); ?>

    <?= $form->field($verifyModel, 'allotment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($verifyModel, 'fund_cluster')->textInput(['maxlength' => true]) ?>

    <?= $form->field($verifyModel, 'rc_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($verifyModel, 'source_of_fund')->textInput(['maxlength' => true]) ?>

    <?= $form->field($verifyModel, 'charge_to')->dropdownList(['' => 'Select One', 'PS' => 'PS', 'MOOE' => 'MOOE', 'CO' => 'CO']) ?>

    <?= $form->field($verifyModel, 'remarks')->textArea(['maxlength' => true, 'rows' => 4]) ?>

    <p>Verified as to inclusion in WFP & appropriate fund source</p>

    <div class="form-group">
        <?= Html::submitButton('Approve', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php
	  	$script = '
	    	$("#verify-form").on("beforeSubmit", function(e) {
	      		e.preventDefault();
	      		var form = $(this);
	      		var formData = form.serialize();
	      		$.ajax({
	        		url: form.attr("action"),
	        		type: form.attr("method"),
	        		data: formData,
	        		success: function (data) {
	        			alert("Verification has been updated");
	          			$("#genericModal").modal("toggle");
	          			viewBasicInformation('.$model->id.');
	          			viewItems('.$model->id.');
	        		},
		        	error: function () {
		          		alert("Something went wrong");
		        	}
		      	});
		      	return false;
		    });
	  	';
	  	$this->registerJs($script, View::POS_END);
	?>
</div>
