<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;
use yii\bootstrap\ButtonDropdown;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\DueDateSearch */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="summary-monitoring-report-table" style="height: 600px;">
    <table class="table table-condensed table-bordered table-striped table-hover table-condensed table-responsive" cellspacing="0" style="min-width: 3000px;">
        <thead>
            <tr style="background-color: #002060; color: white; font-weight: normal">
                <td colspan=5 rowspan=3 align=center style="width: 10% !important;">Program/Project Title</td>
                <td colspan=2 align=center>Implementation Schedule</td>
                <td rowspan=3 align=center>Fund Source</td>
                <td rowspan=3 align=center>Funding Agency</td>
                <td rowspan=3 align=center>Total<br>Program/Project<br>Cost</td>
                <td colspan=6 align=center>Financial Status of Reporting Period</td>
                <td colspan=6 align=center>Physical Status of Reporting Period</td>
                <td colspan=3 align=center>Number of Persons Employed</td>
                <td colspan=2 align=center>Number of Beneficiaries</td>
                <td colspan=5 align=center>Implementation Status</td>
            </tr>
            <tr style="background-color: #002060; color: white; font-weight: normal">
                <td rowspan=2 align=center>Start Date<br>(mm-dd-yy)</td>
                <td rowspan=2 align=center>End Date<br>(mm-dd-yy)</td>
                <td rowspan=2 align=center>Appropriations</td>
                <td rowspan=2 align=center>Allotment</td>
                <td rowspan=2 align=center>Obligations</td>
                <td rowspan=2 align=center>Disbursements</td>
                <td rowspan=2 align=center>Funding<br>Support (%)</td>
                <td rowspan=2 align=center>Fund<br>Utilization<br>Rate (%)</td>
                <td rowspan=2 align=center>Target as of<br>Reporting Period</td>
                <td rowspan=2 align=center>Actual Accomplishment<br>as of<br>Reporting Period</td>
                <td rowspan=2 align=center>Weights</td>
                <td rowspan=2 align=center>Weighted<br>Target (%)</td>
                <td rowspan=2 align=center>Weighted<br>Accomplishment (%)</td>
                <td rowspan=2 align=center>Slippage (%)</td>
                <td rowspan=2 align=center>Male</td>
                <td rowspan=2 align=center>Female</td>
                <td rowspan=2 align=center>Total</td>
                <td rowspan=2 align=center>Individual</td>
                <td rowspan=2 align=center>Group</td>
                <td rowspan=2 align=center>Completed</td>
                <td colspan=3 align=center>Ongoing</td>
                <td rowspan=2 align=center>Not yet started</td>
            </tr>
            <tr style="background-color: #002060; color: white; font-weight: normal">
                <td align=center>Behind Schedule</td>
                <td align=center>On-time</td>
                <td align=center>Ahead of Schedule</td>
            </tr>
        </thead>
        <tbody>
            <tr style="font-weight: bolder;">
                <td colspan=5>Grand Total</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align=right><?= number_format($totals['cost'], 2) ?></td>
                <td align=right><?= number_format($totals['appropriations'], 2) ?></td>
                <td align=right><?= number_format($totals['allotment'], 2) ?></td>
                <td align=right><?= number_format($totals['obligations'], 2) ?></td>
                <td align=right><?= number_format($totals['disbursements'], 2) ?></td>
                <td align=right><?= number_format($totals['fundingSupport'], 2) ?></td>
                <td align=right><?= number_format($totals['fundingUtilizationRate'], 2) ?></td>
                <td align=right><?= number_format($totals['targetOwpa'], 2) ?></td>
                <td align=right><?= number_format($totals['actualOwpa'], 2) ?></td>
                <td align=right><?= number_format($totals['physicalWeights'], 2) ?></td>
                <td align=right><?= number_format($totals['physicalWeightedTarget'], 2) ?></td>
                <td align=right><?= number_format($totals['physicalWeightedAccomplishment'], 2) ?></td>
                <td align=right><?= number_format($totals['slippage'], 2) ?></td>
                <td align=right><?= number_format($totals['malesEmployedActual'], 0) ?></td>
                <td align=right><?= number_format($totals['femalesEmployedActual'], 0) ?></td>
                <td align=right><?= number_format($totals['malesEmployedActual'] + $totals['femalesEmployedActual'], 0) ?></td>
                <td align=right><?= number_format($totals['individualBeneficiaries'], 0) ?></td>
                <td align=right><?= number_format($totals['groupBeneficiaries'], 0) ?></td>
                <td align=right><?= number_format( $totals['isCompleted'], 0) ?></td>
                <td align=right><?= number_format( $totals['isBehindSchedule'], 0) ?></td>
                <td align=right><?= number_format( $totals['isOnTime'], 0) ?></td>
                <td align=right><?= number_format( $totals['isAheadOfSchedule'], 0) ?></td>
                <td align=right><?= number_format( $totals['isNotYetStarted'], 0) ?></td>
            </tr>
            <?php if(!empty($data)){ ?>
            <?php $i = 1; ?>
            <?php foreach($data as $firstLevel => $firstLevels){ ?>
                <tr style="font-weight: bolder;">
                    <td>&nbsp;</td>
                    <td colspan=4><?= $i ?>. <?= $firstLevel ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align=right><?= number_format($firstLevels['content']['cost'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['appropriations'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['allotment'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['obligations'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['disbursements'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['fundingSupport'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['fundingUtilizationRate'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['targetOwpa'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['actualOwpa'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['physicalWeights'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['physicalWeightedTarget'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['physicalWeightedAccomplishment'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['slippage'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['malesEmployedActual'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['femalesEmployedActual'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['malesEmployedActual'] + $firstLevels['content']['femalesEmployedActual'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['individualBeneficiaries'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['groupBeneficiaries'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['isCompleted'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['isBehindSchedule'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['isOnTime'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['isAheadOfSchedule'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['isNotYetStarted'], 0) ?></td>
                </tr>
                <?php if(!empty($firstLevels['firstLevels'])){ ?>
                    <?php $j = 1; ?>
                    <?php foreach($firstLevels['firstLevels'] as $secondLevel => $secondLevels){ ?>
                        <tr>
                            <td align=right>&nbsp;</td>
                            <td align=right>&nbsp;</td>
                            <td colspan=3><?= $i.'.'.$j ?>. <?= $secondLevel ?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align=right><?= number_format($secondLevels['content']['cost'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['appropriations'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['allotment'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['obligations'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['disbursements'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['fundingSupport'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['fundingUtilizationRate'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['targetOwpa'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['actualOwpa'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['physicalWeights'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['physicalWeightedTarget'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['physicalWeightedAccomplishment'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['slippage'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['malesEmployedActual'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['femalesEmployedActual'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['malesEmployedActual'] + $secondLevels['content']['femalesEmployedActual'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['individualBeneficiaries'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['groupBeneficiaries'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['isCompleted'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['isBehindSchedule'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['isOnTime'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['isAheadOfSchedule'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['isNotYetStarted'], 0) ?></td>
                        </tr>
                        <?php if(!empty($secondLevels['secondLevels'])){ ?>
                            <?php $k = 1; ?>
                            <?php foreach($secondLevels['secondLevels'] as $thirdLevel => $thirdLevels){ ?>
                                <tr>
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td colspan=2><?= $i.'.'.$j.'.'.$k ?>. <?= $thirdLevel ?></td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td align=right><?= number_format($thirdLevels['content']['cost'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['appropriations'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['allotment'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['obligations'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['disbursements'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['fundingSupport'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['fundingUtilizationRate'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['targetOwpa'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['actualOwpa'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['physicalWeights'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['physicalWeightedTarget'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['physicalWeightedAccomplishment'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['slippage'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['malesEmployedActual'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['femalesEmployedActual'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['malesEmployedActual'] + $thirdLevels['content']['femalesEmployedActual'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['individualBeneficiaries'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['groupBeneficiaries'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['isCompleted'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['isBehindSchedule'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['isOnTime'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['isAheadOfSchedule'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['isNotYetStarted'], 0) ?></td>
                                </tr>
                                <?php $k++ ?>
                            <?php } ?>
                        <?php } ?>
                        <?php $j++ ?>
                    <?php } ?>
                <?php } ?>
                <?php $i++ ?>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php
    $script = '
        $(document).ready(function(){
            $(".summary-monitoring-report-table").freezeTable({
                "scrollable": true,
            });
        });
    ';

    $this->registerJs($script, View::POS_END);
?>