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

    <?= Html::a('<i class="fa fa-file-excel-o"></i> Generate Form 3', ['/rpmes/project-exception/download', 'id' => $model->id, 'type' => 'excel'], ['class' => 'btn btn-default']) ?>

    <?= Html::button('<i class="fa fa-print"></i> Print Form 3', ['onClick' => 'printSummary("'.$model->id.'")', 'class' => 'btn btn-default']) ?>

    <?= Yii::$app->user->can('AgencyUser') ?
            $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ?
                $dueDate ? 
                    strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ?
                        Html::a('<i class="fa fa-paper-plane-o"></i> Submit Form 3', ['submit', 'id' => $model->id], [
                            'class' => 'btn btn-success',
                            'data' => [
                                'confirm' => 'Are you sure want to submit this Form 3?',
                                'method' => 'post',
                            ],
                        ]) :
                    '' :
                '' :
            '' :
        '';
    ?>

    <?php /* Yii::$app->user->can('Administrator') ? Html::a('<i class="fa fa-tag"></i> Provide NPMC Endorsement', ['endorse', 'id' => $model->id], [
        'class' => 'btn btn-success',
    ]) : Html::a('<i class="fa fa-tag"></i> View NPMC Endorsement', ['endorse', 'id' => $model->id], [
        'class' => 'btn btn-default',
    ]) */
    ?>

    <?= Yii::$app->user->can('Administrator') ?
            $model->currentStatus == 'Submitted' || $model->currentStatus == 'Acknowledged' ?
                Html::button('<i class="fa fa-paper-plane-o"></i> Acknowledge Form 3', ['value' => Url::to(['acknowledge', 'id' => $model->id]), 'class' => 'btn btn-success', 'id' => 'acknowledge-button']) :
            '' :
        '';
    ?>

</div>
<div class="pull-right">
    <div class="project-exception-search">

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
