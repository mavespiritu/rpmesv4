<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\ForContractItem */

$this->title = 'Update For Contract Item';
$this->params['breadcrumbs'][] = ['label' => 'For Contract Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="for-contract-item-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
