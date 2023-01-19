<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Training */

$this->title = 'Update Record';
$this->params['breadcrumbs'][] = ['label' => 'Form 9: List of Training/Workshops Conducted', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="training-update">
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
