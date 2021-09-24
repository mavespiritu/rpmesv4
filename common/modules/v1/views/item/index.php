<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\web\View;
/* @var $searchModel common\modules\v1\models\ItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-index">

    <p>
    <?= Html::button('<i class="fa fa-plus"></i> Create', ['value' => Url::to(['/v1/item/create']), 'class' => 'btn btn-app', 'id' => 'create-button']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <div class="row">
        <div class="col-md-2 col-xs-12">
            <div class="box box-primary">
                <div class="box-header panel-title"><i class="fa fa-search"></i> Search Filter</div>
                <div class="box-body">
                    <?= $this->render('_search', [
                        'model' => $searchModel,
                        'procurementModes' => $procurementModes,
                        'categories' => $categories,
                        'classifications' => $classifications,
                    ]); ?>
                </div>
            </div>
        </div>
        <div class="col-md-10 col-xs-12">
            <div class="box box-primary">
                <div class="box-header panel-title"><i class="fa fa-list"></i> Item List</div>
                <div class="box-body">
                <?= GridView::widget([
                    'options' => [
                        'class' => 'table-responsive',
                    ],
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        [
                            'header' => 'Mode of Procurement', 
                            'attribute' => 'procurement_mode_id',
                            'value' => function($model){
                                return $model->procurementMode->title;
                            },
                        ],
                        'code',
                        'title:ntext',
                        'unit_of_measure',
                        [
                            'header' => 'Cost Per Unit', 
                            'attribute' => 'total',
                            'contentOptions' => ['style' => 'text-align: right;'],
                            'value' => function($model){
                                return number_format($model->currentCost, 2);
                            },
                        ],
                        'cse',
                        [
                            'format' => 'raw', 
                            'value' => function($model){
                                return Html::a('View', ['/v1/item/view', 'id' => $model->id],['class' => 'btn btn-primary btn-xs btn-block']);
                        }],
                    ],
                ]); ?>
                </div>
            </div>
        </div>
    </div>

    <?php Pjax::end(); ?>

</div>
<?php
  Modal::begin([
    'id' => 'create-modal',
    'size' => "modal-md",
    'header' => '<div id="create-modal-header"><h4>Create Item</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="create-modal-content"></div>';
  Modal::end();
?>
<?php
    $script = '
        $(document).ready(function(){
            $("#create-button").click(function(){
              $("#create-modal").modal("show").find("#create-modal-content").load($(this).attr("value"));
            });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>