<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\PpmpFundSource */

$this->title = 'Update Fund Source';
$this->params['breadcrumbs'][] = ['label' => 'Fund Sources', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="fund-source-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
