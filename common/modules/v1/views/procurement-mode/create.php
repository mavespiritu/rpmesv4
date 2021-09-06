<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\ProcurementMode */

$this->title = 'Create Procurement Mode';
$this->params['breadcrumbs'][] = ['label' => 'Procurement Modes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="procurement-mode-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
