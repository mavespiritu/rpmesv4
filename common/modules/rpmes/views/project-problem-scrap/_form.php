<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveField;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use kartik\typeahead\Typeahead;
use yii\web\View;
use yii\widgets\MaskedInput;
use kartik\daterange\DateRangePicker;
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\date\DatePicker;
use \file\components\AttachmentsInput;
use yii\web\JsExpression;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectProblem */
/* @var $form yii\widgets\ActiveForm */
$js = '
jQuery(".problem_wrapper").on("afterInsert", function(e, item) {
    jQuery(".problem_wrapper .problem-counter").each(function(index) {
        jQuery(this).html((index + 1))
    });
});

jQuery(".problem_wrapper").on("afterDelete", function(e, item) {
    jQuery(".problem_wrapper .problem-counter").each(function(index) {
        jQuery(this).html((index + 1))
    });
});
';
$this->registerJs($js);
?>

<div class="project-problem-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['id' => 'project-problem-form', 'class' => 'disable-submit-buttons',],
    ]); ?>
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
    <div class="row">
        <div class="col-md-12 col-xs-12">
        <h4>Project Issues/Problems</h4>
            <hr>
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'problem_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.problem-items', // required: css class selector
                'widgetItem' => '.problem-item', // required: css class
                'limit' => 10, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-problem-item', // css class
                'deleteButton' => '.remove-problem-item', // css class
                'model' => $problemModels[0],
                'formId' => 'project-problem-form',
                'formFields' => [
                    'nature',
                    'detail',
                    'strategy',
                    'responsible_entity',
                    'lesson_learned'
                ],
            ]); ?>
            
            <table class="table table-bordered table-condensed table-responsive">
                <thead>
                    <tr>
                        <td align=center><b>#</b></td>
                        <td align=center><b>Nature</b></td>
                        <td align=center><b>Details</b></td>
                        <td align=center><b>Strategies / Actions Taken to Resolve the Problem / Issue</b></td>
                        <td align=center><b>Responsible Entities / Key Actors and Their Specific Assistance</b></td>
                        <td align=center><b>Lessons Learned and Good Practices that could be Shared to the NPMC / Other PMCs</b></td>
                        <td><button type="button" class="pull-right add-problem-item btn btn-success btn-xs"><i class="fa fa-plus"></i></button></td>
                    </tr>
                </thead>
                <tbody class="problem-items">
                <?php foreach ($problemModels as $oIdx => $problemModel){ ?>
                    <?php
                        // necessary for update action.
                        if (!$problemModel->isNewRecord) {
                            echo Html::activeHiddenInput($problemModel, "[{$oIdx}]id");
                        }
                    ?>
                    <tr class="problem-item">
                        <td class="problem-counter"><?= ($oIdx + 1) ?></td>
                        <td><?= $form->field($problemModel, "[{$oIdx}]nature")->dropDownList([ 'Government / Funding Institution Approvals and Other Preconditions' => 'Government / Funding Institution Approvals and Other Preconditions', 'Design, Scope, Technical' => 'Design, Scope, Technical', 'Procurement' => 'Procurement', 'Site Condition / Availability' => 'Site Condition / Availability', 'Budget and Funds Flow' => 'Budget and Funds Flow', 'Inputs and Cost' => 'Inputs and Cost', 'Contract Management / Administration' => 'Contract Management / Administration', 'Project Monitoring Office, Manpower Capacity / Capability' => 'Project Monitoring Office, Manpower Capacity / Capability', 'Institutional Support' => 'Institutional Support', 'Legal and Policy Issuances' => 'Legal and Policy Issuances', 'Sustainability, Operations and Maintenance' => 'Sustainability, Operations and Maintenance', 'Force Majeure' => 'Force Majeure', 'Peace and Order Situation' => 'Peace and Order Situation', 'Others' => 'Others', ], ['prompt' => ''])->label(false) ?></td>
                        <td><?= $form->field($problemModel, "[{$oIdx}]detail")->textarea(['rows' => 2])->label(false) ?></td>
                        <td><?= $form->field($problemModel, "[{$oIdx}]strategy")->textarea(['rows' => 2])->label(false) ?></td>
                        <td><?= $form->field($problemModel, "[{$oIdx}]responsible_entity")->textarea(['rows' => 2])->label(false) ?></td>
                        <td><?= $form->field($problemModel, "[{$oIdx}]lesson_learned")->textarea(['rows' => 2])->label(false) ?></td>
                        <td><button type="button" class="pull-right remove-problem-item btn btn-danger btn-xs"><i class="fa fa-minus"></i></button></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php DynamicFormWidget::end(); ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
