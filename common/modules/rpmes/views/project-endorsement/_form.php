<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
use yii\web\View;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Project */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-endorsement-form">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'disable-submit-buttons'],
        'layout' => 'horizontal',
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'wrapper' => 'col-sm-9',
            ],
        ],
    ]); ?>

    <?= $form->field($model, 'year')->textInput(['maxlength' => true, 'type' => 'number']) ?>

    <?= $form->field($model, 'quarter')->widget(Select2::classname(), [
        'data' => [
            'Q1' => 'Q1',
            'Q2' => 'Q2',
            'Q3' => 'Q3',
            'Q4' => 'Q4',
        ],
        'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'quarter-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ]);
    ?>

    <?= $form->field($model, 'project_id')->widget(Select2::classname(), [
        'data' => $projects,
        'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'project-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        'pluginEvents' => [
            'change' => 'function() { viewExceptions(this.value) }',
        ],
    ])->label('Project');
    ?>

    <?= $form->field($model, 'npmc_action')->textarea(['rows' => 6]) ?>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="pull-right">
                <?= Html::submitButton('Save Record', ['class' => 'btn btn-success']) ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<style>
    label.control-label{
        font-weight: bolder;
    }
    hr{
        opacity: 0.10;
    }
</style>

<?php
    $script = '
    function viewExceptions(id)
    {
        $.ajax({
            url: "'.Url::to(['/rpmes/project-endorsement/view']).'?id=" + id,
            beforeSend: function(){
                $("#project-exception-details").html("<div class=\"text-center\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                console.log(this.data);
                $("#project-exception-details").empty();
                $("#project-exception-details").hide();
                $("#project-exception-details").fadeIn("slow");
                $("#project-exception-details").html(data);
            },
            error: function (err) {
                console.log(err);
            }
        });      
    }

    $(document).ready(function() {
        var project = $("#projectendorsement-project_id").val();
        if(project != ""){
            viewExceptions(project);
        }
    });
    ';

    $this->registerJs($script, View::POS_END);
?>