<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectProblemSolvingSession */

$this->title = 'Update Record';
$this->params['breadcrumbs'][] = ['label' => 'RPMES Form 8: Problem-Solving Session/Facilitation Meeting Conducted', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="project-problem-solving-session-update">
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
