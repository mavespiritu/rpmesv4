<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\RdpChapter */

$this->title = 'Update Record';
$this->params['breadcrumbs'][] = ['label' => 'RDP Chapters', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="rdp-chapter-update">
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
