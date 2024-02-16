<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pull-left">
    <?= Html::button('<i class="fa fa-plus"></i> Add Accomplishment Report', ['value' => Url::to(['create']), 'class' => 'btn btn-success', 'id' => 'create-button']) ?>
</div>
<div class="pull-right">
    <div class="plan-search">

        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
        ]); ?>

        <?= $form->field($model, 'globalSearch')->textInput(['style' => 'border-top: none !important; border-left: none !important; border-right: none !important;', 'placeholder' => 'Search Records'])->label(false) ?>

        <?php ActiveForm::end(); ?>

    </div>
</div>
<div class="clearfix"></div>

<?php
  Modal::begin([
    'id' => 'create-modal',
    'size' => "modal-md",
    'header' => '<div id="create-modal-header"><h4>Add Accomplishment Report</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="create-modal-content"></div>';
  Modal::end();
?>
<?php
    $script = '
        $(document).ready(function(){
            $("#create-button").click(function(){
              $("#create-modal").modal("show").find("#create-modal-content").load($(this).attr("value"));
            });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>