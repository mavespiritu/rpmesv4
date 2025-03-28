<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\DueDateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Due Dates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="due-date-index">
    
    <div class="box box-solid">
        <div class="box-header with-border">
            <div class="box-title">Due Dates</div>
        </div>
        <div class="box-header box-body">
            <div class="row">
                <div class="col-md-3 col-xs-12">
                    <?= $this->render('_search', [
                        'model' => $model,
                        'years' => $years,
                    ]) ?>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-3 col-xs-12">
                    <div id="monitoring-plan-due-date"></div>
                </div>
            </div>
            <br>
            <div class="row">
                <?php if(!empty($quarters)){ ?>
                    <?php foreach($quarters as $quarter => $value){ ?>
                    <div class="col-md-3 col-xs-12">
                        <div id="accomplishment-<?= $quarter ?>-due-date"></div>
                    </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <br>
            <div class="row">
                <?php if(!empty($quarters)){ ?>
                    <?php foreach($quarters as $quarter => $value){ ?>
                    <div class="col-md-3 col-xs-12">
                        <div id="project-exception-<?= $quarter ?>-due-date"></div>
                    </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <br>
            <div class="row">
                <div class="col-md-3 col-xs-12">
                    <div id="project-results-due-date"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $script = '
    function loadMonitoringPlanDueDate(year)
    {
        $.ajax({
            url: "'.Url::to(['/rpmes/due-date/monitoring-plan-due-date']).'",
            data: {
                year: year,
            },
            beforeSend: function(){
                $("#monitoring-plan-due-date").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                $("#monitoring-plan-due-date").empty();
                $("#monitoring-plan-due-date").hide();
                $("#monitoring-plan-due-date").fadeIn("slow");
                $("#monitoring-plan-due-date").html(data);
            },
            error: function (err) {
                console.log(err);
            }
        });
    }

    function loadAccomplishmentDueDate(year, quarter)
    {
        $.ajax({
            url: "'.Url::to(['/rpmes/due-date/accomplishment-due-date']).'",
            data: {
                year: year,
                quarter: quarter
            },
            beforeSend: function(){
                $("#accomplishment-"+ quarter +"-due-date").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                $("#accomplishment-"+ quarter +"-due-date").empty();
                $("#accomplishment-"+ quarter +"-due-date").hide();
                $("#accomplishment-"+ quarter +"-due-date").fadeIn("slow");
                $("#accomplishment-"+ quarter +"-due-date").html(data);
            },
            error: function (err) {
                console.log(err);
            }
        });
    }

    function loadProjectExceptionDueDate(year, quarter)
    {
        $.ajax({
            url: "'.Url::to(['/rpmes/due-date/project-exception-due-date']).'",
            data: {
                year: year,
                quarter: quarter
            },
            beforeSend: function(){
                $("#project-exception-"+ quarter +"-due-date").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                $("#project-exception-"+ quarter +"-due-date").empty();
                $("#project-exception-"+ quarter +"-due-date").hide();
                $("#project-exception-"+ quarter +"-due-date").fadeIn("slow");
                $("#project-exception-"+ quarter +"-due-date").html(data);
            },
            error: function (err) {
                console.log(err);
            }
        });
    }

    function loadProjectResultsDueDate(year)
    {
        $.ajax({
            url: "'.Url::to(['/rpmes/due-date/project-results-due-date']).'",
            data: {
                year: year,
            },
            beforeSend: function(){
                $("#project-results-due-date").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                $("#project-results-due-date").empty();
                $("#project-results-due-date").hide();
                $("#project-results-due-date").fadeIn("slow");
                $("#project-results-due-date").html(data);
            },
            error: function (err) {
                console.log(err);
            }
        });
    }

    $(document).ready(function(){
        loadMonitoringPlanDueDate('.date("Y").');
        loadAccomplishmentDueDate('.date("Y").',"Q1");
        loadAccomplishmentDueDate('.date("Y").',"Q2");
        loadAccomplishmentDueDate('.date("Y").',"Q3");
        loadAccomplishmentDueDate('.date("Y").',"Q4");
        loadProjectExceptionDueDate('.date("Y").',"Q1");
        loadProjectExceptionDueDate('.date("Y").',"Q2");
        loadProjectExceptionDueDate('.date("Y").',"Q3");
        loadProjectExceptionDueDate('.date("Y").',"Q4");
        loadProjectResultsDueDate('.date("Y").');
    });     
    ';

    $this->registerJs($script, View::POS_END);
?>