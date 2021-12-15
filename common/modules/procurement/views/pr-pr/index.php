<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\modules\procurement\models\PrPrSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Purchase Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pr-pr-index">
    <p>
        <?= Html::a('Create Request', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'dts_no',
            'entity_name',
            //'rc_code',
            //'fund_cluster',
            'purpose:ntext',
            'requester',
            //'requester_designation',
            //'approver',
            //'approver_designation',
            //'source_of_fund',
            //'charge_to',
            'date_requested',

            ['template' => '{view}', 'class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
