<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
use kartik\daterange\DateRangePicker;
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\date\DatePicker;
use \file\components\AttachmentsInput;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Resolution */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="resolution-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <?= $form->field($model, 'resolution_number')->textInput() ?>

    <?= $form->field($model, 'resolution')->textarea(['rows' => 2])->label('Resolution *'); ?>

    <?= $form->field($model, 'date_approved')->widget(DatePicker::className(), [
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'options' => ['placeholder' => 'Enter date', 'autocomplete' => 'off'],
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'yyyy-mm-dd'
                ],
            ])->label('Date Approved *'); ?>

    <?= $form->field($model, 'rpmc_action')->textarea(['rows' => 2])->label('RPMC Action *'); ?>

    <?= $form->field($model, 'quarter')->dropDownList(['' => '', 'Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'])->label('Quarter *'); ?>

    <?= $form->field($model, 'year')->dropDownList(['' => '', $model->getYearsList()])->label('Year *'); ?>

    <h4>Scanned File</h4>
            <hr>
            <div class="row" style="margin-left: 1%;">
                <div class="col-md-3 col-xs-12">
                    <?= empty($model->files) ? AttachmentsInput::widget([
                        'id' => 'file-input', // Optional
                        'model' => $model,
                        'options' => [ 
                            'multiple' => false, 
                            'required' => 'required'
                        ],
                        'pluginOptions' => [ 
                            'showPreview' => false,
                            'showUpload' => false,
                            'maxFileCount' => 1,
                        ]
                    ]) : AttachmentsInput::widget([
                        'id' => 'file-input', // Optional
                        'model' => $model,
                        'options' => [ 
                            'multiple' => false, 
                        ],
                        'pluginOptions' => [ 
                            'showPreview' => false,
                            'showUpload' => false,
                            'maxFileCount' => 1,
                        ]
                    ]) ?>
                    <p>Allowed file types: pdf (max 2MB)</p>
                    <?= \file\components\AttachmentsTable::widget(['model' => $model]) ?>
                    <?= $form->field($model, 'id')->hiddenInput(['value' => $model->id])->label(false) ?>
                </div>
            </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
