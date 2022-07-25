<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\ResolutionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Resolutions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resolution-index">

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

            'id',
            'resolution_number',
            'resolution:ntext',
            'date_approved',
            'rpmc_action:ntext',
            //'scanned_file:ntext',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{update}{delete}'],
        ],
    ]); ?>


</div>
