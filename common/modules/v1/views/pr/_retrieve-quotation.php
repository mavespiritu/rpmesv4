<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\DetailView;
use yii\web\View;
use yii\bootstrap\Collapse;
?>

<div class="box box-primary">
    <div class="box-header panel-title"><i class="fa fa-list"></i>Retrieved RFQ</div>
    <div class="box-body">
        <div class="pull-right">
            <?= Html::button('Retrieve RFQ', ['value' => Url::to(['/v1/pr/retrieve-rfq', 'id' => $model->id]), 'class' => 'btn btn-success', 'id' => 'retrieve-rfq-button']) ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<?php
  Modal::begin([
    'id' => 'retrieve-rfq-modal',
    'size' => "modal-lg",
    'header' => '<div id="retrieve-rfq-modal-header"><h4>Retrieve RFQ</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="retrieve-rfq-modal-content"></div>';
  Modal::end();
?>
<?php
    $script = '
        $(document).ready(function(){
            $("#retrieve-rfq-button").click(function(){
              $("#retrieve-rfq-modal").modal("show").find("#retrieve-rfq-modal-content").load($(this).attr("value"));
            });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>