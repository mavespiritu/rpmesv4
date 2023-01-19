<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\ProjectResultSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project Results';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-result-index">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Project Results Form</h3>
                </div>
                <div class="box-body">
                    <?= $this->render('_search', [
                        'model' => $model,
                        'years' => $years,
                        'quarters' => $quarters,
                        'agencies' => $agencies
                    ]) ?>
                    <hr>
                    <?php if(!empty($outcomes)){ ?>
                        <?= $this->render('_form', [
                            'project' => $project,
                            'outcomes' => $outcomes,
                            'resultModels' => $resultModels,
                            'outcomesPages' => $outcomesPages,
                        ]); ?>
                    <?php }else{ ?>
                        <p class="text-center">No project outcomes registered under completed projects.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
