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
        <div class="box-title">Monitoring Plan</div>
    </div>
    <div class="box-header box-body">
        <h3 class="text-center"><?= $dueDate ? date("D, F j, Y", strtotime($dueDate->due_date)) : 'No due date set' ?></h3>      
        <p class="text-center"><?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? $HtmlHelper->time_elapsed_string($dueDate->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($dueDate->due_date).' ago' : '' ?></p>                      
    </div>
    <div class="box-footer">
    <?= Html::button('<i class="fa fa-calendar"></i> Adjust Date', ['value' => Url::to(['/rpmes/due-date/set-monitoring-plan-due-date', 'report' => $report, 'year' => $year]), 'class' => 'btn  btn-block btn-success', 'id' => 'monitoring-plan-due-date-button']) ?>
    </div>
</div>
<?php
  Modal::begin([
    'id' => 'monitoring-plan-due-date-modal',
    'size' => "modal-md",
    'header' => '<div id="monitoring-plan-due-date-modal-header"><h4>Due Date for Monitoring Plan</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="monitoring-plan-due-date-modal-content"></div>';
  Modal::end();
?>
<?php
    $script = '
        $("#monitoring-plan-due-date-button").click(function(){
            $("#monitoring-plan-due-date-modal").modal("show").find("#monitoring-plan-due-date-modal-content").load($(this).attr("value"));
        });   
    ';

    $this->registerJs($script, View::POS_END);
?>