<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Pap */

$this->title = 'Update PAP';
$this->params['breadcrumbs'][] = ['label' => 'PAPs', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pap-update">

    <?= $this->render('_form', [
        'model' => $model,
        'costStructures' => $costStructures,
        'organizationalOutcomes' => $organizationalOutcomes,
        'programs' => $programs,
        'subPrograms' => $subPrograms,
        'identifiers' => $identifiers,
    ]) ?>

</div>
