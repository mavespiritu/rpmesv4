<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ris */

$this->title = 'Create Project Exception Report';
$this->params['breadcrumbs'][] = ['label' => 'Project Exception Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-exception-create">

    <?= $this->render('_form', [
        'model' => $model,
        'agencies' => $agencies,
        'quarters' => $quarters,
    ]) ?>

</div>
