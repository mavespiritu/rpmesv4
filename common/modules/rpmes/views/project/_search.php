<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'project_no') ?>

    <?= $form->field($model, 'year') ?>

    <?= $form->field($model, 'agency_id') ?>

    <?= $form->field($model, 'program_id') ?>

    <?php // echo $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'sector_id') ?>

    <?php // echo $form->field($model, 'sub_sector_id') ?>

    <?php // echo $form->field($model, 'location_scope_id') ?>

    <?php // echo $form->field($model, 'mode_of_implementation_id') ?>

    <?php // echo $form->field($model, 'fund_source_id') ?>

    <?php // echo $form->field($model, 'typhoon') ?>

    <?php // echo $form->field($model, 'data_type') ?>

    <?php // echo $form->field($model, 'period') ?>

    <?php // echo $form->field($model, 'start_date') ?>

    <?php // echo $form->field($model, 'completion_date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
