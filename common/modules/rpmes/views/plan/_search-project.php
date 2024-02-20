<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;
use yii\bootstrap\ButtonDropdown;
/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pull-left">
    <?= Yii::$app->user->can('AgencyUser') ?
            $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ?
                $dueDate ? 
                    strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? 
                        Html::button('<i class="fa fa-plus"></i> Include Projects', ['value' => Url::to(['include', 'id' => $model->id]), 'class' => 'btn btn-default', 'id' => 'include-button']) :
                    '' :
                '' :
            '' :
        '';
    ?>
    <?= Yii::$app->user->can('AgencyUser') ?
            $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ?
                $dueDate ? 
                    strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ?
                        Html::a('<i class="fa fa-list"></i> Adjust Targets', ['target', 'id' => $model->id], ['class' => 'btn btn-default']) :
                    Html::a('<i class="fa fa-list"></i> View Targets', ['target', 'id' => $model->id], ['class' => 'btn btn-default']) :
                Html::a('<i class="fa fa-list"></i> View Targets', ['target', 'id' => $model->id], ['class' => 'btn btn-default']) :
            Html::a('<i class="fa fa-list"></i> View Targets', ['target', 'id' => $model->id], ['class' => 'btn btn-default']) :
        '';
    ?>
    <?= Html::a('<i class="fa fa-file-excel-o"></i> Generate Form 1', ['/rpmes/plan/download', 'id' => $model->id, 'type' => 'excel'], ['class' => 'btn btn-default']) ?>

    <?= Html::button('<i class="fa fa-print"></i> Print Form 1', ['onClick' => 'printSummary("'.$model->id.'")', 'class' => 'btn btn-default']) ?>

    <?= Yii::$app->user->can('AgencyUser') ?
            $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ?
                $dueDate ? 
                    strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ?
                        Html::a('<i class="fa fa-paper-plane-o"></i> Submit Form 1', ['submit', 'id' => $model->id], [
                            'class' => 'btn btn-success',
                            'data' => [
                                'confirm' => 'Are you sure want to submit this Form 1?',
                                'method' => 'post',
                            ],
                        ]) :
                    '' :
                '' :
            '' :
        '';
    ?>

    <?= Yii::$app->user->can('Administrator') ?
            $model->currentStatus == 'Submitted' || $model->currentStatus == 'Acknowledged' ?
                Html::button('<i class="fa fa-paper-plane-o"></i> Acknowledge Form 1', ['value' => Url::to(['acknowledge', 'id' => $model->id]), 'class' => 'btn btn-success', 'id' => 'acknowledge-button']) :
            '' :
        '';
    ?>
</div>
<div class="pull-right">
    <div class="plan-search">

        <?php $form = ActiveForm::begin([
            'id' => 'project-search-form',
            'action' => ['view', 'id' => $model->id],
            'method' => 'get',
        ]); ?>

        <?= $form->field($searchModel, 'globalSearch')->textInput([
            'style' => 'border-top: none !important; border-left: none !important; border-right: none !important;', 
            'placeholder' => 'Search Records',
        ])->label(false) ?>

        <?php ActiveForm::end(); ?>

    </div>
</div>
<div class="clearfix"></div>

<?php
  Modal::begin([
    'id' => 'include-modal',
    'size' => "modal-xl",
    'header' => '<div id="include-modal-header"><h4>Include Projects</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="include-modal-content"></div>';
  Modal::end();
?>
<?php
  Modal::begin([
    'id' => 'acknowledge-modal',
    'size' => "modal-lg",
    'header' => '<div id="acknowledge-modal-header"><h4>Acknowledge Form 1</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="acknowledge-modal-content"></div>';
  Modal::end();
?>
<?php
    $script = '
        $(document).ready(function(){
            $("#include-button").click(function(){
              $("#include-modal").modal("show").find("#include-modal-content").load($(this).attr("value"));
            });
            $("#acknowledge-button").click(function(){
                $("#acknowledge-modal").modal("show").find("#acknowledge-modal-content").load($(this).attr("value"));
              });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>