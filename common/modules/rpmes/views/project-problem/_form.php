<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectProblem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-problem-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
        'layout' => 'horizontal',
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'wrapper' => 'col-sm-9',
            ],
        ],
    ]); ?>

    <?= $form->field($model, 'project_id')->widget(Select2::classname(), [
        'data' => $projects,
        'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'project-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
    ])->label('Project');
    ?>

    <?= $form->field($model, 'year')->textInput(['maxlength' => true, 'type' => 'number'])->label('Year') ?>
    
    <?= $form->field($model, 'nature')->widget(Select2::classname(), [
        'data' => $natures,
        'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'nature-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
    ])->label('Nature of the Problem');
    ?>

    <?= $form->field($model, 'detail')->textarea(['rows' => 4])->label('Problem Details') ?>

    <?= $form->field($model, 'strategy')->textarea(['rows' => 4])->label('Strategies/Actions Taken to Resolve the Problem/Issue') ?>

    <?= $form->field($model, 'responsible_entity')->textarea(['rows' => 4])->label('Responsible Entity/ Key Actors and Their Specific Assistance') ?>

    <?= $form->field($model, 'lesson_learned')->textarea(['rows' => 4])->label('Lessons learned and Good Practices that could be Shared to the NPMC/Other PMCs') ?>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="pull-right">
                <?= Html::submitButton('Save Record', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
            </div>
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