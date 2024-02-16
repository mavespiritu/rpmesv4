<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Draft Projects';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-index">

    <?= $this->render('_search-draft', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'options' => [
            'class' => 'table-responsive',
        ],
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'title',
                'header' => 'Program/Project Title',
                'headerOptions' => [
                    'style' => 'width: 30%;'
                ]
            ],
            [
                'attribute' => 'agency.code',
                'header' => 'Agency',
                'headerOptions' => [
                    'style' => 'width: 20%;'
                ]
            ],
            [
                'attribute' => 'sector.title',
                'header' => 'Sector',
                'headerOptions' => [
                    'style' => 'width: 25%;'
                ]
            ],
            [
                'attribute' => 'modeOfImplementation.title',
                'header' => 'Mode of Implementation',
                'headerOptions' => [
                    'style' => 'width: 25%;'
                ]
            ],
            [
                'class' => 'yii\grid\ActionColumn', 
                'header' => 'Actions',
                'template' => '<center>{update} {delete}</center>'
            ],
        ],
    ]); ?>


</div>
