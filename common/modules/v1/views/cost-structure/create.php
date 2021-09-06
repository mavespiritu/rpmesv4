<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\CostStructure */

$this->title = 'Create Cost Structure';
$this->params['breadcrumbs'][] = ['label' => 'Cost Structures', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cost-structure-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
