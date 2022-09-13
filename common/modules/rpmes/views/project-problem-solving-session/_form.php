<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectProblemSolvingSession */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-problem-solving-session-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <?= $form->field($model, 'year')->textInput() ?>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <?= $form->field($model, 'quarter')->widget(Select2::classname(), [
                'data' => $quarters,
                'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'quarter-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Quarter *');
            ?>
        </div>
    </div>


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

    <?= $form->field($model, 'pss_date')->widget(DatePicker::className(), [
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'options' => ['placeholder' => 'Enter date', 'autocomplete' => 'off'],
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'yyyy-mm-dd'
            ],
            'pluginEvents' => [
                'changeDate' => "function(e) {
                    const dateReceived = $('#project-problem-solving-session-form-pss_date');
                    const dateActed = $('#project-problem-solving-session-form-pss_date-kvdate');
                    dateActed.val('');
                    dateActed.kvDatepicker('update', '');
                    dateActed.kvDatepicker('setStartDate', dateReceived.val());
                }",
            ]
        ])->label('Project Solving Session Date *:');
    ?>

    <?= $form->field($model, 'agreement_reached')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'next_step')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
