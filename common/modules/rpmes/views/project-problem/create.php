<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectProblem */

$this->title = 'Add New Record';
$this->params['breadcrumbs'][] = ['label' => 'Project Problems', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-problem-create">
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $this->render('_form', [
                'model' => $model,
                'projects' => $projects,
            ]) ?>
        </div>
    </div>
</div>
