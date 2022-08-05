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
        RPMES Form 6: Reports on the Status of Projects Encountering Implementation Problems <br>
        As of reporting period
    </h5>
<table class="table table-condensed table-bordered table-striped table-hover table-condensed table-responsive">
    <thead>
        <tr>
            <td rowspan=2 align=center><b>#</td>
            <td rowspan=2 colspan=2 align=center><b>Project Title</td>
            <td rowspan=2 align=center><b>Total Project Cost</b></td>
            <td rowspan=2 colspan=2 align=center><b>Sector/Subsector</b></td>
            <td rowspan=2 colspan=2 align=center><b>Location</b></td>
            <td rowspan=2 align=center><b>Implementing Agency</b></td>
            <td rowspan=2 align=center><b>Fund Utilization</b></td>
            <td colspan=3 align=center><b>Physical Status (as of Reporting Period)</b></td>
            <td rowspan=2 colspan=2 align=center><b>Issues</b></td>
            <td rowspan=2 align=center><b>Source of Information</b></td>
            <td rowspan=2 colspan=2 align=center><b>Recommendations</b></td>
        </tr>
        <tr>
            <td align=center><b>Target</b></td>
            <td align=center><b>Actual</b></td>
            <td align=center><b>Slippage</b></td>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($projects)){ ?>
        <?php $idx = 1; ?>
        <?php foreach($projects as $project){ ?>
            <?php if((($project['slippage'] <= -15) || ($project['slippage'] >= 15)) && $project['slippage'] != 1){ ?>
                <tr>
                    <td align=center><?= $idx ?></td>
                    <td colspan=2 align=center><?= $project['projectTitle'] ?></td>
                    <td align=center><?= number_format($project['totalCost'], 2) ?></td>
                    <td colspan=2 align=center><?= $project['sectorTitle']. ' / '.$project['subSectorTitle'] ?></td>
                    <td colspan=2 align=center><?= $project['locationTitle'] ?></td>
                    <td align=center><?= $project['agencyTitle'] ?></td>
                    <td align=center><?= $project['releases'] > 0 ? number_format(($project['expenditures'] / $project['releases']) * 100, 2) : number_format(0, 2) ?></td>
                    <td align=center><?= number_format($project['physicalTargetTotalPerQuarter'], 2) ?></td>
                    <td align=center><?= number_format($project['physicalAccompTotalPerQuarter'], 2) ?></td>
                    <td align=center><?= number_format($project['slippage'], 2) ?>%</td>
                    <td colspan=2 align=center><?= $project['causes'] ?></td>
                    <td align=center><?= $project['agencyTitle'] ?></td>
                    <td colspan=2 align=center><?= $project['recommendations'] ?></td>
                </tr>
                <?php $idx ++ ?>
            <?php } ?>
        <?php } ?>
    <?php } ?>
    </tbody>
</table>