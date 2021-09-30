<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\View;
/* @var $this yii\web\View */
/* @var $searchModel common\modules\v1\models\RisSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'RIS';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ris-index">

    <p>
        <?= Html::button('<i class="fa fa-plus"></i> Create', ['value' => Url::to(['/v1/ris/create']), 'class' => 'btn btn-app', 'id' => 'create-button']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'options' => [
            'class' => 'table-responsive',
        ],
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ris_no',
            'officeName',
            'purpose:ntext',
            'date_required',
            'creatorName',
            'requesterName',

            [
                'format' => 'raw', 
                'value' => function($model){
                    return Html::a('View', ['/v1/ris/view', 'id' => $model->id],['class' => 'btn btn-primary btn-sm btn-block']);
            }],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
<?php
  Modal::begin([
    'id' => 'create-modal',
    'size' => "modal-md",
    'header' => '<div id="create-modal-header"><h4>Create RIS</h4></div>',
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