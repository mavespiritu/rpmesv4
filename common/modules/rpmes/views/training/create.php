<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Training */

$this->title = 'Add New Record';
$this->params['breadcrumbs'][] = ['label' => 'Form 9: List of Training/Workshops Conducted', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="training-create">
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $this->render('_form', [
                'model' => $model,
                'yearsRange' => $yearsRange,
            ]) ?>
        </div>
    </div>
</div>
