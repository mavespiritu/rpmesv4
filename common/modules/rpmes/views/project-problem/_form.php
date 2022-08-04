<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
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
    ]); ?>

    <?= $form->field($model, 'year')->widget(Select2::classname(), [
        'data' => $model->YearsList,
        'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'year-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ])->label('Year *');
    ?>

    <?= $form->field($model, 'quarter')->dropDownList(['' => '', 'Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'])->label('Quarter *'); ?>

    <?= $form->field($model, 'project_id')->widget(Select2::classname(), [
        'data' => $projects,
        'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'project-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
    ])->label('Project *');
    ?>
        
    <div class="row">
        <div class="col-md-12 col-xs-12"><h4>Problems/Issues</h4><br>
            <?= $form->field($model, 'nature')->dropDownList([ 'Government / Funding Institution Approvals and Other Preconditions' => 'Government / Funding Institution Approvals and Other Preconditions', 'Design, Scope, Technical' => 'Design, Scope, Technical', 'Procurement' => 'Procurement', 'Site Condition / Availability' => 'Site Condition / Availability', 'Budget and Funds Flow' => 'Budget and Funds Flow', 'Inputs and Cost' => 'Inputs and Cost', 'Contract Management / Administration' => 'Contract Management / Administration', 'Project Monitoring Office, Manpower Capacity / Capability' => 'Project Monitoring Office, Manpower Capacity / Capability', 'Institutional Support' => 'Institutional Support', 'Legal and Policy Issuances' => 'Legal and Policy Issuances', 'Sustainability, Operations and Maintenance' => 'Sustainability, Operations and Maintenance', 'Force Majeure' => 'Force Majeure', 'Peace and Order Situation' => 'Peace and Order Situation', 'Others' => 'Others', ], ['prompt' => ''])->label('Nature *'); ?>

            <?= $form->field($model, 'detail')->textarea(['rows' => 4])->label('Details *') ?>
        </div>
    </div>

    <?= $form->field($model, 'strategy')->textarea(['rows' => 4])->label('Strategies / Actions Taken to Resolve the Problem / Issue *') ?>

    <?= $form->field($model, 'responsible_entity')->textarea(['rows' => 4])->label('Responsible Entities / Key Actors and Their Specific Assistance *') ?>

    <?= $form->field($model, 'lesson_learned')->textarea(['rows' => 4])->label('Lessons Learned and Good Practices that could be Shared to the NPMC / Other PMCs *') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
