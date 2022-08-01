<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Projects';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-index">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Reports on the Status of Projects Encountering Implementation Problems</h3>
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
                    <div id="project-table"></div>
                </div>
            </div>
        </div>
    </div>
</div>
