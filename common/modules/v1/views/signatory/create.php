<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Signatories */

$this->title = 'Create Signatory';
$this->params['breadcrumbs'][] = ['label' => 'Signatories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="signatories-create">

    <?= $this->render('_form', [
        'model' => $model,
        'offices' => $offices,
    ]) ?>

</div>
