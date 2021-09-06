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

	<p>This form is applicable for items that has issues and needs revision or disapproval.</p>

	<h4>Item Details</h4>
	<table class="table table-responsive table-condensed table-bordered">
        <tbody>
            <tr>
                <th>Stock/Property No.</th>
                <td><?= $model->stockInventory->stock_code ?></td>
            </tr>
            <tr>
                <th>Unit</th>
                <td><?= $model->unit ?></td>
            </tr>
            <tr>
                <th>Item</th>
                <td><?= $model->item ?></td>
            </tr>
            <tr>
                <th>Description</th>
                <td><?= $model->description ?></td>
            </tr>
            <tr>
                <th>Quantity</th>
                <td><?= $model->quantity ?></td>
            </tr>
            <tr>
                <th>Unit Cost</th>
                <td><?= number_format($model->unit_cost, 2) ?></td>
            </tr>
        </tbody>
    </table>

    <h4>Action Form</h4>
    <div class="panel panel-default">
        <div class="panel-body">
		    <?php $form = ActiveForm::begin(['id' => 'approve-form', 'enableClientValidation' => true]); ?>

		    <?= $form->errorSummary($model) ?>

		    <?= $form->field($approvalModel, 'status')->dropdownList(['' => 'Select One', 'FOR REVISION' => 'FOR REVISION', 'DISAPPROVED' => 'DISAPPROVED']) ?>

		    <?= $form->field($approvalModel, 'remarks')->textArea(['maxlength' => true, 'rows' => 4]) ?>

		    <div class="form-group">
		        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
		    </div>
		</div>
	</div>

    <h4>Transaction History</h4>
    <table class="table table-responsive table-condensed table-bordered">
    	<thead>
    		<tr>
    			<th>Approval Status</th>
    			<th>Action Taken By</th>
    			<th>Date/Time of Action</th>
    			<th>Remarks</th>
    		</tr>
    	</thead>
    	<tbody>
    		<?php if($model->prItemApprovals){ ?>
    			<?php foreach($model->prItemApprovals as $approval): ?>
    				<tr>
    					<td><?= $approval->status ?></td>
    					<td><?= $approval->actionTakenByName ?></td>
    					<td><?= $approval->date_of_action ?></td>
    					<td><?= $approval->remarks ?></td>
    				</tr>
    			<?php endforeach ?>
    		<?php }else{ ?>
    			<td colspan=4>No transaction history.</td>
    		<?php } ?>
    	</tbody>
    </table>

    <?php ActiveForm::end(); ?>
    <?php
	  	$script = '
	    	$("#approve-form").on("beforeSubmit", function(e) {
	      		e.preventDefault();
	      		var form = $(this);
	      		var formData = form.serialize();
	      		$.ajax({
	        		url: form.attr("action"),
	        		type: form.attr("method"),
	        		data: formData,
	        		success: function (data) {
	          			alert("Item status has been updated");
                        $("#genericModal").modal("toggle");
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
