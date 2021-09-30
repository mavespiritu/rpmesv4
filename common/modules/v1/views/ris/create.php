<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ris */

$this->title = 'Create RIS';
$this->params['breadcrumbs'][] = ['label' => 'Ris', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ris-create">

    <?= $this->render('_form', [
        'model' => $model,
        'offices' => $offices,
        'fundClusters' => $fundClusters,
        'signatories' => $signatories,
    ]) ?>

</div>
