<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\procurement\models\PrProcurementType */

$this->title = 'Create Pr Procurement Type';
$this->params['breadcrumbs'][] = ['label' => 'Pr Procurement Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pr-procurement-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
