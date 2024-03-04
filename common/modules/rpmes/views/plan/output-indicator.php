<?php 

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\bootstrap\Modal;
?>

<h5>
<b>Monitoring Plan <?= $model->year ?></b><br><br>
Project No. <?= $plan->project->project_no.': '.$plan->project->title ?>
</h5>

<table class="table table-bordered table-responsive table-striped table-hover">
    <thead>
        <tr style="background-color: #002060; color: white; font-weight: normal;">
            <td rowspan=2>#</td>
            <td rowspan=2 style="width: 10%;">Output Indicator</td>
            <td rowspan=2 style="width: 10%;">Baseline <br>Accomplishment</td>
            <td colspan=12 style="text-align: center;">Monthly Target</td>
        </tr>
        <tr style="background-color: #002060; color: white; font-weight: normal;">
            <?php foreach($months as $month){ ?>
                <td style="text-align: center;"><?= $month ?></td>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php if($expectedOutputs){ ?>
            <?php foreach($expectedOutputs as $i => $expectedOutput){ ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= $expectedOutput->indicator ?></td>
                    <td align=center><?= $expectedOutput->type == 'Percentage' ? number_format($expectedOutput->baseline, 2) : number_format($expectedOutput->baseline, 0) ?></td>
                    <?php foreach($months as $mo => $month){ ?>
                        <td align=center><?= $expectedOutput->type == 'Percentage' ? number_format($expectedOutput->$mo, 2) : number_format($expectedOutput->$mo, 0) ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>