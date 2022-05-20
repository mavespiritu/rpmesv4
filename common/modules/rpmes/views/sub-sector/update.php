<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\SubSector */

$this->title = 'Update Record';
$this->params['breadcrumbs'][] = ['label' => 'Sub Sectors', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sub-sector-update">
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
