<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\procurement\models\PrMode */

$this->title = 'Update Pr Mode: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Pr Modes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pr-mode-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
