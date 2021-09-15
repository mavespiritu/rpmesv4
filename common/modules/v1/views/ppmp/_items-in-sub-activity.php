<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\View;
use common\modules\v1\models\PpmpItem;

?>

<?= GridView::widget([
    'tableOptions' => [
        'class' => 'table table-responsive table-condensed table-bordered table-striped table-hover',
    ],
    'dataProvider' => $dataProvider,
    'showFooter' => true,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'header' => 'Object', 
            'contentOptions' => ['style' => 'width: 10%;'],
            'format' => 'raw',
            'value' => function($item){
                return $item->obj->code.'<br>'.$item->obj->title;
            }
        ],
        'item.title',
        'item.unit_of_measure',
        [
            'header' => 'Type', 
            'attribute' => 'type',
        ],
        [
            'header' => 'Quantity', 
            'attribute' => 'quantity',
            'value' => function($item){
                return number_format($item->quantity, 0);
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
            'attribute' => 'totalCost',
            'contentOptions' => ['style' => 'text-align: right;'],
            'footerOptions' => ['style' => 'text-align: right;'],
            'value' => function($item){
                return number_format($item->totalCost, 2);
            },
            'footer' => PpmpItem::pageQuantityTotal($dataProvider->models, 'totalCost'),
        ],
        [
            'format' => 'raw', 
            'headerOptions' => ['style' => 'width:100px'],
            'value' => function($item) use ($subActivity){
                return Html::button('Update', ['value' => Url::to(['/v1/ppmp/update-item', 'id' => $item->id]), 'class' => 'btn btn-primary btn-xs btn-block update-item-button-'.$subActivity->id]);
            }
        ],
        [
            'format' => 'raw',
            'headerOptions' => ['style' => 'width:100px'],
            'value' => function($item){
                return Html::button('Delete', ['class' => 'btn btn-danger btn-xs btn-block', 'onClick' => 'deleteItem('.$item->id.')']);
            }
        ],
    ],
]); ?>

<?php
  Modal::begin([
    'id' => 'update-item-modal-'.$subActivity->id,
    'size' => "modal-lg",
    'header' => '<div id="update-item-modal-header-'.$subActivity->id.'"><h4>Update Item</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="update-item-modal-content-'.$subActivity->id.'"></div>';
  Modal::end();
?>

<?php
    $script = '
        function loadItems(id, activity_id, fund_source_id)
        {
            $.ajax({
                url: "'.Url::to(['/v1/ppmp/load-items']).'",
                data: {
                    id: id,
                    activity_id: activity_id,
                    fund_source_id: fund_source_id,
                },
                beforeSend: function(){
                    $("#items").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
                },
                success: function (data) {
                    $("#items").empty();
                    $("#items").hide();
                    $("#items").fadeIn("slow");
                    $("#items").html(data);
                },
                error: function (err) {
                    console.log(err);
                }
            });
        }

        function deleteItem(id)
        {
            var con = confirm("Are you sure you want to delete this item?");
            if(con == true)
            { 
                $.ajax({
                    url: "'.Url::to(['/v1/ppmp/delete-item']).'?id="+ id,
                    method: "POST",
                    success: function (data) {
                        alert("Item Deleted");
                        loadItems('.$model->id.','.$activity->id.','.$fundSource->id.');
                        loadPpmpTotal('.$model->id.');
                        loadOriginalTotal('.$model->id.');
                        loadSupplementalTotal('.$model->id.');
                        loadItemSummary('.$model->id.');
                    },
                    error: function (err) {
                        console.log(err);
                    }
                });
            }
        }

        $(document).ready(function(){
            $(".update-item-button-'.$subActivity->id.'").click(function(){
                //$("#update-item-modal-content-'.$subActivity->id.'").empty();
                //$("#update-item-modal-content-'.$subActivity->id.'").hide();
                //$("#update-item-modal-content-'.$subActivity->id.'").fadeIn("slow");
                //$("#update-item-modal-'.$subActivity->id.'").modal("show").find("#update-item-modal-content-'.$subActivity->id.'").load($(this).attr("value"));
                $("#item-form-container").load($(this).attr("value"));
                $("#close-item-form-button").css("display", "block");
                $("#create-item-button").css("display", "none");
            });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>