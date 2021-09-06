<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\ProcurementMode */

$this->title = 'Update Procurement Mode';
$this->params['breadcrumbs'][] = ['label' => 'Procurement Modes', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="procurement-mode-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
