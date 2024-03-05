<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectProblem */

$this->title = 'Add New Record';
$this->params['breadcrumbs'][] = ['label' => 'Form 11: Key Lessons Learned from Issues Resolved at the RPMC Level', 'url' => ['index']];
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
