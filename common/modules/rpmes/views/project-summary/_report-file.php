<?php if($type != 'pdf'){ ?>
    <style>
    @media print {
        body {-webkit-print-color-adjust: exact; }
        *{
            page-break-before: avoid !important;
            page-break-after: avoid !important;
            page-break-inside: avoid !important;
        }
        table { page-break-inside:avoid !important;}
        tr    { page-break-inside:avoid !important; page-break-after:avoid !important;}
        thead { display:table-header-group !important;}
        tfoot { display:table-footer-group !important;}
    }
    *{
        font-family: "Arial";
    }
    p{
        font-family: "Arial";
    }
    table{
        font-family: "Arial";
        border-collapse: collapse;
        width: 100%;
    }
    thead{
        font-size: 14px;
        text-align: center;
        vertical-align: middle;
    }

    td{
        font-size: 14px;
        border: 1px solid black;
        padding: 5px;
        vertical-align: middle;
    }

    th{
        text-align: center;
        border: 1px solid black;
        padding: 5px;
        vertical-align: middle;
    }
    h1,h2,h3,h4,h5,h6{
        text-align: center;
        font-weight: bolder;
    }
</style>
<?php } ?>
<h5 style="text-align: right; font-family: 'Arial'; font-size: 14px; font-weight: bolder;">RPMES FORM 5</h5>
<br>
<h5 style="text-align: center; font-family: 'Arial'; font-size: 14px;">
    REGIONAL PROJECT MONITORING AND EVALUATION SYSTEM (RPMES) <br>
    SUMMARY OF FINANCIAL AND PHYSICAL ACCOMPLISHMENTS<br>
    <?php if($year != ''){ ?>
        <?php if($quarter != ''){ ?>
            <?php if($quarter == 'Q1'){ ?>
                <?= 'As of March '.$year.' (Quarterly)' ?>
            <?php }else if($quarter == 'Q2'){ ?>
                <?= 'As of June '.$year.' (Quarterly)' ?>
            <?php }else if($quarter == 'Q3'){ ?>
                <?= 'As of September '.$year.' (Quarterly)' ?>
            <?php }else if($quarter == 'Q4'){ ?>
                <?= 'As of December '.$year.' (Quarterly)' ?>
            <?php } ?>
        <?php }else{ ?>
            <?= 'As of '.$year ?>
        <?php } ?>
    <?php } ?>
</h5>
<table class="table table-condensed table-bordered table-striped table-condensed table-responsive" cellspacing="0">
    <thead>
        <tr style="background-color: #002060; color: white">
            <td rowspan=2 style="font-weight: bolder;" align=center>#</td>
            <td rowspan=2 style="font-weight: bolder;" align=center>Program/Project Title</td>
            <td rowspan=2 style="font-weight: bolder;" align=center>Implementing Agency</td>
            <td colspan=2 style="font-weight: bolder;" align=center>Implementing Schedule</td>
            <td rowspan=2 style="font-weight: bolder;" align=center>Sector</td>
            <td rowspan=2 style="font-weight: bolder;" align=center>Fund Source</td>
            <td rowspan=2 style="font-weight: bolder;" align=center>Funding Agency</td>
            <td rowspan=2 style="font-weight: bolder;" align=center>Total Program/Project Cost (PHP)</td>
            <td colspan=6 style="font-weight: bolder;" align=center>Financial Status (in PHP exact figures)</td>
            <td colspan=3 style="font-weight: bolder;" align=center>Physical Accomplishment</td>
            <td colspan=2 style="font-weight: bolder;" align=center>Employment Generated</td>
            <td rowspan=2 style="font-weight: bolder;" align=center>Remarks</td>
        </tr>
        <tr style="background-color: #002060; color: white">
            <td style="font-weight: bolder;" align=center>Start Date (mm-dd-yyyy)</td>
            <td style="font-weight: bolder;" align=center>End Date (mm-dd-yyyy)</td>
            <td style="font-weight: bolder;" align=center>Appropriations</td>
            <td style="font-weight: bolder;" align=center>Allotment</td>
            <td style="font-weight: bolder;" align=center>Obligations</td>
            <td style="font-weight: bolder;" align=center>Disbursements</td>
            <td style="font-weight: bolder;" align=center>Funding Support (%)</td>
            <td style="font-weight: bolder;" align=center>Fund Utilization (%)</td>
            <td style="font-weight: bolder;" align=center>Target OWPA to date (%)</td>
            <td style="font-weight: bolder;" align=center>Actual OWPA to date (%)</td>
            <td style="font-weight: bolder;" align=center>Slippage</td>
            <td style="font-weight: bolder;" align=center>M</td>
            <td style="font-weight: bolder;" align=center>F</td>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($records)){ ?>
        <?php $idx = 1; ?>
        <?php foreach($records as $record){ ?>
            <tr>
                <td><?= $idx ?></td>
                <td><?= $record['projectTitle'] ?></td>
                <td align=center><?= $record['agencyTitle'] ?></td>
                <td align=center><?=  $type == 'excel' ? "'".$record['startDate'] : $record['startDate']?></td>
                <td align=center><?=  $type == 'excel' ? "'".$record['endDate'] : $record['endDate']?></td>
                <td align=center><?= $record['sectorTitle'] ?></td>
                <td align=center><?= $record['fundingSourceTitle'] ?></td>
                <td align=center><?= $record['fundingAgencyTitle'] ?></td>
                <td align=right><?= number_format(floatval($record['cost']), 2) ?></td>
                <td align=right><?= number_format(floatval($record['appropriations']), 2) ?></td>
                <td align=right><?= number_format(floatval($record['allotment']), 2) ?></td>
                <td align=right><?= number_format(floatval($record['obligations']), 2) ?></td>
                <td align=right><?= number_format(floatval($record['disbursements']), 2) ?></td>
                <td align=center><?= $record['appropriations'] > 0 ? number_format((floatval($record['allotment'])/floatval($record['appropriations']))*100, 2) : 0 ?></td>
                <td align=center><?= $record['allotment'] > 0 ? number_format((floatval($record['disbursements'])/floatval($record['allotment']))*100, 2) : 0 ?></td>
                <td align=center><?= number_format(floatval($record['targetOwpa']), 2) ?></td>
                <td align=center><?= number_format(floatval($record['actualOwpa']), 2) ?></td>
                <td align=center><?= number_format(floatval($record['slippage']), 2) ?></td>
                <td align=center><?= number_format(floatval($record['maleEmployed']), 0) ?></td>
                <td align=center><?= number_format(floatval($record['femaleEmployed']), 0) ?></td>
                <td><?= strip_tags($record['remarks']) ?></td>
            </tr>
            <?php $idx++ ?>
        <?php } ?>
    <?php } ?>
    </tbody>
</table>
<br>
<table cellspacing="0">
    <tr>
        <td>Submitted by:</td>
        <td align=center style="width: 30%;">&nbsp;</td>
        <td>Approved by:</td>
        <td align=center style="width: 30%;"><?= $director ? $director->value : '' ?></td>
    </tr>
    <tr>
        <td>Designation/Office:</td>
        <td align=center>&nbsp;</td>
        <td>&nbsp;</td>
        <td align=center>Regional Director</td>
    </tr>
    <tr>
        <td>Date:</td>
        <td>&nbsp;</td>
        <td>Date:</td>
        <td>&nbsp;</td>
    </tr>
</table>