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
                        Html::a('<i class="fa fa-list"></i> Accomplish Form 4', ['accomplish-form', 'id' => $model->id], ['class' => 'btn btn-default']) :
                    '' :
                '' :
            '' :
        '';
    ?>

    <?= Html::a('<i class="fa fa-file-excel-o"></i> Generate Form 4', ['/rpmes/project-result/download', 'id' => $model->id, 'type' => 'excel'], ['class' => 'btn btn-default']) ?>

    <?= Html::button('<i class="fa fa-print"></i> Print Form 4', ['onClick' => 'printSummary("'.$model->id.'")', 'class' => 'btn btn-default']) ?>

    <?= Yii::$app->user->can('AgencyUser') ?
            $model->currentStatus == 'Draft' ?
                $dueDate ? 
                    strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ?
                        Html::a('<i class="fa fa-paper-plane-o"></i> Submit Form 4', ['submit', 'id' => $model->id], [
                            'class' => 'btn btn-success',
                            'data' => [
                                'confirm' => 'Are you sure want to submit this Form 4?',
                                'method' => 'post',
                            ],
                        ]) :
                    '' :
                '' :
            '' :
        '';
    ?>

    <?= Yii::$app->user->can('AgencyUser') ?
            $model->currentStatus == 'For further validation' ?
                $dueDate ? 
                    strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ?
                        Html::a('<i class="fa fa-paper-plane-o"></i> Re-submit Form 4', ['submit', 'id' => $model->id], [
                            'class' => 'btn btn-success',
                            'data' => [
                                'confirm' => 'Are you sure want to re-submit this Form 4?',
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
                Html::button('<i class="fa fa-paper-plane-o"></i> Acknowledge Form 4', ['value' => Url::to(['acknowledge', 'id' => $model->id]), 'class' => 'btn btn-success', 'id' => 'acknowledge-button']) :
            '' :
        '';
    ?>

    <?= Yii::$app->user->can('AgencyUser') ?
            $model->currentStatus == 'Acknowledged' ?
                Html::button('<i class="fa fa-file-o"></i> View Acknowledgment', ['value' => Url::to(['acknowledge', 'id' => $model->id]), 'class' => 'btn btn-default', 'id' => 'acknowledge-button']) :
            '' :
        '';
    ?>

    <?= Yii::$app->user->can('Administrator') ?
            $model->currentStatus == 'Submitted' ?
                Html::button('<i class="fa fa-paper-plane-o"></i> Send Form 4 for further validation', ['value' => Url::to(['revert', 'id' => $model->id]), 'class' => 'btn btn-danger', 'id' => 'revert-button']) :
            '' :
        '';
    ?>

</div>
<div class="pull-right">
    <div class="project-results-search">

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
    'id' => 'acknowledge-modal',
    'size' => "modal-lg",
    'header' => Yii::$app->user->can('Administrator') ? '<div id="acknowledge-modal-header"><h4>Acknowledge Form 4</h4></div>' : '<div id="acknowledge-modal-header"><h4>View Acknowledgment</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="acknowledge-modal-content"></div>';
  Modal::end();
?>
<?php
  Modal::begin([
    'id' => 'revert-modal',
    'size' => "modal-lg",
    'header' => '<div id="revert-modal-header"><h4>Send Form 4 for further validation</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="revert-modal-content"></div>';
  Modal::end();
?>
<?php
    $script = '
        $(document).ready(function(){
            $("#acknowledge-button").click(function(){
                $("#acknowledge-modal").modal("show").find("#acknowledge-modal-content").load($(this).attr("value"));
              });

            $("#revert-button").click(function(){
                $("#revert-modal").modal("show").find("#revert-modal-content").load($(this).attr("value"));
              });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>
