<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Signatories */

$this->title = 'Update Signatory';
$this->params['breadcrumbs'][] = ['label' => 'Signatories', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="signatories-update">

    <?= $this->render('_form', [
        'model' => $model,
        'offices' => $offices,
    ]) ?>

</div>
