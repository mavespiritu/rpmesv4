<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\procurement\models\PrStockInventory */

$this->title = 'Create Pr Stock Inventory';
$this->params['breadcrumbs'][] = ['label' => 'Pr Stock Inventories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pr-stock-inventory-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
