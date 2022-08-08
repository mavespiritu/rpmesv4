<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\DueDateSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="acknowledgment-summary-accomplishment-search">

    <?php $form = ActiveForm::begin([
        'id' => 'search-summary-summary-accomplishment-form'
    ]); ?>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'year')->widget(Select2::classname(), [
                'data' => $years,
                'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'year-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Year *');
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'quarter')->widget(Select2::classname(), [
                'data' => $quarters,
                'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'quarter-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Quarter *');
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'agency_id')->widget(Select2::classname(), [
                'data' => $agencies,
                'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'agency-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Agency');
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'category_id')->widget(Select2::classname(), [
                'data' => $categories,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'category-select'],
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
            <?= $form->field($model, 'region_id')->widget(Select2::classname(), [
                'data' => $regions,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'region-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                'pluginEvents'=>[
                    'select2:select select2:unselect'=>'
                        function(){
                            $.ajax({
                                url: "'.Url::to(['/rpmes/project/province-list-single']).'",
                                data: {
                                        id: this.value,
                                    }
                            }).done(function(result) {
                                $(".province-select").html("").select2({ data:result, multiple:false, theme:"krajee", width:"100%",placeholder:"Select one", allowClear: true});
                                $(".province-select").select2("val","");
                            });
                        }'
    
                ]
                ]);
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'province_id')->widget(Select2::classname(), [
                'data' => $provinces,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'province-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'fund_source_id')->widget(Select2::classname(), [
                'data' => $fundSources,
                'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'fund-source-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Fund Source');
            ?>
        </div>
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
            <?= $form->field($model, 'grouping')->widget(Select2::classname(), [
                    'data' => $sorts,
                    'options' => ['multiple' => false, 'placeholder' => 'Select Grouping', 'class'=>'grouping-select'],
                    'pluginOptions' => [
                        'allowClear' =>  true,
                    ],
                ])->label('Group by *');
            ?>
        </div>
    </div>
    <div class="form-group pull-right">
            <?= Html::submitButton('Generate Data', ['class' => 'btn btn-primary', 'style' => 'margin-top: 5px;']) ?>
    </div>
    <div class="clearfix"></div>
    <?php ActiveForm::end(); ?>

</div>
<?php
    $script = '
    $("#search-summary-summary-accomplishment-form").on("beforeSubmit", function (e) {
        e.preventDefault();
     
        var form = $(this);
        var formData = form.serialize();
        
        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            beforeSend: function(){
                $("#summary-summary-accomplishment-table").html("<div class=\"text-center\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                console.log(this.data);
                $("#summary-summary-accomplishment-table").empty();
                $("#summary-summary-accomplishment-table").hide();
                $("#summary-summary-accomplishment-table").fadeIn("slow");
                $("#summary-summary-accomplishment-table").html(data);
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