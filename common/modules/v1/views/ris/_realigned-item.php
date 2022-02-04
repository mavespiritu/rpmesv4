<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\web\View;
?>
<tr>
    <td><?= $item['itemTitle'] ?></td>
    <td><?= number_format($item['cost'], 2) ?></td>
    <td><?= number_format($item['total'], 0) ?></td>
    <td align=right><?= number_format($item['cost'] * $item['total'], 2) ?></td>
    <td>
    <?= ($model->status->status == 'Draft' || $model->status->status == 'For Revision') ? Html::button('<i class="fa fa-edit"></i> Edit', ['value' => Url::to(['/v1/ris/update-item', 'id' => $model->id, 'activity_id' => $item['activityId'], 'item_id' => $item['stockNo'], 'cost' => $item['cost'], 'type' => 'Realigned']), 'class' => 'btn btn-primary btn-xs btn-block', 'id' => 'update-'.$item['id'].'-button']) : '' ?>
    <?= ($model->status->status == 'Draft' || $model->status->status == 'For Revision') ? Html::a('<i class="fa fa-trash"></i> Delete', ['delete-item', 'id' => $model->id, 'activity_id' => $item['activityId'], 'item_id' => $item['stockNo'], 'cost' => $item['cost'], 'type' => 'Realigned'], [
        'class' => 'btn btn-xs btn-danger btn-block',
        'data' => [
            'confirm' => 'Are you sure you want to remove this item?',
            'method' => 'post',
        ],
    ]) : '' ?>
    </td>
</tr>
<?php
  Modal::begin([
    'id' => 'update-'.$item['id'].'-modal',
    'size' => "modal-md",
    'header' => '<div id="update-'.$item['id'].'-modal-header"><h4>Edit Item</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="update-'.$item['id'].'-modal-content"></div>';
  Modal::end();
?>
<?php
    $script = '
        $(document).ready(function(){
            $("#update-'.$item['id'].'-button").click(function(){
              $("#update-'.$item['id'].'-modal").modal("show").find("#update-'.$item['id'].'-modal-content").load($(this).attr("value"));
            });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>