<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\SubSectorPerSector */

$this->title = 'Add New Record';
$this->params['breadcrumbs'][] = ['label' => 'Sub Sector Per Sectors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-sector-per-sector-create">
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $this->render('_form', [
                'model' => $model,
                'sectors' => $sectors,
                'subSectors' => $subSectors,
            ]) ?>
        </div>
    </div>
</div>
