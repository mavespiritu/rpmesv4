<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ppmp */

$this->title = 'Create NEP';
$this->params['breadcrumbs'][] = ['label' => 'PPMPs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ppmp-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
