<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectFinding */

$this->title = 'Update Record';
$this->params['breadcrumbs'][] = ['label' => 'Project Findings', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="project-finding-update">
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $this->render('_form', [
                'model' => $model,
                'projects' => $projects,
            ]) ?>
        </div>
    </div>
</div>
