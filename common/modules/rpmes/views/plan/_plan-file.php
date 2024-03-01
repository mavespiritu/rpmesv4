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
<h5 style="text-align: right; font-family: 'Arial'; font-size: 14px; font-weight: bolder;">RPMES FORM 1</h5>
<br>
<h5 style="text-align: center; font-family: 'Arial'; font-size: 14px;">
    REGIONAL PROJECT MONITORING AND EVALUATION SYSTEM (RPMES) <br>
    INITIAL PROJECT REPORT <br>
    CY <?= $model->year ?>
</h5>
<p><b>Implementing Agency: <u><?= $model->agency->code ?></u></b></p>
<table class="table table-condensed table-bordered table-striped table-condensed table-responsive" cellspacing="0">
    <thead>
        <tr style="background-color: #002060; color: white">
            <td style="font-weight: bolder;" rowspan=2 align=center>#</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Program/Project Title</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Component Details</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Fund Source</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Funding Agency</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Mode of <br>Implementation</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Total Program/Project<br>Cost<br>(PHP)</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Sector</td>
            <td style="font-weight: bolder;" colspan=3 align=center>Location</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Start Date<br>(mm-dd-yy)</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>End Date<br>(mm-dd-yy)</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Remarks</td>
            <td style="font-weight: bolder;" colspan=2 align=center>Target Employment<br>Generated</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Output Indicators</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Month</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Financial Targets</td>
            <td style="font-weight: bolder;" rowspan=2 align=center>Physical Targets<br>(in %)</td>
            <?php if(!empty($maxOutputIndicator)){ ?>
                <?php for($i = 1; $i <= $maxOutputIndicator['total']; $i++){ ?>
                    <td style="font-weight: bolder;" rowspan=2 align=center>Targets of Output Indicator <?= $i ?></td>
                <?php } ?>
            <?php } ?>
        </tr>
        <tr style="background-color: #002060; color: white">
            <td style="font-weight: bolder;" align=center>Province</td>
            <td style="font-weight: bolder;" align=center>City/<br>Municipality</td>
            <td style="font-weight: bolder;" align=center>Barangay</td>
            <td style="font-weight: bolder;" align=center>M</td>
            <td style="font-weight: bolder;" align=center>F</td>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($projects)){ ?>
        <?php $idx = 1; ?>
        <?php foreach($projects as $project){ ?>
            <tr>
                <td rowspan=13><?= $idx ?></td>
                <td rowspan=13><?= $project['title'] ?></td>
                <td rowspan=13><i><?=  $project['componentTitle'] ?></i></td>
                <td rowspan=13><?=  $project['fundingSourceTitle'] ?></td>
                <td rowspan=13><?=  $project['fundingAgencyTitle'] ?></td>
                <td rowspan=13><?=  $project['modeOfImplementationTitle'] ?></td>
                <td rowspan=13 align=right><?=  number_format($project['cost'], 2) ?></td>
                <td rowspan=13><?=  $project['sectorTitle'] ?></td>
                <td rowspan=13><?=  $project['provinceTitle'] ?></td>
                <td rowspan=13><?=  $project['citymunTitle'] ?></td>
                <td rowspan=13><?=  $project['barangayTitle'] ?></td>
                <td rowspan=13><?=  $type == 'excel' ? "'".$project['startDate'] : $project['startDate']?></td>
                <td rowspan=13><?=  $type == 'excel' ? "'".$project['endDate'] : $project['endDate']?></td>
                <td rowspan=13><?=  $project['remarks'] ?></td>
                <td rowspan=13 align=center><?= number_format(intval($project['maleEmployedTotal']), 0) ?></td>
                <td rowspan=13 align=center><?= number_format(intval($project['femaleEmployedTotal']), 0) ?></td>
                <td rowspan=13><i><?=  $project['outputIndicatorTitle'] ?></i></td>
                <td>Total Target for the Year</td>
                <td align=right><?= number_format($project['financialTotal'], 2) ?></td>
                <td align=center><?= $project['metrics'] == 'Percentage' ? number_format($project['physicalTotal'], 0) : number_format($project['physicalTotal'], 0).' (100.00)' ?></td>
                <?php if(!empty($targets['outputIndicators'][$project['id']])){ ?>
                    <?php for($i = 1; $i <= $maxOutputIndicator['total']; $i++){ ?>
                        <td align=center><?= isset($targets['outputIndicators'][$project['id']][$i]) ? number_format(floatval($targets['outputIndicators'][$project['id']][$i]['rawTotal']), 0).' ('.number_format(floatval($targets['outputIndicators'][$project['id']][$i]['total']), 2).')' : '0 (0.00)' ?></td>
                    <?php } ?>
                <?php }else{ ?>
                    <?php if(!empty($maxOutputIndicator)){ ?>
                        <?php for($i = 1; $i <= $maxOutputIndicator['total']; $i++){ ?>
                            <td>0.00</td>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </tr>
            <?php foreach($months as $mo => $month){ ?>
                <tr>
                    <td><?= $month ?></td>
                    <td align=right><?= isset($targets['financial'][$project['id']]) ? number_format(floatval($targets['financial'][$project['id']][$mo]), 2) : '0.00' ?></td>
                    <td align=center><?= isset($targets['physical'][$project['id']]) ? $project['metrics'] == 'Numerical' ? $project['physicalTotal'] > 0 ? number_format(floatval($targets['physical'][$project['id']][$mo]), 0).' ('.number_format(floatval($targets['physical'][$project['id']][$mo])/floatval($project['physicalTotal']) * 100, 2).')' : '0 (0.00)' : number_format(floatval($targets['physical'][$project['id']][$mo]), 2) : '0 .00' ?></td>
                    <?php if(!empty($targets['outputIndicators'][$project['id']])){ ?>
                        <?php for($i = 1; $i <= $maxOutputIndicator['total']; $i++){ ?>
                            <td align=center><?= isset($targets['outputIndicators'][$project['id']][$i]) ? $targets['outputIndicators'][$project['id']][$i]['total'] > 0 ? number_format(floatval($targets['outputIndicators'][$project['id']][$i][$mo]), 0).' ('.number_format(floatval(($targets['outputIndicators'][$project['id']][$i][$mo]/$targets['outputIndicators'][$project['id']][$i]['rawTotal']) * 100), 2).')' : '0 (0.00)' : '0 (0.00)' ?></td>
                        <?php } ?>
                    <?php }else{ ?>
                        <?php if(!empty($maxOutputIndicator)){ ?>
                            <?php for($i = 1; $i <= $maxOutputIndicator['total']; $i++){ ?>
                                <td>0.00</td>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
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