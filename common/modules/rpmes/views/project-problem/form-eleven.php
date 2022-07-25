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
        RPMES Form 11: LIST OF PROJECT PROBLEMS/ISSUES
    </h5>
    <table class="table table-condensed table-bordered table-striped table-condensed table-responsive" cellspacing="0">
        <thead>
            <tr>
                <td colspan=2 rowspan=2 align=center><b>Name of Project/Total Project Cost</b></td>
                <td colspan=2 rowspan=2 align=center><b>Sector/Sub Sector</b></td>
                <td colspan=2 rowspan=2 align=center><b>Location</b></td>
                <td rowspan=2 align=center><b>Implementing Agency</b></td>
                <td colspan=2 align=center><b>Problems / Issues</b></td>
                <td rowspan=2 align=center><b>Strategies / Actions Taken to Resolve the Problem / Issue</b></td>
                <td rowspan=2 align=center><b>Responsible Entities / Key Actors and Their Specific Assistance</b></td>
                <td rowspan=2 align=center><b>Lessons Learned and Good Practices that could be Shared to the NPMC / Other PMCs</b></td>
            </tr>
            <tr>
                <td>Nature</td>
                <td>Details</td>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($problems)){ ?>
            <?php $idx = 1; ?>
            <?php foreach($problems as $problem){ ?>
                <tr>
                    <td colspan=2 align=center><?= $problem['projectTitle'] ?></td>
                    <td colspan=2 align=center><?= $problem['sectorTitle'] ?></td>
                    <td colspan=2 rowspan=2 align=center><?= $problem['locationTitle'] ?></td>
                    <td rowspan=2 align=center><?= $problem['agencyCode'] ?></td>
                    <td rowspan=2 align=center><?= $problem['nature'] ?></td>
                    <td rowspan=2 align=center><?= $problem['detail'] ?></td>
                    <td rowspan=2 align=center><?= $problem['strategy'] ?></td>
                    <td rowspan=2 align=center><?= $problem['responsible_entity'] ?></td>
                    <td rowspan=2 align=center><?= $problem['lesson_learned'] ?></td>
                </tr>
                <tr>
                    <td colspan=2 align=center><?= number_format($problem['totalCost'], 2) ?></td>
                    <td colspan=2 align=center><?= $problem['subSectorTitle'] ?>
                <?php $idx ++ ?>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
