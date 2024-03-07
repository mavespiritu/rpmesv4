<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
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
        'layout' => 'horizontal',
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'wrapper' => 'col-sm-9',
            ],
        ],
    ]); ?>

    <?= $form->field($model, 'year')->textInput(['maxlength' => true, 'type' => 'number']) ?>

    <?= $form->field($model, 'quarter')->widget(Select2::classname(), [
        'data' => [
            'Q1' => 'Q1',
            'Q2' => 'Q2',
            'Q3' => 'Q3',
            'Q4' => 'Q4',
        ],
        'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'quarter-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ]);
    ?>

    <?= $form->field($model, 'project_id')->widget(Select2::classname(), [
        'data' => $projects,
        'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'project-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
    ]);
    ?>

    <?= $form->field($model, 'inspection_date')->widget(DatePicker::className(), [
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'options' => ['placeholder' => 'Enter date', 'autocomplete' => 'off'],
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'yyyy-mm-dd'
            ],
    ]); ?>

    <?= $form->field($model, 'site_details')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'major_finding')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'issues')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'action')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'action_to_be_taken')->textarea(['rows' => 3]) ?>

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