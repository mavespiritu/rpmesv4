<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;
use yii\bootstrap\ButtonDropdown;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\DueDateSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="pull-left">
    <?= !Yii::$app->user->can('AgencyUser') ? ButtonDropdown::widget([
        'label' => '<i class="fa fa-download"></i> Export',
        'encodeLabel' => false,
        'options' => ['class' => 'btn btn-success btn-sm'],
        'dropdown' => [
            'items' => [
                ['label' => 'Excel', 'encodeLabel' => false, 'url' => Url::to(['/rpmes/report/download-form-one', 'type' => 'excel', 'model' => $model])],
                ['label' => 'PDF', 'encodeLabel' => false, 'url' => Url::to(['/rpmes/summary/download-form-one', 'type' => 'pdf', 'model' => $model])],
            ],
        ],
    ]) : '' ?>
    <?= Html::button('<i class="fa fa-print"></i> Print', ['onClick' => 'printFormOneReport("'.$model->year.'","'.$model->fund_source_id.'","'.$model->sector_id.'","'.$model->sub_sector_id.'","'.$model->region_id.'","'.$model->province_id.'","'.$model->citymun_id.'","'.$model->agency_id.'")', 'class' => 'btn btn-danger btn-sm']) ?>
</div>
<div class="clearfix"></div>
<br>
<div class="form-one-report-table" style="min-height: 600px;">
    <table class="table table-condensed table-bordered table-striped table-condensed table-responsive" cellspacing="0" style="min-width: 2000px;">
        <thead>
            <tr>
                <td rowspan=3 colspan=2 style="width: 15%;">
                    <b>
                    (a) Name of Project <br>
                    (b) Location <br>
                    (c) Sector/Sub-Sector <br>
                    (d) Funding Source <br>
                    (e) Mode of Implementation <br>
                    (f) Project Schedule
                    </b>
                </td>
                <td rowspan=3 align=center><b>Unit of Measure</b></td>
                <td colspan=<?= count($quarters) + 1?> align=center><b>Financial Requirements</b></td>
                <td colspan=<?= count($quarters) + 1?> align=center><b>Physical Targets</b></td>
                <td colspan=<?= (count($quarters) * count($genders)) + 2?> align=center><b>Employment Generated</b></td>
                <td colspan=<?= count($quarters) + 1?> align=center><b>Target Beneficiaries</b></td>
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
                        <td align=center rowspan=2><b><?= $q ?></b></td>
                    <?php } ?>
                <?php } ?>
                <td align=center rowspan=2><b>Total</b></td>
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
                    <td align=right><?= number_format(
                        $project['financialQ1'] +
                        $project['financialQ2'] +
                        $project['financialQ3'] +
                        $project['financialQ4']
                    , 2) ?></td>
                    <td align=right><?= number_format($project['physicalQ1'], 0) ?></td>
                    <td align=right><?= number_format($project['physicalQ2'], 0) ?></td>
                    <td align=right><?= number_format($project['physicalQ3'], 0) ?></td>
                    <td align=right><?= number_format($project['physicalQ4'], 0) ?></td>
                    <td align=right><?= number_format(
                        $project['physicalQ1'] +
                        $project['physicalQ2'] +
                        $project['physicalQ3'] +
                        $project['physicalQ4']
                    , 0) ?></td>
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
                    <td align=right><?= number_format($project['beneficiaryQ2'], 0) ?></td>
                    <td align=right><?= number_format($project['beneficiaryQ3'], 0) ?></td>
                    <td align=right><?= number_format($project['beneficiaryQ4'], 0) ?></td>
                    <td align=right><?= number_format(
                        $project['beneficiaryQ1'] +
                        $project['beneficiaryQ2'] +
                        $project['beneficiaryQ3'] +
                        $project['beneficiaryQ4']
                    , 0) ?></td>
                </tr>
                <?php $idx ++ ?>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php
    $script = '
        $(document).ready(function(){
            $(".form-one-report-table").freezeTable({
                "scrollable": true,
            });
        });
    ';

    $this->registerJs($script, View::POS_END);
?>