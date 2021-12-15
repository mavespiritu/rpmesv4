<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\SubProgram */

$this->title = 'Update Sub Program';
$this->params['breadcrumbs'][] = ['label' => 'Sub Programs', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sub-program-update">

    <?= $this->render('_form', [
        'model' => $model,
        'costStructures' => $costStructures,
        'organizationalOutcomes' => $organizationalOutcomes,
        'programs' => $programs,
    ]) ?>

</div>
