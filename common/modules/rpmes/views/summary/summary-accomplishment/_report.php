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
<div class="pull-left">
    <?= $this->render('_menu', [
        'model' => $model
    ]) ?>
</div>
<div class="clearfix"></div>
<br>
<div class="summary-summary-accomplishment-table" style="height: 600px;">

    </h5>
    <table class="table table-condensed table-bordered table-striped table-hover table-condensed table-responsive" cellspacing="0" style="min-width: 3000px;">
        <thead>
            <tr>
                <td colspan=5 rowspan=3 align=center><b>Project Details</b><br>a. Project Title<br>b. Sector/Subsector<br>c. Fund Source<br>d. Project Schedule<br>e. Category</td>
                <td rowspan=3 align=center><b>Output Indicator</b></td>
                <td colspan=6 align=center><b>Financial Status of Reporting Period</b></td>
                <td colspan=4 align=center><b>Physical Status as of Reporting Period</b></td>
                <td colspan=6 align=center><b>Number of Persons Employed as of Reporting Period</b></td>
                <td colspan=5 align=center><b>Number of Beneficiaries as of Reporting Period</b></td>
                <td colspan=7 align=center><b>Project Implementation Status</b></td>
                <td rowspan=3 align=center><b>Remarks</b></td>
            </tr>
            <tr>
                <td rowspan=2 align=center><b>Allocations</b></td>
                <td rowspan=2 align=center><b>Releases</b></td>
                <td rowspan=2 align=center><b>Obligation</b></td>
                <td rowspan=2 align=center><b>Disbursement</b></td>
                <td rowspan=2 align=center><b>Funding Support (%)</b></td>
                <td rowspan=2 align=center><b>Fund Utilization (%)</b></td>
                <td rowspan=2 align=center><b>Target as of Reporting Period</b></td>
                <td rowspan=2 align=center><b>Actual Accomplishment as of Reporting Period</b></td>
                <td rowspan=2 align=center><b>Slippage as of Reporting Period (%)</b></td>
                <td rowspan=2 align=center><b>Performance as of Reporting Period</b></td>
                <td colspan=3 align=center><b>Target</b></td>
                <td colspan=3 align=center><b>Actual</b></td>
                <td colspan=2 align=center><b>Target</b></td>
                <td colspan=3 align=center><b>Actual</b></td>
                <td rowspan=2 align=center><b>Completed</b></td>
                <td colspan=3 align=center><b>Ongoing</b></td>
                <td colspan=2 align=center><b>Not Yet Started</b></td>
                <td rowspan=2 align=center><b>Total</b></td>
            </tr>
            <tr>
                <?php if($genders){ ?>
                    <?php foreach($genders as $g => $gender){ ?>
                        <td align=center><b><?= $gender ?></b></td>
                    <?php } ?>
                <?php } ?>
                <td align=center><b>Total</b></td>
                <?php if($genders){ ?>
                    <?php foreach($genders as $g => $gender){ ?>
                        <td align=center><b><?= $gender ?></b></td>
                    <?php } ?>
                <?php } ?>
                <td align=center><b>Total</b></td>
                <td align=center><b>Individual</b></td>
                <td align=center><b>Group</b></td>
                <?php if($genders){ ?>
                    <?php foreach($genders as $g => $gender){ ?>
                        <td align=center><b>Indiviual - <?= $gender ?></b></td>
                    <?php } ?>
                <?php } ?>
                <td align=center><b>Group</b></td>
                <td align=center><b>Behind Schedule</b></td>
                <td align=center><b>On-time</b></td>
                <td align=center><b>Ahead of Schedule</b></td>
                <td align=center><b>With Target</b></td>
                <td align=center><b>No Target</b></td>
            </tr>
        </thead>
        <tbody>
        <tr style="font-weight: bolder;">
        <td colspan=5>Grand Total</td>
            <td>&nbsp;</td>
            <td align=right><?= number_format($total['allocations'], 2) ?></td>
            <td align=right><?= number_format($total['releases'], 2) ?></td>
            <td align=right><?= number_format($total['obligations'], 2) ?></td>
            <td align=right><?= number_format($total['expenditures'], 2) ?></td>
            <td align=right><?= $total['allocations'] > 0 ? number_format(($total['releases'] / $total['allocations']) * 100, 2) : number_format(0, 2) ?></td>
            <td align=right><?= $total['releases'] > 0 ? number_format(($total['expenditures'] / $total['releases']) * 100, 2) : number_format(0, 2) ?></td>
            <td align=right>-</td>
            <td align=right>-</td>
            <td align=right><?= $totalPhysical['actual'] - $totalPhysical['target'] >= 0 ? number_format($totalPhysical['actual'] - $totalPhysical['target'], 2) : '('.number_format(abs($totalPhysical['actual'] - $totalPhysical['target']), 2).')' ?></td>
            <td align=right><?= $totalPhysical['target'] > 0 ? number_format(($totalPhysical['actual'] / $totalPhysical['target']) * 100, 2) : number_format(0, 2) ?></td>
            <td align=right><?= number_format($total['malesEmployedTarget'], 0) ?></td>
            <td align=right><?= number_format($total['femalesEmployedTarget'], 0) ?></td>
            <td align=right><?= number_format($total['malesEmployedTarget'] + $total['femalesEmployedTarget'], 0) ?></td>
            <td align=right><?= number_format($total['malesEmployedActual'], 0) ?></td>
            <td align=right><?= number_format($total['femalesEmployedActual'], 0) ?></td>
            <td align=right><?= number_format($total['malesEmployedActual'] + $total['femalesEmployedActual'], 0) ?></td>
            <td align=right><?= number_format($total['beneficiariesTarget'], 0) ?></td>
            <td align=right><?= number_format($total['groupBeneficiariesTarget'], 0) ?></td>
            <td align=right><?= number_format($total['maleBeneficiariesActual'], 0) ?></td>
            <td align=right><?= number_format($total['femaleBeneficiariesActual'], 0) ?></td>
            <td align=right><?= number_format($total['groupBeneficiariesActual'], 0) ?></td>
            <td align=right><?= number_format($total['completed'], 0) ?></td>
            <td align=right><?= number_format($total['behindSchedule'], 0) ?></td>
            <td align=right><?= number_format($total['onSchedule'], 0) ?></td>
            <td align=right><?= number_format($total['aheadOnSchedule'], 0) ?></td>
            <td align=right><?= number_format($total['notYetStartedWithTarget'], 0) ?></td>
            <td align=right><?= number_format($total['notYetStartedWithNoTarget'], 0) ?></td>
            <td align=right><?= number_format($total['completed'] + $total['behindSchedule'] + $total['onSchedule'] + $total['aheadOnSchedule'] + $total['notYetStartedWithTarget'] + $total['notYetStartedWithNoTarget'], 0) ?></td>
            <td>&nbsp;</td>
        </tr>
        <?php if(!empty($data)){ ?>
            <?php $i = 1; ?>
            <?php foreach($data as $firstLevel => $firstLevels){ ?>
                    <tr style="font-weight: bolder;">
                    <td colspan=5><?= $i ?>. <?= $firstLevel ?></td>
                    <td>&nbsp;</td>
                    <td align=right><?= number_format($firstLevels['content']['allocations'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['releases'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['obligations'], 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['expenditures'], 2) ?></td>
                    <td align=right><?= $firstLevels['content']['allocations'] > 0 ? number_format(($firstLevels['content']['releases'] / $firstLevels['content']['allocations']) * 100, 2) : number_format(0, 2) ?></td>
                    <td align=right><?= $firstLevels['content']['releases'] > 0 ? number_format(($firstLevels['content']['expenditures'] / $firstLevels['content']['releases']) * 100, 2) : number_format(0, 2) ?></td>
                    <td align=right><?= number_format($physical['target'][$firstLevel]['value'], 2) ?></td>
                    <td align=right><?= number_format($physical['actual'][$firstLevel]['value'], 2) ?></td>
                    <td align=right><?= $physical['actual'][$firstLevel]['value'] - $physical['target'][$firstLevel]['value'] >= 0 ? number_format($physical['actual'][$firstLevel]['value'] - $physical['target'][$firstLevel]['value'], 2) : number_format(abs($physical['actual'][$firstLevel]['value'] - $physical['target'][$firstLevel]['value']), 2) ?></td>
                    <td align=right><?= $physical['target'][$firstLevel]['value'] > 0 ? number_format(($physical['actual'][$firstLevel]['value'] / $physical['target'][$firstLevel]['value']) * 100, 2) : number_format(0, 2) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['malesEmployedTarget'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['femalesEmployedTarget'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['malesEmployedTarget'] + $firstLevels['content']['femalesEmployedTarget'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['malesEmployedActual'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['femalesEmployedActual'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['malesEmployedActual'] + $firstLevels['content']['femalesEmployedActual'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['beneficiariesTarget'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['groupBeneficiariesTarget'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['maleBeneficiariesActual'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['femaleBeneficiariesActual'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['groupBeneficiariesActual'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['completed'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['behindSchedule'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['onSchedule'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['aheadOnSchedule'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['notYetStartedWithTarget'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                    <td align=right><?= number_format($firstLevels['content']['completed'] + $firstLevels['content']['behindSchedule'] + $firstLevels['content']['onSchedule'] + $firstLevels['content']['aheadOnSchedule'] + $firstLevels['content']['notYetStartedWithTarget'] + $firstLevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                    <td>&nbsp;</td>
                    </tr>
                <?php if(!empty($firstLevels['firstLevels'])){ ?>
                    <?php $j = 1; ?>
                    <?php foreach($firstLevels['firstLevels'] as $secondLevel => $secondLevels){ ?>
                        <tr style="font-weight: bolder;">
                            <td align=right>&nbsp;</td>
                            <td colspan=4><?= $i.'.'.$j ?>.<?= str_replace("*","<br>", $secondLevel) ?></td>
                            <td align=right>&nbsp;</td>
                            <td align=right><?= number_format($secondLevels['content']['allocations'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['releases'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['obligations'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['expenditures'], 2) ?></td>
                            <td align=right><?= $secondLevels['content']['allocations'] > 0 ? number_format(($secondLevels['content']['releases'] / $secondLevels['content']['allocations']) * 100, 2) : number_format(0, 2) ?></td>
                            <td align=right><?= $secondLevels['content']['releases'] > 0 ? number_format(($secondLevels['content']['expenditures'] / $secondLevels['content']['releases']) * 100, 2) : number_format(0, 2) ?></td>
                            <td align=right><?= number_format($physical['target'][$firstLevel]['firstLevels'][$secondLevel]['value'], 2) ?></td>
                            <td align=right><?= number_format($physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['value'], 2) ?></td>
                            <td align=right><?= $physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['value'] - $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['value'] >= 0 ? number_format($physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['value'] - $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['value'], 2) : number_format(abs($physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['value'] - $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['value']), 2) ?></td>
                            <td align=right><?= $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['value'] > 0 ? number_format(($physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['value'] / $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['value']) * 100, 2) : number_format(0, 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['malesEmployedTarget'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['femalesEmployedTarget'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['malesEmployedTarget'] + $secondLevels['content']['femalesEmployedTarget'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['malesEmployedActual'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['femalesEmployedActual'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['malesEmployedActual'] + $secondLevels['content']['femalesEmployedActual'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['beneficiariesTarget'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['groupBeneficiariesTarget'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['maleBeneficiariesActual'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['femaleBeneficiariesActual'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['groupBeneficiariesActual'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['completed'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['behindSchedule'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['onSchedule'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['aheadOnSchedule'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['notYetStartedWithTarget'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['completed'] + $secondLevels['content']['behindSchedule'] + $secondLevels['content']['onSchedule'] + $secondLevels['content']['aheadOnSchedule'] + $secondLevels['content']['notYetStartedWithTarget'] + $secondLevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                            <td>&nbsp;</td>
                        </tr>
                        <?php if(!empty($secondLevels['secondLevels'])){ ?>
                            <?php $k = 1; ?>
                            <?php foreach($secondLevels['secondLevels'] as $thirdLevel => $thirdLevels){ ?>
                                <?php if(!empty($thirdLevels['thirdLevels'])){ ?>
                                <?= '<tr style="font-weight: bolder;">' ?>
                                <?php }else{ ?>
                                <?= '<tr>' ?>
                                <?php } ?>
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <?php if(!empty($thirdLevels['thirdLevels'])){ ?>
                                            <?= '<td colspan=2>'.$i.'.'.$j.'.'.$k.'.'. $thirdLevel.'</td>' ?>
                                            <?php }else{ ?>
                                            <?= '<td colspan=2>'.$i.'.'.$j.'.'.$k.
                                                '<br>a. '.$thirdLevels['content']['projectTitle'].
                                                '<br>b. '.$thirdLevels['content']['sectorTitle'].' / '.$thirdLevels['content']['subSectorTitle'].
                                                '<br>c. '.$thirdLevels['content']['fundSourceTitle'].
                                                '<br>d. '.date('F j, Y', strtotime($thirdLevels['content']['projectStartDate'])).' to '.date('F j, Y', strtotime($thirdLevels['content']['projectCompletionDate'])).
                                                '<br>e. '.$thirdLevels['content']['categoryTitle'].
                                                '<br>f. '.$thirdLevels['content']['locationTitle'].
                                            '</td>' ?>
                                    <?php } ?>
                                    <td align=right>&nbsp;</td>
                                    <?php if(!empty($thirdLevels['thirdLevels'])){ ?>
                                            <?= '<td align=right>&nbsp;</td>' ?>
                                            <?php }else{ ?>
                                            <?= '<td align=right>'. $thirdLevels['content']['indicator'].'</td>' ?>
                                    <?php } ?>
                                    <td align=right><?= number_format($thirdLevels['content']['allocations'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['releases'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['obligations'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['expenditures'], 2) ?></td>
                                    <td align=right><?= $thirdLevels['content']['allocations'] > 0 ? number_format(($thirdLevels['content']['releases'] / $thirdLevels['content']['allocations']) * 100, 2) : number_format(0, 2) ?></td>
                                    <td align=right><?= $thirdLevels['content']['releases'] > 0 ? number_format(($thirdLevels['content']['expenditures'] / $thirdLevels['content']['releases']) * 100, 2) : number_format(0, 2) ?></td>
                                    <?php if(!empty($thirdLevels['thirdLevels'])){ ?>
                                            <?= '<td align=right>'. number_format($physical['target'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['value'], 2).'</td>' ?>
                                            <?= '<td align=right>'. number_format($physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['value'], 2).'</td>' ?>
                                            <?php }else{ ?>
                                            <?= '<td align=right>'. number_format($thirdLevels['content']['projectPhysicalTarget'], 2).'</td>' ?>
                                            <?= '<td align=right>'. number_format($thirdLevels['content']['projectPhysicalAccomp'], 2).'</td>' ?>
                                            <?php } ?>
                                            <?php if(!empty($thirdLevels['thirdLevels'])){ ?>
                                            <td align=right><?= $physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['value'] - $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['value'] >= 0 ? number_format($physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['value'] - $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['value'], 2) : number_format(abs($physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['value'] - $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['value']), 2)?></td>
                                            <td align=right><?= $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['value'] > 0 ? number_format(($physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['value'] / $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['value']) * 100, 2) : number_format(0, 2) ?></td>
                                            <?php }else{ ?>
                                            <td align=right><?= number_format(($thirdLevels['content']['projectPhysicalAccomp'] - $thirdLevels['content']['projectPhysicalTarget']), 2)?></td>
                                            <td align=right><?= $thirdLevels['content']['projectPhysicalTarget'] > 0 ? number_format(($thirdLevels['content']['projectPhysicalAccomp'] / $thirdLevels['content']['projectPhysicalTarget']) * 100, 2) : number_format(0, 2) ?></td>
                                    <?php } ?>
                                    <td align=right><?= number_format($thirdLevels['content']['malesEmployedTarget'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['femalesEmployedTarget'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['malesEmployedTarget'] + $thirdLevels['content']['femalesEmployedTarget'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['malesEmployedActual'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['femalesEmployedActual'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['malesEmployedActual'] + $thirdLevels['content']['femalesEmployedActual'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['beneficiariesTarget'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['groupBeneficiariesTarget'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['maleBeneficiariesActual'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['femaleBeneficiariesActual'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['groupBeneficiariesActual'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['completed'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['behindSchedule'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['onSchedule'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['aheadOnSchedule'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['notYetStartedWithTarget'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['completed'] + $thirdLevels['content']['behindSchedule'] + $thirdLevels['content']['onSchedule'] + $thirdLevels['content']['aheadOnSchedule'] + $thirdLevels['content']['notYetStartedWithTarget'] + $thirdLevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <?php if(!empty($thirdLevels['thirdLevels'])){ ?>
                                    <?php $l = 1; ?>
                                    <?php foreach($thirdLevels['thirdLevels'] as $fourthLevel => $fourthLevels){ ?>
                                        <?php if(!empty($fourthLevels['fourthLevels'])){ ?>
                                        <?= '<tr style="font-weight: bolder;">'; ?>
                                        <?php }else{ ?>
                                        <?= '<tr>'; ?>
                                        <?php } ?>
                                            <td align=right>&nbsp;</td>
                                            <td align=right>&nbsp;</td>
                                            <td align=right>&nbsp;</td>
                                            <?php if(!empty($fourthLevels['fourthLevels'])){ ?>
                                            <?= '<td colspan=2>'.$i.'.'.$j.'.'.$k.'.'.$l.'. '. $fourthLevel.'</td>' ?>
                                            <?php }else{ ?>
                                            <?= '<td colspan=2>'.$i.'.'.$j.'.'.$k.'.'.$l.
                                                '<br>a. '.$fourthLevels['content']['projectTitle'].
                                                '<br>b. '.$fourthLevels['content']['sectorTitle'].' / '.$fourthLevels['content']['subSectorTitle'].
                                                '<br>c. '.$fourthLevels['content']['fundSourceTitle'].
                                                '<br>d. '.date('F j, Y', strtotime($fourthLevels['content']['projectStartDate'])).' to '.date('F j, Y', strtotime($fourthLevels['content']['projectCompletionDate'])).
                                                '<br>e. '.$fourthLevels['content']['categoryTitle'].
                                                '<br>f. '.$fourthLevels['content']['locationTitle'].
                                            '</td>' ?>
                                            <?php } ?>
                                            <?php if(!empty($fourthLevels['fourthLevels'])){ ?>
                                            <?= '<td align=right>&nbsp;</td>' ?>
                                            <?php }else{ ?>
                                            <?= '<td align=right>'. $fourthLevels['content']['indicator'].'</td>' ?>
                                            <?php } ?>
                                            <td align=right><?= number_format($fourthLevels['content']['allocations'], 2) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['releases'], 2) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['obligations'], 2) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['expenditures'], 2) ?></td>
                                            <td align=right><?= $fourthLevels['content']['allocations'] > 0 ? number_format(($fourthLevels['content']['releases'] / $fourthLevels['content']['allocations']) * 100, 2) : number_format(0, 2) ?></td>
                                            <td align=right><?= $fourthLevels['content']['releases'] > 0 ? number_format(($fourthLevels['content']['expenditures'] / $fourthLevels['content']['releases']) * 100, 2) : number_format(0, 2) ?></td>
                                            <?php if(!empty($fourthLevels['fourthLevels'])){ ?>
                                            <?= '<td align=right>'. number_format($physical['target'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['value'], 2).'</td>' ?>
                                            <?= '<td align=right>'. number_format($physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['value'], 2).'</td>' ?>
                                            <?php }else{ ?>
                                            <?= '<td align=right>'. number_format($fourthLevels['content']['projectPhysicalTarget'], 2).'</td>' ?>
                                            <?= '<td align=right>'. number_format($fourthLevels['content']['projectPhysicalAccomp'], 2).'</td>' ?>
                                            <?php } ?>
                                            <?php if(!empty($fourthLevels['fourthLevels'])){ ?>
                                            <td align=right><?= $physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['value'] - $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['value'] >= 0 ? number_format($physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['value'] - $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['value'], 2) : '('.number_format(abs($physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['value'] - $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['value']), 2).')' ?></td>
                                            <td align=right><?= $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['value'] > 0 ? number_format(($physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['value'] / $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['value']) * 100, 2) : number_format(0, 2) ?></td>
                                            <?php }else{ ?>
                                            <td align=right><?= number_format(($fourthLevels['content']['projectPhysicalAccomp'] - $fourthLevels['content']['projectPhysicalTarget']), 2)?></td>
                                            <td align=right><?= $fourthLevels['content']['projectPhysicalTarget'] > 0 ? number_format(($fourthLevels['content']['projectPhysicalAccomp'] / $fourthLevels['content']['projectPhysicalTarget']) * 100, 2) : number_format(0, 2) ?></td>
                                            <?php } ?>
                                            <td align=right><?= number_format($fourthLevels['content']['malesEmployedTarget'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['femalesEmployedTarget'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['malesEmployedTarget'] + $fourthLevels['content']['femalesEmployedTarget'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['malesEmployedActual'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['femalesEmployedActual'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['malesEmployedActual'] + $fourthLevels['content']['femalesEmployedActual'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['beneficiariesTarget'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['groupBeneficiariesTarget'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['maleBeneficiariesActual'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['femaleBeneficiariesActual'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['groupBeneficiariesActual'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['completed'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['behindSchedule'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['onSchedule'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['aheadOnSchedule'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['notYetStartedWithTarget'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['completed'] + $fourthLevels['content']['behindSchedule'] + $fourthLevels['content']['onSchedule'] + $fourthLevels['content']['aheadOnSchedule'] + $fourthLevels['content']['notYetStartedWithTarget'] + $fourthLevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <?php if(!empty($fourthLevels['fourthLevels'])){ ?>
                                            <?php $m = 1; ?>
                                            <?php foreach($fourthLevels['fourthLevels'] as $fifthlevel => $fifthlevels){ ?>
                                                <?php if(!empty($fourthLevels['fourthLevels'])){ ?>
                                                <?= '<tr style="font-weight: bolder;">'; ?>
                                                <?php }else{ ?>
                                                <?= '<tr>'; ?>
                                                <?php } ?>
                                                    <td align=right>&nbsp;</td>
                                                    <td align=right>&nbsp;</td>
                                                    <td align=right>&nbsp;</td>
                                                    <?php if(!empty($fifthlevels['fifthlevels'])){ ?>
                                                    <?= '<td colspan=2>'.$i.'.'.$j.'.'.$k.'.'.$l.'.'.$m.'. '. $fifthlevel.'</td>' ?>
                                                    <?php }else{ ?>
                                                    <?= '<td colspan=2>'.$i.'.'.$j.'.'.$k.'.'.$l.'.'.$m.
                                                        '<br>a. '.$fifthlevels['content']['projectTitle'].
                                                        '<br>b. '.$fifthlevels['content']['sectorTitle'].' / '.$fifthlevels['content']['subSectorTitle'].
                                                        '<br>c. '.$fifthlevels['content']['fundSourceTitle'].
                                                        '<br>d. '.date('F j, Y', strtotime($fifthlevels['content']['projectStartDate'])).' to '.date('F j, Y', strtotime($fifthlevels['content']['projectCompletionDate'])).
                                                        '<br>e. '.$fifthlevels['content']['categoryTitle'].
                                                        '<br>f. '.$fifthlevels['content']['locationTitle'].
                                                    '</td>' ?>
                                                    <?php } ?>
                                                    <?php if(!empty($fifthlevels['fifthlevels'])){ ?>
                                                    <?= '<td align=right>&nbsp;</td>' ?>
                                                    <?php }else{ ?>
                                                    <?= '<td align=right>'. $fifthlevels['content']['indicator'].'</td>' ?>
                                                    <?php } ?>
                                                    <td align=right><?= number_format($fifthlevels['content']['allocations'], 2) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['releases'], 2) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['obligations'], 2) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['expenditures'], 2) ?></td>
                                                    <td align=right><?= $fifthlevels['content']['allocations'] > 0 ? number_format(($fifthlevels['content']['releases'] / $fifthlevels['content']['allocations']) * 100, 2) : number_format(0, 2) ?></td>
                                                    <td align=right><?= $fifthlevels['content']['releases'] > 0 ? number_format(($fifthlevels['content']['expenditures'] / $fifthlevels['content']['releases']) * 100, 2) : number_format(0, 2) ?></td>
                                                    <?php if(!empty($fifthlevels['fifthlevels'])){ ?>
                                                    <?= '<td align=right>'. number_format($physical['target'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['fourthLevels'][$fifthlevel]['value'], 2).'</td>' ?>
                                                    <?= '<td align=right>'. number_format($physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['fourthLevels'][$fifthlevel]['value'], 2).'</td>' ?>
                                                    <?php }else{ ?>
                                                    <?= '<td align=right>'. number_format($fifthlevels['content']['projectPhysicalTarget'], 2).'</td>' ?>
                                                    <?= '<td align=right>'. number_format($fifthlevels['content']['projectPhysicalAccomp'], 2).'</td>' ?>
                                                    <?php } ?>
                                                    <?php if(!empty($fifthlevels['fifthlevels'])){ ?>
                                                    <td align=right><?= $physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['fourthLevels'][$fifthlevel]['value'] - $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['fourthLevels'][$fifthlevel]['value'] >= 0 ? number_format($physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['fourthLevels'][$fifthlevel]['value'] - $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['fourthLevels'][$fifthlevel]['value'], 2) : '('.number_format(abs($physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['fourthLevels'][$fifthlevel]['value'] - $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['fourthLevels'][$fifthlevel]['value']), 2).')' ?></td>
                                                    <td align=right><?= $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['fourthLevels'][$fifthlevel]['value'] > 0 ? number_format(($physical['actual'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['fourthLevels'][$fifthlevel]['value'] / $physical['target'][$firstLevel]['firstLevels'][$secondLevel]['secondLevels'][$thirdLevel]['thirdLevels'][$fourthLevel]['fourthLevels'][$fifthlevel]['value']) * 100, 2) : number_format(0, 2) ?></td>
                                                    <?php }else{ ?>
                                                    <td align=right><?= number_format(($fifthlevels['content']['projectPhysicalAccomp'] - $fifthlevels['content']['projectPhysicalTarget']), 2)?></td>
                                                    <td align=right><?= $fifthlevels['content']['projectPhysicalTarget'] > 0 ? number_format(($fifthlevels['content']['projectPhysicalAccomp'] / $fifthlevels['content']['projectPhysicalTarget']) * 100, 2) : number_format(0, 2) ?></td>
                                                    <?php } ?>
                                                    <td align=right><?= number_format($fifthlevels['content']['malesEmployedTarget'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['femalesEmployedTarget'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['malesEmployedTarget'] + $fifthlevels['content']['femalesEmployedTarget'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['malesEmployedActual'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['femalesEmployedActual'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['malesEmployedActual'] + $fifthlevels['content']['femalesEmployedActual'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['beneficiariesTarget'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['groupBeneficiariesTarget'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['maleBeneficiariesActual'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['femaleBeneficiariesActual'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['groupBeneficiariesActual'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['completed'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['behindSchedule'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['onSchedule'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['aheadOnSchedule'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['notYetStartedWithTarget'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                                                    <td align=right><?= number_format($fifthlevels['content']['completed'] + $fifthlevels['content']['behindSchedule'] + $fifthlevels['content']['onSchedule'] + $fifthlevels['content']['aheadOnSchedule'] + $fifthlevels['content']['notYetStartedWithTarget'] + $fifthlevels['content']['notYetStartedWithNoTarget'], 0) ?></td>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                <?php $m++ ?>
                                            <?php } ?>
                                        <?php } ?>
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
        <tr style="font-weight: bolder;">
            <td colspan=4>Grand Total</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td align=right><?= number_format($total['allocations'], 2) ?></td>
            <td align=right><?= number_format($total['releases'], 2) ?></td>
            <td align=right><?= number_format($total['obligations'], 2) ?></td>
            <td align=right><?= number_format($total['expenditures'], 2) ?></td>
            <td align=right><?= $total['allocations'] > 0 ? number_format(($total['releases'] / $total['allocations']) * 100, 2) : number_format(0, 2) ?></td>
            <td align=right><?= $total['releases'] > 0 ? number_format(($total['expenditures'] / $total['releases']) * 100, 2) : number_format(0, 2) ?></td>
            <td align=right>-</td>
            <td align=right>-</td>
            <td align=right><?= $totalPhysical['actual'] - $totalPhysical['target'] >= 0 ? number_format($totalPhysical['actual'] - $totalPhysical['target'], 2) : '('.number_format(abs($totalPhysical['actual'] - $totalPhysical['target']), 2).')' ?></td>
            <td align=right><?= $totalPhysical['target'] > 0 ? number_format(($totalPhysical['actual'] / $totalPhysical['target']) * 100, 2) : number_format(0, 2) ?></td>
            <td align=right><?= number_format($total['malesEmployedTarget'], 0) ?></td>
            <td align=right><?= number_format($total['femalesEmployedTarget'], 0) ?></td>
            <td align=right><?= number_format($total['malesEmployedTarget'] + $total['femalesEmployedTarget'], 0) ?></td>
            <td align=right><?= number_format($total['malesEmployedActual'], 0) ?></td>
            <td align=right><?= number_format($total['femalesEmployedActual'], 0) ?></td>
            <td align=right><?= number_format($total['malesEmployedActual'] + $total['femalesEmployedActual'], 0) ?></td>
            <td align=right><?= number_format($total['beneficiariesTarget'], 0) ?></td>
            <td align=right><?= number_format($total['groupBeneficiariesTarget'], 0) ?></td>
            <td align=right><?= number_format($total['maleBeneficiariesActual'], 0) ?></td>
            <td align=right><?= number_format($total['femaleBeneficiariesActual'], 0) ?></td>
            <td align=right><?= number_format($total['groupBeneficiariesActual'], 0) ?></td>
            <td align=right><?= number_format($total['completed'], 0) ?></td>
            <td align=right><?= number_format($total['behindSchedule'], 0) ?></td>
            <td align=right><?= number_format($total['onSchedule'], 0) ?></td>
            <td align=right><?= number_format($total['aheadOnSchedule'], 0) ?></td>
            <td align=right><?= number_format($total['notYetStartedWithTarget'], 0) ?></td>
            <td align=right><?= number_format($total['notYetStartedWithNoTarget'], 0) ?></td>
            <td align=right><?= number_format($total['completed'] + $total['behindSchedule'] + $total['onSchedule'] + $total['aheadOnSchedule'] + $total['notYetStartedWithTarget'] + $total['notYetStartedWithNoTarget'], 0) ?></td>
            <td>&nbsp;</td>
        </tr>
        </tbody>
    </table>
</div>
<?php
    $script = '
        $(document).ready(function(){
            $(".summary-summary-accomplishment-table").freezeTable({
                "scrollable": true,
            });
        });
    ';

    $this->registerJs($script, View::POS_END);
?>