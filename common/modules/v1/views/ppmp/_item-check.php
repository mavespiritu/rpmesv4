<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\web\View;
?>
<tr>
    <td><?= $item['id'] ?></td>
    <td><?= $item['activity'] ?></td>
    <td><?= $item['subactivity'] ?></td>
    <td><?= $item['item'] ?></td>
    <td><?= $item['unitOfMeasure'] ?></td>
    <td><?= number_format($item['cost'], 2) ?></td>
    <td><?= $item['total'] ?></td>
    <td><?= $item['fundSource'] ?></td>
    <td>
    <?php //(Yii::$app->user->can('ProcurementStaff') || Yii::$app->user->can('Administrator')) ? Html::button('<i class="fa fa-edit"></i> Fix Item', ['value' => Url::to(['/v1/ppmp/fix-item', 'id' => $item['id']]), 'class' => 'btn btn-primary btn-xs btn-block', 'id' => 'fix-'.$item['id'].'-button']) : '' ?>
    </td>
</tr>
<?php
  Modal::begin([
    'id' => 'fix-'.$item['id'].'-modal',
    'size' => "modal-md",
    'header' => '<div id="fix-'.$item['id'].'-modal-header"><h4>Fix item</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="fix-'.$item['id'].'-modal-content"></div>';
  Modal::end();
?>
<?php
    $script = '
        $(document).ready(function(){
            $("#fix-'.$item['id'].'-button").click(function(){
              $("#fix-'.$item['id'].'-modal").modal("show").find("#fix-'.$item['id'].'-modal-content").load($(this).attr("value"));
            });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>