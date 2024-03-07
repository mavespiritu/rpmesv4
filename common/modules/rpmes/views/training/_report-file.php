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
<h5 style="text-align: right; font-family: 'Arial'; font-size: 14px; font-weight: bolder;">RPMES FORM 9</h5>
<br>
<h5 style="text-align: center; font-family: 'Arial'; font-size: 14px;">
    REGIONAL PROJECT MONITORING AND EVALUATION SYSTEM (RPMES) <br>
    TRAINING/WORKSHOP CONDUCTED/FACILITATED/ATTENDED BY THE RPMC <br>
    <?= $year != '' ? 'In '.$year : '' ?>
</h5>
<table class="table table-condensed table-bordered table-striped table-condensed table-responsive" cellspacing="0">
    <thead>
        <tr style="background-color: #002060; color: white">
            <td style="font-weight: bolder;" align=center rowspan=2>#</td>
            <td style="font-weight: bolder;" align=center rowspan=2>Title of Training/Workshop</td>
            <td style="font-weight: bolder;" align=center rowspan=2>Objective of Training/Workshop</td>
            <td style="font-weight: bolder;" align=center rowspan=2>Date</td>
            <td style="font-weight: bolder;" align=center rowspan=2>Conducted/Facilitated/Attended</td>
            <td style="font-weight: bolder;" align=center rowspan=2>Lead Office/Unit</td>
            <td style="font-weight: bolder;" align=center rowspan=2>Participating Offices/Agencies/Organizations</td>
            <td style="font-weight: bolder;" align=center colspan=3>Total No. of Participants</td>
            <td style="font-weight: bolder;" align=center rowspan=2>Results and Feedback</td>
        </tr>
        <tr style="background-color: #002060; color: white">
            <td style="font-weight: bolder;" align=center>M</td>
            <td style="font-weight: bolder;" align=center>F</td>
            <td style="font-weight: bolder;" align=center>Total</td>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($records)){ ?>
        <?php $idx = 1; ?>
        <?php foreach($records as $record){ ?>
            <tr>
                <td><?= $idx ?></td>
                <td><?= $record['title'] ?></td>
                <td><?= $record['objective'] ?></td>
                <td align=center><?= strtotime($record['start_date']) == strtotime($record['end_date']) ? date("F j, Y", strtotime($record['start_date'])) : date("F j, Y", strtotime($record['start_date'])).' to '.date("F j, Y", strtotime($record['end_date'])) ?></td>
                <td align=center><?= $record['action'] ?></td>
                <td align=center><?= $record['office'] ?></td>
                <td><?= $record['organization'] ?></td>
                <td align=center><?= number_format($record['male_participant'], 0) ?></td>
                <td align=center><?= number_format($record['female_participant'], 0) ?></td>
                <td align=center><?= number_format($record['male_participant'] + $record['female_participant'], 0) ?></td>
                <td><?= $record['feedback'] ?></td>
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