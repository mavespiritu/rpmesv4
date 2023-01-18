<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectResult */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-result-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <h4><?= $project->project_no.': '.$project->title ?></h4>

    <h5>List of Specific Project Outcome</h5>
    <table class="table table-condensed table-responsive table-hover table-striped table-bordered">
        <thead>
            <tr>
                <td align=center><b>#</td>
                <td align=center><b>Objective</td>
                <td align=center><b>Results Indicator</td>
                <td align=center><b>Target</td>
                <td align=center><b>Observed Results</td>
            </tr>   
        </thead>
        <tbody>
        <?php if($outcomes){ ?>
            <?php $i = 1; ?>
            <?php foreach($outcomes as $outcome){ ?>
                <tr>
                    <td align=center><?= $i ?></td>
                    <td> <?= $form->field($resultModels[$outcome->id], "[$outcome->id]objective")->textArea(['rows' => '3', 'style' => 'resize: none;'])->label(false) ?></td>
                    <td><?= $outcome->performance_indicator ?></td>
                    <td><?= $outcome->target ?></td>
                    <td> <?= $form->field($resultModels[$outcome->id], "[$outcome->id]observed_results")->textArea(['rows' => '3', 'style' => 'resize: none;'])->label(false) ?></td>
                </tr>
                <?php $i++ ?>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>

    <div class="form-group pull-right">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>
    <div class="clearfix"></div>

    <?php ActiveForm::end(); ?>

</div>
