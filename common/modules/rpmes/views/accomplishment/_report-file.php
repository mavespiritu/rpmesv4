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
    <thead>
        <tr>
            <td rowspan=4 style="width: 3%;">&nbsp;</td>
            <td rowspan=4 colspan=2 style="width: 7%;">
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
            <?php $i = 1; ?>
            <?php foreach ($projects as $project){ ?>
                <td>Submitted by: <br><?= $model->getSubmitterName() ?></td>
            <?php } ?>  
            <?php $i++; ?>
        <?php } ?>
    </tbody>

</table>