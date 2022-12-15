<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\EventImage */

$this->title = 'Add New Record';
$this->params['breadcrumbs'][] = ['label' => 'Event Images', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-image-create">
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
