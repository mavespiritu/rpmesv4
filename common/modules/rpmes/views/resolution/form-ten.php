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
        RPMES Form 10: LIST OF RESOLUTIONS PASSED
    </h5>
    <table class="table table-condensed table-bordered table-striped table-condensed table-responsive" cellspacing="0">
        <thead>
            <tr>
                <td align=center><b>Quarter </b></td>
                <td align=center><b>Year </b></td>
                <td align=center><b>Resolution Number</b></td>
                <td align=center><b>Resolution/s Passed</b></td>
                <td align=center><b>Date Approved</b></td>
                <td align=center><b>RPMC Actions/Remarks</b></td>
                <td align=center><b>Attached Scanned File of the Resolution</b></td>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($resolutions)){ ?>
            <?php $idx = 1; ?>
            <?php foreach($resolutions as $resolution){ ?>
                <tr>
                    <td align=center><?= $resolution->quarter ?></td>
                    <td align=center><?= $resolution->year ?></td>
                    <td align=center><?= $resolution->resolution_number ?></td>
                    <td align=center><?= $resolution->resolution ?></td>
                    <td align=center><?= $resolution->date_approved ?></td>
                    <td align=center><?= $resolution->rpmc_action ?></td>
                    <td align=center>
                    <?php if ($resolution->files){ ?>
                        <?php foreach($resolution->files as $file){ ?>
                            <?= $file->name.'.'.$file->type.'<br>' ?>
                        <?php } ?>
                    <?php } ?>
                    </td>
                </tr>
                <?php $idx ++ ?>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
