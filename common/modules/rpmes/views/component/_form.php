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
use buttflatteryormwizard\FormWizard;
use dosamigos\switchery\Switchery;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Project */
/* @var $form yii\widgets\ActiveForm */
$months = [
    'jan' => 'Jan',
    'feb' => 'Feb',
    'mar' => 'Mar',
    'apr' => 'Apr',
    'may' => 'May',
    'jun' => 'Jun',
    'jul' => 'Jul',
    'aug' => 'Aug',
    'sep' => 'Sep',
    'oct' => 'Oct',
    'nov' => 'Nov',
    'dec' => 'Dec',
];

$js = '
jQuery(".fund_source_wrapper").on("afterInsert", function(e, item) {
    $(".col-sm-9.col-sm-offset-3").removeClass("col-sm-9 col-sm-offset-3").addClass("col-md-12 col-xs-12");
    jQuery(".fund_source_wrapper .fund_source-counter").each(function(index) {
        jQuery(this).html((index + 1))
    });
});
jQuery(".revised_schedule_wrapper").on("afterInsert", function(e, item) {
    $(".col-sm-9.col-sm-offset-3").removeClass("col-sm-9 col-sm-offset-3").addClass("col-md-12 col-xs-12");
    jQuery(".revised_schedule_wrapper .revised_schedule-counter").each(function(index) {
        jQuery(this).html((index + 1))
    });

    $( ".revised_start_date" ).each(function() {
        $( this ).datepicker({
           dateFormat : "yy-mm-dd",
           yearRange : "1925:+10",
           maxDate : "-1D",
           changeMonth: true,
           changeYear: true
        });
   });     
   
   $( ".revised_end_date" ).each(function() {
        $( this ).datepicker({
        dateFormat : "yy-mm-dd",
        yearRange : "1925:+10",
        maxDate : "-1D",
        changeMonth: true,
        changeYear: true
        });
    });     

    var datePickers = $(item).find("[data-krajee-kvdatepicker]");
    datePickers.each(function(index, el) {
        // Destroy the existing instance
        $(el).kvDatepicker("destroy");

        // Initialize the datepicker manually with the desired format
        $(el).kvDatepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
        });

        if ($(el).attr("name").includes("start_date")) {
            // Handle the changeDate event for the start date picker
            $(el).on("changeDate", function(e) {
                // Clear the associated end date when the start date changes
                var endIndex = $(el).attr("name").replace("start_date", "end_date");
                var endDatePicker = $(item).find("[name=\'" + endIndex.replace(/\'/g, "\\\\\'") + "\']");
                endDatePicker.val("").kvDatepicker("update");
    
                // Set the minimum date for the associated end date picker
                var selectedStartDate = e.date;
                endDatePicker.kvDatepicker("setStartDate", selectedStartDate);
            });
        }
    });
});

jQuery(".revised_schedule_wrapper").on("afterDelete", function(e, item) {
    jQuery(".revised_schedule_wrapper .revised_schedule-counter").each(function(index) {
        jQuery(this).html((index + 1))
    });
});
jQuery(".fund_source_wrapper").on("afterDelete", function(e, item) {
    jQuery(".fund_source_wrapper .fund_source-counter").each(function(index) {
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
        //'enableAjaxValidation' => true,
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'wrapper' => 'col-sm-9',
            ],
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <?= Yii::$app->user->can('Administrator') || Yii::$app->user->can('SuperAdministrator') ? $form->field($model, 'agency_id')->widget(Select2::classname(), [
                    'data' => $agencies,
                    'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'agency-select'],
                    'pluginOptions' => [
                        'allowClear' =>  true,
                    ],
                ])->label('Agency') : ''
            ?>

            <?= $form->field($model, 'source_id')->widget(Select2::classname(), [
                'data' => $projects,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'source-select'],
                'pluginOptions' => [
                    'allowClear' =>  false,
                ]
                ])->label('Mother Project');
            ?>

            <?= $form->field($model, 'title')->textarea(['rows' => 2, 'style' => 'resize: none;'])->label('Program/Project Title') ?>

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
                ])->label('Sector');
            ?>

            <?= $form->field($model, 'sub_sector_id')->widget(Select2::classname(), [
                'data' => $subSectors,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'sub-sector-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Sub-Sector');
            ?>

            <?= $form->field($model, 'cost')->widget(MaskedInput::classname(), [
                'options' => [
                    'autocomplete' => 'off',
                ],
                'clientOptions' => [
                    'alias' =>  'decimal',
                    'removeMaskOnSubmit' => true,
                    'groupSeparator' => ',',
                    'autoGroup' => true
                ],
            ])->label('Total Project Cost (in PhP)') ?>

            <?= $form->field($model, 'mode_of_implementation_id')->widget(Select2::classname(), [
                'data' => $modes,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'mode-of-implementation-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Mode of Implementation');
            ?>

            <div id="div_mode_name">
                <?= $form->field($model, 'mode_name')->textInput()->label('Mode Name') ?>
            </div>

            <div id="div_other_mode">
                <?= $form->field($model, 'other_mode')->textInput()->label('Other mode? Please specify') ?>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-3" for="fund_sources">Funding Sources</label>
                <div class="col-sm-9">
                    <?php DynamicFormWidget::begin([
                        'widgetContainer' => 'fund_source_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.fund-source-items', // required: css class selector
                        'widgetItem' => '.fund-source-item', // required: css class
                        'limit' => 5, // the maximum times, an element can be cloned (default 999)
                        'min' => 1, // 0 or 1 (default 1)
                        'insertButton' => '.add-fund-source-item', // css class
                        'deleteButton' => '.remove-fund-source-item', // css class
                        'model' => $fundSourceModels[0],
                        'formId' => 'project-form',
                        'formFields' => [
                            'fund_source_id',
                            'type',
                            'agency',
                        ],
                    ]); ?>
                    
                    <table class="table table-bordered table-condensed table-responsive">
                        <thead>
                            <tr>
                                <td style="width: 2%;" align=center>#</td>
                                <td style="width: 34%;">Funding Source</td>
                                <td style="width: 15%;">Type</td>
                                <td style="width: 29%;">Agency</td>
                                <td style="width: 10%;"><button type="button" class="pull-right add-fund-source-item btn btn-info btn-xs btn-block">Add Funding Source</button></td>
                            </tr>
                        </thead>
                        <tbody class="fund-source-items">
                        <?php foreach ($fundSourceModels as $fsIdx => $fundSourceModel){ ?>
                            <?php
                                // necessary for update action.
                                if (!$fundSourceModel->isNewRecord) {
                                    echo Html::activeHiddenInput($fundSourceModel, "[{$fsIdx}]id");
                                }
                            ?>
                            <tr class="fund-source-item">
                                <td class="fund_source-counter" align=center><?= ($fsIdx + 1) ?></td>
                                <td><?= $form->field($fundSourceModel, "[{$fsIdx}]fund_source_id")->widget(Select2::classname(), [
                                    'data' => $fundSources,
                                    'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class' => 'fund-source-select'],
                                    'pluginOptions' => [
                                        'allowClear' =>  true,
                                    ],
                                    ])->label(false) ?></td>
                                <td><?= $form->field($fundSourceModel, "[{$fsIdx}]type")->textInput(['maxlength' => true])->label(false) ?></td>
                                <td><?= $form->field($fundSourceModel, "[{$fsIdx}]agency")->textInput(['maxlength' => true])->label(false) ?></td>
                                <td><button type="button" class="pull-right remove-fund-source-item btn btn-danger btn-xs btn-block">Delete Row</button></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php DynamicFormWidget::end(); ?>
                </div>
            </div>

            <h4>Project Coverage</h4>
            <hr>
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
                ])->label('Region');
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

            <div class="form-group">
                <label class="control-label col-sm-3" for="project-latitude">Map Location</label>
                <div class="col-sm-9">
                    <div id="map" style="width: 100%; height: 400px;"></div>
                    <div class="row">
                        <div class="col-md-6 col-xs-12">
                            <?= $form->field($model, 'latitude')->hiddenInput(['id' => 'project-lat'])->label(false) ?>
                        </div>
                        <div class="col-md-6 col-xs-12">
                            <?= $form->field($model, 'longitude')->hiddenInput(['id' => 'project-lng'])->label(false) ?>
                        </div>
                    </div> 
                </div>
            </div>

            <h4>Implementation Schedule</h4>
            <hr>
            
            <div class="form-group">
                <label class="control-label col-sm-3" for="project-start_date">Original Project Schedule</label>
                <div class="col-sm-9">
                    <div class="row">
                        <div class="col-md-6 col-xs-12">
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
                                ])->label("Start Date"); ?>
                        </div>
                        <div class="col-md-6 col-xs-12">
                            <?= $form->field($model, 'completion_date')->widget(DatePicker::className(), [
                                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                    'options' => ['placeholder' => 'Enter date', 'autocomplete' => 'off'],
                                    'pluginOptions' => [
                                        'autoclose'=>true,
                                        'format' => 'yyyy-mm-dd'
                                    ],
                                ])->label("End Date"); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-sm-3" for="project-revised_start_date">Revised Project Schedule</label>
                <div class="col-sm-9">
                    <?php DynamicFormWidget::begin([
                        'widgetContainer' => 'revised_schedule_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.revised-schedule-items', // required: css class selector
                        'widgetItem' => '.revised-schedule-item', // required: css class
                        'limit' => 5, // the maximum times, an element can be cloned (default 999)
                        'min' => 0, // 0 or 1 (default 1)
                        'insertButton' => '.add-revised-schedule-item', // css class
                        'deleteButton' => '.remove-revised-schedule-item', // css class
                        'model' => $revisedScheduleModels[0],
                        'formId' => 'project-form',
                        'formFields' => [
                            'start_date',
                            'end_date',
                        ],
                    ]); ?>
                    
                    <table class="table table-bordered table-condensed table-responsive">
                        <thead>
                            <tr>
                                <td style="width: 2%;" align=center>#</td>
                                <td style="width: 44%;">Start Date</td>
                                <td style="width: 44%;">End Date</td>
                                <td style="width: 10%;"><button type="button" class="pull-right add-revised-schedule-item btn btn-info btn-xs btn-block">Add Revised Schedule</button></td>
                            </tr>
                        </thead>
                        <tbody class="revised-schedule-items">
                        <?php foreach ($revisedScheduleModels as $rsIdx => $revisedScheduleModel){ ?>
                            <?php
                                // necessary for update action.
                                if (!$revisedScheduleModel->isNewRecord) {
                                    echo Html::activeHiddenInput($revisedScheduleModel, "[{$rsIdx}]id");
                                }
                            ?>
                            <tr class="revised-schedule-item">
                                <td class="revised_schedule-counter" align=center><?= ($rsIdx + 1) ?></td>
                                <td><?= $form->field($revisedScheduleModel, "[{$rsIdx}]start_date")->widget(DatePicker::className(), [
                                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                    'options' => ['placeholder' => 'Enter date', 'autocomplete' => 'off'],
                                    'pluginOptions' => [
                                        'autoclose'=>true,
                                        'format' => 'yyyy-mm-dd'
                                        // other plugin options...
                                    ],
                                ])->label(false); ?></td>
                                <td><?= $form->field($revisedScheduleModel, "[{$rsIdx}]end_date")->widget(DatePicker::className(), [
                                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                    'options' => ['placeholder' => 'Enter date', 'autocomplete' => 'off'],
                                    'pluginOptions' => [
                                        'autoclose'=>true,
                                        'format' => 'yyyy-mm-dd'
                                        // other plugin options...
                                    ],
                                ])->label(false); ?></td>
                                <td><button type="button" class="pull-right remove-revised-schedule-item btn btn-danger btn-xs btn-block">Delete Row</button></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php DynamicFormWidget::end(); ?>
                </div>
            </div>

            <?php /* $form->field($kraModel, 'key_result_area_id')->widget(Select2::classname(), [
                'data' => $kras,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'kra-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]); */
            ?>

        </div>
        <!-- <div class="col-md-5">
            <h4>Map Location</h4>
            <hr>
            

            <?php /* $form->field($model, 'fund_source_id')->widget(Select2::classname(), [
                'data' => $fundSources,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'fund-source-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Fund Source *'); */
            ?>

            <?php // $form->field($model, 'typhoon')->textInput(['maxlength' => true]) ?>

            <h4>Physical Location</h4>
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
                ])->label('Region');
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
        </div> -->
    </div>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="pull-right">
                <?php // $model->draft == 'Yes' || $model->draft == '' ? Html::button('Save as draft only', ['class' => 'btn btn-primary', 'id' => 'save-draft-btn', 'data' => ['disabled-text' => 'Please Wait']]) : '' ?>
                <?= Html::submitButton('Save component project', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']])?>
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

        if($("#project-mode_of_implementation_id").val() == 3){ 
            $("#div_other_mode").css("display", "block");                   
        }else{
            $("#div_other_mode").css("display", "none"); 
        }

        if($("#project-mode_of_implementation_id").val() == 1 || $("#project-mode_of_implementation_id").val() == 4 || $("#project-mode_of_implementation_id").val() == 5){ 
            $("#div_mode_name").css("display", "block");                   
        }else{
            $("#div_mode_name").css("display", "none"); 
        }
        
        if($("#project-mode_of_implementation_id").val() == 4){ 
            $("label[for=\"project-mode_name\"]").text("Name of Development Partner/Funding Agency");             
        }

        if($("#project-mode_of_implementation_id").val() == 5){ 
            $("label[for=\"project-mode_name\"]").text("Name of NGOs/CSOs");             
        }

        var map = L.map("map").setView([16.6170, 120.3190], 14);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "© OpenStreetMap contributors"
        }).addTo(map);

        var marker = L.marker([16.6170, 120.3190], { draggable: true }).addTo(map);

        // Function to update map center based on input values
        function updateMapCenter() {
            var lat = parseFloat($("#project-lat").val()) || 16.6170;
            var lng = parseFloat($("#project-lng").val()) || 120.3190;
    
            map.setView([lat, lng], 14);
            marker.setLatLng([lat, lng]);
        }

        // Initial map center update
        updateMapCenter();

        // Event listeners for changes in latitude and longitude inputs
        $(".map-input").on("change", function () {
            updateMapCenter();
        });

        marker.on("dragend", function (event) {
            var markerLatLng = marker.getLatLng();
            $("#project-lat").val(markerLatLng.lat);
            $("#project-lng").val(markerLatLng.lng);
        });

        });     

    $("#project-mode_of_implementation_id").on("change", function(){
        if($("#project-mode_of_implementation_id").val() == 3){ 
            $("#div_other_mode").css("display", "block");                   
        }else{
            $("#div_other_mode").css("display", "none"); 
        }

        if($("#project-mode_of_implementation_id").val() == 1 || $("#project-mode_of_implementation_id").val() == 4 || $("#project-mode_of_implementation_id").val() == 5){ 
            $("#div_mode_name").css("display", "block");                   
        }else{
            $("#div_mode_name").css("display", "none"); 
        }

        if($("#project-mode_of_implementation_id").val() == 1){ 
            $("label[for=\"project-mode_name\"]").text("Name of Contractor");             
        }

        if($("#project-mode_of_implementation_id").val() == 4){ 
            $("label[for=\"project-mode_name\"]").text("Name of Development Partner/Funding Agency");             
        }

        if($("#project-mode_of_implementation_id").val() == 5){ 
            $("label[for=\"project-mode_name\"]").text("Name of NGOs/CSOs");             
        }
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
<style>
    label.control-label{
        font-weight: bolder;
    }
    hr{
        opacity: 0.10;
    }
</style>