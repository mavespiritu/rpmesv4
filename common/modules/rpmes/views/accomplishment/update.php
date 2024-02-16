<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ris */

$this->title = 'Update Accomplishment Report';
$this->params['breadcrumbs'][] = ['label' => 'Accomplishment Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="accomplishment-update">

    <?= $this->render('_form', [
        'model' => $model,
        'agencies' => $agencies,
        'quarters' => $quarters,
    ]) ?>

</div>
