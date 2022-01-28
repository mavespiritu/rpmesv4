<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Pr */

$this->title = 'Create Pr';
$this->params['breadcrumbs'][] = ['label' => 'Prs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pr-create">

    <?= $this->render('_form', [
        'model' => $model,
        'offices' => $offices,
        'fundSources' => $fundSources,
        'fundClusters' => $fundClusters,
        'signatories' => $signatories,
        'types' => $types,
        'years' => $years,
    ]) ?>

</div>
