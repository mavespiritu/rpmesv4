<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\procurement\models\PrMode */

$this->title = 'Create Pr Mode';
$this->params['breadcrumbs'][] = ['label' => 'Pr Modes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pr-mode-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
