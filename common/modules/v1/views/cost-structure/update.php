<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\CostStructure */

$this->title = 'Update Cost Structure';
$this->params['breadcrumbs'][] = ['label' => 'Cost Structures', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cost-structure-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
