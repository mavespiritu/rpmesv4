<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pull-left">
    <?= Html::a('<i class="fa fa-plus"></i> Add Project', ['create'], ['class' => 'btn btn-success']) ?>
</div>
<div class="pull-right">
    <div class="project-search">

        <?php $form = ActiveForm::begin([
            'action' => ['draft'],
            'method' => 'get',
        ]); ?>

        <?= $form->field($model, 'globalSearch')->label('Search Records') ?>

        <?php ActiveForm::end(); ?>

    </div>
</div>
<div class="clearfix"></div>