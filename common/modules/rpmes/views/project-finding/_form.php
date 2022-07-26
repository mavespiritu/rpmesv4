<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectFinding */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-finding-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <?= $form->field($model, 'year')->dropDownList(['' => '', $model->getYearsList()])->label('Year *'); ?>

    <?= $form->field($model, 'quarter')->dropDownList([ 'Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter', ], ['prompt' => '']) ?>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <?= $form->field($model, 'project_id')->widget(Select2::classname(), [
                'data' => $projects,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'project-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
            ])->label('Project *:');
            ?>
        </div>
    </div>

    <?= $form->field($model, 'inspection_date')->widget(DatePicker::className(), [
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'options' => ['placeholder' => 'Enter date', 'autocomplete' => 'off'],
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'yyyy-mm-dd'
            ],
            'pluginEvents' => [
                'changeDate' => "function(e) {
                    const dateReceived = $('#project-finding-form-inspection_date');
                    const dateActed = $('#project-finding-form-inspection_date-kvdate');
                    dateActed.val('');
                    dateActed.kvDatepicker('update', '');
                    dateActed.kvDatepicker('setStartDate', dateReceived.val());
                }",
            ]
        ])->label('Date of Inspection *'); 
    ?>

    <?= $form->field($model, 'major_finding')->textarea(['rows' => 4])->label('Major Finding/s *') ?>

    <?= $form->field($model, 'issues')->textarea(['rows' => 4])->label('Issues *') ?>

    <?= $form->field($model, 'action')->textarea(['rows' => 4])->label('Action/s Taken/Recommendation *') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
