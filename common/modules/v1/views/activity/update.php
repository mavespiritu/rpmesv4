<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Activity */

$this->title = 'Update Activity';
$this->params['breadcrumbs'][] = ['label' => 'Activities', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="activity-update">

    <?= $this->render('_form', [
        'model' => $model,
        'paps' => $paps,
    ]) ?>

</div>
