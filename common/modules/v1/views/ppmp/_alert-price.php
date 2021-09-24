<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\web\View;
/* @var $form yii\widgets\ActiveForm */
?>
<p>
    <i class="fa fa-exclamation-circle"></i> You have <?= $itemCount?> item<?= $itemCount > 0 ? 's' : '' ?> with outdated prices.&nbsp;&nbsp;
    <?= Html::button('Review', ['value' => Url::to(['/v1/ppmp/update-price', 'id' => $model->id]), 'class' => 'btn btn-primary btn-sm', 'id' => 'update-item']) ?>
    &nbsp;&nbsp;<a javascript:void(0); onclick="ignoreAlert(<?= $model->id ?>, 'update-price')" style="cursor: pointer;" class="btn btn-default btn-sm">Ignore</a>
</p>
<?php
  Modal::begin([
    'id' => 'update-price-modal',
    'size' => "modal-lg",
    'header' => '<div id="update-price-modal-header"><h4>Review Item Prices</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="update-price-modal-content"></div>';
  Modal::end();
?>
<?php
    $script = '
        $(document).ready(function(){
            $("#update-item").click(function(){
              $("#update-price-modal").modal("show").find("#update-price-modal-content").load($(this).attr("value"));
            });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>