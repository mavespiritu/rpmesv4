<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectProblemSolvingSession */

$this->title = 'Add New Record';
$this->params['breadcrumbs'][] = ['label' => 'Form 8: Problem Solving Session/Facilitation Meeting Conducted', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-problem-solving-session-create">
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $this->render('_form', [
                'model' => $model,
                'projects' => $projects,
                'quarters' => $quarters,
                'years' => $years
            ]) ?>
        </div>
    </div>
</div>
