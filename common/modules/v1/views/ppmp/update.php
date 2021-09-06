<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ppmp */

$this->title = 'Update PPMP';
$this->params['breadcrumbs'][] = ['label' => 'PPMPs', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ppmp-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
