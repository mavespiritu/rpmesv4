<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Identifier */

$this->title = 'Create Identifier';
$this->params['breadcrumbs'][] = ['label' => 'Identifiers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="identifier-create">

    <?= $this->render('_form', [
        'model' => $model,
        'costStructures' => $costStructures,
        'organizationalOutcomes' => $organizationalOutcomes,
        'programs' => $programs,
        'subPrograms' => $subPrograms,
    ]) ?>

</div>
