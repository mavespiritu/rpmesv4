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
/* @var $model common\modules\rpmes\models\Project */
/* @var $form yii\widgets\ActiveForm */
$js = '
jQuery(".expected_output_wrapper").on("afterInsert", function(e, item) {
    $(".col-sm-9.col-sm-offset-3").removeClass("col-sm-9 col-sm-offset-3").addClass("col-md-12 col-xs-12");
    jQuery(".expected_output_wrapper .expected-output-counter").each(function(index) {
        jQuery(this).html((index + 1))
    });
});
jQuery(".outcome_wrapper").on("afterInsert", function(e, item) {
    $(".col-sm-9.col-sm-offset-3").removeClass("col-sm-9 col-sm-offset-3").addClass("col-md-12 col-xs-12");
    jQuery(".outcome_wrapper .outcome-counter").each(function(index) {
        jQuery(this).html((index + 1))
    });
});

jQuery(".expected_output_wrapper").on("afterDelete", function(e, item) {
    jQuery(".expected_output_wrapper .expected-output-counter").each(function(index) {
        jQuery(this).html((index + 1))
    });
});
jQuery(".outcome_wrapper").on("afterDelete", function(e, item) {
    jQuery(".outcome_wrapper .outcome-counter").each(function(index) {
        jQuery(this).html((index + 1))
    });
});
';

$this->registerJs($js);
?>

<div class="project-form">
    <?php $form = ActiveForm::begin([
    	'options' => ['id' => 'project-form', 'class' => 'disable-submit-buttons', 'enctype' => 'multipart/form-data'],
        'layout' => 'horizontal',
        'enableAjaxValidation' => true,
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'wrapper' => 'col-sm-9',
            ],
        ],
    ]); ?>

    <div class="row">
    <?php if(Yii::$app->controller->action->id != 'carry-over'){ ?>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'period')->widget(Select2::classname(), [
                'data' => ['Current Year' => 'Current Year', 'Carry-Over' => 'Carry-Over'],
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'period-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Period *');
            ?>
        </div>
    <?php } ?>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'year')->textInput(['type' => 'number', 'min' => date("Y") - 1, 'max' => date("Y")])->label('Year *') ?>
        </div>    
    </div>

    <div class="row">
        <div class="col-md-6 col-xs-12">
            <h4>Basic Information</h4>
            <hr>
            <?= Yii::$app->user->can('Administrator') || Yii::$app->user->can('SuperAdministrator') ? $form->field($model, 'agency_id')->widget(Select2::classname(), [
                    'data' => $agencies,
                    'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'agency-select'],
                    'pluginOptions' => [
                        'allowClear' =>  true,
                    ],
                ])->label('Agency *') : ''
            ?>
            <?= $form->field($model, 'program_id')->widget(Select2::classname(), [
                    'initValueText' => $model->program ? $model->program->title : '',
                    'data' => $programs,
                    'options' => ['placeholder' => 'Search Program', 'class' => 'program-select'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['/rpmes/project/program-list']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(program) { return program.text; }'),
                        'templateSelection' => new JsExpression('function (program) { return program.text; }'),
                    ],
                ]); 
            ?>

            <?= $form->field($model, 'title')->textInput()->label('Project Title *') ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 2]) ?>

            <?= $form->field($categoryModel, 'category_id')->widget(Select2::classname(), [
                'data' => $categories,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'category-select'],
                'pluginOptions' => [
                    'allowClear' =>  false,
                ],
                'pluginEvents'=>[
                    'select2:select'=>'
                        function(){
                            $.ajax({
                                url: "'.Url::to(['/rpmes/project/kra-list']).'",
                                data: {
                                    id: this.value,
                                    }
                            }).done(function(result) {
                                $(".kra-select").html("").select2({ data:result, multiple: false, theme:"krajee", width:"100%",placeholder:"Select one", allowClear: true});
                                $(".kra-select").select2("val","");
                            });
                        }'
    
                ]
                ])->label('Category *');
            ?>

            <?= $form->field($kraModel, 'key_result_area_id')->widget(Select2::classname(), [
                'data' => $kras,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'kra-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>

            <?= $form->field($model, 'sector_id')->widget(Select2::classname(), [
                'data' => $sectors,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'sector-select'],
                'pluginOptions' => [
                    'allowClear' =>  false,
                ],
                'pluginEvents'=>[
                    'select2:select'=>'
                        function(){
                            $.ajax({
                                url: "'.Url::to(['/rpmes/project/sub-sector-list']).'",
                                data: {
                                        id: this.value,
                                    }
                            }).done(function(result) {
                                $(".sub-sector-select").html("").select2({ data:result, theme:"krajee", width:"100%",placeholder:"Select one", allowClear: true});
                                $(".sub-sector-select").select2("val","");
                            });
                        }'
    
                ]
                ])->label('Sector *');
            ?>

            <?= $form->field($model, 'sub_sector_id')->widget(Select2::classname(), [
                'data' => $subSectors,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'sub-sector-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Sub-Sector *');
            ?>

            <h4>RDP-related Information</h4>
            <hr>
            
            <?= $form->field($sdgModel, 'sdg_goal_id')->widget(Select2::classname(), [
                'data' => $goals,
                'options' => ['multiple' => true, 'placeholder' => 'Select one or more', 'class'=>'sdg-goal-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('SDG Goal *');
            ?>

            <?= $form->field($rdpChapterModel, 'rdp_chapter_id')->widget(Select2::classname(), [
                'data' => $chapters,
                'options' => ['multiple' => true, 'placeholder' => 'Select one or more', 'class'=>'rdp-chapter-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                'pluginEvents'=>[
                    'select2:select select2:unselect'=>'
                        function(){
                            $.ajax({
                                url: "'.Url::to(['/rpmes/project/chapter-outcome-list']).'",
                                dataType: "JSON",
                                data: {
                                        id: JSON.stringify($("#projectrdpchapter-rdp_chapter_id").select2("data")),
                                    }
                            }).done(function(result) {
                                $(".rdp-chapter-outcome-select").html("").select2({ data:result, multiple:true, theme:"krajee", width:"100%",placeholder:"Select one or more", allowClear: true});
                                $(".rdp-chapter-outcome-select").select2("val","");
                                $(".rdp-sub-chapter-outcome-select").select2("val","");
                            });
                            $.ajax({
                                url: "'.Url::to(['/rpmes/project/chapter-to-sub-chapter-outcome-list']).'",
                                dataType: "JSON",
                                data: {
                                        id: JSON.stringify($("#projectrdpchapter-rdp_chapter_id").select2("data")),
                                    }
                            }).done(function(result) {
                                $(".rdp-sub-chapter-outcome-select").html("").select2({ data:result, multiple:true, theme:"krajee", width:"100%",placeholder:"Select one or more", allowClear: true});
                                $(".rdp-sub-chapter-outcome-select").select2("val","");
                            });
                        }'
    
                ]
                ])->label('RDP Chapter *');
            ?>

            <?= $form->field($rdpChapterOutcomeModel, 'rdp_chapter_outcome_id')->widget(Select2::classname(), [
                'data' => $chapterOutcomes,
                'options' => ['multiple' => true, 'placeholder' => 'Select one or more', 'class'=>'rdp-chapter-outcome-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                'pluginEvents'=>[
                    'select2:select select2:unselect'=>'
                        function(){
                            $.ajax({
                                url: "'.Url::to(['/rpmes/project/sub-chapter-outcome-list']).'",
                                dataType: "JSON",
                                data: {
                                        id: JSON.stringify($("#projectrdpchapteroutcome-rdp_chapter_outcome_id").select2("data")),
                                    }
                            }).done(function(result) {
                                $(".rdp-sub-chapter-outcome-select").html("").select2({ data:result, multiple:true, theme:"krajee", width:"100%",placeholder:"Select one or more", allowClear: true});
                                $(".rdp-sub-chapter-outcome-select").select2("val","");
                            });
                        }'
    
                ]
                ]);
            ?>

            <?= $form->field($rdpSubChapterOutcomeModel, 'rdp_sub_chapter_outcome_id')->widget(Select2::classname(), [
                'data' => $subChapterOutcomes,
                'options' => ['multiple' => true, 'placeholder' => 'Select one or more', 'class'=>'rdp-sub-chapter-outcome-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>

            <h4>Expected Output</h4>
            <hr>
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'expected_output_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.expected-output-items', // required: css class selector
                'widgetItem' => '.expected-output-item', // required: css class
                'limit' => 10, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-expected-output-item', // css class
                'deleteButton' => '.remove-expected-output-item', // css class
                'model' => $expectedOutputModels[0],
                'formId' => 'project-form',
                'formFields' => [
                    'indicator',
                    'target',
                ],
            ]); ?>
            
            <table class="table table-bordered table-condensed table-responsive">
                <thead>
                    <tr>
                        <td align=center><b>#</b></td>
                        <td align=center><b>Output Indicator *</b></td>
                        <td align=center><b>Target Output *</b></td>
                        <td><button type="button" class="pull-right add-expected-output-item btn btn-success btn-xs"><i class="fa fa-plus"></i></button></td>
                    </tr>
                </thead>
                <tbody class="expected-output-items">
                <?php foreach ($expectedOutputModels as $eoIdx => $expectedOutputModel){ ?>
                    <?php
                        // necessary for update action.
                        if (!$expectedOutputModel->isNewRecord) {
                            echo Html::activeHiddenInput($expectedOutputModel, "[{$eoIdx}]id");
                        }
                    ?>
                    <tr class="expected-output-item">
                        <td class="expected-output-counter"><?= ($eoIdx + 1) ?></td>
                        <td><?= $form->field($expectedOutputModel, "[{$eoIdx}]indicator")->textInput(['maxlength' => true])->label(false) ?></td>
                        <td><?= $form->field($expectedOutputModel, "[{$eoIdx}]target")->textInput(['maxlength' => true])->label(false) ?></td>
                        <td><button type="button" class="pull-right remove-expected-output-item btn btn-danger btn-xs"><i class="fa fa-minus"></i></button></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php DynamicFormWidget::end(); ?>

        </div>
        <div class="col-md-6">
            <h4>&nbsp;</h4>
            <hr>
            <?= $form->field($model, 'mode_of_implementation_id')->widget(Select2::classname(), [
                'data' => $modes,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'mode-of-implementation-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Mode of Implementation *');
            ?>

            <?= $form->field($model, 'other_mode')->textInput() ?>

            <?= $form->field($model, 'fund_source_id')->widget(Select2::classname(), [
                'data' => $fundSources,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'fund-source-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Fund Source *');
            ?>

            <?= $form->field($model, 'typhoon')->textInput(['maxlength' => true]) ?>
            <h4>Timeline</h4>
            <hr>
            <?= $form->field($model, 'start_date')->widget(DatePicker::className(), [
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'options' => ['placeholder' => 'Enter date', 'autocomplete' => 'off'],
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'yyyy-mm-dd'
                ],
                'pluginEvents' => [
                    'changeDate' => "function(e) {
                        const dateReceived = $('#project-start_date');
                        const dateActed = $('#project-completion_date-kvdate');
                        dateActed.val('');
                        dateActed.kvDatepicker('update', '');
                        dateActed.kvDatepicker('setStartDate', dateReceived.val());
                    }",
                ]
            ])->label('Start Date *'); ?>

            <?= $form->field($model, 'completion_date')->widget(DatePicker::className(), [
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'options' => ['placeholder' => 'Enter date', 'autocomplete' => 'off'],
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'yyyy-mm-dd'
                ],
            ])->label('Completion Date *'); ?>

            <h4>Location</h4>
            <hr>
            <?php /* $form->field($model, 'location_scope_id')->widget(Select2::classname(), [
                'data' => $scopes,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'location-scope-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]); */
            ?>
            <?= $form->field($regionModel, 'region_id')->widget(Select2::classname(), [
                'data' => $regions,
                'options' => ['multiple' => true, 'placeholder' => 'Select one or more', 'class'=>'region-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                'pluginEvents'=>[
                    'select2:select select2:unselect'=>'
                        function(){
                            $.ajax({
                                url: "'.Url::to(['/rpmes/project/province-list']).'",
                                dataType: "JSON",
                                data: {
                                        id: JSON.stringify($("#projectregion-region_id").select2("data")),
                                    }
                            }).done(function(result) {
                                $(".province-select").html("").select2({ data:result, multiple:true, theme:"krajee", width:"100%",placeholder:"Select one or more", allowClear: true});
                                $(".province-select").select2("val","");
                                $(".citymun-select").select2("val","");
                                $(".barangay-select").select2("val","");
                            });
                        }'
    
                ]
                ])->label('Region *');
            ?>
            <?= $form->field($provinceModel, 'province_id')->widget(Select2::classname(), [
                'data' => $provinces,
                'options' => ['multiple' => true, 'placeholder' => 'Select one or more', 'class'=>'province-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                'pluginEvents'=>[
                    'select2:select select2:unselect'=>'
                        function(){
                            $.ajax({
                                url: "'.Url::to(['/rpmes/project/citymun-list']).'",
                                dataType: "JSON",
                                data: {
                                        id: JSON.stringify($("#projectprovince-province_id").select2("data")),
                                    }
                            }).done(function(result) {
                                $(".citymun-select").html("").select2({ data:result, multiple:true, theme:"krajee", width:"100%",placeholder:"Select one or more", allowClear: true});
                                $(".citymun-select").select2("val","");
                                $(".barangay-select").select2("val","");
                            });
                        }'
    
                ]
                ]);
            ?>
            <?= $form->field($citymunModel, 'citymun_id')->widget(Select2::classname(), [
                'data' => $citymuns,
                'options' => ['multiple' => true, 'placeholder' => 'Select one or more', 'class'=>'citymun-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                'pluginEvents'=>[
                    'select2:select select2:unselect'=>'
                        function(){
                            $.ajax({
                                url: "'.Url::to(['/rpmes/project/barangay-list']).'",
                                dataType: "JSON",
                                data: {
                                        id: JSON.stringify($("#projectcitymun-citymun_id").select2("data")),
                                    }
                            }).done(function(result) {
                                $(".barangay-select").html("").select2({ data:result, multiple:true, theme:"krajee", width:"100%",placeholder:"Select one or more", allowClear: true});
                                $(".barangay-select").select2("val","");
                            });
                        }'
    
                ]
                ]);
            ?>
            <?= $form->field($barangayModel, 'barangay_id')->widget(Select2::classname(), [
                'data' => $barangays,
                'options' => ['multiple' => true, 'placeholder' => 'Select one or more', 'class'=>'barangay-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <h4>Specific Project Outcome</h4>
            <hr>
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'outcome_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.outcome-items', // required: css class selector
                'widgetItem' => '.outcome-item', // required: css class
                'limit' => 10, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-outcome-item', // css class
                'deleteButton' => '.remove-outcome-item', // css class
                'model' => $outcomeModels[0],
                'formId' => 'project-form',
                'formFields' => [
                    'outcome',
                    'performance_indicator',
                    'target',
                    'timeline',
                    'remarks'
                ],
            ]); ?>
            
            <table class="table table-bordered table-condensed table-responsive">
                <thead>
                    <tr>
                        <td align=center><b>#</b></td>
                        <td align=center><b>Outcome</b></td>
                        <td align=center><b>Performance Indicator</b></td>
                        <td align=center><b>Target</b></td>
                        <td align=center><b>Timeline</b></td>
                        <td align=center><b>Remarks</b></td>
                        <td><button type="button" class="pull-right add-outcome-item btn btn-success btn-xs"><i class="fa fa-plus"></i></button></td>
                    </tr>
                </thead>
                <tbody class="outcome-items">
                <?php foreach ($outcomeModels as $oIdx => $outcomeModel){ ?>
                    <?php
                        // necessary for update action.
                        if (!$outcomeModel->isNewRecord) {
                            echo Html::activeHiddenInput($outcomeModel, "[{$oIdx}]id");
                        }
                    ?>
                    <tr class="outcome-item">
                        <td class="outcome-counter"><?= ($oIdx + 1) ?></td>
                        <td><?= $form->field($outcomeModel, "[{$oIdx}]outcome")->textInput(['maxlength' => true])->label(false) ?></td>
                        <td><?= $form->field($outcomeModel, "[{$oIdx}]performance_indicator")->textInput(['maxlength' => true])->label(false) ?></td>
                        <td><?= $form->field($outcomeModel, "[{$oIdx}]target")->textInput(['maxlength' => true])->label(false) ?></td>
                        <td><?= $form->field($outcomeModel, "[{$oIdx}]timeline")->textInput(['maxlength' => true])->label(false) ?></td>
                        <td><?= $form->field($outcomeModel, "[{$oIdx}]remarks")->textInput(['maxlength' => true])->label(false) ?></td>
                        <td><button type="button" class="pull-right remove-outcome-item btn btn-danger btn-xs"><i class="fa fa-minus"></i></button></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php DynamicFormWidget::end(); ?>
            <h4>Quarterly Target Setting</h4>
            <hr>
            <div class="row">
                <div class="col-md-6 col-xs-12">
                    <?= $form->field($model, 'data_type')->widget(Select2::classname(), [
                        'data' => ['Default' => 'Default', 'Maintained' => 'Maintained', 'Cumulative' => 'Cumulative'],
                        'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'data-type-select'],
                        'pluginOptions' => [
                            'allowClear' =>  true,
                        ],
                        ])->label('Data Type *');
                    ?>
                </div>
            </div>
            <table class="table table-bordered table-condensed table-responsive">
                <thead>
                    <tr>
                        <td align=center rowspan=2 colspan=2 style="width: 20%;"><b>Type</b></td>
                        <td align=center rowspan=2 style="width: 20%;"><b>Indicator *</b></td>
                        <td align=center colspan=4><b>Quarterly Targets</b></td>
                    </tr>
                    <tr>
                        <?php if(!empty($quarters)){ ?>
                            <?php foreach($quarters as $q => $quarter){ ?>
                                <td align=center><b><?= $q ?>*</b></td>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan=2 align=right>Physical Targets</td>
                        <td>
                            <?= $form->field($targets[0], "[0]indicator")->textInput(['type' => 'text'])->label(false) ?>
                            <i class="fa fa-exclamation-circle"></i> Include keyword "%" for indicator using percent as unit of measure
                        </td>
                        <td align=center>
                            <?= $form->field($targets[0], "[0]q1")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q1-physical-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[0]['q1'] != '' ? $targets[0]['q1'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                        <td align=center>
                            <?= $form->field($targets[0], "[0]q2")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q2-physical-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[0]['q2'] != '' ? $targets[0]['q2'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                        <td align=center>
                            <?= $form->field($targets[0], "[0]q3")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q3-physical-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[0]['q3'] != '' ? $targets[0]['q3'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                        <td align=center>
                            <?= $form->field($targets[0], "[0]q4")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q4-physical-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[0]['q4'] != '' ? $targets[0]['q4'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=2 align=right>Financial Targets</td>
                        <td>&nbsp;</td>
                        <td align=center>
                            <?= $form->field($targets[1], "[1]q1")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q1-financial-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[1]['q1'] != '' ? $targets[1]['q1'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                        <td align=center>
                            <?= $form->field($targets[1], "[1]q2")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q2-financial-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[1]['q2'] != '' ? $targets[1]['q2'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                        <td align=center>
                            <?= $form->field($targets[1], "[1]q3")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q3-financial-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[1]['q3'] != '' ? $targets[1]['q3'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                        <td align=center>
                            <?= $form->field($targets[1], "[1]q4")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q4-financial-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[1]['q4'] != '' ? $targets[1]['q4'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                    </tr>
                    <tr>
                        <td rowspan=2 align=right>Persons Employed</td>
                        <td align=right>Male</td>
                        <td align=center>&nbsp;</td>
                        <td align=center>
                            <?= $form->field($targets[2], "[2]q1")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q1-male-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[2]['q1'] != '' ? $targets[2]['q1'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                        <td align=center>
                            <?= $form->field($targets[2], "[2]q2")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q2-male-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[2]['q2'] != '' ? $targets[2]['q2'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                        <td align=center>
                            <?= $form->field($targets[2], "[2]q3")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q3-male-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[2]['q3'] != '' ? $targets[2]['q3'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                        <td align=center>
                            <?= $form->field($targets[2], "[2]q4")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q4-male-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[2]['q4'] != '' ? $targets[2]['q4'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                    </tr>
                    <tr>
                        <td align=right>Female</td>
                        <td align=center>&nbsp;</td>
                        <td align=center>
                            <?= $form->field($targets[3], "[3]q1")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q1-female-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[3]['q1'] != '' ? $targets[3]['q1'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                        <td align=center>
                            <?= $form->field($targets[3], "[3]q2")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q2-female-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[3]['q2'] != '' ? $targets[3]['q2'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                        <td align=center>
                            <?= $form->field($targets[3], "[3]q3")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q3-female-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[3]['q3'] != '' ? $targets[3]['q3'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                        <td align=center>
                            <?= $form->field($targets[3], "[3]q4")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q4-female-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[3]['q4'] != '' ? $targets[3]['q4'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                    </tr>
                    <tr>
                        <td rowspan=2 align=right>No. of Beneficiaries</td>
                        <td align=right>Individual</td>
                        <td>&nbsp;</td>
                        <td align=center>
                            <?= $form->field($targets[4], "[4]q1")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q1-beneficiary-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[4]['q1'] != '' ? $targets[4]['q1'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                        <td align=center>
                            <?= $form->field($targets[4], "[4]q2")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q2-beneficiary-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[4]['q2'] != '' ? $targets[4]['q2'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                        <td align=center>
                            <?= $form->field($targets[4], "[4]q3")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q3-beneficiary-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[4]['q3'] != '' ? $targets[4]['q3'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                        <td align=center>
                            <?= $form->field($targets[4], "[4]q4")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q4-beneficiary-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[4]['q4'] != '' ? $targets[4]['q4'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                    </tr>
                    <tr>
                        <td align=right>Group</td>
                        <td><?= $form->field($targets[5], "[5]indicator")->textInput(['type' => 'text'])->label(false) ?></td>
                        <td align=center>
                            <?= $form->field($targets[5], "[5]q1")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q1-group-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[5]['q1'] != '' ? $targets[5]['q1'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                        <td align=center>
                            <?= $form->field($targets[5], "[5]q2")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q2-group-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[5]['q2'] != '' ? $targets[5]['q2'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                        <td align=center>
                            <?= $form->field($targets[5], "[5]q3")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q3-group-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[5]['q3'] != '' ? $targets[5]['q3'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                        <td align=center>
                            <?= $form->field($targets[5], "[5]q4")->widget(MaskedInput::classname(), [
                                'options' => [
                                    'id' => 'q4-group-input',
                                    'autocomplete' => 'off',
                                    'value' => $targets[5]['q4'] != '' ? $targets[5]['q4'] : 0,
                                ],
                                'clientOptions' => [
                                    'alias' =>  'decimal',
                                    'removeMaskOnSubmit' => true,
                                    'groupSeparator' => ',',
                                    'autoGroup' => true
                                ],
                            ])->label(false) ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <h4>Attach Project Profile</h4>
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
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="pull-right">
                <?= $model->draft == 'Yes' || $model->draft == '' ? Html::button('Save Project as Draft', ['class' => 'btn btn-primary', 'id' => 'save-draft-btn', 'data' => ['disabled-text' => 'Please Wait']]) : '' ?>
                <?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? Html::submitButton('Save Project to Monitoring Plan', ['class' => 'btn btn-success', 'onclick' => '$("#file-input").fileinput("upload");', 'data' => ['disabled-text' => 'Please Wait']]) : '' : '' ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<?php
    $script = '
    $(document).ready(function(){
        $(".col-sm-9.col-sm-offset-3").removeClass("col-sm-9 col-sm-offset-3").addClass("col-md-12 col-xs-12");
        $("#project-typhoon").prop("disabled", "disabled");

        if($("#project-period").val() == "Carry-Over"){            
            $("#project-source_id").removeAttr("disabled");
        }else{
            $("#project-source_id").prop("disabled", "disabled");
        }

        if($("#project-mode_of_implementation_id").val() == 3){            
            $("#project-other_mode").removeAttr("disabled");
        }else{
            $("#project-other_mode").prop("disabled", "disabled");
        }
        
        if($("#project-fund_source_id").val() != ""){            
            $("#project-typhoon").removeAttr("disabled");
        }else{
            $("#project-typhoon").prop("disabled", "disabled");
        }

        $("#project-period").on("change", function(){
            if($("#project-period").val() == "Carry-Over"){            
                $("#project-source_id").removeAttr("disabled");
            }else{
                $("#project-source_id").prop("disabled", "disabled");
            }
        });
        $("#project-mode_of_implementation_id").on("change", function(){
            if($("#project-mode_of_implementation_id").val() == 3){            
                $("#project-other_mode").removeAttr("disabled");
            }else{
                $("#project-other_mode").prop("disabled", "disabled");
            }
        });
        if($("#project-typhoon").val() != ""){
            $("#project-typhoon").removeAttr("disabled");
        }else{
            $("#project-typhoon").prop("disabled", "disabled");
        }

        $("#project-fund_source_id").on("change", function(){
            if($("#project-fund_source_id").val() != ""){            
                $("#project-typhoon").removeAttr("disabled");
            }else{
                $("#project-typhoon").prop("disabled", "disabled");
            }
        });
    });     

    $("#save-draft-btn").on("click", function (e) {
        e.preventDefault();
     
        var form = $("#project-form");
        var formData = form.serialize();
        
        $.ajax({
           type: "POST",
           url: "'.Url::to(['/rpmes/project/save-draft']).'",
           data: formData,
           success: function (data) {
            
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