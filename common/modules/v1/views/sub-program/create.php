<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\SubProgram */

$this->title = 'Create Sub Program';
$this->params['breadcrumbs'][] = ['label' => 'Sub Programs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-program-create">

    <?= $this->render('_form', [
        'model' => $model,
        'costStructures' => $costStructures,
        'organizationalOutcomes' => $organizationalOutcomes,
        'programs' => $programs,
    ]) ?>

</div>
