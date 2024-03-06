<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
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
        'options' => ['enctype' => 'multipart/form-data'],
        'layout' => 'horizontal',
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'wrapper' => 'col-sm-9',
            ],
        ],
    ]); ?>

    <?= $form->field($model, 'year')->textInput(['maxlength' => true, 'type' => 'number'])->label('Year') ?>

    <?= $form->field($model, 'resolution_number')->textInput(['maxlength' => true])->label('Resolution No.') ?>

    <?= $form->field($model, 'resolution_title')->textInput(['maxlength' => true])->label('Resolution Title') ?>

    <?= $form->field($model, 'date_approved')->widget(DatePicker::className(), [
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'options' => ['placeholder' => 'Enter date', 'autocomplete' => 'off'],
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'yyyy-mm-dd'
            ],
    ])->label('Date Approved'); ?>

    <?= $form->field($model, 'resolution')->textarea(['rows' => 2])->label('Resolution (Specific actions done by the RPMC, or additional information if the title does not sufficiently describe the resolution)'); ?>

    <?= $form->field($model, 'resolution_url')->textInput(['maxlength' => true])->label('Link to the Resolution') ?>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="form-group">
                <label class="control-label col-sm-3" for="project-attachments">Scanned Copy of Resolution</label>
                <div class="col-sm-9"
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <?= AttachmentsInput::widget([
                                'id' => 'file-input', // Optional
                                'model' => $model,
                                'options' => [ 
                                    'multiple' => true
                                ],
                                'pluginOptions' => [ 
                                    'showPreview' => false,
                                    'showUpload' => false,
                                    'maxFileCount' => 1,
                                ]
                            ]) ?>
                            <p style="text-align: right">Allowed file types: jpg, png, pdf (max 5MB each)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="pull-right">
                <?= Html::submitButton('Save Record', ['class' => 'btn btn-success']) ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<style>
    label.control-label{
        font-weight: bolder;
    }
    hr{
        opacity: 0.10;
    }
</style>