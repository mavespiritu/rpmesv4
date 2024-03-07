<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectProblem */

$this->title = 'Add New Record';
$this->params['breadcrumbs'][] = ['label' => 'RPMES Form 11: Key Lessons Learned from Issues Resolved and Best Practices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-problem-create">
    <div class="box box-solid">
        <div class="box-header with-border"><h3 class="box-title">Record Entry Form</h3></div>
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
                'projects' => $projects,
                'natures' => $natures,
            ]) ?>
        </div>
    </div>
</div>
