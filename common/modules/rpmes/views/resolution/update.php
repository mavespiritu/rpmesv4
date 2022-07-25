<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Resolution */

$this->title = 'Update Resolution';
$this->params['breadcrumbs'][] = ['label' => 'Resolutions', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="resolution-update">
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
