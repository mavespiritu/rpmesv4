<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\KeyResultArea */

$this->title = 'Update Record';
$this->params['breadcrumbs'][] = ['label' => 'KRA/Clusters', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="key-result-area-update">
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $this->render('_form', [
                'model' => $model,
                'categories' => $categories,
            ]) ?>
        </div>
    </div>
</div>
