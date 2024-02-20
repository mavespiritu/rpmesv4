<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ris */

$this->title = 'Update Project Exception Report';
$this->params['breadcrumbs'][] = ['label' => 'Project Exception Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-exception-update">

    <?= $this->render('_form', [
        'model' => $model,
        'agencies' => $agencies,
        'quarters' => $quarters,
    ]) ?>

</div>
