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
            <?php foreach($data as $location => $locations){ ?>
                    <tr style="font-weight: bolder;">
                        <td colspan=4><?= $bigCaps[$i] ?>. <?= $location ?></td>
                        <td align=right><?= number_format($locations['content']['allocationAsOfReportingPeriod'], 2) ?></td>
                        <td align=right><?= number_format($locations['content']['releasesAsOfReportingPeriod'], 2) ?></td>
                        <td align=right><?= number_format($locations['content']['obligationsAsOfReportingPeriod'], 2) ?></td>
                        <td align=right><?= number_format($locations['content']['expendituresAsOfReportingPeriod'], 2) ?></td>
                        <td align=right><?= $locations['content']['allocationAsOfReportingPeriod'] > 0 ? number_format(($locations['content']['releasesAsOfReportingPeriod'] / $locations['content']['allocationAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                        <td align=right><?= $locations['content']['releasesAsOfReportingPeriod'] > 0 ? number_format(($locations['content']['expendituresAsOfReportingPeriod'] / $locations['content']['releasesAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                        <td align=right><?= number_format($locations['content']['physicalTargetAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($locations['content']['physicalActualAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= count($locations) > 0 ? number_format(($locations['content']['slippage']/count($locations)), 2) : number_format(0, 2) ?></td>
                        <td align=right><?= $locations['content']['physicalTargetAsOfReportingPeriod'] > 0 ? number_format(($locations['content']['physicalActualAsOfReportingPeriod'] / $locations['content']['physicalTargetAsOfReportingPeriod']) * 100, 2) : number_format(0, 2) ?></td>
                        <td align=right><?= number_format($locations['content']['malesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($locations['content']['femalesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($locations['content']['malesEmployedTargetAsOfReportingPeriod'] + $locations['content']['femalesEmployedTargetAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($locations['content']['malesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($locations['content']['femalesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($locations['content']['malesEmployedActualAsOfReportingPeriod'] + $locations['content']['femalesEmployedActualAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($locations['content']['beneficiariesTargetAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($locations['content']['maleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($locations['content']['femaleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($locations['content']['maleBeneficiariesActualAsOfReportingPeriod'] + $locations['content']['femaleBeneficiariesActualAsOfReportingPeriod'], 0) ?></td>
                        <td align=right><?= number_format($locations['content']['completed'], 0) ?></td>
                        <td align=right><?= number_format($locations['content']['behindSchedule'], 0) ?></td>
                        <td align=right><?= number_format($locations['content']['onSchedule'], 0) ?></td>
                        <td align=right><?= number_format($locations['content']['aheadOnSchedule'], 0) ?></td>
                        <td align=right><?= number_format($locations['content']['notYetStarted'], 0) ?></td>
                        <td align=right><?= number_format($locations['content']['completed'] + $locations['content']['behindSchedule'] + $locations['content']['onSchedule'] + $locations['content']['aheadOnSchedule'] + $locations['content']['notYetStarted'], 0) ?></td>
                    </tr>
                <?php if(!empty($locations['sectors'])){ ?>
                    <?php $j = 0; ?>
                    <?php foreach($locations['sectors'] as $sector => $sectors){ ?>
                        <tr>
                            <td align=right>&nbsp;</td>
                            <td colspan=3><?= $smallCaps[$j] ?>. <?= $sector ?></td>
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
                        <?php $j++ ?>
                    <?php } ?>
                <?php } ?>
                <?php $i++ ?>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>