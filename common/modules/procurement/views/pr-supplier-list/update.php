<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\procurement\models\PrSupplierList */

$this->title = 'Update Pr Supplier List: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pr Supplier Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pr-supplier-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
