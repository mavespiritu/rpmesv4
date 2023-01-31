<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Url;
use yii\web\View;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Project */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pull-right">
    <?= !Yii::$app->user->can('AgencyUser') ? ButtonDropdown::widget([
        'label' => '<i class="fa fa-download"></i> Export',
        'encodeLabel' => false,
        'options' => ['class' => 'btn btn-success btn-sm'],
        'dropdown' => [
            'items' => [
                ['label' => 'Excel', 'encodeLabel' => false, 'url' => Url::to(['/rpmes/project-status/download-form-six', 'type' => 'excel', 'year' => $model->year == null ? '2022' : $model->year, 'quarter' => $model->quarter == null ? '' : $model->quarter, 'agency_id' => $model->agency_id == null ? '' : $model->agency_id, 'sector_id' => $model->sector_id == null ? '' : $model->sector_id, 'region_id' => $model->region_id == null ? '' : $model->region_id, 'province_id' => $model->province_id == null ? '' : $model->province_id, 'model' => json_encode($model)])],
                ['label' => 'PDF', 'encodeLabel' => false, 'url' => Url::to(['/rpmes/project-status/download-form-six', 'type' => 'pdf', 'year' => $model->year == null ? '2022' : $model->year, 'quarter' => $model->quarter == null ? '' : $model->quarter, 'agency_id' => $model->agency_id == null ? '' : $model->agency_id, 'sector_id' => $model->sector_id == null ? '' : $model->sector_id, 'region_id' => $model->region_id == null ? '' : $model->region_id, 'province_id' => $model->province_id == null ? '' : $model->province_id, 'model' => json_encode($model)])],
            ],
        ],
    ]) : '' ?>
        <?= Html::button('<i class="fa fa-print"></i> Print', ['onClick' => 'printFormSixReport("'.$model->year.'", "'.$model->quarter.'", "'.$model->agency_id.'", "'.$model->sector_id.'", "'.$model->region_id.'", "'.$model->province_id.'")', 'class' => 'btn btn-danger btn-sm']) ?>
</div>
<div class="clearfix"></div><br>
<div class="project-status-table" style="height: 600px;">
    <table class="table table-condensed table-bordered table-striped table-hover table-condensed table-responsive" cellspacing="0" style="min-width: 3000px;">
        <thead>
            <tr>
                <td rowspan=2 align=center><b>#</td>
                <td rowspan=2 colspan=2 align=center style="width: 20%;"><b>Project Title</td>
                <td rowspan=2 align=center><b>Total Project Cost</b></td>
                <td rowspan=2 colspan=2 align=center><b>Sector/Subsector</b></td>
                <td rowspan=2 colspan=2 align=center><b>Location</b></td>
                <td rowspan=2 align=center><b>Implementing Agency</b></td>
                <td rowspan=2 align=center><b>Fund Utilization</b></td>
                <td colspan=3 align=center><b>Physical Status (as of Reporting Period)</b></td>
                <td rowspan=2 colspan=2 align=center><b>Issues</b></td>
                <td rowspan=2 align=center><b>Source of Information</b></td>
                <td rowspan=2 colspan=2 align=center><b>Recommendations</b></td>
            </tr>
            <tr>
                <td align=center><b>Target</b></td>
                <td align=center><b>Actual</b></td>
                <td align=center><b>Slippage</b></td>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($projects)){ ?>
            <?php $idx = 1; ?>
            <?php foreach($projects as $project){ ?>
                <?php if((($project['slippage'] <= -15)) && $project['isCompleted'] != 1){ ?>
                    <tr>
                        <td align=center><?= $idx ?></td>
                        <td colspan=2><?= $project['projectTitle'] ?></td>
                        <td align=right><?= number_format($project['totalCost'], 2) ?></td>
                        <td colspan=2><?= $project['sectorTitle']. ' / '.$project['subSectorTitle'] ?></td>
                        <td colspan=2><?= $project['locationTitle'] ?></td>
                        <td align=center><?= $project['agencyTitle'] ?></td>
                        <td align=center><?= $project['releases'] > 0 ? number_format(($project['expenditures'] / $project['releases']) * 100, 2) : number_format(0, 2) ?></td>
                        <td align=center><?= $project['physicalTargetTotalPerQuarter'] != '' ? number_format($project['physicalTargetTotalPerQuarter'], 2) : 0 ?></td>
                        <td align=center><?= $project['physicalAccompTotalPerQuarter'] != '' ? number_format($project['physicalAccompTotalPerQuarter'], 2) : 0 ?></td>
                        <td align=center><?= number_format($project['slippage'], 2) ?>%</td>
                        <td colspan=2><?= $project['causes'] ?></td>
                        <td align=center><?= $project['agencyTitle'] ?></td>
                        <td colspan=2><?= $project['recommendations'] ?></td>
                    </tr>
                    <?php $idx ++ ?>
                <?php } ?>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php
    $script = '
        $(document).ready(function(){
            $(".project-status-table").freezeTable({
                "scrollable": true,
                "columnNum" : 3,
            });
        });
    ';

    $this->registerJs($script, View::POS_END);
?>
