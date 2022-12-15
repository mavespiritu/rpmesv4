<table class="table table-condensed table-bordered table-striped table-hover table-condensed table-responsive" style="width: 100%; height: 200px;" >
    <thead>
        <tr>
            <td rowspan=2 align=center style="width: 40%"><b>Sector</b></td>
            <td rowspan=2 align=center><b>Actual Accomplishment</b></td>
            <td rowspan=2 align=center><b>Target Accomplishment</b></td>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($sectors)){ ?>
            <?php foreach($sectors as $project){ ?>
                <tr style="font-weight: bolder;">
                    <td><?= $project['sectorTitle'] ?></td>
                    <td align=center><?= number_format(floatval($project['physicalAccompTotalPerQuarter']), 0) ?></td>
                    <td align=center><?= number_format(floatval($project['physicalTargetTotalPerQuarter']), 0) ?></td>
                </tr>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>
