<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\modules\v1\models\ProcurementModeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Procurement Modes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="procurement-mode-index">

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create', ['create'], ['class' => 'btn btn-app']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'options' => [
            'class' => 'table-responsive',
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'title',
            'description:ntext',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{update}{delete}'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>
