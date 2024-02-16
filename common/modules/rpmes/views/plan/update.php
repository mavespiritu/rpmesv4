<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ris */

$this->title = 'Update Monitoring Plan';
$this->params['breadcrumbs'][] = ['label' => 'Plan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="plan-update">

    <?= $this->render('_form', [
        'model' => $model,
        'agencies' => $agencies,
    ]) ?>

</div>
