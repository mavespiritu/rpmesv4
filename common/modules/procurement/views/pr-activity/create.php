<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\procurement\models\PrActivity */

$this->title = 'Create Pr Activity';
$this->params['breadcrumbs'][] = ['label' => 'Pr Activities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pr-activity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
