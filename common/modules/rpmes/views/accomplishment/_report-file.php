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
<h5 style="text-align: right; font-family: 'Arial'; font-size: 14px; font-weight: bolder;">RPMES FORM 2</h5>
<br>
<h5 style="text-align: center; font-family: 'Arial'; font-size: 14px;">
    REGIONAL PROJECT MONITORING AND EVALUATION SYSTEM (RPMES) <br>
    PHYSICAL AND FINANCIAL ACCOMPLISHMENT REPORT <br>
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
<p><b>Implementing Agency: <u><?= $model->agency->code ?></u></b></p>
<table class="table table-condensed table-bordered table-striped table-condensed table-responsive" cellspacing="0">
    <thead>
        <tr style="background-color: #002060; color: white">
            <td style="font-weight: bolder;" rowspan=2 align=center>#</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Program/Project Title</td>
            <td style="font-weight: bolder;" colspan=2 align=center>Implementation Schedule</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Fund Source</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Funding Agency</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Total Program/Project<br>Cost<br>(PHP)</td>
            <td style="font-weight: bolder;" colspan=4 align=center>Financial Status (in PHP exact figures)</td>
            <td style="font-weight: bolder;" colspan=3 align=center>Physical Accomplishment</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Output Indicator</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>End-of-Project Target</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Target to date</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Actual to date</td>
            <td style="font-weight: bolder;" colspan=2 align=center>Employment Generated</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Remarks</td>
        </tr>
        <tr style="background-color: #002060; color: white">
            <td style="font-weight: bolder;" align=center>Start Date<br>(mm-dd-yyyy)</td>
            <td style="font-weight: bolder;" align=center>End Date<br>(mm-dd-yyyy)</td>
            <td style="font-weight: bolder;" align=center>Appropriations</td>
            <td style="font-weight: bolder;" align=center>Allotment</td>
            <td style="font-weight: bolder;" align=center>Obligations</td>
            <td style="font-weight: bolder;" align=center>Disbursements</td>
            <td style="font-weight: bolder;" align=center>Target OWPA to <br>date (%)</td>
            <td style="font-weight: bolder;" align=center>Actual OWPA to <br>date (%)</td>
            <td style="font-weight: bolder;" align=center>Slippage</td>
            <td style="font-weight: bolder;" align=center>M</td>
            <td style="font-weight: bolder;" align=center>F</td>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($projects)){ ?>
            <?php $idx = 1; ?>
            <?php foreach($projects as $project){ ?>
                <tr>
                    <td align=center rowspan=<?= isset($ois[$project['id']]) ? count($ois[$project['id']]) : '1' ?>><?= $idx ?></td>
                    <td rowspan=<?= isset($ois[$project['id']]) ? count($ois[$project['id']]) : '1' ?>><?= $project['title'] ?></td>
                    <td align=center rowspan=<?= isset($ois[$project['id']]) ? count($ois[$project['id']]) : '1' ?>><?=  $type == 'excel' ? "'".$project['startDate'] : $project['startDate']?></td>
                    <td align=center rowspan=<?= isset($ois[$project['id']]) ? count($ois[$project['id']]) : '1' ?>><?=  $type == 'excel' ? "'".$project['endDate'] : $project['endDate']?></td>
                    <td align=center rowspan=<?= isset($ois[$project['id']]) ? count($ois[$project['id']]) : '1' ?>><?=  $project['fundingSourceTitle'] ?></td>
                    <td align=center rowspan=<?= isset($ois[$project['id']]) ? count($ois[$project['id']]) : '1' ?>><?=  $project['fundingAgencyTitle'] ?></td>
                    <td align=right rowspan=<?=isset($ois[$project['id']]) ? count($ois[$project['id']]) : '1' ?>><?=  number_format($project['cost'], 2) ?></td>
                    <td align=right rowspan=<?=isset($ois[$project['id']]) ? count($ois[$project['id']]) : '1' ?>><?=  number_format($project['appropriations'], 2) ?></td>
                    <td align=right rowspan=<?=isset($ois[$project['id']]) ? count($ois[$project['id']]) : '1' ?>><?=  number_format($project['allotment'], 2) ?></td>
                    <td align=right rowspan=<?=isset($ois[$project['id']]) ? count($ois[$project['id']]) : '1' ?>><?=  number_format($project['obligations'], 2) ?></td>
                    <td align=right rowspan=<?=isset($ois[$project['id']]) ? count($ois[$project['id']]) : '1' ?>><?=  number_format($project['disbursements'], 2) ?></td>
                    <td align=center rowspan=<?= isset($ois[$project['id']]) ? count($ois[$project['id']]) : '1' ?>><?=  number_format($project['targetOwpa'], 2) ?></td>
                    <td align=center rowspan=<?= isset($ois[$project['id']]) ? count($ois[$project['id']]) : '1' ?>><?=  number_format($project['actualOwpa'], 2) ?></td>
                    <td align=center rowspan=<?= isset($ois[$project['id']]) ? count($ois[$project['id']]) : '1' ?>><?=  number_format($project['slippage'], 2) ?></td>
    
                    <?php if(isset($ois[$project['id']])){ ?>
                        <?php for($i = 0; $i < count($ois[$project['id']]); $i++){ ?>
                            <?php if($i == 0){ ?>
                                    <td><?= isset($ois[$project['id']][$i]['indicator']) ? $ois[$project['id']][$i]['indicator'] : '&nbsp;' ?></td>
                                    <td align=center><?= isset($ois[$project['id']][$i]['endOfProjectTarget']) ? number_format($ois[$project['id']][$i]['endOfProjectTarget'], 0) : '&nbsp;' ?></td>
                                    <td align=center><?= isset($ois[$project['id']][$i]['target']) ? number_format($ois[$project['id']][$i]['target'], 0) : '&nbsp;' ?></td>
                                    <td align=center><?= isset($ois[$project['id']][$i]['actual']) ? number_format($ois[$project['id']][$i]['actual'], 0) : '&nbsp;' ?></td>
                                    <td align=center rowspan=<?= count($ois[$project['id']]) ?>><?=  number_format($project['maleEmployed'], 0) ?></td>
                                    <td align=center rowspan=<?= count($ois[$project['id']]) ?>><?=  number_format($project['femaleEmployed'], 0) ?></td>
                                    <td rowspan=<?= count($ois[$project['id']]) ?>><?=  $project['remarks'] ?></td>
                                </tr>
                            <?php }else{ ?>
                                    <td><?= isset($ois[$project['id']][$i]['indicator']) ? $ois[$project['id']][$i]['indicator'] : '&nbsp;' ?></td>
                                    <td align=center><?= isset($ois[$project['id']][$i]['endOfProjectTarget']) ? number_format($ois[$project['id']][$i]['endOfProjectTarget'], 0) : '&nbsp;' ?></td>
                                    <td align=center><?= isset($ois[$project['id']][$i]['target']) ? number_format($ois[$project['id']][$i]['target'], 0) : '&nbsp;' ?></td>
                                    <td align=center><?= isset($ois[$project['id']][$i]['actual']) ? number_format($ois[$project['id']][$i]['actual'], 0) : '&nbsp;' ?></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    <?php }else{ ?>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align=center rowspan=<?= isset($ois[$project['id']]) ? count($ois[$project['id']]) : '1' ?>><?=  number_format($project['maleEmployed'], 0) ?></td>
                            <td align=center rowspan=<?= isset($ois[$project['id']]) ? count($ois[$project['id']]) : '1' ?>><?=  number_format($project['femaleEmployed'], 0) ?></td>
                            <td align=left rowspan=<?= isset($ois[$project['id']]) ? count($ois[$project['id']]) : '1' ?>><?=  $project['remarks'] ?></td>
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