<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectResult */

$this->title = 'Update Record';
$this->params['breadcrumbs'][] = ['label' => 'Project Results', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="project-result-update">
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $this->render('_form', [
                'model' => $model,
                'projects' => $projects,
                'years' => $years,
            ]) ?>
        </div>
    </div>
</div>
