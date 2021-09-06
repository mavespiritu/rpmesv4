<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Obj */

$this->title = 'Update Object';
$this->params['breadcrumbs'][] = ['label' => 'Objects', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="obj-update">

    <?= $this->render('_form', [
        'model' => $model,
        'objects' => $objects,
    ]) ?>

</div>
