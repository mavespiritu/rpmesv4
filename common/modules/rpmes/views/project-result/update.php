<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ris */

$this->title = 'Update Project Results Report';
$this->params['breadcrumbs'][] = ['label' => 'Project Results Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-results-update">

    <?= $this->render('_form', [
        'model' => $model,
        'agencies' => $agencies,
    ]) ?>

</div>
