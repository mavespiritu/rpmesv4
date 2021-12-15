<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;
use yii\helpers\Url;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ppmp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="nep-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
        'id' => 'pap-form',
    ]); ?>

    <div class="row">
        <div class="col-md-5 col-xs-12">
            <?php 
                $fundSourcesUrl = \yii\helpers\Url::to(['/v1/nep/fund-source-list']);
                echo $form->field($papModel, 'pap_id')->widget(Select2::classname(), [
                    'data' => $paps,
                    'options' => ['placeholder' => 'Select Program','multiple' => false,'class'=>'program-select'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                    'pluginEvents'=>[
                        'select2:select'=>'
                            function(){
                                $.ajax({
                                    url: "'.$fundSourcesUrl.'",
                                    data: {
                                            id: '.$model->id.', 
                                            pap_id: this.value
                                        }
                                    
                                }).done(function(result) {
                                    var h;
                                    $(".fund-source-select").html("").select2({ data:result, theme:"krajee", width:"100%",placeholder:"Select Fund Source", allowClear: true,});
                                    $(".fund-source-select").select2("val","");
                                });
                            }'

                    ]
                ]);
            ?>
        </div>
        <div class="col-md-5 col-xs-12">
            <?= $form->field($papModel, 'fund_source_id')->widget(Select2::classname(), [
                'data' => $fundSources,
                'options' => ['placeholder' => 'Select Fund Source','multiple' => false, 'class'=>'fund-source-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>
        </div>
        <div class="col-md-2 col-xs-12">
            <div class="form-group">
                <label>&nbsp;</label>
                <br>
                <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-block', 'data' => ['disabled-text' => 'Please Wait']]) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
  $script = '
    $(document).ready(function() {
        $("#pap-form").one("beforeSubmit", function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = form.serialize();

            $.ajax({
                url: form.attr("action"),
                type: form.attr("method"),
                data: formData,
                beforeSend: function(){
                    $("#programs").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
                },
                success: function (data) {
                    $("#appropriationpap-pap_id").val("");
                    $("#appropriationpap-fund_source_id").val("");
                    $("#programs").empty();
                    $("#programs").hide();
                    $("#programs").fadeIn("slow");
                    $("#programs").html(data);
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