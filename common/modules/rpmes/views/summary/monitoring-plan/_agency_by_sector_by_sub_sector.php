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
    <?= ButtonDropdown::widget([
        'label' => '<i class="fa fa-download"></i> Export',
        'encodeLabel' => false,
        'options' => ['class' => 'btn btn-success btn-sm'],
        'dropdown' => [
            'items' => [
                ['label' => 'Excel', 'encodeLabel' => false, 'url' => Url::to(['/rpmes/summary/download-monitoring-plan', 'type' => 'excel', 'year' => $model->year, 'grouping' => $model->grouping])],
                ['label' => 'PDF', 'encodeLabel' => false, 'url' => Url::to(['/rpmes/summary/download-monitoring-plan', 'type' => 'pdf', 'year' => $model->year, 'grouping' => $model->grouping])],
            ],
        ],
    ]); ?>
    <?= Html::button('<i class="fa fa-print"></i> Print', ['onClick' => 'printSummary("'.$model->year.'", "'.$model->grouping.'")', 'class' => 'btn btn-danger btn-sm']) ?>
</div>
<div class="clearfix"></div>
<br>
<div class="summary-monitoring-plan-table" style="min-height: 600px;">
    <table class="table table-condensed table-bordered table-striped table-condensed table-responsive" cellspacing="0" style="min-width: 2000px;">
        <thead>
            <tr>
                <td rowspan=3 colspan=4 align=center><b>Project Category</b></td>
                <td colspan=5 align=center><b>Financial Requirements</b></td>
                <td colspan=5 align=center><b>Number of Projects</b></td>
                <td colspan=10 align=center><b>Number of Persons Employed</b></td>
                <td colspan=4 align=center><b>Number of Beneficiaries</b></td>
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
        <?php if(!empty($data)){ ?>
            <?php $i = 0; ?>
            <?php foreach($data as $subSector => $subSectors){ ?>
                    <tr style="font-weight: bolder;">
                        <td colspan=4><?= $bigCaps[$i] ?>. <?= $subSector ?></td>
                        <td align=right><?= number_format($subSectors['content']['q1financial'], 2) ?></td>
                        <td align=right><?= number_format($subSectors['content']['q2financial'], 2) ?></td>
                        <td align=right><?= number_format($subSectors['content']['q3financial'], 2) ?></td>
                        <td align=right><?= number_format($subSectors['content']['q4financial'], 2) ?></td>
                        <td align=right><?= number_format(
                            $subSectors['content']['q1financial'] +
                            $subSectors['content']['q2financial'] +
                            $subSectors['content']['q3financial'] +
                            $subSectors['content']['q4financial']
                            , 2) ?>
                        </td>
                        <td align=right><?= number_format($subSectors['content']['q1physical'], 0) ?></td>
                        <td align=right><?= number_format($subSectors['content']['q2physical'], 0) ?></td>
                        <td align=right><?= number_format($subSectors['content']['q3physical'], 0) ?></td>
                        <td align=right><?= number_format($subSectors['content']['q4physical'], 0) ?></td>
                        <td align=right><?= number_format(
                            $subSectors['content']['q1physical'] +
                            $subSectors['content']['q2physical'] +
                            $subSectors['content']['q3physical'] +
                            $subSectors['content']['q4physical']
                            , 0) ?>
                        </td>
                        <td align=right><?= number_format($subSectors['content']['q1maleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($subSectors['content']['q1femaleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($subSectors['content']['q2maleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($subSectors['content']['q2femaleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($subSectors['content']['q3maleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($subSectors['content']['q3femaleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($subSectors['content']['q4maleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($subSectors['content']['q4femaleEmployed'], 0) ?></td>
                        <td align=right><?= number_format(
                            $subSectors['content']['q1maleEmployed'] +
                            $subSectors['content']['q2maleEmployed'] +
                            $subSectors['content']['q3maleEmployed'] +
                            $subSectors['content']['q4maleEmployed']
                            , 0) ?>
                        </td>
                        <td align=right><?= number_format(
                            $subSectors['content']['q1femaleEmployed'] +
                            $subSectors['content']['q2femaleEmployed'] +
                            $subSectors['content']['q3femaleEmployed'] +
                            $subSectors['content']['q4femaleEmployed']
                            , 0) ?>
                        </td>
                        <td align=right><?= number_format($subSectors['content']['q1beneficiary'], 0) ?></td>
                        <td align=right><?= number_format($subSectors['content']['q2beneficiary'], 0) ?></td>
                        <td align=right><?= number_format($subSectors['content']['q3beneficiary'], 0) ?></td>
                        <td align=right><?= number_format($subSectors['content']['q4beneficiary'], 0) ?></td>
                    </tr>
                    <?php if(!empty($subSectors['sectors'])){ ?>
                        <?php $j = 0; ?>
                        <?php foreach($subSectors['sectors'] as $sector => $sectors){ ?>
                            <tr style="font-weight: bolder;">
                                <td align=right>&nbsp;</td>
                                <td colspan=3><?= $smallCaps[$j] ?>. <?= $sector ?></td>
                                <td align=right><?= number_format($sectors['content']['q1financial'], 2) ?></td>
                                <td align=right><?= number_format($sectors['content']['q2financial'], 2) ?></td>
                                <td align=right><?= number_format($sectors['content']['q3financial'], 2) ?></td>
                                <td align=right><?= number_format($sectors['content']['q4financial'], 2) ?></td>
                                <td align=right><?= number_format(
                                    $sectors['content']['q1financial'] +
                                    $sectors['content']['q2financial'] +
                                    $sectors['content']['q3financial'] +
                                    $sectors['content']['q4financial']
                                    , 2) ?>
                                </td>
                                <td align=right><?= number_format($sectors['content']['q1physical'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['q2physical'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['q3physical'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['q4physical'], 0) ?></td>
                                <td align=right><?= number_format(
                                    $sectors['content']['q1physical'] +
                                    $sectors['content']['q2physical'] +
                                    $sectors['content']['q3physical'] +
                                    $sectors['content']['q4physical']
                                    , 0) ?>
                                </td>
                                <td align=right><?= number_format($sectors['content']['q1maleEmployed'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['q1femaleEmployed'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['q2maleEmployed'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['q2femaleEmployed'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['q3maleEmployed'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['q3femaleEmployed'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['q4maleEmployed'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['q4femaleEmployed'], 0) ?></td>
                                <td align=right><?= number_format(
                                    $sectors['content']['q1maleEmployed'] +
                                    $sectors['content']['q2maleEmployed'] +
                                    $sectors['content']['q3maleEmployed'] +
                                    $sectors['content']['q4maleEmployed']
                                    , 0) ?>
                                </td>
                                <td align=right><?= number_format(
                                    $sectors['content']['q1femaleEmployed'] +
                                    $sectors['content']['q2femaleEmployed'] +
                                    $sectors['content']['q3femaleEmployed'] +
                                    $sectors['content']['q4femaleEmployed']
                                    , 0) ?>
                                </td>
                                <td align=right><?= number_format($sectors['content']['q1beneficiary'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['q2beneficiary'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['q3beneficiary'], 0) ?></td>
                                <td align=right><?= number_format($sectors['content']['q4beneficiary'], 0) ?></td>
                            </tr>
                            <?php if(!empty($sectors['agencies'])){ ?>
                                <?php $k = 0; ?>
                                <?php foreach($sectors['agencies'] as $agency => $agencies){ ?>
                                    <tr>
                                        <td align=right>&nbsp;</td>
                                        <td align=right>&nbsp;</td>
                                        <td colspan=2><?= $numbers[$k] ?>. <?= $agency ?></td>
                                        <td align=right><?= number_format($agencies['content']['q1financial'], 2) ?></td>
                                        <td align=right><?= number_format($agencies['content']['q2financial'], 2) ?></td>
                                        <td align=right><?= number_format($agencies['content']['q3financial'], 2) ?></td>
                                        <td align=right><?= number_format($agencies['content']['q4financial'], 2) ?></td>
                                        <td align=right><?= number_format(
                                            $agencies['content']['q1financial'] +
                                            $agencies['content']['q2financial'] +
                                            $agencies['content']['q3financial'] +
                                            $agencies['content']['q4financial']
                                            , 2) ?>
                                        </td>
                                        <td align=right><?= number_format($agencies['content']['q1physical'], 0) ?></td>
                                        <td align=right><?= number_format($agencies['content']['q2physical'], 0) ?></td>
                                        <td align=right><?= number_format($agencies['content']['q3physical'], 0) ?></td>
                                        <td align=right><?= number_format($agencies['content']['q4physical'], 0) ?></td>
                                        <td align=right><?= number_format(
                                            $agencies['content']['q1physical'] +
                                            $agencies['content']['q2physical'] +
                                            $agencies['content']['q3physical'] +
                                            $agencies['content']['q4physical']
                                            , 0) ?>
                                        </td>
                                        <td align=right><?= number_format($agencies['content']['q1maleEmployed'], 0) ?></td>
                                        <td align=right><?= number_format($agencies['content']['q1femaleEmployed'], 0) ?></td>
                                        <td align=right><?= number_format($agencies['content']['q2maleEmployed'], 0) ?></td>
                                        <td align=right><?= number_format($agencies['content']['q2femaleEmployed'], 0) ?></td>
                                        <td align=right><?= number_format($agencies['content']['q3maleEmployed'], 0) ?></td>
                                        <td align=right><?= number_format($agencies['content']['q3femaleEmployed'], 0) ?></td>
                                        <td align=right><?= number_format($agencies['content']['q4maleEmployed'], 0) ?></td>
                                        <td align=right><?= number_format($agencies['content']['q4femaleEmployed'], 0) ?></td>
                                        <td align=right><?= number_format(
                                            $agencies['content']['q1maleEmployed'] +
                                            $agencies['content']['q2maleEmployed'] +
                                            $agencies['content']['q3maleEmployed'] +
                                            $agencies['content']['q4maleEmployed']
                                            , 0) ?>
                                        </td>
                                        <td align=right><?= number_format(
                                            $agencies['content']['q1femaleEmployed'] +
                                            $agencies['content']['q2femaleEmployed'] +
                                            $agencies['content']['q3femaleEmployed'] +
                                            $agencies['content']['q4femaleEmployed']
                                            , 0) ?>
                                        </td>
                                        <td align=right><?= number_format($agencies['content']['q1beneficiary'], 0) ?></td>
                                        <td align=right><?= number_format($agencies['content']['q2beneficiary'], 0) ?></td>
                                        <td align=right><?= number_format($agencies['content']['q3beneficiary'], 0) ?></td>
                                        <td align=right><?= number_format($agencies['content']['q4beneficiary'], 0) ?></td>
                                    </tr>
                                    <?php $k++ ?>
                                <?php } ?>
                            <?php } ?>
                            <?php $j++ ?>
                        <?php } ?>
                    <?php } ?>
                <?php $i++ ?>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php
    $script = '
        $(document).ready(function(){
            $(".summary-monitoring-plan-table").freezeTable({
                "scrollable": true,
            });
        });
    ';

    $this->registerJs($script, View::POS_END);
?>