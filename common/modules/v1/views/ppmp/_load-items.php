<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
use yii\web\View;
/* @var $model common\modules\v1\models\Ppmp */
/* @var $form yii\widgets\ActiveForm */
$id = $model->reference ? $model->reference->id : 0;
?>

<div class="ppmp">

    <?php $form = ActiveForm::begin([
    	//'options' => ['class' => 'disable-submit-buttons'],
        'id' => 'activity-form',
    ]); ?>

    <div class="row">
        <div class="col-md-4 col-xs-12">
        <?php 
            $fundSourcesUrl = \yii\helpers\Url::to(['/v1/ppmp/fund-source-list']);
            echo $form->field($appropriationItemModel, 'activity_id')->widget(Select2::classname(), [
            'data' => $activities,
            'options' => ['placeholder' => 'Select Activity','multiple' => false, 'class'=>'activity-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
            'pluginEvents'=>[
                'select2:select'=>'
                    function(){
                        $.ajax({
                            url: "'.$fundSourcesUrl.'",
                            data: {
                                    id: '.$id.', 
                                    activity_id: this.value
                                }
                            
                        }).done(function(result) {
                            $(".fund-source-select").html("").select2({ data:result, theme:"krajee", width:"100%",placeholder:"Select Fund Source", allowClear: true});
                            $(".fund-source-select").select2("val","");
                        });
                    }'

            ]
            ]);
        ?>
        </div>
        <div class="col-md-4 col-xs-12">
        <?= $form->field($appropriationItemModel, 'fund_source_id')->widget(Select2::classname(), [
            'data' => $fundSources,
            'options' => ['placeholder' => 'Select Fund Source','multiple' => false, 'class'=>'fund-source-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
            ]);
        ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <div class="form-group" style="padding-top: 25px;">
                <?= Html::submitButton('<i class="fa fa-refresh"></i> Load Items', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
  $script = '
    $(document).ready(function() {
        $("#activity-form").on("beforeSubmit", function(e) {
            var form = $(this);
            e.preventDefault();
            $.ajax({
                url: "'.Url::to(['/v1/ppmp/load-items']).'",
                data: {
                    id: '.$model->id.',
                    activity_id: $("#appropriationitem-activity_id").val(),
                    fund_source_id: $("#appropriationitem-fund_source_id").val(),
                },
                beforeSend: function(){
                    $("#items").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
                },
                success: function (data) {
                    console.log(this.data);
                    $("#items").empty();
                    $("#items").hide();
                    $("#items").fadeIn("slow");
                    $("#items").html(data);
                    form.enableSubmitButtons();
                },
                error: function (err) {
                    console.log(err);
                }
            });

            return false;
        });
    });
  ';
  $this->registerJs($script, View::POS_END);
?>