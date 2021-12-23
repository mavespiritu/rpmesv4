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

<?php /* $dataProvider->getTotalCount() > 0 ? ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => function($item) use ($model){ return $this->render('_ris-item',['model' => $item, 'item' => $model]); },
    'layout' => "<div class='text-info'>{pager}{summary}</div>\n{items}\n{pager}",
    'pager' => [
        'firstPageLabel' => 'First',
        'lastPageLabel'  => 'Last',
        'prevPageLabel' => '<i class="fa fa-backward"></i>',
        'nextPageLabel' => '<i class="fa fa-forward"></i>',
    ],
]) : '<p class="text-center">No items found</p>' */ ?>

<?php if($items){ ?>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Item</th>
        <th>Available Qty</th>
        <?php if($months){ ?>
          <?php foreach($months as $month){ ?>
            <th><?= $month->abbreviation ?></th>
          <?php } ?>
        <?php } ?>
      </tr>
    </thead>
  <?php foreach($items as $item){ ?>
    <tr>
      <td><?= $item->item->title ?></td>
      <td><?= $item->remainingQuantity ?></td>
      <?php if($months){ ?>
        <?php foreach($months as $month){ ?>
          <td>Max: <?= $item->getRemainingQuantityPerMonth($month->id) ?></td>
        <?php } ?>
      <?php } ?>
      </tr>
  <?php } ?>
  </table>
<?php } ?>
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
        $("#ris-item-form").empty();
        $("#ris-item-form").hide();
        $("#ris-item-form").fadeIn("slow");
        $("#ris-item-form").load($(this).attr("value"));
    });
    ';

    $this->registerJs($script, View::POS_END);
?>