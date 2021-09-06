<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\FundCluster */

$this->title = 'Create Fund Cluster';
$this->params['breadcrumbs'][] = ['label' => 'Fund Clusters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fund-cluster-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
