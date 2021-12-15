<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\FundCluster */

$this->title = 'Update Fund Cluster';
$this->params['breadcrumbs'][] = ['label' => 'Fund Clusters', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="fund-cluster-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
