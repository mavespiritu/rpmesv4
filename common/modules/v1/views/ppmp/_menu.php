<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\web\View;

?>

<div>
    <div class="pull-left">
        <?= Html::a('<i class="fa fa-angle-double-left"></i> Back to PPMP List', ['/v1/ppmp/'], ['class' => 'btn btn-app']) ?>
    </div>
    <div class="pull-right">
        <?= Html::button('<i class="fa fa-edit"></i> Edit PPMP', ['value' => Url::to(['/v1/ppmp/update', 'id' => $model->id]), 'class' => 'btn btn-app', 'id' => 'update-button']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> Delete PPMP', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-app',
            'data' => [
                'confirm' => 'Deleting this PPMP will also delete all included items. Would you like to proceed?',
                'method' => 'post',
            ],
        ]) ?>
    </div>
    <div class="clearfix"></div>
</div>
<?php
  Modal::begin([
    'id' => 'update-modal',
    'size' => "modal-sm",
    'header' => '<div id="update-modal-header"><h4>Edit PPMP</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="update-modal-content"></div>';
  Modal::end();
?>
<?php
    $script = '
        $(document).ready(function(){
            $("#update-button").click(function(){
              $("#update-modal").modal("show").find("#update-modal-content").load($(this).attr("value"));
            });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>