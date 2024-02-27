<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use dosamigos\datepicker\DatePicker;
use yii\web\View;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\DueDate */
/* @var $form yii\widgets\ActiveForm */
$report = str_replace(' ', '', $report);
?>

<div class="due-date-form">

    <?php $form = ActiveForm::begin([
        'id' => 'due-date-form-'.$report.'-'.$year.'-'.$quarter,
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <?= $form->field($model, 'due_date')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Enter date', 'autocomplete' => 'off', 'id' => 'duedate-due_date-'.$report.'-'.$year.'-'.$quarter],
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
        ],
    ])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
    $script = '$("#due-date-form-'.$report.'-'.$year.'-'.$quarter.'").on("beforeSubmit", function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = form.serialize();

        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            success: function (data) {
                form.enableSubmitButtons();
                alert("Due date saved successfully");
                $(".modal").remove();
                $(".modal-backdrop").remove();
                $("body").removeClass("modal-open");
                loadMonitoringPlanDueDate('.$year.');
                loadAccomplishmentDueDate('.$year.',"Q1");
                loadAccomplishmentDueDate('.$year.',"Q2");
                loadAccomplishmentDueDate('.$year.',"Q3");
                loadAccomplishmentDueDate('.$year.',"Q4");
                loadProjectExceptionDueDate('.$year.',"Q1");
                loadProjectExceptionDueDate('.$year.',"Q2");
                loadProjectExceptionDueDate('.$year.',"Q3");
                loadProjectExceptionDueDate('.$year.',"Q4");
                loadProjectResultsDueDate('.$year.');
            },
            error: function (err) {
                console.log(err);
            }
        }); 
        
        return false;
    });
    ';

    $this->registerJs($script, View::POS_END);
?>
