<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
?>

<p><b>No. of projects enrolled: </b> <br> <h3><?= number_format($projectTotal, 0) ?></h3></p>
<table class="table table-bordered table-condensed table-striped table-hover table-responsive">
    <thead>
        <tr>
            <th rowspan=2>Report</th>
            <td align=center colspan=4><b>Quarter</b></td>
        </tr>
        <tr>
            <td align=center style="width: 20%;"><b>Q1</b></td>
            <td align=center style="width: 20%;"><b>Q2</b></td>
            <td align=center style="width: 20%;"><b>Q3</b></td>
            <td align=center style="width: 20%;"><b>Q4</b></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Monitoring Plan</td>
            <td colspan=4 align=center><?= $monitoringPlan ? '<span class="text-green">Submitted by '.ucwords(strtolower($monitoringPlan->submitter)).' last '.date("F j, Y H:i:s", strtotime($monitoringPlan->date_submitted)).'</span>' : '<span class="text-red">No submission</span>' ?></td>
        </tr>
        <tr>
            <td>Accomplishment</td>
            <td><?= $accompQ1 ? '<span class="text-green">Submitted by '.ucwords(strtolower($accompQ1->submitter)).' last '.date("F j, Y H:i:s", strtotime($accompQ1->date_submitted)).'</span>' : '<span class="text-red">No submission</span>' ?></td>
            <td><?= $accompQ2 ? '<span class="text-green">Submitted by '.ucwords(strtolower($accompQ2->submitter)).' last '.date("F j, Y H:i:s", strtotime($accompQ2->date_submitted)).'</span>' : '<span class="text-red">No submission</span>' ?></td>
            <td><?= $accompQ3 ? '<span class="text-green">Submitted by '.ucwords(strtolower($accompQ3->submitter)).' last '.date("F j, Y H:i:s", strtotime($accompQ3->date_submitted)).'</span>' : '<span class="text-red">No submission</span>' ?></td>
            <td><?= $accompQ4 ? '<span class="text-green">Submitted by '.ucwords(strtolower($accompQ4->submitter)).' last '.date("F j, Y H:i:s", strtotime($accompQ4->date_submitted)).'</span>' : '<span class="text-red">No submission</span>' ?></td>
        </tr>
        <tr>
            <td>Project Exception</td>
            <td><?= $exceptionQ1 ? '<span class="text-green">Submitted by '.ucwords(strtolower($exceptionQ1->submitter)).' last '.date("F j, Y H:i:s", strtotime($exceptionQ1->date_submitted)).'</span>' : '<span class="text-red">No submission</span>' ?></td>
            <td><?= $exceptionQ2 ? '<span class="text-green">Submitted by '.ucwords(strtolower($exceptionQ2->submitter)).' last '.date("F j, Y H:i:s", strtotime($exceptionQ2->date_submitted)).'</span>' : '<span class="text-red">No submission</span>' ?></td>
            <td><?= $exceptionQ3 ? '<span class="text-green">Submitted by '.ucwords(strtolower($exceptionQ3->submitter)).' last '.date("F j, Y H:i:s", strtotime($exceptionQ3->date_submitted)).'</span>' : '<span class="text-red">No submission</span>' ?></td>
            <td><?= $exceptionQ4 ? '<span class="text-green">Submitted by '.ucwords(strtolower($exceptionQ4->submitter)).' last '.date("F j, Y H:i:s", strtotime($exceptionQ4->date_submitted)).'</span>' : '<span class="text-red">No submission</span>' ?></td>
        </tr>
    </tbody>
</table>