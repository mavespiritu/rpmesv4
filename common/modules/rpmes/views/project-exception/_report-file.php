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
<h5 style="text-align: right; font-family: 'Arial'; font-size: 14px; font-weight: bolder;">RPMES FORM 3</h5>
<br>
<h5 style="text-align: center; font-family: 'Arial'; font-size: 14px;">
    REGIONAL PROJECT MONITORING AND EVALUATION SYSTEM (RPMES) <br>
    PROJECT EXCEPTION REPORT <br>
    As of
    <?php if($model->quarter == 'Q1'){ ?>
        <?= 'March' ?>
    <?php }else if($model->quarter == 'Q2'){ ?>
        <?= 'June' ?>
    <?php }else if($model->quarter == 'Q3'){ ?>
        <?= 'September' ?>
    <?php }else if($model->quarter == 'Q4'){ ?>
        <?= 'December' ?>
    <?php } ?>
    <?= $model->year ?> (Quarterly)
</h5>
<p><b>Implementing Agency/NGOs/Concerned Citizens: <u><?= $model->agency->code ?></u></b></p>
<table class="table table-condensed table-bordered table-striped table-condensed table-responsive" cellspacing="0">
    <thead>
        <tr style="background-color: #002060; color: white">
            <td style="font-weight: bolder;" rowspan=2 align=center>#</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Program/Project Title</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Implementating Agency</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Sector</td>
            <td style="font-weight: bolder;" colspan=3 align=center>Location</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Findings</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Typology</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Issue Status</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Reasons</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Actions Taken</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Actions to be taken</td>
        </tr>
        <tr style="background-color: #002060; color: white">
            <td style="font-weight: bolder;" align=center>Province</td>
            <td style="font-weight: bolder;" align=center>City/Municipality</td>
            <td style="font-weight: bolder;" align=center>Barangay</td>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($projects)){ ?>
            <?php $idx = 1; ?>
            <?php foreach($projects as $project){ ?>
                <tr>
                    <td align=center rowspan=<?= isset($exceptions[$project['id']]) ? count($exceptions[$project['id']]) : '1' ?>><?= $idx ?></td>
                    <td rowspan=<?= isset($exceptions[$project['id']]) ? count($exceptions[$project['id']]) : '1' ?>><?= $project['title'] ?></td>
                    <td align=center rowspan=<?= isset($exceptions[$project['id']]) ? count($exceptions[$project['id']]) : '1' ?>><?=  $project['agencyTitle'] ?></td>
                    <td align=center rowspan=<?= isset($exceptions[$project['id']]) ? count($exceptions[$project['id']]) : '1' ?>><?=  $project['sectorTitle'] ?></td>
                    <td align=center rowspan=<?= isset($exceptions[$project['id']]) ? count($exceptions[$project['id']]) : '1' ?>><?=  $project['provinceTitle'] ?></td>
                    <td align=center rowspan=<?= isset($exceptions[$project['id']]) ? count($exceptions[$project['id']]) : '1' ?>><?=  $project['citymunTitle'] ?></td>
                    <td align=center rowspan=<?= isset($exceptions[$project['id']]) ? count($exceptions[$project['id']]) : '1' ?>><?=  $project['barangayTitle'] ?></td>
    
                    <?php if(isset($exceptions[$project['id']])){ ?>
                        <?php for($i = 0; $i < count($exceptions[$project['id']]); $i++){ ?>
                                <td><?= isset($exceptions[$project['id']][$i]) ? strip_tags($exceptions[$project['id']][$i]->findings) : '&nbsp;' ?></td>
                                <td align=center><?= isset($exceptions[$project['id']][$i]) ? $exceptions[$project['id']][$i]->typology ? $exceptions[$project['id']][$i]->typology->title : '&nbsp;' : '&nbsp;' ?></td>
                                <td align=center><?= isset($exceptions[$project['id']][$i]) ? $exceptions[$project['id']][$i]->issue_status : '&nbsp;' ?></td>
                                <td><?= isset($exceptions[$project['id']][$i]) ? strip_tags($exceptions[$project['id']][$i]->causes) : '&nbsp;' ?></td>
                                <td><?= isset($exceptions[$project['id']][$i]) ? strip_tags($exceptions[$project['id']][$i]->action_taken) : '&nbsp;' ?></td>
                                <td><?= isset($exceptions[$project['id']][$i]) ? strip_tags($exceptions[$project['id']][$i]->recommendations) : '&nbsp;' ?></td>
                            </tr>
                        <?php } ?>
                    <?php }else{ ?>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    <?php } ?>
            <?php $idx++ ?>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>
<br>
<table class="table table-condensed table-bordered table-striped table-condensed table-responsive" cellspacing="0">
    <tr>
        <td colspan=2>Submitted by:</td>
        <td colspan=2 align=center><?= $model->submitter ?></td>
        <td>Approved by:</td>
        <td colspan=2 align=center><?= $model->agency->salutation.' '.$model->agency->head ?></td>
    </tr>
    <tr>
        <td colspan=2>Designation/Office:</td>
        <td colspan=2 align=center><?= $model->submitterPosition ?></td>
        <td>&nbsp;</td>
        <td colspan=2 align=center>Head of Agency/Office</td>
    </tr>
    <tr>
        <td colspan=2>Date:</td>
        <td colspan=2>&nbsp;</td>
        <td>Date:</td>
        <td colspan=2>&nbsp;</td>
    </tr>
</table>