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

$newTotals = [
    'targetOwpa' => 0,
    'actualOwpa' => 0,
];

?>

<div class="summary-monitoring-report-table" style="height: 600px;">
    <table class="table table-condensed table-bordered table-striped table-hover table-condensed table-responsive" cellspacing="0" style="min-width: 3000px;">
        <thead>
            <tr style="background-color: #002060; color: white; font-weight: normal">
                <td colspan=6 rowspan=3 align=center style="width: 10% !important;">Grouping</td>
                <td rowspan=3 align=center>Total<br>Program/Project<br>Cost</td>
                <td colspan=6 align=center>Financial Status of Reporting Period</td>
                <td colspan=6 align=center>Physical Status of Reporting Period</td>
                <td colspan=3 align=center>Number of Persons Employed</td>
                <td colspan=2 align=center>Number of Beneficiaries</td>
                <td colspan=5 align=center>Implementation Status</td>
            </tr>
            <tr style="background-color: #002060; color: white; font-weight: normal">
                <td rowspan=2 align=center>Appropriations</td>
                <td rowspan=2 align=center>Allotment</td>
                <td rowspan=2 align=center>Obligations</td>
                <td rowspan=2 align=center>Disbursements</td>
                <td rowspan=2 align=center>Funding<br>Support (%)</td>
                <td rowspan=2 align=center>Fund<br>Utilization<br>Rate (%)</td>
                <td rowspan=2 align=center>Target as of<br>Reporting Period (%)</td>
                <td rowspan=2 align=center>Actual Accomplishment<br>as of<br>Reporting Period (%)</td>
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
        <?php if(!empty($data)){ ?>
            <?php $i = 1; ?>
            <?php foreach($data as $firstLevel => $firstLevels){ ?>
                <?php
                    $weight = $firstLevels['content']['cost'] > 0 ? $firstLevels['content']['cost']/$totals['cost'] : 0;
                    $targetOwpa = $totals['targetOwpa'] ? $firstLevels['content']['targetOwpa']/$totals['targetOwpa'] : 0;
                    $actualOwpa = $totals['actualOwpa'] ? $firstLevels['content']['targetOwpa']/$totals['actualOwpa'] : 0;
                    $slippage = ($targetOwpa * $weight * 100) > ($actualOwpa * $weight * 100) ? ($targetOwpa * $weight * 100) - ($actualOwpa * $weight * 100) : abs(($targetOwpa * $weight * 100) - ($actualOwpa * $weight * 100));

                    $newTotals['targetOwpa'] += $targetOwpa * 100;
                    $newTotals['actualOwpa'] += $actualOwpa * 100;
                ?>
                <tr style="font-weight: bolder; font-size: 18px;">
                    <td>&nbsp;</td>
                    <td colspan=5><?= $i ?>. <?= $firstLevel ?></td>
                    <td align=right><?= number_format($firstLevels['content']['cost'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['appropriations'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['allotment'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['obligations'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['disbursements'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['appropriations'] > 0 ? ($firstLevels['content']['allotment']/$firstLevels['content']['appropriations'])*100 : 0, 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['allotment'] > 0 ? ($firstLevels['content']['disbursements']/$firstLevels['content']['allotment'])*100 : 0, 2) ?></td>
                    <td align=right><?= number_format($targetOwpa * 100, 2) ?></td>
                    <td align=right><?= number_format($actualOwpa * 100, 2) ?></td>
                    <td align=right><?= number_format($weight, 4) ?></td>
                    <td align=right><?= number_format($targetOwpa * $weight * 100, 2) ?></td>
                    <td align=right><?= number_format($actualOwpa * $weight * 100, 2) ?></td>
                    <td align=right style="color: <?= $slippage < 0 ? 'red' : 'black'?>"><?= number_format($slippage, 2) ?></td>
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
                        <?php
                            $weight =$firstLevels['content']['cost'] > 0 ? $secondLevels['content']['cost']/$firstLevels['content']['cost'] : 0;
                            $targetOwpa = $firstLevels['content']['targetOwpa'] ? $secondLevels['content']['targetOwpa']/$firstLevels['content']['targetOwpa'] : 0;
                            $actualOwpa = $firstLevels['content']['actualOwpa'] ? $secondLevels['content']['targetOwpa']/$firstLevels['content']['actualOwpa'] : 0;
                            $slippage = ($targetOwpa * $weight * 100) > ($actualOwpa * $weight * 100) ? ($targetOwpa * $weight * 100) - ($actualOwpa * $weight * 100) : abs(($targetOwpa * $weight * 100) - ($actualOwpa * $weight * 100));

                        ?>
                        <tr style="font-weight: bolder; font-size: 16px;">
                            <td align=right>&nbsp;</td>
                            <td align=right>&nbsp;</td>
                            <td colspan=4><?= $i.'.'.$j ?>. <?= $secondLevel ?></td>
                            <td align=right><?= number_format($secondLevels['content']['cost'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['appropriations'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['allotment'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['obligations'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['disbursements'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['appropriations'] > 0 ? ($secondLevels['content']['allotment']/$secondLevels['content']['appropriations'])*100 : 0, 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['allotment'] > 0 ? ($secondLevels['content']['disbursements']/$secondLevels['content']['allotment'])*100 : 0, 2) ?></td>
                            <td align=right><?= number_format($targetOwpa * 100, 2) ?></td>
                            <td align=right><?= number_format($actualOwpa * 100, 2) ?></td>
                            <td align=right><?= number_format($weight, 3) ?></td>
                            <td align=right><?= number_format($targetOwpa * $weight * 100, 2) ?></td>
                            <td align=right><?= number_format($actualOwpa * $weight * 100, 2) ?></td>
                            <td align=right style="color: <?= $slippage < 0 ? 'red' : 'black'?>"><?= number_format($slippage, 2) ?></td>
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
                        <?php if(!empty($secondLevels['projectLevels'])){ ?>
                            <?php $k = 1; ?>
                            <?php foreach($secondLevels['projectLevels'] as $projectLevel => $projectLevels){ ?>
                                <tr style="font-size: 13px;">
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td colspan=3><?= $i.'.'.$j.'.'.$k ?>. <?= $projectLevels['content']['projectNo'].': '.$projectLevels['content']['projectTitle'] ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['cost'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['appropriations'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['allotment'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['obligations'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['disbursements'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['fundingSupport'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['fundingUtilizationRate'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['targetOwpa'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['actualOwpa'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['physicalWeights'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['physicalWeightedTarget'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['physicalWeightedAccomplishment'], 2) ?></td>
                                    <td align=right style="color: <?= $projectLevels['content']['slippage'] < 0 ? 'red' : 'black'?>"><?= number_format($projectLevels['content']['slippage'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['malesEmployedActual'], 0) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['femalesEmployedActual'], 0) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['malesEmployedActual'] + $projectLevels['content']['femalesEmployedActual'], 0) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['individualBeneficiaries'], 0) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['groupBeneficiaries'], 0) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['isCompleted'], 0) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['isBehindSchedule'], 0) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['isOnTime'], 0) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['isAheadOfSchedule'], 0) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['isNotYetStarted'], 0) ?></td>
                                </tr>
                                <?php $k++ ?>
                            <?php } ?>
                        <?php } ?>
                        <?php if(!empty($secondLevels['secondLevels'])){ ?>
                            <?php $k = 1; ?>
                            <?php foreach($secondLevels['secondLevels'] as $thirdLevel => $thirdLevels){ ?>
                                <?php
                                    $weight =$secondLevels['content']['cost'] > 0 ? $thirdLevels['content']['cost']/$secondLevels['content']['cost'] : 0;
                                    $targetOwpa = $secondLevels['content']['targetOwpa'] ? $thirdLevels['content']['targetOwpa']/$secondLevels['content']['targetOwpa'] : 0;
                                    $actualOwpa = $secondLevels['content']['actualOwpa'] ? $thirdLevels['content']['targetOwpa']/$secondLevels['content']['actualOwpa'] : 0;
                                    $slippage = ($targetOwpa * $weight * 100) > ($actualOwpa * $weight * 100) ? ($targetOwpa * $weight * 100) - ($actualOwpa * $weight * 100) : abs(($targetOwpa * $weight * 100) - ($actualOwpa * $weight * 100));

                                ?>
                                <tr style="font-weight:bolder; font-size: 15px;">
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td colspan=3><?= $i.'.'.$j.'.'.$k ?>. <?= $thirdLevel ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['cost'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['appropriations'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['allotment'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['obligations'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['disbursements'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['appropriations'] > 0 ? ($thirdLevels['content']['allotment']/$thirdLevels['content']['appropriations'])*100 : 0, 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['allotment'] > 0 ? ($thirdLevels['content']['disbursements']/$thirdLevels['content']['allotment'])*100 : 0, 2) ?></td>
                                    <td align=right><?= number_format($targetOwpa * 100, 2) ?></td>
                                    <td align=right><?= number_format($actualOwpa * 100, 2) ?></td>
                                    <td align=right><?= number_format($weight, 3) ?></td>
                                    <td align=right><?= number_format($targetOwpa * $weight * 100, 2) ?></td>
                                    <td align=right><?= number_format($actualOwpa * $weight * 100, 2) ?></td>
                                    <td align=right style="color: <?= $slippage < 0 ? 'red' : 'black'?>"><?= number_format($slippage, 2) ?></td>
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
                                <?php if(!empty($thirdLevels['projectLevels'])){ ?>
                                    <?php $l = 1; ?>
                                    <?php foreach($thirdLevels['projectLevels'] as $projectLevel => $projectLevels){ ?>
                                        <tr style="font-size: 13px;">
                                            <td align=right>&nbsp;</td>
                                            <td align=right>&nbsp;</td>
                                            <td align=right>&nbsp;</td>
                                            <td align=right>&nbsp;</td>
                                            <td colspan=2><?= $i.'.'.$j.'.'.$k.'.'.$l ?>. <?= $projectLevels['content']['projectNo'].': '.$projectLevels['content']['projectTitle'] ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['cost'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['appropriations'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['allotment'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['obligations'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['disbursements'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['fundingSupport'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['fundingUtilizationRate'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['targetOwpa'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['actualOwpa'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['physicalWeights'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['physicalWeightedTarget'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['physicalWeightedAccomplishment'], 2) ?></td>
                                            <td align=right style="color: <?= $projectLevels['content']['slippage'] < 0 ? 'red' : 'black'?>"><?= number_format($projectLevels['content']['slippage'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['malesEmployedActual'], 0) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['femalesEmployedActual'], 0) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['malesEmployedActual'] + $projectLevels['content']['femalesEmployedActual'], 0) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['individualBeneficiaries'], 0) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['groupBeneficiaries'], 0) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['isCompleted'], 0) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['isBehindSchedule'], 0) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['isOnTime'], 0) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['isAheadOfSchedule'], 0) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['isNotYetStarted'], 0) ?></td>
                                        </tr>
                                        <?php $l++ ?>
                                    <?php } ?>
                                <?php } ?>
                                <?php $k++ ?>
                            <?php } ?>
                        <?php } ?>
                        <?php $j++ ?>
                    <?php } ?>
                <?php } ?>
                <?php $i++ ?>
            <?php } ?>
        <?php } ?>
        <?php 
        $slippage = $newTotals['targetOwpa'] > $newTotals['actualOwpa'] ? $newTotals['targetOwpa'] - $newTotals['actualOwpa'] : abs($newTotals['targetOwpa'] - $newTotals['actualOwpa'])
        ?>
            <tr style="font-weight: bolder; font-size: 20px;">
                <td colspan=6>Grand Total</td>
                <td align=right><?= number_format($totals['cost'], 2) ?></td>
                <td align=right><?= number_format($totals['appropriations'], 2) ?></td>
                <td align=right><?= number_format($totals['allotment'], 2) ?></td>
                <td align=right><?= number_format($totals['obligations'], 2) ?></td>
                <td align=right><?= number_format($totals['disbursements'], 2) ?></td>
                <td align=right><?= number_format($totals['appropriations'] > 0 ? ($totals['allotment']/$totals['appropriations'])*100 : 0, 2) ?></td>
                <td align=right><?= number_format($totals['allotment'] > 0 ? ($totals['disbursements']/$totals['allotment'])*100 : 0, 2) ?></td>
                <td align=right><?= number_format($newTotals['targetOwpa'], 2) ?></td>
                <td align=right><?= number_format($newTotals['actualOwpa'], 2) ?></td>
                <td align=right><?= number_format(1, 5) ?></td>
                <td align=right><?= number_format($newTotals['targetOwpa'], 2) ?></td>
                <td align=right><?= number_format($newTotals['actualOwpa'], 2) ?></td>
                <td align=right style="color: <?= $slippage < 0 ? 'red' : 'black'?>"><?= number_format($slippage, 2) ?></td>
                <td align=right><?= number_format($totals['malesEmployedActual'], 0) ?></td>
                <td align=right><?= number_format($totals['femalesEmployedActual'], 0) ?></td>
                <td align=right><?= number_format($totals['malesEmployedActual'] + $totals['femalesEmployedActual'], 0) ?></td>
                <td align=right><?= number_format($totals['individualBeneficiaries'], 0) ?></td>
                <td align=right><?= number_format($totals['groupBeneficiaries'], 0) ?></td>
                <td align=right><?= number_format($totals['isCompleted'], 0) ?></td>
                <td align=right><?= number_format($totals['isBehindSchedule'], 0) ?></td>
                <td align=right><?= number_format($totals['isOnTime'], 0) ?></td>
                <td align=right><?= number_format($totals['isAheadOfSchedule'], 0) ?></td>
                <td align=right><?= number_format($totals['isNotYetStarted'], 0) ?></td>
            </tr>
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