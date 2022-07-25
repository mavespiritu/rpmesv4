<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ResolutionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="resolution-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'resolution_number') ?>

    <?= $form->field($model, 'resolution') ?>

    <?= $form->field($model, 'date_approved') ?>

    <?= $form->field($model, 'rpmc_action') ?>

    <?php // echo $form->field($model, 'scanned_file') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
