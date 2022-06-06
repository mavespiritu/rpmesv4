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
    <?= $this->render('_menu', [
        'model' => $model
    ]) ?>
</div>
<div class="clearfix"></div>
<br>
<div class="summary-monitoring-plan-table" style="height: 600px;">
    <table class="table table-condensed table-bordered table-striped table-condensed table-responsive" cellspacing="0" style="min-width: 2000px;">
        <thead>
            <tr>
                <td rowspan=3 colspan=4 align=center><b>Project Category</b></td>
                <td colspan=5 align=center><b>Financial Requirements</b></td>
                <td colspan=5 align=center><b>Number of Projects</b></td>
                <td colspan=10 align=center><b>Number of Persons Employed</b></td>
                <td colspan=10 align=center><b>Number of Beneficiaries</b></td>
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
        <tr style="font-weight: bolder;">
            <td colspan=4>Grand Total</td>
            <td align=right><?= number_format($total['q1financial'], 2) ?></td>
            <td align=right><?= number_format($total['q2financial'], 2) ?></td>
            <td align=right><?= number_format($total['q3financial'], 2) ?></td>
            <td align=right><?= number_format($total['q4financial'], 2) ?></td>
            <td align=right><?= number_format(
                $total['q1financial'] +
                $total['q2financial'] +
                $total['q3financial'] +
                $total['q4financial']
                , 2) ?>
            </td>
            <td align=right><?= number_format($total['q1physical'], 0) ?></td>
            <td align=right><?= number_format($total['q2physical'], 0) ?></td>
            <td align=right><?= number_format($total['q3physical'], 0) ?></td>
            <td align=right><?= number_format($total['q4physical'], 0) ?></td>
            <td align=right><?= number_format(
                $total['q1physical'] +
                $total['q2physical'] +
                $total['q3physical'] +
                $total['q4physical']
                , 0) ?>
            </td>
            <td align=right><?= number_format($total['q1maleEmployed'], 0) ?></td>
            <td align=right><?= number_format($total['q1femaleEmployed'], 0) ?></td>
            <td align=right><?= number_format($total['q2maleEmployed'], 0) ?></td>
            <td align=right><?= number_format($total['q2femaleEmployed'], 0) ?></td>
            <td align=right><?= number_format($total['q3maleEmployed'], 0) ?></td>
            <td align=right><?= number_format($total['q3femaleEmployed'], 0) ?></td>
            <td align=right><?= number_format($total['q4maleEmployed'], 0) ?></td>
            <td align=right><?= number_format($total['q4femaleEmployed'], 0) ?></td>
            <td align=right><?= number_format(
                $total['q1maleEmployed'] +
                $total['q2maleEmployed'] +
                $total['q3maleEmployed'] +
                $total['q4maleEmployed']
                , 0) ?>
            </td>
            <td align=right><?= number_format(
                $total['q1femaleEmployed'] +
                $total['q2femaleEmployed'] +
                $total['q3femaleEmployed'] +
                $total['q4femaleEmployed']
                , 0) ?>
            </td>
            <td align=right><?= number_format($total['q1beneficiary'], 0) ?></td>
            <td align=right><?= number_format($total['q1groupBeneficiary'], 0) ?></td>
            <td align=right><?= number_format($total['q2beneficiary'], 0) ?></td>
            <td align=right><?= number_format($total['q2groupBeneficiary'], 0) ?></td>
            <td align=right><?= number_format($total['q3beneficiary'], 0) ?></td>
            <td align=right><?= number_format($total['q3groupBeneficiary'], 0) ?></td>
            <td align=right><?= number_format($total['q4beneficiary'], 0) ?></td>
            <td align=right><?= number_format($total['q4groupBeneficiary'], 0) ?></td>
            <td align=right><?= number_format(
                $total['q1beneficiary'] +
                $total['q2beneficiary'] +
                $total['q3beneficiary'] +
                $total['q4beneficiary']
                , 0) ?>
            </td>
            <td align=right><?= number_format(
                $total['q1groupBeneficiary'] +
                $total['q2groupBeneficiary'] +
                $total['q3groupBeneficiary'] +
                $total['q4groupBeneficiary']
                , 0) ?>
            </td>
        </tr>
        <?php if(!empty($data)){ ?>
            <?php $i = 1; ?>
            <?php foreach($data as $firstLevel => $firstLevels){ ?>
                    <tr style="font-weight: bolder;">
                        <td colspan=4><?= $i ?>. <?= $firstLevel ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q1financial'], 2) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q2financial'], 2) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q3financial'], 2) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q4financial'], 2) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['financialTotal'], 2) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q1physical'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q2physical'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q3physical'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q4physical'], 0) ?></td>
                        <td align=right><?= number_format(
                            $firstLevels['content']['q1physical'] +
                            $firstLevels['content']['q2physical'] +
                            $firstLevels['content']['q3physical'] +
                            $firstLevels['content']['q4physical']
                            , 0) ?>
                        </td>
                        <td align=right><?= number_format($firstLevels['content']['q1maleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q1femaleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q2maleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q2femaleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q3maleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q3femaleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q4maleEmployed'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q4femaleEmployed'], 0) ?></td>
                        <td align=right><?= number_format(
                            $firstLevels['content']['q1maleEmployed'] +
                            $firstLevels['content']['q2maleEmployed'] +
                            $firstLevels['content']['q3maleEmployed'] +
                            $firstLevels['content']['q4maleEmployed']
                            , 0) ?>
                        </td>
                        <td align=right><?= number_format(
                            $firstLevels['content']['q1femaleEmployed'] +
                            $firstLevels['content']['q2femaleEmployed'] +
                            $firstLevels['content']['q3femaleEmployed'] +
                            $firstLevels['content']['q4femaleEmployed']
                            , 0) ?>
                        </td>
                        <td align=right><?= number_format($firstLevels['content']['q1beneficiary'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q1groupBeneficiary'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q2beneficiary'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q2groupBeneficiary'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q3beneficiary'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q3groupBeneficiary'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q4beneficiary'], 0) ?></td>
                        <td align=right><?= number_format($firstLevels['content']['q4groupBeneficiary'], 0) ?></td>
                        <td align=right><?= number_format(
                            $firstLevels['content']['q1beneficiary'] +
                            $firstLevels['content']['q2beneficiary'] +
                            $firstLevels['content']['q3beneficiary'] +
                            $firstLevels['content']['q4beneficiary']
                            , 0) ?>
                        </td>
                        <td align=right><?= number_format(
                            $firstLevels['content']['q1groupBeneficiary'] +
                            $firstLevels['content']['q2groupBeneficiary'] +
                            $firstLevels['content']['q3groupBeneficiary'] +
                            $firstLevels['content']['q4groupBeneficiary']
                            , 0) ?>
                        </td>
                    </tr>
                <?php if(!empty($firstLevels['firstLevels'])){ ?>
                    <?php $j = 1; ?>
                    <?php foreach($firstLevels['firstLevels'] as $secondLevel => $secondLevels){ ?>
                        <tr>
                            <td align=right>&nbsp;</td>
                            <td colspan=3><?= $i.'.'.$j ?>. <?= $secondLevel ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q1financial'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q2financial'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q3financial'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q4financial'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['financialTotal'], 2) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q1physical'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q2physical'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q3physical'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q4physical'], 0) ?></td>
                            <td align=right><?= number_format(
                                $secondLevels['content']['q1physical'] +
                                $secondLevels['content']['q2physical'] +
                                $secondLevels['content']['q3physical'] +
                                $secondLevels['content']['q4physical']
                                , 0) ?>
                            </td>
                            <td align=right><?= number_format($secondLevels['content']['q1maleEmployed'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q1femaleEmployed'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q2maleEmployed'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q2femaleEmployed'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q3maleEmployed'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q3femaleEmployed'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q4maleEmployed'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q4femaleEmployed'], 0) ?></td>
                            <td align=right><?= number_format(
                                $secondLevels['content']['q1maleEmployed'] +
                                $secondLevels['content']['q2maleEmployed'] +
                                $secondLevels['content']['q3maleEmployed'] +
                                $secondLevels['content']['q4maleEmployed']
                                , 0) ?>
                            </td>
                            <td align=right><?= number_format(
                                $secondLevels['content']['q1femaleEmployed'] +
                                $secondLevels['content']['q2femaleEmployed'] +
                                $secondLevels['content']['q3femaleEmployed'] +
                                $secondLevels['content']['q4femaleEmployed']
                                , 0) ?>
                            </td>
                            <td align=right><?= number_format($secondLevels['content']['q1beneficiary'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q1groupBeneficiary'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q2beneficiary'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q2groupBeneficiary'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q3beneficiary'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q3groupBeneficiary'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q4beneficiary'], 0) ?></td>
                            <td align=right><?= number_format($secondLevels['content']['q4groupBeneficiary'], 0) ?></td>
                            <td align=right><?= number_format(
                                $secondLevels['content']['q1beneficiary'] +
                                $secondLevels['content']['q2beneficiary'] +
                                $secondLevels['content']['q3beneficiary'] +
                                $secondLevels['content']['q4beneficiary']
                                , 0) ?>
                            </td>
                            <td align=right><?= number_format(
                                $secondLevels['content']['q1groupBeneficiary'] +
                                $secondLevels['content']['q2groupBeneficiary'] +
                                $secondLevels['content']['q3groupBeneficiary'] +
                                $secondLevels['content']['q4groupBeneficiary']
                                , 0) ?>
                            </td>
                        </tr>
                        <?php if(!empty($secondLevels['secondLevels'])){ ?>
                            <?php $k = 1; ?>
                            <?php foreach($secondLevels['secondLevels'] as $thirdLevel => $thirdLevels){ ?>
                                <tr>
                                    <td align=right>&nbsp;</td>
                                    <td align=right>&nbsp;</td>
                                    <td colspan=2><?= $i.'.'.$j.'.'.$k ?>. <?= $thirdLevel ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q1financial'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q2financial'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q3financial'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q4financial'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['financialTotal'], 2) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q1physical'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q2physical'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q3physical'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q4physical'], 0) ?></td>
                                    <td align=right><?= number_format(
                                        $thirdLevels['content']['q1physical'] +
                                        $thirdLevels['content']['q2physical'] +
                                        $thirdLevels['content']['q3physical'] +
                                        $thirdLevels['content']['q4physical']
                                        , 0) ?>
                                    </td>
                                    <td align=right><?= number_format($thirdLevels['content']['q1maleEmployed'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q1femaleEmployed'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q2maleEmployed'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q2femaleEmployed'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q3maleEmployed'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q3femaleEmployed'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q4maleEmployed'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q4femaleEmployed'], 0) ?></td>
                                    <td align=right><?= number_format(
                                        $thirdLevels['content']['q1maleEmployed'] +
                                        $thirdLevels['content']['q2maleEmployed'] +
                                        $thirdLevels['content']['q3maleEmployed'] +
                                        $thirdLevels['content']['q4maleEmployed']
                                        , 0) ?>
                                    </td>
                                    <td align=right><?= number_format(
                                        $thirdLevels['content']['q1femaleEmployed'] +
                                        $thirdLevels['content']['q2femaleEmployed'] +
                                        $thirdLevels['content']['q3femaleEmployed'] +
                                        $thirdLevels['content']['q4femaleEmployed']
                                        , 0) ?>
                                    </td>
                                    <td align=right><?= number_format($thirdLevels['content']['q1beneficiary'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q1groupBeneficiary'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q2beneficiary'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q2groupBeneficiary'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q3beneficiary'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q3groupBeneficiary'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q4beneficiary'], 0) ?></td>
                                    <td align=right><?= number_format($thirdLevels['content']['q4groupBeneficiary'], 0) ?></td>
                                    <td align=right><?= number_format(
                                        $thirdLevels['content']['q1beneficiary'] +
                                        $thirdLevels['content']['q2beneficiary'] +
                                        $thirdLevels['content']['q3beneficiary'] +
                                        $thirdLevels['content']['q4beneficiary']
                                        , 0) ?>
                                    </td>
                                    <td align=right><?= number_format(
                                        $thirdLevels['content']['q1groupBeneficiary'] +
                                        $thirdLevels['content']['q2groupBeneficiary'] +
                                        $thirdLevels['content']['q3groupBeneficiary'] +
                                        $thirdLevels['content']['q4groupBeneficiary']
                                        , 0) ?>
                                    </td>
                                </tr>
                                <?php if(!empty($thirdLevels['thirdLevels'])){ ?>
                                    <?php $l = 1; ?>
                                    <?php foreach($thirdLevels['thirdLevels'] as $fourthLevel => $fourthLevels){ ?>
                                        <tr>
                                            <td align=right>&nbsp;</td>
                                            <td align=right>&nbsp;</td>
                                            <td align=right>&nbsp;</td>
                                            <td><?= $i.'.'.$j.'.'.$k.'.'.$l ?>. <?= $fourthLevel ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q1financial'], 2) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q2financial'], 2) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q3financial'], 2) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q4financial'], 2) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['financialTotal'], 2) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q1physical'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q2physical'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q3physical'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q4physical'], 0) ?></td>
                                            <td align=right><?= number_format(
                                                $fourthLevels['content']['q1physical'] +
                                                $fourthLevels['content']['q2physical'] +
                                                $fourthLevels['content']['q3physical'] +
                                                $fourthLevels['content']['q4physical']
                                                , 0) ?>
                                            </td>
                                            <td align=right><?= number_format($fourthLevels['content']['q1maleEmployed'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q1femaleEmployed'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q2maleEmployed'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q2femaleEmployed'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q3maleEmployed'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q3femaleEmployed'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q4maleEmployed'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q4femaleEmployed'], 0) ?></td>
                                            <td align=right><?= number_format(
                                                $fourthLevels['content']['q1maleEmployed'] +
                                                $fourthLevels['content']['q2maleEmployed'] +
                                                $fourthLevels['content']['q3maleEmployed'] +
                                                $fourthLevels['content']['q4maleEmployed']
                                                , 0) ?>
                                            </td>
                                            <td align=right><?= number_format(
                                                $fourthLevels['content']['q1femaleEmployed'] +
                                                $fourthLevels['content']['q2femaleEmployed'] +
                                                $fourthLevels['content']['q3femaleEmployed'] +
                                                $fourthLevels['content']['q4femaleEmployed']
                                                , 0) ?>
                                            </td>
                                            <td align=right><?= number_format($fourthLevels['content']['q1beneficiary'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q1groupBeneficiary'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q2beneficiary'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q2groupBeneficiary'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q3beneficiary'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q3groupBeneficiary'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q4beneficiary'], 0) ?></td>
                                            <td align=right><?= number_format($fourthLevels['content']['q4groupBeneficiary'], 0) ?></td>
                                            <td align=right><?= number_format(
                                                $fourthLevels['content']['q1beneficiary'] +
                                                $fourthLevels['content']['q2beneficiary'] +
                                                $fourthLevels['content']['q3beneficiary'] +
                                                $fourthLevels['content']['q4beneficiary']
                                                , 0) ?>
                                            </td>
                                            <td align=right><?= number_format(
                                                $fourthLevels['content']['q1groupBeneficiary'] +
                                                $fourthLevels['content']['q2groupBeneficiary'] +
                                                $fourthLevels['content']['q3groupBeneficiary'] +
                                                $fourthLevels['content']['q4groupBeneficiary']
                                                , 0) ?>
                                            </td>
                                        </tr>
                                        <?php $l++ ?>
                                    <?php } ?>
                                <?php } ?>
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