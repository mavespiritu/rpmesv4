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
                    <?php if(!empty($agencies['chapterOutcomes'])){ ?>
                        <?php $j = 0; ?>
                        <?php foreach($agencies['chapterOutcomes'] as $chapterOutcome => $chapterOutcomes){ ?>
                            <tr style="font-weight: bolder;">
                                <td align=right>&nbsp;</td>
                                <td colspan=3><?= $smallCaps[$j] ?>. <?= $chapterOutcome ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['allocationAsOfReportingPeriod'], 2) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['releasesAsOfReportingPeriod'], 2) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['obligationsAsOfReportingPeriod'], 2) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['expendituresAsOfReportingPeriod'], 2) ?></td>
                                <td align=right><?= $chapterOutcomes['content']['allocationAsOfReportingPeriod'] > 0 ? number_format(($chapterOutcomes['content']['releasesAsOfReportingPeriod'] / $chapterOutcomes['content']['allocationAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                                <td align=right><?= $chapterOutcomes['content']['releasesAsOfReportingPeriod'] > 0 ? number_format(($chapterOutcomes['content']['expendituresAsOfReportingPeriod'] / $chapterOutcomes['content']['releasesAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['physicalTargetAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['physicalActualAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= count($chapterOutcomes) > 0 ? number_format(($chapterOutcomes['content']['slippage']/count($chapterOutcomes)), 2) : number_format(0, 2) ?></td>
                                <td align=right><?= $chapterOutcomes['content']['physicalTargetAsOfReportingPeriod'] > 0 ? number_format(($chapterOutcomes['content']['physicalActualAsOfReportingPeriod'] / $chapterOutcomes['content']['physicalTargetAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['malesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['femalesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['malesEmployedTargetAsOfReportingPeriod'] + $chapterOutcomes['content']['femalesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['malesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['femalesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['malesEmployedActualAsOfReportingPeriod'] + $chapterOutcomes['content']['femalesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['beneficiariesTargetAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['maleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['femaleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['maleBeneficiariesActualAsOfReportingPeriod'] + $chapterOutcomes['content']['femaleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['completed'], 0) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['behindSchedule'], 0) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['onSchedule'], 0) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['aheadOnSchedule'], 0) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['notYetStarted'], 0) ?></td>
                                <td align=right><?= number_format($chapterOutcomes['content']['completed'] + $chapterOutcomes['content']['behindSchedule'] + $chapterOutcomes['content']['onSchedule'] + $chapterOutcomes['content']['aheadOnSchedule'] + $chapterOutcomes['content']['notYetStarted'], 0) ?></td>
                            </tr>
                            <?php if(!empty($chapterOutcomes['chapters'])){ ?>
                                <?php $k = 0; ?>
                                <?php foreach($chapterOutcomes['chapters'] as $chapter => $chapters){ ?>
                                    <tr>
                                        <td align=right>&nbsp;</td>
                                        <td align=right>&nbsp;</td>
                                        <td colspan=2><?= $numbers[$k] ?>. <?= $chapter ?></td>
                                        <td align=right><?= number_format($chapters['content']['allocationAsOfReportingPeriod'], 2) ?></td>
                                        <td align=right><?= number_format($chapters['content']['releasesAsOfReportingPeriod'], 2) ?></td>
                                        <td align=right><?= number_format($chapters['content']['obligationsAsOfReportingPeriod'], 2) ?></td>
                                        <td align=right><?= number_format($chapters['content']['expendituresAsOfReportingPeriod'], 2) ?></td>
                                        <td align=right><?= $chapters['content']['allocationAsOfReportingPeriod'] > 0 ? number_format(($chapters['content']['releasesAsOfReportingPeriod'] / $chapters['content']['allocationAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                                        <td align=right><?= $chapters['content']['releasesAsOfReportingPeriod'] > 0 ? number_format(($chapters['content']['expendituresAsOfReportingPeriod'] / $chapters['content']['releasesAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                                        <td align=right><?= number_format($chapters['content']['physicalTargetAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($chapters['content']['physicalActualAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= count($chapters) > 0 ? number_format(($chapters['content']['slippage']/count($chapters)), 2) : number_format(0, 2) ?></td>
                                        <td align=right><?= $chapters['content']['physicalTargetAsOfReportingPeriod'] > 0 ? number_format(($chapters['content']['physicalActualAsOfReportingPeriod'] / $chapters['content']['physicalTargetAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                                        <td align=right><?= number_format($chapters['content']['malesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($chapters['content']['femalesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($chapters['content']['malesEmployedTargetAsOfReportingPeriod'] + $chapters['content']['femalesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($chapters['content']['malesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($chapters['content']['femalesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($chapters['content']['malesEmployedActualAsOfReportingPeriod'] + $chapters['content']['femalesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($chapters['content']['beneficiariesTargetAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($chapters['content']['maleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($chapters['content']['femaleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($chapters['content']['maleBeneficiariesActualAsOfReportingPeriod'] + $chapters['content']['femaleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                                        <td align=right><?= number_format($chapters['content']['completed'], 0) ?></td>
                                        <td align=right><?= number_format($chapters['content']['behindSchedule'], 0) ?></td>
                                        <td align=right><?= number_format($chapters['content']['onSchedule'], 0) ?></td>
                                        <td align=right><?= number_format($chapters['content']['aheadOnSchedule'], 0) ?></td>
                                        <td align=right><?= number_format($chapters['content']['notYetStarted'], 0) ?></td>
                                        <td align=right><?= number_format($chapters['content']['completed'] + $chapters['content']['behindSchedule'] + $chapters['content']['onSchedule'] + $chapters['content']['aheadOnSchedule'] + $chapters['content']['notYetStarted'], 0) ?></td>
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