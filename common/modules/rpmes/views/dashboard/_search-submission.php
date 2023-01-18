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

<div class="plan-search">

    <?php $form = ActiveForm::begin([
        'id' => 'search-submission-log-form',
    ]); ?>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <?= $form->field($logModel, 'year')->widget(Select2::classname(), [
                'data' => $years,
                'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'year-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>
        </div>
        <?php if(Yii::$app->user->can('Administrator')){ ?>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($logModel, 'agency_id')->widget(Select2::classname(), [
                    'data' => $agencies,
                    'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'agency-select'],
                    'pluginOptions' => [
                        'allowClear' =>  true,
                    ],
                ]);
            ?>
        </div>
        <?php } ?>
        <div class="col-md-3 col-xs-12">
            <br>
            <label for="">&nbsp;</label>
            <?= Html::submitButton('View Log', ['class' => 'btn btn-primary', 'style' => 'margin-top: 5px;']) ?>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php ActiveForm::end(); ?>

</div>
<?php
    $script = '
    $("#search-submission-log-form").on("beforeSubmit", function (e) {
        e.preventDefault();
     
        var form = $(this);
        var formData = form.serialize();

        var year = $(".year-select").val();
        var agency_id = $(".agency-select").val();
        
        $.ajax({
            url: "'.Url::to(['/rpmes/dashboard/submission-log']).'?year=" + year + "&agency_id=" + agency_id,
            type: form.attr("method"),
            data: formData,
            beforeSend: function(){
                $("#submission-log").html("<div class=\"text-center\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                console.log(this.data);
                $("#submission-log").empty();
                $("#submission-log").hide();
                $("#submission-log").fadeIn("slow");
                $("#submission-log").html(data);
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