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
    <h4>Procurement Verification Form</h4>
	<p>By filling-up this form, you are hereby approving the purchase request being made.</p>

    <?php $form = ActiveForm::begin([
        'id' => 'verify-form'.$group, 
        'enableClientValidation' => true
    ]); ?>

    <?= $form->field($verifyModel, 'pr_no')->textInput(['maxlength' => true,'enableAjaxValidation' => true]) ?>

    <?= $form->field($verifyModel, 'mode_id')->widget(Select2::classname(), [
        'data' => $modes,
        'options' => ['placeholder' => 'Select One','multiple' => false, 'class'=>'mode-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ]);
    ?>

    <?= $form->field($verifyModel, 'procurement_type_id')->widget(Select2::classname(), [
        'data' => $procurementTypes,
        'options' => ['placeholder' => 'Select One','multiple' => false, 'class'=>'procurement-type-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ]);
    ?>

    <?= $form->field($verifyModel, 'remarks')->textArea(['maxlength' => true, 'rows' => 4]) ?>

    <p>Verified as to inclusion in APP / PPMP & completeness of specifications & supporting documents</p>

    <div class="form-group">
        <?= Html::submitButton('Approve', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php
	  	$script = '
	    	$("#verify-form'.$group.'").on("beforeSubmit", function(e) {
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
                        location.reload();
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
