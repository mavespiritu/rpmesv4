<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectResultSearch */
/* @var $form yii\widgets\ActiveForm */

$projectsUrl = \yii\helpers\Url::to(['/rpmes/project-result/project-list']);

?>

<div class="project-result-search">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
        'method' => 'get'
    ]); ?>

    <div class="row">
        <?php if(Yii::$app->user->can('Administrator')){ ?>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'agency_id')->widget(Select2::classname(), [
                'data' => $agencies,
                'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'agency-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                'pluginEvents'=>[
                    'select2:select'=>'
                        function(){
                            $.ajax({
                                url: "'.$projectsUrl.'",
                                data: {
                                        agency_id: this.value,
                                        year: $("#project-year").val(),
                                    }
                            }).done(function(result) {
                                $(".project-select").html("").select2({ data:result, theme:"krajee", width:"100%",placeholder:"Select One", allowClear: true});
                                $(".project-select").select2("val","");
                            });
                        }'
                ]
                ])->label('Agency');
            ?>
        </div>
        <?php } ?>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'year')->widget(Select2::classname(), [
                'data' => $years,
                'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'year-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                'pluginEvents'=>[
                    'select2:select'=>'
                        function(){
                            $.ajax({
                                url: "'.$projectsUrl.'",
                                data: {
                                        agency_id: $("#project-agency_id").val(),
                                        year: this.value,
                                    }
                            }).done(function(result) {
                                $(".project-select").html("").select2({ data:result, theme:"krajee", width:"100%",placeholder:"Select One", allowClear: true});
                                $(".project-select").select2("val","");
                            });
                        }'
                ]
                ])->label('Year *');
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'id')->widget(Select2::classname(), [
                'data' => $projects,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'project-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Project *');
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'quarter')->widget(Select2::classname(), [
                'data' => $quarters,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'quarter-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Quarter *');
            ?>
        </div>
    </div>

    <div class="pull-right">
        <?= Html::submitButton('Generate Form', ['class' => 'btn btn-primary', 'style' => 'margin-top: 5px;']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
    $script = '
    $("#search-project-result-form").on("beforeSubmit", function (e) {
        e.preventDefault();
     
        var form = $(this);
        var formData = form.serialize();
        
        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            beforeSend: function(){
                $("#project-result-table").html("<div class=\"text-center\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                console.log(this.data);
                $("#project-result-table").empty();
                $("#project-result-table-table").hide();
                $("#project-result-table").fadeIn("slow");
                $("#project-result-table").html(data);
            },
            error: function (err) {
                console.log(err);
            }
        });      

        return false;
    });
    ';

    $this->registerJs($script, View::POS_END);

