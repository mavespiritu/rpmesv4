<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ris */

$this->title = 'Update RIS';
$this->params['breadcrumbs'][] = ['label' => 'Ris', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ris-update">

    <?= $this->render('_form', [
        'model' => $model,
        'offices' => $offices,
        'ppmps' => $ppmps,
        'fundSources' => $fundSources,
        'fundClusters' => $fundClusters,
        'signatories' => $signatories,
        'types' => $types
    ]) ?>

</div>
