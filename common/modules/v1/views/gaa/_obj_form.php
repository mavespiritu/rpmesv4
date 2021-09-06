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
<?php $objectsUrl = \yii\helpers\Url::to(['/v1/gaa/object-list']); ?>

<div class="gaa-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
        'id' => 'obj-form',
    ]); ?>

    <div class="row">
        <div class="col-md-10 col-xs-12">
            
            <?= $form->field($objModel, 'obj_id')->widget(Select2::classname(), [
                'data' => $objs,
                'options' => ['placeholder' => 'Select Object','multiple' => false, 'class'=>'object-select'],
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
    function loadObjects()
    {
        $.ajax({
          url: "'.$objectsUrl.'",
          data: {
                  id: '.$model->id.'
              }
        }).done(function(result) {
          var h;
          $(".object-select").html("").select2({ data:result, theme:"krajee", width:"100%",placeholder:"Select Object", allowClear: true,});
          $(".object-select").select2("val","");
        });
    }

    $(document).ready(function() {
        $("#obj-form").one("beforeSubmit", function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = form.serialize();

            $.ajax({
                url: form.attr("action"),
                type: form.attr("method"),
                data: formData,
                beforeSend: function(){
                    $("#object-form").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
                    $("#objects").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
                },
                success: function (data) {
                    objects();
                    objectForm();
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