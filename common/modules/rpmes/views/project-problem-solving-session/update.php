<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectProblemSolvingSession */

$this->title = 'Update Record';
$this->params['breadcrumbs'][] = ['label' => 'Project Problem Solving Sessions', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="project-problem-solving-session-update">
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
