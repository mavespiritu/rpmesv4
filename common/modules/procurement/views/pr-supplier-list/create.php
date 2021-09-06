<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\procurement\models\PrSupplierList */

$this->title = 'Create Pr Supplier List';
$this->params['breadcrumbs'][] = ['label' => 'Pr Supplier Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pr-supplier-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
