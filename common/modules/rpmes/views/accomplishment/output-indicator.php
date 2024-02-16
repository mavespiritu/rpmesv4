<h5>
<b>Form 2 OI/s for <?= $model->quarter ?> <?= $model->year ?></b><br><br>
Project No. <?= $plan->project->project_no.': '.$plan->project->title ?>
</h5>

<table class="table table-bordered table-responsive table-striped table-hover">
    <thead>
        <tr style="background-color: #002060; color: white; font-weight: normal;">
            <td>#</td>
            <td style="width: 28%;">Output Indicator</td>
            <td style="width: 22%; text-align: center;">End-of-Project Target</td>
            <td style="width: 22%; text-align: center;">Target-to-Date</td>
            <td style="width: 22%; text-align: center;">Actual-to-Date</td>
        </tr>
    </thead>
    <tbody>
        <?php if($expectedOutputs){ ?>
            <?php foreach($expectedOutputs as $i => $expectedOutput){ ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= $expectedOutput->indicator ?></td>
                    <td align=center><?= number_format($expectedOutput->getEndOfProjectTarget($model->year), 0) ?></td>
                    <td align=center><?= number_format($expectedOutput->getPhysicalTargetPerQuarter($model->year)[$model->quarter], 0) ?></td>
                    <td align=center><?= number_format($expectedOutput->getAccomplishmentPerQuarter($model->year)[$model->quarter], 0) ?></td>
                </tr>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>