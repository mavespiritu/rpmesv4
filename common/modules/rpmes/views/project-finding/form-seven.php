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
        RPMES Form 7: LIST OF PROJECT FINDINGS
    </h5>
    <table class="table table-condensed table-bordered table-striped table-condensed table-responsive">
        <thead>
            <tr>
                <td rowspan=2 align=center><b>Quarter</b></td>
                <td rowspan=2 align=center><b>Year</b></td>
                <td colspan=2 align=center><b>Name of Project</b></td>
                <td colspan=2 align=center><b>Sector</b></td>
                <td colspan=2 rowspan=2 align=center><b>Location</b></td>
                <td rowspan=2 align=center><b>Implementing Agency</b></td>
                <td rowspan=2 align=center><b>Date of Inspection</b></td>
                <td rowspan=2 align=center><b>Major Finding</b></td>
                <td rowspan=2 align=center><b>Issues</b></td>
                <td rowspan=2 align=center><b>Action</b></td>
            </tr>
            <tr>
                <td colspan=2 align=center><b>Total Project Cost</b></td>
                <td colspan=2 align=center><b>Sub Sector</b></td>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($projectFindings)){ ?>
            <?php $idx = 1; ?>
            <?php foreach($projectFindings as $projectFinding){ ?>
                <tr>
                    <td rowspan=2 align=center><?= $projectFinding['quarter'] ?></td>
                    <td rowspan=2 align=center><?= $projectFinding['year'] ?></td>
                    <td colspan=2 align=center><?= $projectFinding['projectTitle'] ?></td>
                    <td colspan=2 align=center><?= $projectFinding['sectorTitle'] ?></td>
                    <td colspan=2 rowspan=2 align=center><?= $projectFinding['locationTitle'] ?></td>
                    <td rowspan=2 align=center><?= $projectFinding['agencyCode'] ?></td>
                    <td rowspan=2 align=center><?= date("F j, Y", strtotime($projectFinding['inspection_date'])) ?></td>
                    <td rowspan=2 align=center><?= $projectFinding['major_finding'] ?></td>
                    <td rowspan=2 align=center><?= $projectFinding['issues'] ?></td>
                    <td rowspan=2 align=center><?= $projectFinding['action'] ?></td>
                </tr>
                <tr>
                    <td colspan=2 align=center><?= number_format($projectFinding['totalCost'], 2) ?></td>
                    <td colspan=2 align=center><?= $projectFinding['subSectorTitle'] ?> </td>
                </tr>
                <?php $idx ++ ?>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
