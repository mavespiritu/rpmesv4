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

<div class="acknowledgment-monitoring-plan-search">

    <?php $form = ActiveForm::begin([
        'id' => 'search-summary-monitoring-plan-form'
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
            <?= $form->field($model, 'category_id')->widget(Select2::classname(), [
                'data' => $categories,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'category-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'sector_id')->widget(Select2::classname(), [
                'data' => $sectors,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'sector-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'province_id')->widget(Select2::classname(), [
                'data' => $locations,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'province-select'],
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
                ]);
            ?>
        </div>
    </div>
    <div class="form-group pull-right">
        <?= Html::submitButton('Generate Data', ['class' => 'btn btn-primary']) ?>
    </div>
    <div class="clearfix"></div>
    <?php ActiveForm::end(); ?>
</div>
<?php
    $script = '
    $("#search-summary-monitoring-plan-form").on("beforeSubmit", function (e) {
        e.preventDefault();
     
        var form = $(this);
        var formData = form.serialize();
        
        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            beforeSend: function(){
                $("#summary-monitoring-plan-table").html("<div class=\"text-center\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                console.log(this.data);
                $("#summary-monitoring-plan-table").empty();
                $("#summary-monitoring-plan-table").hide();
                $("#summary-monitoring-plan-table").fadeIn("slow");
                $("#summary-monitoring-plan-table").html(data);
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