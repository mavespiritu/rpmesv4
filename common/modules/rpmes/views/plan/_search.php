<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\DueDateSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="plan-search">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'year')->widget(Select2::classname(), [
                'data' => $years,
                'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'year-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'agency_id')->widget(Select2::classname(), [
                    'data' => $agencies,
                    'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'agency-select'],
                    'pluginOptions' => [
                        'allowClear' =>  true,
                    ],
                ]);
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($categoryModel, 'category_id')->widget(Select2::classname(), [
                'data' => $categories,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'category-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'fund_source_id')->widget(Select2::classname(), [
                'data' => $fundSources,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'fund-source-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>
        </div>
    </div>
    <div class="row">
    <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'sector_id')->widget(Select2::classname(), [
                'data' => $sectors,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'sector-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
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
                ]);
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'sub_sector_id')->widget(Select2::classname(), [
                'data' => $subSectors,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'sub-sector-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
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
                            });
                        }'
    
                ]
                ]);
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($provinceModel, 'province_id')->widget(Select2::classname(), [
                'data' => $provinces,
                'options' => ['multiple' => true, 'placeholder' => 'Select one or more', 'class'=>'province-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'period')->widget(Select2::classname(), [
                'data' => $periods,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'period-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'data_type')->widget(Select2::classname(), [
                'data' => $dataTypes,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'data-type-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'project_no')->textInput() ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'title')->textInput() ?>
        </div>
    </div>
    <div class="pull-right">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
    </div>
    <div class="clearfix"></div>
    <?php ActiveForm::end(); ?>

</div>
