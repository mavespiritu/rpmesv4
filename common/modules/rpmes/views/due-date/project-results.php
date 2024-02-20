<?php 

use yii\helpers\Html;
use yii\web\View;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use common\components\helpers\HtmlHelper;

$HtmlHelper = new HtmlHelper();
?>
<div class="box box-solid">
    <div class="box-header with-border <?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? 'positive' : 'negative' : 'positive' ?>">
        <div class="box-title">Project Results</div>
    </div>
    <div class="box-header box-body">
        <h3 class="text-center"><?= $dueDate ? date("D, F j, Y", strtotime($dueDate->due_date)) : 'No due date set' ?></h3>      
        <p class="text-center"><?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? $HtmlHelper->time_elapsed_string($dueDate->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($dueDate->due_date).' ago' : '' ?></p>                      
    </div>
    <div class="box-footer">
    <?= Html::button('<i class="fa fa-calendar"></i> Adjust Date', ['value' => Url::to(['/rpmes/due-date/set-project-results-due-date', 'report' => $report, 'year' => $year]), 'class' => 'btn  btn-block btn-success', 'id' => 'project-results-due-date-button']) ?>
    </div>
</div>
<?php
  Modal::begin([
    'id' => 'project-results-due-date-modal',
    'size' => "modal-md",
    'header' => '<div id="project-results-due-date-modal-header"><h4>Due Date for Project Results</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="project-results-due-date-modal-content"></div>';
  Modal::end();
?>
<?php
    $script = '
        $("#project-results-due-date-button").click(function(){
            $("#project-results-due-date-modal").modal("show").find("#project-results-due-date-modal-content").load($(this).attr("value"));
        });   
    ';

    $this->registerJs($script, View::POS_END);
?>