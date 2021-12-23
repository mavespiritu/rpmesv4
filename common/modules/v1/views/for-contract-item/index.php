<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\v1\models\ForContractItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'For Contract Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="for-contract-item-index">

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Add Item', ['create'], ['class' => 'btn btn-app']) ?>
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

            'itemName',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{delete}'],
        ],
    ]); ?>


</div>
