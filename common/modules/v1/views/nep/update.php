<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ppmp */

$this->title = 'Update NEP';
$this->params['breadcrumbs'][] = ['label' => 'NEPs', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ppmp-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
