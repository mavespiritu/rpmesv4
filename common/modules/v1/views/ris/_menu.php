<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\web\View;

?>

<div>
    <div class="pull-left">
        <?= Html::a('<i class="fa fa-angle-double-left"></i> Back to RIS List', ['/v1/ris/'], ['class' => 'btn btn-app']) ?>
        <?= Html::button('<i class="fa fa-plus"></i> Add Supplemental', ['value' => Url::to(['/v1/ris/create-supplemental', 'id' => $model->id]), 'class' => 'btn btn-app', 'id' => 'supp-button']) ?>
    </div>
    <div class="pull-right">
        <?= Html::button('<i class="fa fa-eye"></i> View Details', ['value' => Url::to(['/v1/ris/info', 'id' => $model->id]), 'class' => 'btn btn-app', 'id' => 'view-button']) ?>
        <?= Html::button('<i class="fa fa-edit"></i> Edit RIS', ['value' => Url::to(['/v1/ris/update', 'id' => $model->id]), 'class' => 'btn btn-app', 'id' => 'update-button']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> Delete RIS', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-app',
            'data' => [
                'confirm' => 'Deleting this RIS will also delete all included items. Would you like to proceed?',
                'method' => 'post',
            ],
        ]) ?>
    </div>
    <div class="clearfix"></div>
</div>
<?php
  Modal::begin([
    'id' => 'update-modal',
    'size' => "modal-md",
    'header' => '<div id="update-modal-header"><h4>Edit RIS</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="update-modal-content"></div>';
  Modal::end();
?>
<?php
  Modal::begin([
    'id' => 'view-modal',
    'size' => "modal-md",
    'header' => '<div id="view-modal-header"><h4>RIS Details</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="view-modal-content"></div>';
  Modal::end();
?>
<?php
  Modal::begin([
    'id' => 'supp-modal',
    'size' => "modal-md",
    'header' => '<div id="supp-modal-header"><h4>Add Supplemental Item</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="supp-modal-content"></div>';
  Modal::end();
?>
<?php
    $script = '
        $(document).ready(function(){
            $("#update-button").click(function(){
              $("#update-modal").modal("show").find("#update-modal-content").load($(this).attr("value"));
            });
            $("#view-button").click(function(){
                $("#view-modal").modal("show").find("#view-modal-content").load($(this).attr("value"));
            });
            $("#supp-button").click(function(){
                $("#supp-modal").modal("show").find("#supp-modal-content").load($(this).attr("value"));
            });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>