<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\View;
/* @var $this yii\web\View */
/* @var $searchModel common\modules\v1\models\PpmpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'PPMP';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ppmp-index">

    <p>
    	<?= Html::button('<i class="fa fa-plus"></i> Create', ['value' => Url::to(['/v1/ppmp/create']), 'class' => 'btn btn-app', 'id' => 'create-button']) ?>
    	<?= Html::button('<i class="fa fa-copy"></i> Copy', ['value' => Url::to(['/v1/ppmp/copy']), 'class' => 'btn btn-app', 'id' => 'copy-button']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <div class="row">
        <div class="col-md-2 col-xs-12">
            <h4>Search Filter</h4>
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>
        <div class="col-md-10 col-xs-12">
            <h4>List of PPMP</h4>
            <?= GridView::widget([
                    'options' => [
                        'class' => 'table-responsive',
                    ],
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        'officeName',
                        'year',
                        'stage',
                        'creatorName',
                        'date_created',
                        //'updated_by',
                        //'date_updated',
                        [
                            'format' => 'raw', 
                            'value' => function($model){
                                return Html::a('View', ['/v1/nep/view', 'id' => $model->id],['class' => 'btn btn-primary btn-block']);
                        }],
                    ],
                ]); ?>
            </div>
        </div>
    </div>

    <?php Pjax::end(); ?>

</div>
<?php
  Modal::begin([
    'id' => 'create-modal',
    'size' => "modal-sm",
    'header' => '<div id="create-modal-header"><h4>Create PPMP</h4></div>'
  ]);
  echo '<div id="create-modal-content"></div>';
  Modal::end();
?>
<?php
  Modal::begin([
    'id' => 'copy-modal',
    'size' => "modal-sm",
    'header' => '<div id="copy-modal-header"><h4>Copy PPMP</h4></div>'
  ]);
  echo '<div id="copy-modal-content"></div>';
  Modal::end();
?>
<?php
    $script = '
        $(document).ready(function(){
            $("#create-button").click(function(){
              $("#create-modal").modal("show").find("#create-modal-content").load($(this).attr("value"));
            });
            $("#copy-button").click(function(){
                $("#copy-modal").modal("show").find("#copy-modal-content").load($(this).attr("value"));
            });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>