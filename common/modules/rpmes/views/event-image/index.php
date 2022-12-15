<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\EventImageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Event Images';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-image-index">

    <p>
        <?= Html::a('<i class=\"fa fa-plus\"></i> Add New', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'options' => [
            'class' => 'table-responsive',
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'title:ntext',
            //'uploaded_by',
            'date_uploaded',
            'image',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}{delete}'],
        ],
    ]); ?>


</div>
