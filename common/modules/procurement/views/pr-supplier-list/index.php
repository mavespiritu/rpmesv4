<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\modules\procurement\models\PrSupplierListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pr Supplier Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pr-supplier-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Pr Supplier List', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'service_type_id',
            'type',
            'business_name:ntext',
            'business_address:ntext',
            //'contact_person',
            //'landline',
            //'mobile',
            //'email_address:email',
            //'philgeps_no',
            //'bir_registration',
            //'tin_no',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
