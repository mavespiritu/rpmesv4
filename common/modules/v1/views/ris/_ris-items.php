<?php 

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\View;
use common\modules\v1\models\PpmpItem;
use fedemotta\datatables\DataTables;
use yii\widgets\ListView;
?>

<?= $dataProvider->getTotalCount() > 0 ? ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => function($item) use ($model){ return $this->render('_ris-item',['model' => $item, 'item' => $model]); },
    'layout' => "<div class='text-info'>{pager}{summary}</div>\n{items}\n{pager}",
    'pager' => [
        'firstPageLabel' => 'First',
        'lastPageLabel'  => 'Last',
        'prevPageLabel' => '<i class="fa fa-backward"></i>',
        'nextPageLabel' => '<i class="fa fa-forward"></i>',
    ],
]) : '<p class="text-center">No items found</p>' ?>
<?php
  Modal::begin([
    'id' => 'buy-modal',
    'size' => "modal-lg",
    'header' => '<div id="buy-modal-header"><h4>Add to RIS</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="buy-modal-content"></div>';
  Modal::end();
?>
<?php
    $script = '
    $(".buy-button").click(function(){
        $("#ris-item-list").empty();
        $("#ris-item-list").hide();
        $("#ris-item-list").fadeIn("slow");
        $("#ris-item-list").load($(this).attr("value"));
    });
    ';

    $this->registerJs($script, View::POS_END);
?>