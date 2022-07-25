<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectProblemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-problem-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'project_id') ?>

    <?= $form->field($model, 'nature') ?>

    <?= $form->field($model, 'detail') ?>

    <?= $form->field($model, 'strategy') ?>

    <?php // echo $form->field($model, 'responsible_entity') ?>

    <?php // echo $form->field($model, 'lesson_learned') ?>

    <?php // echo $form->field($model, 'submitted_by') ?>

    <?php // echo $form->field($model, 'date_submitted') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
