<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectFinding */

$this->title = 'Add New Record';
$this->params['breadcrumbs'][] = ['label' => 'RPMES Form 7: Project Inspection Report', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-finding-create">
    <div class="box box-solid">
        <div class="box-header with-border"><h3 class="box-title">Record Entry Form</h3></div>
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
                'projects' => $projects,
            ]) ?>
        </div>
    </div>
</div>
