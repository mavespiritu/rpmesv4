<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Agreement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="agreement-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <?= $form->field($model, 'year')->dropDownList(['' => '', $model->getYearsList()])->label('Year *'); ?>

    <?= $form->field($model, 'quarter')->dropDownList([ 'Q1' => 'Q1', 'Q2' => 'Q2', 'Q3' => 'Q3', 'Q4' => 'Q4', ], ['prompt' => ''])->label('Quarter *') ?>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <?= $form->field($model, 'project_id')->widget(Select2::classname(), [
                'data' => $projects,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'project-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
            ])->label('Project *');
            ?>
        </div>
    </div>

    <?= $form->field($model, 'date_of_pss')->widget(DatePicker::className(), [
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'options' => ['placeholder' => 'Enter date', 'autocomplete' => 'off'],
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'yyyy-mm-dd'
                ],
            ])->label('Facilitation Meeting Date *'); ?>

    <?= $form->field($model, 'agreement_reached')->textarea(['rows' => 6])->label('Agreements Reached *') ?>

    <?= $form->field($model, 'next_step')->textarea(['rows' => 6])->label('Next Step/s *') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
