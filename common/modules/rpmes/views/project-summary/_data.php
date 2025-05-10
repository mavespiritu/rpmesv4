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
                <td colspan=4 rowspan=3 align=center style="width: 10% !important;">Grouping</td>
                <td rowspan=3 align=center>Program/Project Title</td>
                <td rowspan=3 align=center>Implementing Agency</td>

                <td colspan=2 rowspan=2 align=center>Implementing Schedule</td>

                <td rowspan=3 align=center>Sector</td>
                <td rowspan=3 align=center>Fund Source</td>
                <td rowspan=3 align=center>Funding Agency</td>
                <td rowspan=3 align=center>Total<br>Program/Project<br>Cost (PHP)</td>
                <td colspan=6 rowspan=2 align=center>Financial Status (in PHP exact figures)</td>
                <td colspan=3 rowspan=2 align=center>Physical Accomplishment</td>
                <td colspan=3 rowspan=2 align=center>Employment Generated</td>
                <td colspan=2 rowspan=2 align=center>Number of Beneficiaries</td>
                <td colspan=5 align=center>Implementation Status</td>
                <td rowspan=3 align=center>Remarks</td>
            </tr>
            <tr style="background-color: #002060; color: white; font-weight: normal">
                <td rowspan=2 align=center>Completed</td>
                <td colspan=3 align=center>Ongoing</td>
                <td rowspan=2 align=center>Not yet started</td>
            </tr>
            <tr style="background-color: #002060; color: white; font-weight: normal">

                <td align=center>Start Date<br>(mm-dd-yy)</td>
                <td align=center>End Date<br>(mm-dd-yy)</td>

                <td align=center>Appropriations</td>
                <td align=center>Allotment</td>
                <td align=center>Obligations</td>
                <td align=center>Disbursements</td>
                <td align=center>Funding<br>Support (%)</td>
                <td align=center>Fund<br>Utilization<br>Rate (%)</td>

                <td rowspan=2 align=center>Target <br>OWPA to <br> date (%)</td>
                <td rowspan=2 align=center>Actual <br>OWPA to <br>date (%)</td>
                <td rowspan=2 align=center>Slippage</td>

                <td rowspan=2 align=center>M</td>
                <td rowspan=2 align=center>F</td>
                <td rowspan=2 align=center>Total</td>

                <td rowspan=2 align=center>Individual</td>
                <td rowspan=2 align=center>Group</td>

                <td align=center>Behind Schedule</td>
                <td align=center>On-time</td>
                <td align=center>Ahead of Schedule</td>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($data)){ ?>
            <?php 
                $allotment = $data['__grandTotal']['allotment'];
                $disbursements =$data['__grandTotal']['disbursements'];
                $appropriations = $data['__grandTotal']['appropriations'];
                $fundingSupport = $data['__grandTotal']['fundingSupport'];
                $weightedTarget = $data['__grandTotal']['weightedTarget'];
                $weightedAccomplishment = $data['__grandTotal']['weightedAccomplishment'];
                
                $fundingSupport = $appropriations > 0 ? ($allotment/$appropriations)*100 : 0;
                $fundingUtilizationRate = $allotment > 0 ? ($disbursements/$allotment)*100 : 0;
                $slippage = $weightedAccomplishment - $weightedTarget;
            ?>
                <tr style="font-weight: bolder; font-size: 20px;">
                    <td colspan=4>Grand Total</td>
                    <td align=right>&nbsp;</td>
                    <td align=right>&nbsp;</td>
                    <td align=right>&nbsp;</td>
                    <td align=right>&nbsp;</td>
                    <td align=right>&nbsp;</td>
                    <td align=right>&nbsp;</td>
                    <td align=right>&nbsp;</td>
                    <td align=right><?= number_format($data['__grandTotal']['cost'], 2) ?></td>
                    <td align=right><?= number_format($data['__grandTotal']['appropriations'], 2) ?></td>
                    <td align=right><?= number_format($data['__grandTotal']['allotment'], 2) ?></td>
                    <td align=right><?= number_format($data['__grandTotal']['obligations'], 2) ?></td>
                    <td align=right><?= number_format($data['__grandTotal']['disbursements'], 2) ?></td>
                    <td align=right><?= number_format($fundingSupport, 2) ?></td>
                    <td align=right><?= number_format($fundingUtilizationRate, 2) ?></td>
                    <td align=right><?= number_format($data['__grandTotal']['weightedTarget'], 2) ?></td>
                    <td align=right><?= number_format($data['__grandTotal']['weightedAccomplishment'], 2) ?></td>
                    <td align=right style="color: <?= $slippage < 0 ? 'red' : 'black'?>"><?= number_format($slippage, 2) ?></td>
                    <td align=right><?= number_format($data['__grandTotal']['maleEmployed'], 0) ?></td>
                    <td align=right><?= number_format($data['__grandTotal']['femaleEmployed'], 0) ?></td>
                    <td align=right><?= number_format($data['__grandTotal']['totalEmployed'], 0) ?></td>
                    <td align=right><?= number_format($data['__grandTotal']['individualBeneficiaries'], 0) ?></td>
                    <td align=right><?= number_format($data['__grandTotal']['groupBeneficiaries'], 0) ?></td>
                    <td align=right><?= number_format($data['__grandTotal']['isCompleted'], 0) ?></td>
                    <td align=right><?= number_format($data['__grandTotal']['isBehindSchedule'], 0) ?></td>
                    <td align=right><?= number_format($data['__grandTotal']['isOnTime'], 0) ?></td>
                    <td align=right><?= number_format($data['__grandTotal']['isAheadOfSchedule'], 0) ?></td>
                    <td align=right><?= number_format($data['__grandTotal']['isNotYetStarted'], 0) ?></td>
                    <td align=right>&nbsp;</td>
                </tr>
            <?php $i = 1; ?>
            <?php foreach($data as $firstLevel => $firstLevels){ ?>
                <?php if(isset($firstLevels['content'])){ ?>
                <?php
                    $allotment = $firstLevels['content']['allotment'];
                    $disbursements = $firstLevels['content']['disbursements'];
                    $appropriations = $firstLevels['content']['appropriations'];
                    $fundingSupport = $firstLevels['content']['fundingSupport'];
                    $weightedTarget = $firstLevels['content']['weightedTarget'];
                    $weightedAccomplishment = $firstLevels['content']['weightedAccomplishment'];
                    
                    $fundingSupport = $appropriations > 0 ? ($allotment/$appropriations)*100 : 0;
                    $fundingUtilizationRate = $allotment > 0 ? ($disbursements/$allotment)*100 : 0;
                    $slippage = $weightedAccomplishment - $weightedTarget;
                ?>
                <tr style="font-weight: bolder; font-size: 18px;">
                    <td>&nbsp;</td>
                    <td><?= $i ?>. <?= $firstLevel ?></td>
                    <td align=right>&nbsp;</td>
                    <td align=right>&nbsp;</td>
                    <td align=right>&nbsp;</td>
                    <td align=right>&nbsp;</td>
                    <td align=right>&nbsp;</td>
                    <td align=right>&nbsp;</td>
                    <td align=right>&nbsp;</td>
                    <td align=right>&nbsp;</td>
                    <td align=right>&nbsp;</td>
                    <td align=right><?= number_format($firstLevels['content']['cost'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['appropriations'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['allotment'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['obligations'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['disbursements'], 2) ?></td>
                    <td align=right><?= number_format($fundingSupport, 2) ?></td>
                    <td align=right><?= number_format($fundingUtilizationRate, 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['weightedTarget'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['weightedAccomplishment'], 2) ?></td>
                    <td align=right style="color: <?= $slippage < 0 ? 'red' : 'black'?>"><?= number_format($slippage, 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['maleEmployed'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['femaleEmployed'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['totalEmployed'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['individualBeneficiaries'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['groupBeneficiaries'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['isCompleted'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['isBehindSchedule'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['isOnTime'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['isAheadOfSchedule'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['isNotYetStarted'], 0) ?></td>
                    <td align=right>&nbsp;</td>
                </tr>
                <?php } ?>
                <?php if(!empty($firstLevels['firstLevels'])){ ?>
                    <?php $j = 1; ?>
                    <?php foreach($firstLevels['firstLevels'] as $secondLevel => $secondLevels){ ?>
    
                        <?php if(isset($secondLevels['content'])){ ?>
                        <?php
                            $allotment = $secondLevels['content']['allotment'];
                            $disbursements = $secondLevels['content']['disbursements'];
                            $appropriations = $secondLevels['content']['appropriations'];
                            $fundingSupport = $secondLevels['content']['fundingSupport'];
                            $weightedTarget = $secondLevels['content']['weightedTarget'];
                            $weightedAccomplishment = $secondLevels['content']['weightedAccomplishment'];
                            
                            $fundingSupport = $appropriations > 0 ? ($allotment/$appropriations)*100 : 0;
                            $fundingUtilizationRate = $allotment > 0 ? ($disbursements/$allotment)*100 : 0;
                            $slippage = $weightedAccomplishment - $weightedTarget;
                            

                        ?>
                        <tr style="font-weight: bolder; font-size: 16px;">
                            <td align=right>&nbsp;</td>
                            <td align=right>&nbsp;</td>
                            <td><?= $i.'.'.$j ?>. <?= $secondLevel ?></td>
                            <td align=right>&nbsp;</td>
                            <td align=right>&nbsp;</td>
                            <td align=right>&nbsp;</td>
                            <td align=right>&nbsp;</td>
                            <td align=right>&nbsp;</td>
                            <td align=right>&nbsp;</td>
                            <td align=right>&nbsp;</td>
                            <td align=right>&nbsp;</td>
                            <td align=right><?= number_format($secondLevels['content']['cost'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['appropriations'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['allotment'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['obligations'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['disbursements'], 2) ?></td>
                            <td align=right><?= number_format($fundingSupport, 2) ?></td>
                            <td align=right><?= number_format($fundingUtilizationRate, 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['weightedTarget'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['weightedAccomplishment'], 2) ?></td>
                            <td align=right style="color: <?= $slippage < 0 ? 'red' : 'black'?>"><?= number_format($slippage, 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['maleEmployed'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['femaleEmployed'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['totalEmployed'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['individualBeneficiaries'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['groupBeneficiaries'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['isCompleted'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['isBehindSchedule'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['isOnTime'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['isAheadOfSchedule'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['isNotYetStarted'], 0) ?></td>
                            <td>&nbsp;</td>
                        </tr>
                        <?php } ?>
                        <?php if(!empty($secondLevels['projectLevels'])){ ?>
                            <?php $k = 1; ?>
                            <?php foreach($secondLevels['projectLevels'] as $projectLevel => $projectLevels){ ?>
                                <tr style="font-size: 13px;">
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td><?php //$i.'.'.$j.'.'.$k ?><?= $projectLevels['content']['projectTitle'] ?></td>
                                    <td align=center><?= $projectLevels['content']['agencyTitle'] ?></td>
                                    <td align=center><?= $projectLevels['content']['startDate'] ?></td>
                                    <td align=center><?= $projectLevels['content']['endDate'] ?></td>
                                    <td align=center><?= $projectLevels['content']['sectorTitle'] ?></td>
                                    <td align=center><?= $projectLevels['content']['fundingSourceTitle'] ?></td>
                                    <td align=center><?= $projectLevels['content']['fundingAgencyTitle'] ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['cost'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['appropriations'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['allotment'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['obligations'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['disbursements'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['fundingSupport'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['fundingUtilizationRate'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['targetOwpa'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['actualOwpa'], 2) ?></td>
                                    <td align=right style="color: <?= $projectLevels['content']['slippage'] < 0 ? 'red' : 'black'?>"><?= number_format($projectLevels['content']['slippage'], 2) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['maleEmployed'], 0) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['femaleEmployed'], 0) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['totalEmployed'], 0) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['individualBeneficiaries'], 0) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['groupBeneficiaries'], 0) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['isCompleted'], 0) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['isBehindSchedule'], 0) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['isOnTime'], 0) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['isAheadOfSchedule'], 0) ?></td>
                                    <td align=right><?= number_format($projectLevels['content']['isNotYetStarted'], 0) ?></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <?php $k++ ?>
                            <?php } ?>
                        <?php } ?>
                        <?php if(!empty($secondLevels['secondLevels'])){ ?>
                            <?php $k = 1; ?>
                            <?php foreach($secondLevels['secondLevels'] as $thirdLevel => $thirdLevels){ ?>
                                <?php if(isset($thirdLevels['content'])){ ?>
                                <?php
                                    $allotment = $thirdLevels['content']['allotment'];
                                    $disbursements = $thirdLevels['content']['disbursements'];
                                    $appropriations = $thirdLevels['content']['appropriations'];
                                    $fundingSupport = $thirdLevels['content']['fundingSupport'];
                                    $weightedTarget = $thirdLevels['content']['weightedTarget'];
                                    $weightedAccomplishment = $thirdLevels['content']['weightedAccomplishment'];
                                    
                                    $fundingSupport = $appropriations > 0 ? ($allotment/$appropriations)*100 : 0;
                                    $fundingUtilizationRate = $allotment > 0 ? ($disbursements/$allotment)*100 : 0;
                                    $slippage = $weightedAccomplishment - $weightedTarget;
                                    

                                ?>
                                <tr style="font-weight:bolder; font-size: 15px;">
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td colspan=3><?= $i.'.'.$j.'.'.$k ?>. <?= $thirdLevel ?></td>
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td align=right><?= number_format($thirdLevels['content']['cost'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['appropriations'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['allotment'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['obligations'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['disbursements'], 2) ?></td>
                                    <td align=right><?= number_format($fundingSupport, 2) ?></td>
                                    <td align=right><?= number_format($fundingUtilizationRate, 2) ?></td>
                                    <td align=right><?= number_format($targetOwpa * 100, 2) ?></td>
                                    <td align=right><?= number_format($actualOwpa * 100, 2) ?></td>
                                    <td align=right style="color: <?= $slippage < 0 ? 'red' : 'black'?>"><?= number_format($slippage, 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['maleEmployed'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['femaleEmployed'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['totalEmployed'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['individualBeneficiaries'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['groupBeneficiaries'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['isCompleted'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['isBehindSchedule'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['isOnTime'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['isAheadOfSchedule'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['isNotYetStarted'], 0) ?></td>
                                </tr>
                                <?php } ?>
                                <?php if(!empty($thirdLevels['projectLevels'])){ ?>
                                    <?php $l = 1; ?>
                                    <?php foreach($thirdLevels['projectLevels'] as $projectLevel => $projectLevels){ ?>
                                        <tr style="font-size: 13px;">
                                            <td align=right>&nbsp;</td>
                                            <td align=right>&nbsp;</td>
                                            <td align=right>&nbsp;</td>
                                            <td align=right>&nbsp;</td>
                                            <td><?php //$i.'.'.$j.'.'.$k.'.'.$l ?><?= $projectLevels['content']['projectTitle'] ?></td>
                                            <td align=center><?= $projectLevels['content']['agencyTitle'] ?></td>
                                            <td align=center><?= $projectLevels['content']['startDate'] ?></td>
                                            <td align=center><?= $projectLevels['content']['endDate'] ?></td>
                                            <td align=center><?= $projectLevels['content']['sectorTitle'] ?></td>
                                            <td align=center><?= $projectLevels['content']['fundingSourceTitle'] ?></td>
                                            <td align=center><?= $projectLevels['content']['fundingAgencyTitle'] ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['cost'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['appropriations'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['allotment'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['obligations'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['disbursements'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['fundingSupport'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['fundingUtilizationRate'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['targetOwpa'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['actualOwpa'], 2) ?></td>
                                            <td align=right style="color: <?= $projectLevels['content']['slippage'] < 0 ? 'red' : 'black'?>"><?= number_format($projectLevels['content']['slippage'], 2) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['maleEmployed'], 0) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['femaleEmployed'], 0) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['totalEmployed'], 0) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['individualBeneficiaries'], 0) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['groupBeneficiaries'], 0) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['isCompleted'], 0) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['isBehindSchedule'], 0) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['isOnTime'], 0) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['isAheadOfSchedule'], 0) ?></td>
                                            <td align=right><?= number_format($projectLevels['content']['isNotYetStarted'], 0) ?></td>
                                            <td>&nbsp;</td>
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