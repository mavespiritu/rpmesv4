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
    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Add New', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Summary of Project Results</h3>
                </div>
                <div class="box-body">
                    <?= $this->render('_search', [
                        'model' => $model,
                        'years' => $years,
                        'agencies' => $agencies,
                    ]) ?>
                    <br><br>
                    <hr>
                    <div id="project-result-table"></div>
                </div>
            </div>
        </div>
    </div>
</div>
