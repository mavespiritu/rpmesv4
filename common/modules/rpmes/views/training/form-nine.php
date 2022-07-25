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
        RPMES Form 9: LIST OF TRAININGS AND WORKSHOPS
    </h5>
    <table class="table table-condensed table-bordered table-striped table-condensed table-responsive" cellspacing="0">
        <thead>
            <tr>
                <td rowspan=2 align=center><b>Quarter</b></td>
                <td rowspan=2 align=center><b>Year</b></td>
                <td rowspan=2 colspan=1 align=center><b>Title of Training</b></td>
                <td rowspan=2 colspan=1 align=center><b>Objective of Training</b></td>
                <td rowspan=2 colspan=1 align=center><b>Lead Office</b></td>
                <td rowspan=2 align=center><b>Participating Office/Agencies/Organizations</b></td>
                <td rowspan=2 align=center><b>Start Date</b></td>
                <td rowspan=2 align=center><b>End Date</b></td>
                <td colspan=3 align=center><b>Participants</b></td>
            </tr>
            <tr>
                <td align=center><b>Male</b></td>
                <td align=center><b>Female</b></td>
                <td align=center><b>Total</b></td>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($trainings)){ ?>
            <?php $idx = 1; ?>
            <?php foreach($trainings as $training){ ?>
                <tr>
                    <td align=center><?= $training['quarter'] ?></td>
                    <td align=center><?= $training['year'] ?></td>
                    <td align=center><?= $training['title'] ?></td>
                    <td align=center><?= $training['objective'] ?></td>
                    <td align=center><?= $training['office'] ?></td>
                    <td align=center><?= $training['organization'] ?></td>
                    <td align=center><?= $training['start_date'] ?></td>
                    <td align=center><?= $training['end_date'] ?></td>
                    <td align=center><?= $training['male_participant'] ?></td>
                    <td align=center><?= $training['female_participant'] ?></td>
                    <td align=center><?= $training['male_participant'] + $training['female_participant'] ?></td>
                </tr>
                <?php $idx ++ ?>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
