<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\procurement\models\PrPr */

$this->title = 'Update Purchase Request';
$this->params['breadcrumbs'][] = ['label' => 'Purchase Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->prProcVerification ? $model->prProcVerification->pr_no : $model->dts_no, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pr-pr-update">

    <?= $this->render('_form', [
        'model' => $model,
        'ppmpModel' => $ppmpModel,
    ]) ?>

</div>
