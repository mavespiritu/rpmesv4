<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use yii\widgets\LinkPager;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectResult */
/* @var $form yii\widgets\ActiveForm */
function renderSummary($page)
{
    $firstNumber = $page->offset + 1;
    $lastNumber = $page->pageCount - 1 == $page->page ? $page->totalCount : ($page->page + 1) * $page->limit;
    $total = $page->totalCount;
    return 'Showing <b>'.$firstNumber.'-'.$lastNumber.'</b> of <b>'.$total.'</b> items.';
}
?>

<div class="project-result-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <h5>List of Specific Project Outcome</h5>

    <div class="summary"><?= renderSummary($outcomesPages) ?></div>
    <table class="table table-condensed table-responsive table-hover table-striped table-bordered">
        <thead>
            <tr>
                <td align=center><b>#</td>
                <td align=center style="width: 15%;"><b>Project</td>
                <td align=center><b>Objective</td>
                <td align=center style="width: 20%;"><b>Results Indicator</td>
                <td align=center style="width: 20%;"><b>Target</td>
                <td align=center><b>Observed Results</td>
            </tr>   
        </thead>
        <tbody>
        <?php if($outcomes){ ?>
            <?php $i = $outcomesPages->offset + 1; ?>
            <?php foreach($outcomes as $outcome){ ?>
                <tr>
                    <td align=center><?= $i ?></td>
                    <td><?= $outcome->project->project_no.': '.$outcome->project->title ?></td>
                    <td> <?= $form->field($resultModels[$outcome->id], "[$outcome->id]objective")->textArea(['rows' => '6', 'style' => 'resize: none;'])->label(false) ?></td>
                    <td align=center><?= $outcome->performance_indicator ?></td>
                    <td align=center><?= $outcome->target ?></td>
                    <td> <?= $form->field($resultModels[$outcome->id], "[$outcome->id]observed_results")->textArea(['rows' => '6', 'style' => 'resize: none;'])->label(false) ?></td>
                </tr>
                <?php $i++ ?>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>

    <div>
        <div class="pull-left"><?= LinkPager::widget(['pagination' => $outcomesPages]); ?></div>
        <div class="form-group pull-right">
            <br>
            <?= Html::submitButton('Save Results', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
