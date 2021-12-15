<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\OrganizationalOutcome */

$this->title = 'Update Organizational Outcome';
$this->params['breadcrumbs'][] = ['label' => 'Organizational Outcomes', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="organizational-outcome-update">

    <?= $this->render('_form', [
        'model' => $model,
        'costStructures' => $costStructures,
    ]) ?>

</div>
