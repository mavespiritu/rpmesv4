<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Url;
use yii\web\View;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Pr */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="pr-ris-form">

<?php $form = ActiveForm::begin([
    //'options' => ['class' => 'disable-submit-buttons'],
    'id' => 'pr-ris-form',
    //'enableAjaxValidation' => true,
]); ?>

<div class="row">
    <div class="col-md-8 col-xs-12">
        <?= $form->field($model, 'ris_id')->widget(Select2::classname(), [
            'data' => $rises,
            'options' => ['placeholder' => 'Select RIS','multiple' => false, 'class'=>'ris-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
            ]);
        ?>
    </div>
    <div class="col-md-2 col-xs-12">
        <label for="ris_id">&nbsp;</label>
        <?= Html::submitButton('Show Items', ['class' => 'btn btn-success btn-block']) ?>
    </div>
    <div class="col-md-2 col-xs-12">
        <label for="ris_id">&nbsp;</label>
        <?= Html::button('Hide Items', ['class' => 'btn btn-danger btn-block', 'onClick' => 'items('.$model->id.')']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

</div>
<?php
  $script = '
    $("#pr-ris-form").on("beforeSubmit", function(e) {
        var form = $(this);
        e.preventDefault();
        $.ajax({
            url: "'.Url::to(['/v1/pr/load-ris-items']).'",
            data: {
                id: '.$model->id.',
                ris_id: $("#pr-ris_id").val(),
            },
            beforeSend: function(){
                $("#ris-items").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                console.log(this.data);
                $("#ris-items").empty();
                $("#ris-items").hide();
                $("#ris-items").fadeIn("slow");
                $("#ris-items").html(data);
            },
            error: function (err) {
                console.log(err);
            }
        });

        return false;
    });
    $(document).ready(function() {
        
    });
  ';
  $this->registerJs($script, View::POS_END);
?>