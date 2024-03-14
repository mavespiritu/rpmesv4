<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\web\View;
?>

<tr>
    <td align=center><?= $i ?></td>
    <td align=center><?= $agency->code ?></td>
    <?php if(!empty($quarters)){ ?>
        <?php foreach($quarters as $q => $quarter){ ?>
            <td style="width: 22%;">
                <table style="width: 100%;">
                    <tr>
                        <td align=right style="width: 50%; vertical-align: top;">Submitted By:</td>
                        <td align=right style="width: 50%; vertical-align: top;"><?= $agency->getProjectExceptionSubmission($getData['year'], $q) ? '<span class="text-green">'.$agency->getProjectExceptionSubmission($getData['year'], $q)->submitter.'</span>' : '<span class="text-red">No submission</span>' ?></td>
                    </tr>
                    <tr>
                        <td align=right style="vertical-align: top;">Date Submitted:</td>
                        <td align=right style="vertical-align: top;"><?= $agency->getProjectExceptionSubmission($getData['year'], $q) ? date("F j, Y H:i:s", strtotime($agency->getProjectExceptionSubmission($getData['year'], $q)->date_submitted)) : '-' ?></td>
                    </tr>
                    <tr>
                        <td align=right style="vertical-align: top;">Acknowledged By:</td>
                        <td align=right style="vertical-align: top;"><?= $agency->getProjectExceptionAcknowledgment($getData['year'], $q) ? '<span class="text-green">'.$agency->getProjectExceptionAcknowledgment($getData['year'], $q)->acknowledger.'</span>' : '<span class="text-red">No acknowledgment</span>' ?></td>
                    </tr>
                    <tr>
                        <td align=right style="vertical-align: top;">Date Acknowledged:</td>
                        <td align=right style="vertical-align: top;"><?= $agency->getProjectExceptionAcknowledgment($getData['year'], $q) ? date("F j, Y H:i:s", strtotime($agency->getProjectExceptionAcknowledgment($getData['year'], $q)->date_acknowledged)) : '-' ?></td>
                    </tr>
                </table>
                <br>
                <!-- <span class="pull-right"><?= Yii::$app->user->can('Administrator') ? $agency->getProjectExceptionSubmission($getData['year'], $q) ? Html::button('Acknowledge', ['value' => Url::to(['/rpmes/acknowledgment/acknowledge-monitoring-report', 'id' => $agency->getProjectExceptionSubmission($getData['year'], $q)->id]), 'class' => 'btn btn-success btn-sm', 'id' => 'acknowledge-monitoring-report-'.$agency->id.'-'.$q.'-button']) : '' : '' ?>
                <?= $agency->getProjectExceptionAcknowledgment($getData['year'], $q) ? Html::button('Print', ['onClick' => 'printAcknowledgmentMonitoringReport('.$agency->getProjectExceptionAcknowledgment($getData['year'], $q)->id.')', 'class' => 'btn btn-info btn-sm']) : '' ?>
                <?= Yii::$app->user->can('Administrator') ? $agency->getProjectExceptionSubmission($getData['year'], $q) ? Html::a('Delete', ['/rpmes/acknowledgment/delete-submission', 'id' => $agency->getProjectExceptionSubmission($getData['year'], $q)->id, 'report' => 'Accomplishment'], ['class' => 'btn btn-danger btn-sm', 'id' => 'delete-monitoring-report-'.$agency->id.'-button', 'data' => [
                    'confirm' => 'Are you sure you want to remove submission of this agency?',
                    'method' => 'post',
                ],]) : '' : '' ?></span> -->
                <?= $agency->getProjectExceptionSubmission($getData['year'], $q) ? Html::a('View Submission', ['/rpmes/project-exception/view', 'id' => $agency->getProjectExceptionSubmission($getData['year'], $q)->id], ['class' => 'btn btn-block btn-success btn-sm', 'target' => '_blank']) : '' ?>
            </td>
        <?php } ?>
    <?php } ?>
</tr>

<?php
$script = '$(document).ready(function(){';
if(!empty($quarters)){
    foreach($quarters as $q => $quarter){
        Modal::begin([
            'id' => 'acknowledge-project-exception-'.$agency->id.'-'.$q.'-modal',
            'size' => "modal-xl",
            'header' => '<div id="acknowledge-project-exception-'.$agency->id.'-'.$q.'-modal-header"><h4>Acknowledge Form 3 Submission</h4></div>',
            'options' => ['tabindex' => false],
          ]);
        echo '<div id="acknowledge-project-exception-'.$agency->id.'-'.$q.'-modal-content"></div>';
        Modal::end();

        $script .= '
            $("#acknowledge-project-exception-'.$agency->id.'-'.$q.'-button").click(function(){
                $("#acknowledge-project-exception-'.$agency->id.'-'.$q.'-modal").modal("show").find("#acknowledge-project-exception-'.$agency->id.'-'.$q.'-modal-content").load($(this).attr("value"));
            });   
        ';

        
    }
}
$script .= '});';
$this->registerJs($script, View::POS_END);
?>
