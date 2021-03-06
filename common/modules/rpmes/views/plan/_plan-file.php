<?php if($type != 'pdf'){ ?>
    <style>
    *{
        font-family: "Arial";
    }
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
    h1,h2,h3,h4,h5,h6{
        text-align: center;
        font-weight: bolder;
    }
</style>
<?php } ?>
<?php if($type != 'pdf'){ ?>
    <h5 style="text-align: center;">REGIONAL PROJECT MONITORING AND EVALUATION SYSTEM (RPMES) <br>
RPMES Form 1: INITIAL PROJECT REPORT (Physical and Financial Targets for Ongoing Programs and Projects)
</h5>
<?php }else{ ?>
    <h6 style="text-align: center;">REGIONAL PROJECT MONITORING AND EVALUATION SYSTEM (RPMES) <br>
RPMES Form 1: INITIAL PROJECT REPORT (Physical and Financial Targets for Ongoing Programs and Projects)
</h6>
<?php } ?>
<table class="table table-condensed table-bordered table-striped table-condensed table-responsive" cellspacing="0">
    <thead>
        <tr>
            <td rowspan=3 colspan=2 style="width: 10%;" align=left>
                <b>
                (a) Name of Project <br>
                (b) Location <br>
                (c) Sector/Sub-Sector <br>
                (d) Funding Source <br>
                (e) Mode of Implementation <br>
                (f) Project Schedule
                </b>
            </td>
            <td rowspan=3 align=center style="width: 5%;"><b>Unit of Measure</b></td>
            <td colspan=<?= count($quarters) + 1?> align=center><b>Financial Requirements</b></td>
            <td colspan=<?= count($quarters) + 1?> align=center><b>Physical Targets</b></td>
            <td colspan=<?= (count($quarters) * count($genders)) + 2?> align=center><b>Employment Generated</b></td>
            <td colspan=<?= (count($quarters) * 2) + 2 ?> align=center><b>Target Beneficiaries</b></td>
        </tr>
        <tr>
            <?php if($quarters){ ?>
                <?php foreach($quarters as $q => $quarter){ ?>
                    <td align=center rowspan=2><b><?= $q ?></b></td>
                <?php } ?>
            <?php } ?>
            <td align=center rowspan=2><b>Total</b></td>
            <?php if($quarters){ ?>
                <?php foreach($quarters as $q => $quarter){ ?>
                    <td align=center rowspan=2><b><?= $q ?></b></td>
                <?php } ?>
            <?php } ?>
            <td align=center rowspan=2><b>Total</b></td>
            <?php if($quarters){ ?>
                <?php foreach($quarters as $q => $quarter){ ?>
                    <td align=center colspan=2><b><?= $q ?></b></td>
                <?php } ?>
            <?php } ?>
            <td align=center colspan=2><b>Total</b></td>
            <?php if($quarters){ ?>
                <?php foreach($quarters as $q => $quarter){ ?>
                    <td align=center colspan=2><b><?= $q ?></b></td>
                <?php } ?>
            <?php } ?>
            <td align=center colspan=2><b>Total</b></td>
        </tr>
        <tr>
            <?php if($quarters){ ?>
                <?php foreach($quarters as $quarter){ ?>
                    <?php if($genders){ ?>
                        <?php foreach($genders as $g => $gender){ ?>
                            <td align=center><b><?= $g ?></b></td>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
            <?php if($genders){ ?>
                <?php foreach($genders as $g => $gender){ ?>
                    <td align=center><b><?= $g ?></b></td>
                <?php } ?>
            <?php } ?>
            <?php if($quarters){ ?>
                <?php foreach($quarters as $quarter){ ?>
                    <td align=center><b>Individual</b></td>
                    <td align=center><b>Group</b></td>
                <?php } ?>
            <?php } ?>
            <td align=center><b>Individual</b></td>
            <td align=center><b>Group</b></td>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($projects)){ ?>
        <?php $idx = 1; ?>
        <?php foreach($projects as $project){ ?>
            <tr>
                <td><?= $idx ?></td>
                <td>
                    (a) <?= $project['projectTitle'] ?> <br>
                    (b) <?= $project['locationTitle'] ?> <br>
                    (c) <?= $project['sectorTitle'].'/'.$project['subSectorTitle'] ?><br>
                    (d) <?= $project['fundSourceTitle'] ?><br>
                    (e) <?= $project['modeOfImplementationTitle'] ?><br>
                    (f) <?= date("F j, Y", strtotime($project['startDate'])) ?> to <?= date("F j, Y", strtotime($project['completionDate'])) ?><br>
                </td>
                <td><?=  $project['unitOfMeasure'] ?></td>
                <td align=right><?= number_format($project['financialQ1'], 2) ?></td>
                <td align=right><?= number_format($project['financialQ2'], 2) ?></td>
                <td align=right><?= number_format($project['financialQ3'], 2) ?></td>
                <td align=right><?= number_format($project['financialQ4'], 2) ?></td>
                <td align=right><?= number_format($project['financialTotal'], 2) ?></td>
                <td align=right><?= number_format($project['physicalQ1'], 2) ?></td>
                <td align=right><?= number_format($project['physicalQ2'], 2) ?></td>
                <td align=right><?= number_format($project['physicalQ3'], 2) ?></td>
                <td align=right><?= number_format($project['physicalQ4'], 2) ?></td>
                <td align=right><?= number_format($project['physicalTotal'], 2) ?></td>
                <td align=right><?= number_format($project['maleEmployedQ1'], 0) ?></td>
                <td align=right><?= number_format($project['femaleEmployedQ1'], 0) ?></td>
                <td align=right><?= number_format($project['maleEmployedQ2'], 0) ?></td>
                <td align=right><?= number_format($project['femaleEmployedQ2'], 0) ?></td>
                <td align=right><?= number_format($project['maleEmployedQ3'], 0) ?></td>
                <td align=right><?= number_format($project['femaleEmployedQ3'], 0) ?></td>
                <td align=right><?= number_format($project['maleEmployedQ4'], 0) ?></td>
                <td align=right><?= number_format($project['femaleEmployedQ4'], 0) ?></td>
                <td align=right><?= number_format(
                    $project['maleEmployedQ1'] +
                    $project['maleEmployedQ2'] +
                    $project['maleEmployedQ3'] +
                    $project['maleEmployedQ4']
                , 0) ?></td>
                <td align=right><?= number_format(
                    $project['femaleEmployedQ1'] +
                    $project['femaleEmployedQ2'] +
                    $project['femaleEmployedQ3'] +
                    $project['femaleEmployedQ4']
                , 0) ?></td>
                <td align=right><?= number_format($project['beneficiaryQ1'], 0) ?></td>
                <td align=right><?= number_format($project['groupBeneficiaryQ1'], 0) ?></td>
                <td align=right><?= number_format($project['beneficiaryQ2'], 0) ?></td>
                <td align=right><?= number_format($project['groupBeneficiaryQ2'], 0) ?></td>
                <td align=right><?= number_format($project['beneficiaryQ3'], 0) ?></td>
                <td align=right><?= number_format($project['groupBeneficiaryQ3'], 0) ?></td>
                <td align=right><?= number_format($project['beneficiaryQ4'], 0) ?></td>
                <td align=right><?= number_format($project['groupBeneficiaryQ4'], 0) ?></td>
                <td align=right><?= number_format(
                    $project['beneficiaryQ1'] +
                    $project['beneficiaryQ2'] +
                    $project['beneficiaryQ3'] +
                    $project['beneficiaryQ4']
                , 0) ?></td>
                <td align=right><?= number_format(
                    $project['groupBeneficiaryQ1'] +
                    $project['groupBeneficiaryQ2'] +
                    $project['groupBeneficiaryQ3'] +
                    $project['groupBeneficiaryQ4']
                , 0) ?></td>
            </tr>
            <?php $idx ++ ?>
        <?php } ?>
    <?php } ?>
    </tbody>
</table>