<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\ProjectProblemSolvingSessionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project Problem Solving Sessions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-problem-solving-session-index">

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
            'year',
            'quarter',
            'project_id',
            'pss_date',
            //'agreement_reached:ntext',
            //'next_step:ntext',
            //'submitted_by',
            //'submitted_date',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{update}{delete}'],
        ],
    ]); ?>


</div>
