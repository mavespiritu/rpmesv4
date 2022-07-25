<?php if($type != 'pdf'){ ?>
    <style>
    table{
        font-family: "Arial";
        border-collapse: collapse;
        width: 100%;
    }
    thead{
        font-size: 12px;
        text-align: center;
    }

    td{
        font-size: 10px;
        border: 1px solid black;
        padding: 5px;
    }

    th{
        text-align: center;
        border: 1px solid black;
        padding: 5px;
    }
</style>
<?php } ?>
<table class="table table-condensed table-bordered table-striped table-condensed table-responsive">
        <thead>
            <tr>
                <td colspan=4 rowspan=3 align=center><b>Project Details</b></td>
                <td colspan=6 align=center><b>Financial Status of Reporting Period</b></td>
                <td colspan=4 align=center><b>Physical Status as of Reporting Period</b></td>
                <td colspan=6 align=center><b>Number of Persons Employed as of Reporting Period</b></td>
                <td colspan=4 align=center><b>Number of Beneficiaries as of Reporting Period</b></td>
                <td colspan=6 align=center><b>Project Implementation Status</b></td>
            </tr>
            <tr>
                <td rowspan=2 align=center><b>Allocation</b></td>
                <td rowspan=2 align=center><b>Releases</b></td>
                <td rowspan=2 align=center><b>Obligation</b></td>
                <td rowspan=2 align=center><b>Disbursement</b></td>
                <td rowspan=2 align=center><b>Funding Support (%)</b></td>
                <td rowspan=2 align=center><b>Fund Utilization (%)</b></td>
                <td rowspan=2 align=center><b>Target as of Reporting Period</b></td>
                <td rowspan=2 align=center><b>Actual Accomplishment as of Reporting Period</b></td>
                <td rowspan=2 align=center><b>Slippage as of Reporting Period (%)</b></td>
                <td rowspan=2 align=center><b>Performance as of Reporting Period</b></td>
                <td colspan=3 align=center><b>Target Persons Employed as of Reporting Period</b></td>
                <td colspan=3 align=center><b>Actual Person Employed as of Reporting Period</b></td>
                <td align=center><b>Target Beneficiaries as of Reporting Period</b></td>
                <td colspan=3 align=center><b>Actual Number as of Reporting Period</b></td>
                <td rowspan=2 align=center><b>Completed</b></td>
                <td colspan=3 align=center><b>Ongoing</b></td>
                <td rowspan=2 align=center><b>Not yet started</b></td>
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
                <td align=center><b>Total</b></td>
                <?php if($genders){ ?>
                    <?php foreach($genders as $g => $gender){ ?>
                        <td align=center><b><?= $gender ?></b></td>
                    <?php } ?>
                <?php } ?>
                <td align=center><b>Total</b></td>
                <td align=center><b>Behind Schedule</b></td>
                <td align=center><b>On-time</b></td>
                <td align=center><b>Ahead of Schedule</b></td>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($data)){ ?>
            <?php $i = 0; ?>
            <?php foreach($data as $agency => $agencies){ ?>
                    <tr style="font-weight: bolder;">
                        <td colspan=4><?= $bigCaps[$i] ?>. <?= $agency ?></td>
                        <td align=right><?= number_format($agencies['content']['allocationAsOfReportingPeriod'], 2) ?></td>
                        <td align=right><?= number_format($agencies['content']['releasesAsOfReportingPeriod'], 2) ?></td>
                        <td align=right><?= number_format($agencies['content']['obligationsAsOfReportingPeriod'], 2) ?></td>
                        <td align=right><?= number_format($agencies['content']['expendituresAsOfReportingPeriod'], 2) ?></td>
                        <td align=right><?= $agencies['content']['allocationAsOfReportingPeriod'] > 0 ? number_format(($agencies['content']['releasesAsOfReportingPeriod'] / $agencies['content']['allocationAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                        <td align=right><?= $agencies['content']['releasesAsOfReportingPeriod'] > 0 ? number_format(($agencies['content']['expendituresAsOfReportingPeriod'] / $agencies['content']['releasesAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                        <td align=right><?= number_format($agencies['content']['physicalTargetAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($agencies['content']['physicalActualAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= count($agencies) > 0 ? number_format(($agencies['content']['slippage']/count($agencies)), 2) : number_format(0, 2) ?></td>
                        <td align=right><?= $agencies['content']['physicalTargetAsOfReportingPeriod'] > 0 ? number_format(($agencies['content']['physicalActualAsOfReportingPeriod'] / $agencies['content']['physicalTargetAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                        <td align=right><?= number_format($agencies['content']['malesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($agencies['content']['femalesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($agencies['content']['malesEmployedTargetAsOfReportingPeriod'] + $agencies['content']['femalesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($agencies['content']['malesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($agencies['content']['femalesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($agencies['content']['malesEmployedActualAsOfReportingPeriod'] + $agencies['content']['femalesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($agencies['content']['beneficiariesTargetAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($agencies['content']['maleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($agencies['content']['femaleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($agencies['content']['maleBeneficiariesActualAsOfReportingPeriod'] + $agencies['content']['femaleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($agencies['content']['completed'], 0) ?></td>
                        <td align=right><?= number_format($agencies['content']['behindSchedule'], 0) ?></td>
                        <td align=right><?= number_format($agencies['content']['onSchedule'], 0) ?></td>
                        <td align=right><?= number_format($agencies['content']['aheadOnSchedule'], 0) ?></td>
                        <td align=right><?= number_format($agencies['content']['notYetStarted'], 0) ?></td>
                        <td align=right><?= number_format($agencies['content']['completed'] + $agencies['content']['behindSchedule'] + $agencies['content']['onSchedule'] + $agencies['content']['aheadOnSchedule'] + $agencies['content']['notYetStarted'], 0) ?></td>
                    </tr>
                    <?php if(!empty($agencies['sectors'])){ ?>
                        <?php $m = 0; ?>
                        <?php foreach($agencies['sectors'] as $sector => $sectors){ ?>
                            <tr style="font-weight: bolder;">
                                <td align=right>&nbsp;</td>
                                <td colspan=3><?= $smallCaps[$m] ?>. <?= $sector ?></td>
                                <td align=right><?= number_format($sectors['content']['allocationAsOfReportingPeriod'], 2) ?></td>
                                <td align=right><?= number_format($sectors['content']['releasesAsOfReportingPeriod'], 2) ?></td>
                                <td align=right><?= number_format($sectors['content']['obligationsAsOfReportingPeriod'], 2) ?></td>
                                <td align=right><?= number_format($sectors['content']['expendituresAsOfReportingPeriod'], 2) ?></td>
                                <td align=right><?= $sectors['content']['allocationAsOfReportingPeriod'] > 0 ? number_format(($sectors['content']['releasesAsOfReportingPeriod'] / $sectors['content']['allocationAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                                <td align=right><?= $sectors['content']['releasesAsOfReportingPeriod'] > 0 ? number_format(($sectors['content']['expendituresAsOfReportingPeriod'] / $sectors['content']['releasesAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                                <td align=right><?= number_format($sectors['content']['physicalTargetAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['physicalActualAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= count($sectors) > 0 ? number_format(($sectors['content']['slippage']/count($sectors)), 2) : number_format(0, 2) ?></td>
                                <td align=right><?= $sectors['content']['physicalTargetAsOfReportingPeriod'] > 0 ? number_format(($sectors['content']['physicalActualAsOfReportingPeriod'] / $sectors['content']['physicalTargetAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                                <td align=right><?= number_format($sectors['content']['malesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['femalesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['malesEmployedTargetAsOfReportingPeriod'] + $sectors['content']['femalesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['malesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['femalesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['malesEmployedActualAsOfReportingPeriod'] + $sectors['content']['femalesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['beneficiariesTargetAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['maleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['femaleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['maleBeneficiariesActualAsOfReportingPeriod'] + $sectors['content']['femaleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['completed'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['behindSchedule'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['onSchedule'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['aheadOnSchedule'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['notYetStarted'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['completed'] + $sectors['content']['behindSchedule'] + $sectors['content']['onSchedule'] + $sectors['content']['aheadOnSchedule'] + $sectors['content']['notYetStarted'], 0) ?></td>
                            </tr>
                            <?php if(!empty($sectors['categories'])){ ?>
                                <?php $j = 0; ?>
                                <?php foreach($sectors['categories'] as $category => $categories){ ?>
                                    <tr style="font-weight: bolder;">
                                        <td align=right>&nbsp;</td>
                                        <td align=right>&nbsp;</td>
                                        <td colspan=2><?= $numbers[$j] ?>. <?= $category ?></td>
                                        <td align=right><?= number_format($categories['content']['allocationAsOfReportingPeriod'], 2) ?></td>
                                        <td align=right><?= number_format($categories['content']['releasesAsOfReportingPeriod'], 2) ?></td>
                                        <td align=right><?= number_format($categories['content']['obligationsAsOfReportingPeriod'], 2) ?></td>
                                        <td align=right><?= number_format($categories['content']['expendituresAsOfReportingPeriod'], 2) ?></td>
                                        <td align=right><?= $categories['content']['allocationAsOfReportingPeriod'] > 0 ? number_format(($categories['content']['releasesAsOfReportingPeriod'] / $categories['content']['allocationAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                                        <td align=right><?= $categories['content']['releasesAsOfReportingPeriod'] > 0 ? number_format(($categories['content']['expendituresAsOfReportingPeriod'] / $categories['content']['releasesAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                                        <td align=right><?= number_format($categories['content']['physicalTargetAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($categories['content']['physicalActualAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= count($categories) > 0 ? number_format(($categories['content']['slippage']/count($categories)), 2) : number_format(0, 2) ?></td>
                                        <td align=right><?= $categories['content']['physicalTargetAsOfReportingPeriod'] > 0 ? number_format(($categories['content']['physicalActualAsOfReportingPeriod'] / $categories['content']['physicalTargetAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                                        <td align=right><?= number_format($categories['content']['malesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($categories['content']['femalesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($categories['content']['malesEmployedTargetAsOfReportingPeriod'] + $categories['content']['femalesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($categories['content']['malesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($categories['content']['femalesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($categories['content']['malesEmployedActualAsOfReportingPeriod'] + $categories['content']['femalesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($categories['content']['beneficiariesTargetAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($categories['content']['maleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($categories['content']['femaleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($categories['content']['maleBeneficiariesActualAsOfReportingPeriod'] + $categories['content']['femaleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($categories['content']['completed'], 0) ?></td>
                                        <td align=right><?= number_format($categories['content']['behindSchedule'], 0) ?></td>
                                        <td align=right><?= number_format($categories['content']['onSchedule'], 0) ?></td>
                                        <td align=right><?= number_format($categories['content']['aheadOnSchedule'], 0) ?></td>
                                        <td align=right><?= number_format($categories['content']['notYetStarted'], 0) ?></td>
                                        <td align=right><?= number_format($categories['content']['completed'] + $categories['content']['behindSchedule'] + $categories['content']['onSchedule'] + $categories['content']['aheadOnSchedule'] + $categories['content']['notYetStarted'], 0) ?></td>
                                    </tr>
                                    <?php if(!empty($categories['sdgGoals'])){ ?>
                                        <?php $k = 0; ?>
                                        <?php foreach($categories['sdgGoals'] as $sdgGoal => $sdgGoals){ ?>
                                            <tr>
                                                <td align=right>&nbsp;</td>
                                                <td align=right>&nbsp;</td>
                                                <td align=right>&nbsp;</td>
                                                <td><?= $numbers[$j].'.'.$numbers[$k] ?>. <?= $sdgGoal ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['allocationAsOfReportingPeriod'], 2) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['releasesAsOfReportingPeriod'], 2) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['obligationsAsOfReportingPeriod'], 2) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['expendituresAsOfReportingPeriod'], 2) ?></td>
                                                <td align=right><?= $sdgGoals['content']['allocationAsOfReportingPeriod'] > 0 ? number_format(($sdgGoals['content']['releasesAsOfReportingPeriod'] / $sdgGoals['content']['allocationAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                                                <td align=right><?= $sdgGoals['content']['releasesAsOfReportingPeriod'] > 0 ? number_format(($sdgGoals['content']['expendituresAsOfReportingPeriod'] / $sdgGoals['content']['releasesAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['physicalTargetAsOfReportingPeriod'], 0) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['physicalActualAsOfReportingPeriod'], 0) ?></td>
                                                <td align=right><?= count($sdgGoals) > 0 ? number_format(($sdgGoals['content']['slippage']/count($sdgGoals)), 2) : number_format(0, 2) ?></td>
                                                <td align=right><?= $sdgGoals['content']['physicalTargetAsOfReportingPeriod'] > 0 ? number_format(($sdgGoals['content']['physicalActualAsOfReportingPeriod'] / $sdgGoals['content']['physicalTargetAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['malesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['femalesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['malesEmployedTargetAsOfReportingPeriod'] + $sdgGoals['content']['femalesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['malesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['femalesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['malesEmployedActualAsOfReportingPeriod'] + $sdgGoals['content']['femalesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['beneficiariesTargetAsOfReportingPeriod'], 0) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['maleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['femaleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['maleBeneficiariesActualAsOfReportingPeriod'] + $sdgGoals['content']['femaleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['completed'], 0) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['behindSchedule'], 0) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['onSchedule'], 0) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['aheadOnSchedule'], 0) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['notYetStarted'], 0) ?></td>
                                                <td align=right><?= number_format($sdgGoals['content']['completed'] + $sdgGoals['content']['behindSchedule'] + $sdgGoals['content']['onSchedule'] + $sdgGoals['content']['aheadOnSchedule'] + $sdgGoals['content']['notYetStarted'], 0) ?></td>
                                            </tr>
                                            <?php $k++ ?>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php $j++ ?>
                                <?php } ?>
                            <?php } ?>
                            <?php $m++ ?>
                        <?php } ?>
                    <?php } ?>
                <?php $i++ ?>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>