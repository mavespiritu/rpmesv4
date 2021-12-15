<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\SubActivity */

$this->title = 'Create Sub Activity';
$this->params['breadcrumbs'][] = ['label' => 'Sub Activities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-activity-create">

    <?= $this->render('_form', [
        'model' => $model,
        'activities' => $activities,
    ]) ?>

</div>
