<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\procurement\models\PrServiceType */

$this->title = 'Update Pr Service Type: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Pr Service Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pr-service-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
