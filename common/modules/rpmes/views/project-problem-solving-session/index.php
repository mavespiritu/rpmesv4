<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\ProjectProblemSolvingSessionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project Problem Solving Sessions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-problem-solving-session-index">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Summary of Financial and Physical Accomplishment</h3>
                </div>
                <div class="box-body">
                    <?= $this->render('_search', [
                        'model' => $model,
                        'quarters' => $quarters,
                        'years' => $years,
                        'agencies' => $agencies,
                        'sectors' => $sectors,
                        'regions' => $regions,
                        'provinces' => $provinces,
                    ]) ?>
                    <hr>
                    <div id="summary-summary-accomplishment-table"></div>
                </div>
            </div>
        </div>
    </div>
</div>
