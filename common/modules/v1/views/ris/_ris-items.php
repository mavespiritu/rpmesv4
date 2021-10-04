<?php 

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\View;
use common\modules\v1\models\PpmpItem;

?>

<div class="ris-items">
    <table class="table table-responsive table-condensed">
        <tr>
            <td align=right style="width: 20%;">Activity:</td>
            <td><b><?= $activity->title ?></b></td>
        </tr>
        <tr>
            <td align=right style="width: 20%;">Sub Activity:</td>
            <td><b><?= $subActivity->title ?></b></td>
        </tr>
        <tr>
            <td align=right>Fund Source:</td>
            <td><b><?= $fundSource->code ?></b></td>
        </tr>
    </table>
</div>

<?= GridView::widget([
    'options' => [
        'class' => 'table-responsive table-condensed table-bordered table-striped table-hover',
    ],
    'dataProvider' => $dataProvider,
    'showFooter' => true,
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['style' => 'width: 3%;'],
        ],

        [
            'header' => 'Type', 
            'attribute' => 'type',
        ],
        [
            'header' => 'Object', 
            'format' => 'raw',
            'value' => function($item){
                return $item->obj->code.'<br>'.$item->obj->title;
            }
        ],
        [
            'header' => 'Title', 
            'format' => 'raw',
            'value' => function($item){
                return $item->item->title;
            }
        ],
        'item.unit_of_measure',
        [
            'header' => 'Original Qty', 
            'attribute' => 'quantity',
            'value' => function($item){
                return number_format($item->quantity, 0);
            }
        ],
        [
            'header' => 'Remaining Qty', 
            'attribute' => 'remainingQuantity',
            'value' => function($item){
                return number_format($item->remainingQuantity, 0);
            }
        ],
        [
            'header' => 'Cost Per Unit', 
            'attribute' => 'cost',
            'value' => function($item){
                return number_format($item->cost, 2);
            },
        ],
        [
            'header' => 'Total', 
            'attribute' => 'remainingQuantityTotalCost',
            'contentOptions' => ['style' => 'text-align: right;'],
            'footerOptions' => ['style' => 'text-align: right;'],
            'value' => function($item){
                return number_format($item->remainingQuantityTotalCost, 2);
            },
            'footer' => PpmpItem::pageQuantityTotal($dataProvider->models, 'remainingQuantityTotalCost'),
        ],
        [
            'header' => 'Remarks', 
            'attribute' => 'remarks',
        ],
        [
            'format' => 'raw',
            'headerOptions' => ['style' => 'width:100px'],
            'value' => function($item) use ($model){
                return $item->remainingQuantity > 0 ? Html::button('<i class="fa fa-shopping-cart"></i> Add to RIS', ['value' => Url::to(['/v1/ris/buy', 'id' => $model->id, 'item_id' => $item->id]), 'class' => 'btn btn-primary btn-xs btn-block buy-button']) : '';
            }
        ],
    ],
]); ?>
<?php
  Modal::begin([
    'id' => 'buy-modal',
    'size' => "modal-md",
    'header' => '<div id="buy-modal-header"><h4>Add to RIS</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="buy-modal-content"></div>';
  Modal::end();
?>
<?php
    $script = '
        $(document).ready(function(){
            $(".buy-button").click(function(){
              $("#buy-modal").modal("show").find("#buy-modal-content").load($(this).attr("value"));
            });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>