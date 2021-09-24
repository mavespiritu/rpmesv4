<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Item */

$this->title = 'Update Item';
$this->params['breadcrumbs'][] = ['label' => 'Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="item-update">

    <?= $this->render('_form', [
        'model' => $model,
        'procurementModes' => $procurementModes,
        'categories' => $categories,
        'classifications' => $classifications,
    ]) ?>

</div>
