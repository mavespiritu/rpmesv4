<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\select2\Select2;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\modules\v1\models\OrganizationalOutcomeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Organizational Outcomes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organizational-outcome-index">

    <p>
        <?= Html::a('<i class=\'fa fa-plus\'></i> Create', ['create'], ['class' => 'btn btn-app']) ?>
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

            [
                'attribute' => 'costStructureTitle',
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'cost_structure_id',
                    'data' => $costStructures,
                    'options' => ['placeholder' => 'Select Cost Structure'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]),
            ],
            'code',
            'title:ntext',
            'description:ntext',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{update}{delete}'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>
