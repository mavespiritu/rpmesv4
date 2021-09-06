<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Obj */

$this->title = 'Create Object';
$this->params['breadcrumbs'][] = ['label' => 'Objects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="obj-create">

    <?= $this->render('_form', [
        'model' => $model,
        'objects' => $objects,
    ]) ?>

</div>
