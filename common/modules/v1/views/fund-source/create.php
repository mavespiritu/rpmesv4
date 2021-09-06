<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\PpmpFundSource */

$this->title = 'Create Fund Source';
$this->params['breadcrumbs'][] = ['label' => 'Fund Sources', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ppmp-fund-source-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
