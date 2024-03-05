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
<h5 style="text-align: right; font-family: 'Arial'; font-size: 14px; font-weight: bolder;">RPMES FORM 11</h5>
<br>
<h5 style="text-align: center; font-family: 'Arial'; font-size: 14px;">
    REGIONAL PROJECT MONITORING AND EVALUATION SYSTEM (RPMES) <br>
    KEY LESSONS LEARNED FROM ISSUES RESOLVED AND BEST PRACTICES <br>
    <?= $year != '' ? 'In '.$year : '' ?>
</h5>
<table class="table table-condensed table-bordered table-striped table-condensed table-responsive" cellspacing="0">
    <thead>
        <tr style="background-color: #002060; color: white">
            <td style="font-weight: bolder;" rowspan=2 align=center>#</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Program/Project Title</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Location</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Implementing Agency</td>
            <td style="font-weight: bolder;" colspan=2 align=center>Problem/Issue</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Strategies/Actions Taken to Resolve the Problem/Issue</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Responsible Entity/Key Actors and their Specific Assistance</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Lessons learned and Good Practices that could be Shared to the NPMC/Other PMCs</td>
        </tr>
        <tr style="background-color: #002060; color: white">
            <td style="font-weight: bolder;" align=center>Nature</td>
            <td style="font-weight: bolder;" align=center>Details</td>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($records)){ ?>
        <?php $idx = 1; ?>
        <?php foreach($records as $record){ ?>
            <tr>
                <td><?= $idx ?></td>
                <td><?= $record['projectTitle'] ?></td>
                <td align=center><?= $record['regionTitle'] != '' ? $record['provinceTitle'] != '' ? $record['citymunTitle'] != '' ? $record['citymunTitle'] : $record['provinceTitle'] : $record['regionTitle'] : '' ?></td>
                <td align=center><?= $record['agencyTitle'] ?></td>
                <td><?= $record['nature'] ?></td>
                <td><?= $record['detail'] ?></td>
                <td><?= $record['strategy'] ?></td>
                <td><?= $record['responsible_entity'] ?></td>
                <td><?= $record['lesson_learned'] ?></td>
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