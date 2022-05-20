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
                        <td align=right style="width: 50%; vertical-align: top;"><?= $agency->getMonitoringReportSubmission($getData['year'], $q) ? '<span class="label label-success">'.$agency->getMonitoringReportSubmission($getData['year'], $q)->submitter.'</span>' : '<span class="label label-danger">No submission</span>' ?></td>
                    </tr>
                    <tr>
                        <td align=right style="vertical-align: top;">Date Submitted:</td>
                        <td align=right style="vertical-align: top;"><?= $agency->getMonitoringReportSubmission($getData['year'], $q) ? date("F j, Y H:i:s", strtotime($agency->getMonitoringReportSubmission($getData['year'], $q)->date_submitted)) : '-' ?></td>
                    </tr>
                    <tr>
                        <td align=right style="vertical-align: top;">Acknowledged By:</td>
                        <td align=right style="vertical-align: top;"><?= $agency->getMonitoringReportAcknowledgment($getData['year'], $q) ? '<span class="label label-success">'.$agency->getMonitoringReportAcknowledgment($getData['year'], $q)->acknowledger.'</span>' : '<span class="label label-danger">No acknowledgment</span>' ?></td>
                    </tr>
                    <tr>
                        <td align=right style="vertical-align: top;">Date Acknowledged:</td>
                        <td align=right style="vertical-align: top;"><?= $agency->getMonitoringReportAcknowledgment($getData['year'], $q) ? date("F j, Y H:i:s", strtotime($agency->getMonitoringReportAcknowledgment($getData['year'], $q)->date_acknowledged)) : '-' ?></td>
                    </tr>
                </table>
                <br>
                <?= $agency->getMonitoringReportSubmission($getData['year'], $q) ? Html::button('<i class="fa fa-edit"></i> Acknowledge', ['value' => Url::to(['/rpmes/acknowledgment/acknowledge-monitoring-report', 'id' => $agency->getMonitoringReportSubmission($getData['year'], $q)->id]), 'class' => 'btn btn-primary btn-xs btn-block', 'id' => 'acknowledge-monitoring-report-'.$agency->id.'-'.$q.'-button']) : '' ?>
                <?= $agency->getMonitoringReportAcknowledgment($getData['year'], $q) ? Html::button('<i class="fa fa-print"></i> Print', ['onClick' => 'printAcknowledgmentMonitoringReport('.$agency->getMonitoringReportAcknowledgment($getData['year'], $q)->id.')', 'class' => 'btn btn-danger btn-xs btn-block']) : '' ?>
            </td>
        <?php } ?>
    <?php } ?>
</tr>

<?php
$script = '$(document).ready(function(){';
if(!empty($quarters)){
    foreach($quarters as $q => $quarter){
        Modal::begin([
            'id' => 'acknowledge-monitoring-report-'.$agency->id.'-'.$q.'-modal',
            'size' => "modal-xl",
            'header' => '<div id="acknowledge-monitoring-report-'.$agency->id.'-'.$q.'-modal-header"><h4>Acknowledge Monitoring Report</h4></div>',
            'options' => ['tabindex' => false],
          ]);
        echo '<div id="acknowledge-monitoring-report-'.$agency->id.'-'.$q.'-modal-content"></div>';
        Modal::end();

        $script .= '
            $("#acknowledge-monitoring-report-'.$agency->id.'-'.$q.'-button").click(function(){
                $("#acknowledge-monitoring-report-'.$agency->id.'-'.$q.'-modal").modal("show").find("#acknowledge-monitoring-report-'.$agency->id.'-'.$q.'-modal-content").load($(this).attr("value"));
            });   
        ';

        
    }
}
$script .= '});';
$this->registerJs($script, View::POS_END);
?>
