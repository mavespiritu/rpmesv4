<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ris */

$this->title = 'Create Monitoring Plan';
$this->params['breadcrumbs'][] = ['label' => 'Plan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="plan-create">

    <?= $this->render('_form', [
        'model' => $model,
        'agencies' => $agencies,
    ]) ?>

</div>
