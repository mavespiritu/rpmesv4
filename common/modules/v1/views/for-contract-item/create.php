<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\ForContractItem */

$this->title = 'Create For Contract Item';
$this->params['breadcrumbs'][] = ['label' => 'For Contract Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="for-contract-item-create">

    <?= $this->render('_form', [
        'model' => $model,
        'items' => $items,
    ]) ?>

</div>
