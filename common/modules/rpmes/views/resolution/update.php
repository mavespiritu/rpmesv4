<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Resolution */

$this->title = 'Update Resolution';
$this->params['breadcrumbs'][] = ['label' => 'RPMES Form 10: RPMC and RDC Resolutions Related to Implementation of RPMES', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="resolution-update">
    <div class="box box-solid">
        <div class="box-header with-border"><h3 class="box-title">Record Entry Form</h3></div>
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
