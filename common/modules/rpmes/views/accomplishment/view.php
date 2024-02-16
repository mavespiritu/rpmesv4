<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\web\View;
use yii\web\JsExpression;
use yii\bootstrap\Dropdown;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Project */

$this->title = 'Accomplishment Report for '.$model->quarter.' '.$model->year;
$this->params['breadcrumbs'][] = ['label' => 'Accomplishment Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$successMessage = \Yii::$app->getSession()->getFlash('success');
?>

<?php
Modal::begin([
    'id' => 'update-modal',
    'size' => "modal-md",
    'header' => '<div id="update-modal-header"><h4>Update Accomplishment Report</h4></div>',
    'options' => ['tabindex' => false],
]);
echo '<div id="update-modal-content"></div>';
Modal::end();
?>

<?php
Modal::begin([
    'id' => 'oi-modal',
    'size' => "modal-xl",
    'header' => '<div id="oi-modal-header"><h4>View Output Indicators</h4></div>',
    'options' => ['tabindex' => false],
]);
echo '<div id="oi-modal-content"></div>';
Modal::end();
?>

<div class="project-view">
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Status: 
                <?php if($model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation'){ ?>
                    <?= $model->currentStatus ?>
                <?php }else { ?>
                    <?= $model->currentStatus ?> <small>by <?= $model->currentSubmissionLog->actor ?> last <?= date("F j, Y H:i:s", strtotime($model->currentSubmissionLog->datetime)) ?></small>
                <?php } ?>
            </h3>
            <div class="box-tools pull-right">
                <?= Html::a('<i class="fa fa-backward"></i> Go back to Accomplishment Reports', ['index'], [
                    'class' => 'btn btn-box-tool',
                ]) ?>
                <?= Yii::$app->user->can('AgencyUser') ? 
                        $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
                            count($model->plans) < 1 ? 
                                Html::a('<i class="fa fa-pencil"></i> Update Accomplishment Report', '#', [
                                    'class' => 'update-button btn btn-box-tool',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#update-modal',
                                    'data-url' => Url::to(['update', 'id' => $model->id]),
                                ]) : 
                            '' : 
                        '' : 
                    '' ?>
                <?= Yii::$app->user->can('AgencyUser') ? 
                        $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
                            count($model->plans) < 1 ?  
                                Html::a('<i class="fa fa-trash"></i> Delete Accomplishment Report', ['delete', 'id' => $model->id], [
                                    'class' => 'btn btn-box-tool',
                                    'data' => [
                                        'confirm' => 'Are you sure want to delete this item?',
                                        'method' => 'post',
                                    ],
                                ]) : 
                            '' : 
                        '' : 
                    '' ?>
            </div>  
        </div>
        <div class="box-body" style="min-height: calc(100vh - 235px);">

        <?= $this->render('_search-project', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dueDate' => $dueDate
        ]); ?>

        <?= GridView::widget([
            'options' => [
                'class' => 'table-responsive'
            ],
            'tableOptions' => [
                'class' => 'table table-bordered table-striped table-hover',
                'id' => 'projects-table'
            ],
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions' => [
                        'style' => 'background-color: #002060; color: white; font-weight: normal;'
                    ]
                ],

                //'id',
                [
                    'attribute' => 'project.project_no',
                    'header' => 'Project No.',
                    'headerOptions' => [
                        'style' => 'width: 5%; background-color: #002060; color: white; font-weight: normal;'
                    ]
                ],
                [
                    'attribute' => 'project.title',
                    'header' => 'Program/Project Title',
                    'headerOptions' => [
                        'style' => 'width: 15%; background-color: #002060; color: white; font-weight: normal;'
                    ]
                ],
                [
                    'header' => 'Appropriations',
                    'headerOptions' => [
                        'style' => 'width: 10%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'contentOptions' => [
                        'style' => 'text-align: right;'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        return number_format($plan->project->getNewAccomplishedAppropriationsForQuarter($model->year)[$model->quarter], 2);
                    }
                ],
                [
                    'header' => 'Allotment',
                    'headerOptions' => [
                        'style' => 'width: 10%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'contentOptions' => [
                        'style' => 'text-align: right;'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        return number_format($plan->project->getNewAccomplishedAllotmentForQuarter($model->year)[$model->quarter], 2);
                    }
                ],
                [
                    'header' => 'Obligations',
                    'headerOptions' => [
                        'style' => 'width: 10%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'contentOptions' => [
                        'style' => 'text-align: right;'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        return number_format($plan->project->getNewAccomplishedObligationForQuarter($model->year)[$model->quarter], 2);
                    }
                ],
                [
                    'header' => 'Disbursements',
                    'headerOptions' => [
                        'style' => 'width: 10%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'contentOptions' => [
                        'style' => 'text-align: right;'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        return number_format($plan->project->getNewAccomplishedDisbursementForQuarter($model->year)[$model->quarter], 2);
                    }
                ],
                [
                    'header' => 'Target <br> OWPA (%)',
                    'headerOptions' => [
                        'style' => 'width: 10%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'contentOptions' => [
                        'style' => 'text-align: center;'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        return number_format($plan->project->getTargetOwpa($model->year)[$model->quarter], 2);
                    }
                ],
                [
                    'header' => 'Actual <br> OWPA (%)',
                    'headerOptions' => [
                        'style' => 'width: 10%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'contentOptions' => [
                        'style' => 'text-align: center;'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        return number_format($plan->project->getActualOwpa($model->year)[$model->quarter], 2);
                    }
                ],
                [
                    'header' => 'Slippage <br> (%)',
                    'headerOptions' => [
                        'style' => 'width: 10%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'contentOptions' => [
                        'style' => 'text-align: center;'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        return $plan->project->getSlippage($model->year)[$model->quarter] >= 0 ? number_format($plan->project->getSlippage($model->year)[$model->quarter], 2) : '<font style="color:red">'.number_format($plan->project->getSlippage($model->year)[$model->quarter], 2).'</font>';
                    }
                ],
                [
                    'header' => 'Output <br>Indicators (OI)',
                    'headerOptions' => [
                        'style' => 'width: 10%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'contentOptions' => [
                        'style' => 'text-align: center'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        return $plan->project->getProjectExpectedOutputs()->where(['year' => $model->year])->count() > 1 ? 
                            Html::button('<i class="fa fa-list"></i> '.$plan->project->getProjectExpectedOutputs()->where(['year' => $model->year])->count().' OI/s', ['value' => Url::to(['output-indicator', 'id' => $model->id, 'plan_id' => $plan->id]), 'class' => 'btn btn-link oi-button']) : 
                            Html::button('<i class="fa fa-list"></i> '.$plan->project->getProjectExpectedOutputs()->where(['year' => $model->year])->count().' OI', ['value' => Url::to(['output-indicator', 'id' => $model->id, 'plan_id' => $plan->id]), 'class' => 'btn btn-link oi-button']);
                    }
                ],
                /* [
                    'format' => 'raw',
                    'header' => $model->draft == 'Yes' ? '<input type="checkbox" class="check-all-projects" />' : '',
                    'headerOptions' => [
                        'style' => 'background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'value' => function ($plan) use ($form, $projects, $model) {
                        return $model->draft == 'Yes' ? $form->field($projects[$plan->id], "[$plan->id]id")->checkbox([
                            'value' => $plan->id, 
                            'class' => 'check-project', 
                            'id' => 'check-project-'.$plan->id, 
                            'label' => ''
                        ]) : '';
                    },
                ], */
            ],
        ]); ?>
        </div>
    </div>
</div>


<?php
// update plan modal
$this->registerJs('
    $(".update-button").click(function(e){
        e.preventDefault();

        $("#update-modal").modal("show").find("#update-modal-content").load($(this).data("url"));
        
        return false;
    });
');
?>

<?php
// alert message for actions
if ($successMessage) {
    $this->registerJs("
        $(document).ready(function() {
            // Display the flash message
            $('.alert-success').fadeIn();

            // Hide the flash message after 5 seconds
            setTimeout(function() {
                $('.alert-success').fadeOut();
            }, 5000);
        });
    ");
}
?>

<?php
$this->registerJs('
    $(".oi-button").click(function(e){
        $("#oi-modal").modal("show").find("#oi-modal-content").load($(this).attr("value"));
    });
');
?>

<?php
    $script = '
        function printSummary(id)
        {
            var printWindow = window.open(
                "'.Url::to(['/rpmes/accomplishment/download']).'?type=print&id=" + id, 
                "Print",
                "left=200", 
                "top=200", 
                "width=650", 
                "height=500", 
                "toolbar=0", 
                "resizable=0"
                );
                printWindow.addEventListener("load", function() {
                    printWindow.print();
                    setTimeout(function() {
                    printWindow.close();
                }, 1);
                }, true);
        }
    ';

    $this->registerJs($script, View::POS_END);
?>

<style>
.isChecked {
  background-color: #F5F5F5;
}
.bold-style {
    font-weight: bold;
}
tr{
  background-color: white; font-weight: normal;
}
/* click-through element */
.check-project {
  pointer-events: none;
}
</style>
