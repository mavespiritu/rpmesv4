<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\SubActivity */

$this->title = 'Update Sub Activity';
$this->params['breadcrumbs'][] = ['label' => 'Sub Activities', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sub-activity-update">

    <?= $this->render('_form', [
        'model' => $model,
        'activities' => $activities,
    ]) ?>

</div>
