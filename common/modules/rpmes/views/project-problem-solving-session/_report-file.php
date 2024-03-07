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
<h5 style="text-align: right; font-family: 'Arial'; font-size: 14px; font-weight: bolder;">RPMES FORM 8</h5>
<br>
<h5 style="text-align: center; font-family: 'Arial'; font-size: 14px;">
    REGIONAL PROJECT MONITORING AND EVALUATION SYSTEM (RPMES) <br>
    PROBLEM SOLVING SESSION/FACILITATION MEETING CONDUCTED <br>
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
            <td style="font-weight: bolder;" align=center>#</td>
            <td style="font-weight: bolder;" align=center>Program/Project Title</td>
            <td style="font-weight: bolder;" align=center>Issue Details</td>
            <td style="font-weight: bolder;" align=center>Issue Typology</td>
            <td style="font-weight: bolder;" align=center>Location</td>
            <td style="font-weight: bolder;" align=center>IA</td>
            <td style="font-weight: bolder;" align=center>Date of Meeting</td>
            <td style="font-weight: bolder;" align=center>Concerned Agencies</td>
            <td style="font-weight: bolder;" align=center>Agreements Reached</td>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($records)){ ?>
        <?php $idx = 1; ?>
        <?php foreach($records as $record){ ?>
            <tr>
                <td><?= $idx ?></td>
                <td><?= $record['projectTitle'] ?></td>
                <td><?= $record['issue_details'] ?></td>
                <td><?= $record['issue_typology'] ?></td>
                <td><?= $record['regionTitle'] != '' ? $record['provinceTitle'] != '' ? $record['citymunTitle'] != '' ? $record['citymunTitle'] : $record['provinceTitle'] : $record['regionTitle'] : '' ?></td>
                <td align=center><?= $record['agencyTitle'] ?></td>
                <td align=center><?= date("F j, Y", strtotime($record['pss_date'])) ?></td>
                <td><?= $record['agencies'] ?></td>
                <td><?= $record['agreement_reached'] ?></td>
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