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
<h5 class="text-center" style="text-align:center;">REGIONAL PROJECT MONITORING AND EVALUATION SYSTEM (RPMES) <br>
        RPMES Form 2: ACCOMPLISHMENTS
    </h5>
<table class="table table-condensed table-bordered table-striped table-condensed table-responsive">
    <thead>
        <tr>
            <td rowspan=3 >&nbsp;</td>
            <td rowspan=3 colspan=2>
                <b>
                (a) Project ID <br>
                (b) Name of Project <br>
                (c) Project Schedule <br>
                (d) Location <br>
                (e) Funding Source
                </b>
            </td>
            <td colspan=8 align=center><b>Financial Status (PhP)</b></td>
            <td rowspan=3 align=center><b>Output Indicator</b></td>
            <td colspan=4 align=center><b>Physical Status</b></td>
            <td colspan=6 align=center><b>No. of Persons Employed</b></td>
            <td colspan=7 align=center><b>No. of Beneficiaries</b></td>
            <td rowspan=3 align=center><b>Remarks</b></td>
            <td rowspan=3 align=center><b>Action</b></td>
        </tr>
        <tr>
            <td colspan=2 align=center><b>Allocation</b></td>
            <td colspan=2 align=center><b>Releases</b></td>
            <td colspan=2 align=center><b>Obligation</b></td>
            <td colspan=2 align=center><b>Disbursements</b></td>
            <td rowspan=2 align=center><b>Target to Date</b></td>
            <td rowspan=2 align=center><b>Target for the Qtr</b></td>
            <td rowspan=2 align=center><b>Actual to Date</b></td>
            <td rowspan=2 align=center><b>Actual for the Qtr</b></td>
            <td colspan=3 align=center><b>Target</b></td>
            <td colspan=3 align=center><b>Actual</b></td>
            <td colspan=2 align=center><b>Target</b></td>
            <td colspan=5 align=center><b>Actual</b></td>
        </tr>
        <tr>
            <td align=center><b>As of Reporting Period</b></td>
            <td align=center><b>For the Qtr</b></td>
            <td align=center><b>As of Reporting Period</b></td>
            <td align=center><b>For the Qtr</b></td>
            <td align=center><b>As of Reporting Period</b></td>
            <td align=center><b>For the Qtr</b></td>
            <td align=center><b>As of Reporting Period</b></td>
            <td align=center><b>For the Qtr</b></td>
            <?php if(!empty($genders)){ ?>
                <?php foreach($genders as $gender){ ?>
                    <td align=center><b><?= $gender ?></b></td>
                <?php } ?>
            <?php } ?>
            <td align=center><b>Total</b></td>
            <?php if(!empty($genders)){ ?>
                <?php foreach($genders as $gender){ ?>
                    <td align=center><b><?= $gender ?></b></td>
                <?php } ?>
            <?php } ?>
            <td align=center><b>Total</b></td>
            <td align=center><b>Individual</b></td>
            <td align=center><b>Group</b></td>
            <?php if(!empty($genders)){ ?>
                <?php foreach($genders as $gender){ ?>
                    <td align=center><b><?= $gender ?></b></td>
                <?php } ?>
            <?php } ?>
            <td align=center><b>Total</b></td>
            <td align=center><b>Group</b></td>
            <td align=center><b>Total</b></td>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($projects)){ ?>
            <?php $idx = 1; ?>
            <?php foreach($projects as $project){ ?>
                <tr>
                    <td align=center>Submitted by: <br><?= $project['submitterName'] ?> at <?= date("F j, Y H:i:s", strtotime($project['date_submitted'])) ?></td>
                    <td align=center><?= $idx ?></td>
                    <td>
                        (a) <?= $project['projectNo'] ?> <br>
                        (b) <?= $project['projectTitle'] ?> <br>
                        (c) <?= date("F j, Y ", strtotime($project['startDate'])) ?> to <?= date("F j, Y", strtotime($project['completionDate'])) ?> <br>
                        (d) <?= $project['locationTitle'] ?> <br>
                        (e) <?= $project['fundSourceTitle'] ?> <br>
                    </td>
                    <td align=right><?= number_format($project['allocationsAsOf'], 2) ?></td>
                    <td align=right><b><?= number_format($project['allocationPerQtr'], 2) ?></b></td>
                    <td align=right><?= number_format($project['releasesAsOf'], 2) ?></td>
                    <td align=right><b><?= number_format($project['releasesPerQtr'], 2) ?></b></td>
                    <td align=right><?= number_format($project['obligationsAsOf'], 2) ?></td>
                    <td align=right><b><?= number_format($project['obligationsPerQtr'], 2) ?></b></td>
                    <td align=right><?= number_format($project['expendituresAsOf'], 2) ?></td>
                    <td align=right><b><?= number_format($project['expendituresPerQtr'], 2) ?></b></td>
                    <td><?= $project['dataType'] != "" ? $project['indicator'].'<br>('.$project['dataType'].')' : $project['indicator'].'<br>(No Data Type)' ?></td>
                    <td align=center><?= $project['isPercent'] > true ? number_format($project['physicalTargetTotalPerQtr'], 2).'%' : number_format($project['physicalTargetTotalPerQtr'], 0) ?></td>
                    <td align=center><?= $project['isPercent'] > true ? number_format($project['physicalTargetPerQtr'], 2).'%' : number_format($project['physicalTargetPerQtr'], 0) ?></td>
                    <td align=center><b><?= $project['isPercent'] > true ? number_format($project['physicalAccompTotalPerQuarter'], 2).'%' : number_format($project['physicalAccompTotalPerQuarter'], 0) ?></b></td>
                    <td align=center><?= $project['isPercent'] > true ? number_format($project['physicalAccompPerQuarter'], 2).'%' : number_format($project['physicalAccompPerQuarter'], 0) ?></td>
                    <td align=center><?= number_format($project['malesEmployedTarget'], 0) ?></td>
                    <td align=center><?= number_format($project['femalesEmployedTarget'], 0) ?></td>
                    <td align=center><b><?= number_format(($project['malesEmployedTarget'] + $project['femalesEmployedTarget']), 0) ?></b></td>
                    <td align=center><?= number_format($project['malesEmployedActual'], 0) ?></td>
                    <td align=center><?= number_format($project['femalesEmployedActual'], 0) ?></td>
                    <td align=center><b><?= number_format(($project['malesEmployedActual'] + $project['femalesEmployedActual']), 0) ?></b></td>
                    <td align=center><?= number_format($project['beneficiariesTarget'], 0 ) ?></td>
                    <td align=center><?= number_format($project['groupBeneficiariesTarget'], 0 ) ?></td>
                    <td align=center><?= number_format($project['maleBeneficiariesActual'], 0) ?></td>
                    <td align=center><?= number_format($project['femaleBeneficiariesActual'], 0) ?></td>
                    <td align=center><b><?= number_format(($project['maleBeneficiariesActual'] + $project['femaleBeneficiariesActual']), 0) ?></b></td>
                    <td align=center><?= number_format($project['groupBeneficiariesActual'], 0) ?></td>
                    <td align=center><?= number_format($project['groupBeneficiariesActual'], 0) ?></td>
                    <td align=center><?= $project['remarks'] == "" ? "No Remarks" : $project['remarks'] ?></td>
                    <td align=center><?= $project['completed'] >= 1 ? 'Yes' : 'No' ?></td>
                </tr>
                <?php $idx ++ ?>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>