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
<h5 style="text-align: right; font-family: 'Arial'; font-size: 14px; font-weight: bolder;">RPMES FORM 4</h5>
<br>
<h5 style="text-align: center; font-family: 'Arial'; font-size: 14px;">
    REGIONAL PROJECT MONITORING AND EVALUATION SYSTEM (RPMES) <br>
    PROJECT RESULTS <br>
    As of December <?= $model->year ?>
</h5>
<p><b>Implementing Agency: <u><?= $model->agency->code ?></u></b></p>
<table class="table table-condensed table-bordered table-striped table-condensed table-responsive" cellspacing="0">
    <thead>
        <tr style="background-color: #002060; color: white">
            <td style="font-weight: bolder;" align=center>#</td>
            <td style="width: 23%; font-weight: bolder;" align=center>Program/Project Title</td>
            <td style="width: 25%; font-weight: bolder;" align=center>Program/Project Objectives</td>
            <td style="width: 25%; font-weight: bolder;" align=center>Results/Outcome Indicator/Target</td>
            <td style="width: 25%; font-weight: bolder;" align=center>Observed Results/Outcome/Impact</td>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($projects)){ ?>
            <?php $idx = 1; ?>
            <?php foreach($projects as $project){ ?>
                <tr>
                    <td align=center rowspan=<?= isset($outcomes[$project['id']]) ? count($outcomes[$project['id']]) : '1' ?>><?= $idx ?></td>
                    <td rowspan=<?= isset($outcomes[$project['id']]) ? count($outcomes[$project['id']]) : '1' ?>><?= $project['title'] ?></td>
                    <td rowspan=<?= isset($outcomes[$project['id']]) ? count($outcomes[$project['id']]) : '1' ?>><?=  $project['objective'] ?></td>
    
                    <?php if(isset($outcomes[$project['id']])){ ?>
                        <?php for($i = 0; $i < count($outcomes[$project['id']]); $i++){ ?>
                                <td><?= isset($outcomes[$project['id']][$i]) ? strip_tags($outcomes[$project['id']][$i]->outcome) : '&nbsp;' ?></td>
                                <td><?= isset($outcomes[$project['id']][$i]) ? $outcomes[$project['id']][$i]->getAccomplishment($model->year) ? strip_tags($outcomes[$project['id']][$i]->getAccomplishment($model->year)->value) : '&nbsp;' : '&nbsp;' ?></td>
                            </tr>
                        <?php } ?>
                    <?php }else{ ?>
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
        <td align=center><?= $model->submitter ?></td>
        <td>Approved by:</td>
        <td align=center><?= $model->agency->salutation.' '.$model->agency->head ?></td>
    </tr>
    <tr>
        <td colspan=2>Designation/Office:</td>
        <td align=center><?= $model->submitterPosition ?></td>
        <td>&nbsp;</td>
        <td align=center>Head of Agency/Office</td>
    </tr>
    <tr>
        <td colspan=2>Date:</td>
        <td>&nbsp;</td>
        <td>Date:</td>
        <td>&nbsp;</td>
    </tr>
</table>