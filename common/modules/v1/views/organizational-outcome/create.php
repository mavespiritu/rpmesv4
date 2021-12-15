<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\OrganizationalOutcome */

$this->title = 'Create Organizational Outcome';
$this->params['breadcrumbs'][] = ['label' => 'Organizational Outcomes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organizational-outcome-create">

    <?= $this->render('_form', [
        'model' => $model,
        'costStructures' => $costStructures,
    ]) ?>

</div>
