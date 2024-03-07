<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Training */

$this->title = 'Add New Record';
$this->params['breadcrumbs'][] = ['label' => 'RPMES Form 9: Trainings/Workshops conducted/facilitated/attended by the RPMC', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="training-create">
    <div class="box box-solid">
        <div class="box-header with-border"><h3 class="box-title">Record Entry Form</h3></div>
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
