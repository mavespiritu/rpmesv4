<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectEndorsement */

$this->title = 'Update Record';
$this->params['breadcrumbs'][] = ['label' => 'RPMES Form 6: Reports on the Status of Projects Encountering Implementation Problems', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-endorsement-create">
    <div class="row">
        <div class="col-md-5 col-xs-12">
            <div class="box box-solid">
                <div class="box-header with-border"><h3 class="box-title">Record Entry Form</h3></div>
                <div class="box-body" style="min-height: calc(100vh - 230px);">
                    <?= $this->render('_form', [
                        'model' => $model,
                        'projects' => $projects,
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-7 col-xs-12">
            <div class="box box-solid">
                    <div class="box-header with-border"><h3 class="box-title">Project Exception Details</h3></div>
                    <div class="box-body" style="height: calc(100vh - 230px); overflow: auto;">
                        <div id="project-exception-details" style="padding: 10px auto;"></div>
                    </div>
            </div>
        </div>
    </div>
</div>
