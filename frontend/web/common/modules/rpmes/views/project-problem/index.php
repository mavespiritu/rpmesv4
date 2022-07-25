<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\ProjectProblemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project Problems';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-problem-index">

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
            'project_id',
            'nature',
            'detail:ntext',
            'strategy:ntext',
            //'responsible_entity:ntext',
            //'lesson_learned:ntext',
            //'submitted_by',
            //'date_submitted',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{update}{delete}'],
        ],
    ]); ?>


</div>
