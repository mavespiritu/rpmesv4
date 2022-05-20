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
        'action' => Url::to(['/rpmes/plan/submit']),
        'method' => 'post',
        'id' => 'monitoring-plan-submission-form',
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <?php /* Yii::$app->user->can('Administrator') || Yii::$app->user->can('SuperAdministrator') ? $form->field($submissionModel, 'agency_id')->widget(Select2::classname(), [
                    'data' => $agencies,
                    'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'agency-select'],
                    'pluginOptions' => [
                        'allowClear' =>  true,
                    ],
                    'pluginEvents'=>[
                    'select2:select select2:unselect'=>'
                        function(){
                            $.ajax({
                                url: "'.Url::to(['/rpmes/plan/count']).'",
                                data: {
                                        agency_id: this.value,
                                },
                                beforeSend: function(){
                                    $("#number_of_projects_enrolled").html("<span class=\"text-center\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></span>");
                                },
                                success: function (data) {
                                    console.log(data);
                                    $("#number_of_projects_enrolled").empty();
                                    $("#number_of_projects_enrolled").hide();
                                    $("#number_of_projects_enrolled").fadeIn("slow");
                                    $("#number_of_projects_enrolled").html(data);
                                    if(data == 0){
                                        $("#submit-plan-div").html("Please enroll projects in the monitoring plan. <a href=\"/rpmes/rpmes/project/create\">Click here</a>");
                                    }else{
                                        $("#submit-plan-div").html("<button type=\"submit\" id=\"submit-monitoring-plan-button\" class=\"btn btn-success\" data-method=\"post\">Submit Monitoring Plan '.date("Y").'</button>");
                                    }
                                },
                                error: function (err) {
                                    console.log(err);
                                }
                            });

                            $.ajax({
                                url: "'.Url::to(['/rpmes/plan/submission-info']).'",
                                data: {
                                        agency_id: this.value,
                                },
                                beforeSend: function(){
                                    $("#submission-p").html("<span class=\"text-center\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></span>");
                                },
                                success: function (data) {
                                    console.log(data);
                                    $("#submission-p").empty();
                                    $("#submission-p").hide();
                                    $("#submission-p").fadeIn("slow");
                                    $("#submission-p").html(data);
                                },
                                error: function (err) {
                                    console.log(err);
                                }
                            })
                        }'
                    ]
                ]) : ''; */
            ?>
            <h5>No. of projects enrolled: <br>
                <h3 id="number_of_projects_enrolled"><?= number_format($projectCount, 0) ?></h3>
            </h5>
        </div>
    </div>
    <div class="pull-left">
        <p id="submission-p"><?= !$submissionModel->isNewRecord ? 'Monitoring plan has been submitted last '.date("F j, Y H:i:s", strtotime($submissionModel->date_submitted)).' by '.$submissionModel->submitter : '' ?></p>
        <div id="submit-plan-div">
            <?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? $submissionModel->isNewRecord ? $projectCount > 0 ? Html::submitButton('Submit Monitoring Plan '.date("Y"), ['class' => 'btn btn-success', 'id' => 'submit-monitoring-plan-button', 'data' => [
            'method' => 'post',
        ]]) : 'Please enroll projects in the monitoring plan. '.Html::a('Click here',['/rpmes/project/create']) : '' : '' : '' ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<?php
        $script = '
        $("#submit-monitoring-plan-button").on("click", function(e) {
            e.preventDefault();

            var con = confirm("The data I encoded had been duly approved by my agency head. I am providing my name and designation in the appropriate fields as an attestation of my submission\'s data integrity. Proceed?");
            if(con == true)
            {
                var form = $("#monitoring-plan-submission-form");
                var formData = form.serialize();

                $.ajax({
                    url: form.attr("action"),
                    type: form.attr("method"),
                    data: formData,
                    success: function (data) {
                        console.log(data);
                        $.growl.notice({ title: "Success!", message: "Monitoring Plan has been submitted" });
                    },
                    error: function (err) {
                        console.log(err);
                    }
                }); 
            }

            return false;
        });
        ';

        $this->registerJs($script, View::POS_END);
    ?>