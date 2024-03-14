<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\web\View;
?>

<tr>
    <td align=center><?= $i ?></td>
    <td align=center><?= $agency->code ?></td>
    <td align=center><?= $agency->getProjectResultsSubmission($getData['year']) ? '<span class="text-green">'.ucwords(strtolower($agency->getProjectResultsSubmission($getData['year'])->submitter)).'</span>' : '<span class="text-red">No submission</span>' ?></td>
    <td align=center><?= $agency->getProjectResultsSubmission($getData['year']) ? date("F j, Y H:i:s", strtotime($agency->getProjectResultsSubmission($getData['year'])->date_submitted)) : '-' ?></td>
    <td align=center><?= $agency->getProjectResultsAcknowledgment($getData['year']) ? '<span class="text-green">'.ucwords(strtolower($agency->getProjectResultsAcknowledgment($getData['year'])->acknowledger)).'</span>' : '<span class="text-red">No acknowledgment</span>' ?></td>
    <td align=center><?= $agency->getProjectResultsAcknowledgment($getData['year']) ? date("F j, Y H:i:s", strtotime($agency->getProjectResultsAcknowledgment($getData['year'])->date_acknowledged)) : '-' ?></td>
    <td style="width: 10%;">
        <?php /* Yii::$app->user->can('Administrator') ? $agency->getMonitoringPlanSubmission($getData['year']) ? Html::button('<i class="fa fa-edit"></i>', ['value' => Url::to(['/rpmes/acknowledgment/acknowledge-monitoring-plan', 'id' => $agency->getMonitoringPlanSubmission($getData['year'])->id]), 'class' => 'btn btn-success btn-sm', 'id' => 'acknowledge-monitoring-plan-'.$agency->id.'-button']) : '' : '' ?>
        <?= $agency->getMonitoringPlanAcknowledgment($getData['year']) ? Html::button('<i class="fa fa-print"></i>', ['onClick' => 'printAcknowledgmentMonitoringPlan('.$agency->getMonitoringPlanAcknowledgment($getData['year'])->id.')', 'class' => 'btn btn-info btn-sm']) : '' ?>
        <?= Yii::$app->user->can('Administrator') ? $agency->getMonitoringPlanSubmission($getData['year']) ? Html::a('<i class="fa fa-trash"></i>', ['/rpmes/acknowledgment/delete-submission', 'id' => $agency->getMonitoringPlanSubmission($getData['year'])->id, 'report' => 'Monitoring Plan'], ['class' => 'btn btn-danger btn-sm', 'id' => 'delete-monitoring-plan-'.$agency->id.'-button', 'data' => [
            'confirm' => 'Are you sure you want to remove submission of this agency?',
            'method' => 'post',
        ],]) : '' : '' */ ?>
        <?= $agency->getProjectResultsSubmission($getData['year']) ? Html::a('View Submission', ['/rpmes/project-result/view', 'id' => $agency->getProjectResultsSubmission($getData['year'])->id], ['class' => 'btn btn-block btn-success btn-sm', 'target' => '_blank']) : '' ?>
    </td>
</tr>

<?php
  Modal::begin([
    'id' => 'acknowledge-project-results-'.$agency->id.'-modal',
    'size' => "modal-xl",
    'header' => '<div id="acknowledge-project-results-'.$agency->id.'-modal-header"><h4>Acknowledge Form 4 Submission</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="acknowledge-project-results-'.$agency->id.'-modal-content"></div>';
  Modal::end();
?>
<?php
    $script = '
        $(document).ready(function(){
            $("#acknowledge-project-results-'.$agency->id.'-button").click(function(){
              $("#acknowledge-project-results-'.$agency->id.'-modal").modal("show").find("#acknowledge-project-results-'.$agency->id.'-modal-content").load($(this).attr("value"));
            });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>