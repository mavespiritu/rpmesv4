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

$this->title = Yii::$app->user->can('Administrator') ? $model->agency->code.': Monitoring Plan '.$model->year : 'Monitoring Plan '.$model->year;
$this->params['breadcrumbs'][] = ['label' => 'RPMES Form 1: Initial Project Report', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$successMessage = \Yii::$app->getSession()->getFlash('success');
?>

<?php
Modal::begin([
    'id' => 'update-modal',
    'size' => "modal-md",
    'header' => '<div id="update-modal-header"><h4>Update Monitoring Plan</h4></div>',
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

<?php foreach ($dataProvider->models as $plan): ?>
    <?php
    $modelID = $plan->id;
    Modal::begin([
        'id' => 'create-oi-modal-'.$modelID,
        'size' => "modal-md",
        'header' => '<div id="create-oi-modal-'.$modelID.'-header"><h4>Add Output Indicator</h4></div>',
        'options' => ['tabindex' => false],
    ]);
    echo '<div id="create-oi-modal-'.$modelID.'-content"></div>';
    Modal::end();
    ?>
    <?php
        $ois = $plan->project->getProjectExpectedOutputs()->where(['year' => $model->year])->all();

        if($ois)
        {
            foreach($ois as $oi){
                Modal::begin([
                    'id' => 'update-oi-modal-'.$oi->id,
                    'size' => "modal-md",
                    'header' => '<div id="update-oi-modal-'.$oi->id.'-header"><h4>Update Output Indicator</h4></div>',
                    'options' => ['tabindex' => false],
                ]);
                echo '<div id="update-oi-modal-'.$oi->id.'-content"></div>';
                Modal::end();
            }
        }
        ?>
<?php endforeach ?>

<div class="project-view">
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Status: 
                <?php if($model->currentStatus == 'Draft'){ ?>
                    <?= $model->currentStatus ?>
                <?php }else { ?>
                    <?= $model->currentStatus ?> <small>by <?= $model->currentSubmissionLog->actor ?> last <?= date("F j, Y H:i:s", strtotime($model->currentSubmissionLog->datetime)) ?></small>
                <?php } ?>
            </h3>
            <div class="box-tools pull-right">
                <?= Html::a('<i class="fa fa-backward"></i> Go back to Monitoring Plans', ['index'], [
                    'class' => 'btn btn-box-tool',
                ]) ?>
                <?= !Yii::$app->user->can('Administrator') ? 
                        $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
                            count($model->plans) < 1 ? 
                                Html::a('<i class="fa fa-pencil"></i> Update Plan', '#', [
                                    'class' => 'update-button btn btn-box-tool',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#update-modal',
                                    'data-url' => Url::to(['update', 'id' => $model->id]),
                                ]) : 
                            '' : 
                        '' : 
                    Html::a('<i class="fa fa-pencil"></i> Update Plan', '#', [
                        'class' => 'update-button btn btn-box-tool',
                        'data-toggle' => 'modal',
                        'data-target' => '#update-modal',
                        'data-url' => Url::to(['update', 'id' => $model->id]),
                    ]) ?>
                <?= !Yii::$app->user->can('Administrator') ? 
                        $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
                            count($model->plans) < 1 ?  
                                Html::a('<i class="fa fa-trash"></i> Delete Plan', ['delete', 'id' => $model->id], [
                                    'class' => 'btn btn-box-tool',
                                    'data' => [
                                        'confirm' => 'Are you sure want to delete this item?',
                                        'method' => 'post',
                                    ],
                                ]) : 
                            '' : 
                        '' : 
                    Html::a('<i class="fa fa-trash"></i> Delete Plan', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-box-tool',
                        'data' => [
                            'confirm' => 'Are you sure want to delete this item?',
                            'method' => 'post',
                        ],
                    ]) ?>
            </div>  
        </div>
        <div class="box-body" style="min-height: calc(100vh - 235px);">
        <p style="color: <?= $model->currentStatus != 'For further validation' ? $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? 'black' : 'red' : 'red' : 'red' ?>"><i class="fa  fa-info-circle"></i> <?= $model->currentStatus != 'For further validation' ? $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? 'Submission is open until '.date("F j, Y", strtotime($dueDate->due_date)).'.' : 'Submission is closed. The deadline of submission is '.date("F j, Y", strtotime($dueDate->due_date)).'.' : 'No set due date. Contact the administrator for due date setup.' : 'Your submission has been reverted for further validation. Please see remarks for your guidance: ' ?><div style="color: red !important;"><?= $model->currentSubmissionLog ? $model->currentSubmissionLog->remarks : '' ?> <?=  $model->currentStatus == 'For further validation' ? $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? 'Re-submit not later than '.date("F j, Y", strtotime($dueDate->due_date)).'.' : '' : '' : '' ?> </div></p>
        <?= $this->render('_search-project', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dueDate' => $dueDate
        ]); ?>
            
        <?php $form = ActiveForm::begin([
            'id' => 'monitoring-plan-form',
            'options' => ['class' => 'disable-submit-buttons'],
        ]); ?>

        <?= GridView::widget([
            'options' => [
                'class' => 'table-responsive'
            ],
            'tableOptions' => [
                'class' => 'table table-bordered table-hover',
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
                        'style' => 'width: 10%; background-color: #002060; color: white; font-weight: normal;'
                    ]
                ],
                [
                    'header' => '
                                (a) Program/Project Title <br>
                                (b) Implementing Agency <br>
                                (c) Sector <br>
                                (d) Province <br>
                                (e) City/Municipality <br>
                                (f) Barangay
                                ',
                    'headerOptions' => [
                        'style' => 'width: 20%; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        return 
                            '(a) '.$plan->project->title.'<br>'.
                            '(b) '.$plan->project->agency->code.'<br>'.
                            '(c) '.$plan->project->sector->title.'<br>'.
                            '(d) '.$plan->project->provinceTitle.'<br>'.
                            '(e) '.$plan->project->citymunTitle.'<br>'.
                            '(f) '.$plan->project->barangayTitle
                        ;
                    }
                ],
                [
                    'header' => 'Financial Target<br> (in PhP)',
                    'headerOptions' => [
                        'style' => 'text-align: center; width: 10%; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'contentOptions' => [
                        'style' => 'text-align: right'
                    ],
                    'value' => function($plan) use ($model){
                        return number_format($plan->project->getNewAllocationTotal($model->year), 2);
                    }
                ],
                [
                    'header' => 'Physical Indicator',
                    'headerOptions' => [
                        'style' => 'text-align: center; width: 15%; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'contentOptions' => [
                        'style' => 'text-align: center'
                    ],
                    'value' => function($plan) use ($model){
                        return $plan->project->getPhysicalTarget($model->year) ? $plan->project->getPhysicalTarget($model->year)->indicator : '';
                    }
                ],
                [
                    'header' => 'Physical Target (%)',
                    'headerOptions' => [
                        'style' => 'text-align: center; width: 5%; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'contentOptions' => [
                        'style' => 'text-align: center'
                    ],
                    'value' => function($plan) use ($model){
                        return number_format($plan->project->getNewPhysicalTotal($model->year), 2);
                    }
                ],
                [
                    'header' => 'EG-Male',
                    'headerOptions' => [
                        'style' => 'text-align: center; width: 5%; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'contentOptions' => [
                        'style' => 'text-align: center'
                    ],
                    'value' => function($plan) use ($model){
                        return number_format($plan->project->getNewMalesEmployedTarget($model->year), 0);
                    }
                ],
                [
                    'header' => 'EG-Female',
                    'headerOptions' => [
                        'style' => 'text-align: center; width: 5%; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'contentOptions' => [
                        'style' => 'text-align: center'
                    ],
                    'value' => function($plan) use ($model){
                        return number_format($plan->project->getNewFemalesEmployedTarget($model->year), 0);
                    }
                ],
                [
                    'header' => 'Output <br>Indicators (OI)',
                    'headerOptions' => [
                        'style' => 'text-align: center; width: 25%; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        $modalID = $plan->id;
                        $str = '<div class="pull-left">';
                        $str .= Html::button('View All', ['value' => Url::to(['output-indicator', 'id' => $model->id, 'plan_id' => $plan->id, 'year' => $model->year]), 'class' => 'btn btn-link oi-button']);
                        $str .= '</div>';
                        $str .= '<div class="pull-right">';
                        $str .= !Yii::$app->user->can('Administrator') ? 
                                    $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
                                        Html::a('Add Output Indicator', '#', [
                                            'class' => 'btn btn-link pull-right',
                                            'id' => 'create-oi-'.$modalID.'-button',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#create-oi-modal-'.$modalID,
                                            'data-url' => Url::to(['create-output-indicator', 'plan_id' => $plan->id, 'submission_id' => $model->id, 'page' => isset(Yii::$app->request->queryParams['page']) ? Yii::$app->request->queryParams['page'] : 1]),
                                        ]) :
                                    '' :
                                Html::a('Add Output Indicator', '#', [
                                    'class' => 'btn btn-link pull-right',
                                    'id' => 'create-oi-'.$modalID.'-button',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#create-oi-modal-'.$modalID,
                                    'data-url' => Url::to(['create-output-indicator', 'plan_id' => $plan->id, 'submission_id' => $model->id, 'page' => isset(Yii::$app->request->queryParams['page']) ? Yii::$app->request->queryParams['page'] : 1]),
                                ]);
                        $str .= '</div>';
                        $str .= '<div class="clearfix"></div>';
                        $str .= '<table style="width: 100%;">';
                        $ois = $plan->project->getProjectExpectedOutputs()->where(['year' => $model->year])->all();

                        if($ois){
                            foreach($ois as $i => $oi){
                                $str .= '<tr>';
                                $str .= '<td style="width: 80%; padding: 5px;">'.($i+1).'. '.$oi->indicator.'</td>'; 
                                $str.= !Yii::$app->user->can('Administrator') ? 
                                            $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
                                                $oi->indicator != 'number of individual beneficiaries served' || $oi->indicator != 'number of group beneficiaries served' ?
                                                    '<td style="vertical-align:top; width: 10%; padding-right: 5px;" align=center>'.Html::a('Edit', '#', [
                                                        'class' => 'btn btn-link update-oi-button',
                                                        'id' => 'update-oi-'.$oi->id.'-button',
                                                        'data-toggle' => 'modal',
                                                        'data-target' => '#update-oi-modal-'.$oi->id,
                                                        'data-url' => Url::to(['update-output-indicator', 'id' => $oi->id, 'plan_id' => $plan->id, 'submission_id' => $model->id, 'page' => isset(Yii::$app->request->queryParams['page']) ? Yii::$app->request->queryParams['page'] : 1]),
                                                    ]).'</td>' :
                                                '' :
                                            '' :
                                        '<td style="vertical-align:top; width: 10%; padding-right: 5px;" align=center>'.Html::a('Edit', '#', [
                                            'class' => 'btn btn-link update-oi-button',
                                            'id' => 'update-oi-'.$oi->id.'-button',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#update-oi-modal-'.$oi->id,
                                            'data-url' => Url::to(['update-output-indicator', 'id' => $oi->id, 'plan_id' => $plan->id, 'submission_id' => $model->id, 'page' => isset(Yii::$app->request->queryParams['page']) ? Yii::$app->request->queryParams['page'] : 1]),
                                        ]).'</td>';
                                $str.= !Yii::$app->user->can('Administrator') ? 
                                            $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
                                                $oi->indicator != 'number of individual beneficiaries served' || $oi->indicator != 'number of group beneficiaries served' ?
                                                    '<td style="vertical-align:top; width: 10%;" align=center>'.Html::a('Remove', ['/rpmes/plan/delete-output-indicator', 'id' => $oi->id, 'plan_id' => $plan->id, 'submission_id' => $model->id, 'page' => isset(Yii::$app->request->queryParams['page']) ? Yii::$app->request->queryParams['page'] : 1],[
                                                        'class' => 'btn btn-link',
                                                        'data' => [
                                                            'confirm' => 'Are you sure want to remove this output indicator?',
                                                            'method' => 'post',
                                                        ],
                                                    ]).'</td>' :
                                                '' :
                                            '' :
                                        '<td style="vertical-align:top; width: 10%;" align=center>'.Html::a('Remove', ['/rpmes/plan/delete-output-indicator', 'id' => $oi->id, 'plan_id' => $plan->id, 'submission_id' => $model->id, 'page' => isset(Yii::$app->request->queryParams['page']) ? Yii::$app->request->queryParams['page'] : 1],[
                                            'class' => 'btn btn-link',
                                            'data' => [
                                                'confirm' => 'Are you sure want to remove this output indicator?',
                                                'method' => 'post',
                                            ],
                                        ]).'</td>';
                                $str .= '</tr>';
                            }  
                        }

                        $str .= '</table>';
                        
                        return $str;
                    }
                ],
                [
                    'format' => 'raw',
                    'header' => $model->draft == 'Yes' ? '<input type="checkbox" class="check-all-projects" />' : '',
                    'headerOptions' => [
                        'style' => 'background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'value' => function ($plan) use ($form, $projects, $model, $dataProvider, $dueDate) {
                        return !Yii::$app->user->can('Administrator') ? 
                                    $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
                                        $dataProvider->getCount() > 0 ? 
                                            $form->field($projects[$plan->id], "[$plan->id]id")->checkbox([
                                                'value' => $plan->id, 
                                                'class' => 'check-project', 
                                                'id' => 'check-project-'.$plan->id, 
                                                'label' => ''
                                            ]) : 
                                        '' :
                                    '' :
                                $form->field($projects[$plan->id], "[$plan->id]id")->checkbox([
                                    'value' => $plan->id, 
                                    'class' => 'check-project', 
                                    'id' => 'check-project-'.$plan->id, 
                                    'label' => ''
                                ]);
                    },
                ],
            ],
        ]); ?>

        <div class="form-group pull-right"> 
            <?= Yii::$app->user->can('Administrator') ? 
                    $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
                        $dataProvider->getCount() > 0 ? 
                                Html::submitButton('Remove Selected', [
                                    'class' => 'btn btn-danger', 
                                    'id' => 'remove-project-button', 
                                    'data' => [
                                        'disabled-text' => 'Please Wait', 
                                        'method' => 'post', 
                                        'confirm' => 'Are you sure you want to remove selected projects to this monitoring plan?'
                                    ], 
                                    'disabled' => true
                                ]) :
                        '' : 
                    '' : 
                Html::submitButton('Remove Selected', [
                    'class' => 'btn btn-danger', 
                    'id' => 'remove-project-button', 
                    'data' => [
                        'disabled-text' => 'Please Wait', 
                        'method' => 'post', 
                        'confirm' => 'Are you sure you want to remove selected projects to this monitoring plan?'
                    ], 
                    'disabled' => true
                ]) ?>
        </div>
        <div class="clearfix"></div>

        <?php ActiveForm::end(); ?>
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
// check all checkboxes
$this->registerJs(
    new JsExpression('
        $(".check-all-projects").change(function() {
            $(".check-project").prop("checked", $(this).prop("checked"));
            $("#projects-table tr").toggleClass("isChecked", $(".check-project").is(":checked"));
            toggleBoldStyle();
            enableRemoveButton();
        });

        $("tr").click(function() {
            var inp = $(this).find(".check-project");
            var tr = $(this).closest("tr");
            inp.prop("checked", !inp.is(":checked"));
         
            tr.toggleClass("isChecked", inp.is(":checked"));
            toggleBoldStyle();
            enableRemoveButton();
        });

        function toggleBoldStyle() {
            $("#projects-table tr").removeClass("bold-style"); // Remove bold style from all rows
            $("#projects-table .isChecked").addClass("bold-style"); // Add bold style to selected rows
            enableRemoveButton();
        }

        function enableRemoveButton()
        {
            $("#monitoring-plan-form input:checkbox:checked").length > 0 ? $("#remove-project-button").attr("disabled", false) : $("#remove-project-button").attr("disabled", true);
        }

        $(document).ready(function(){
            $(".check-project").removeAttr("checked");
            enableRemoveButton();
        });
    ')
);

?>

<?php foreach ($dataProvider->models as $plan): ?>
    <?php
    $this->registerJs('
        $("#create-oi-'.$plan->id.'-button").click(function(e){
            e.preventDefault();

            var modalId = $(this).data("target");
            $(modalId).modal("show").find(modalId + "-content").load($(this).data("url"));
            
            return false;
        });');
    ?>
    <?php
    $ois = $plan->project->getProjectExpectedOutputs()->where(['year' => $model->year])->all();

    if($ois)
    {
        foreach($ois as $oi){
            $this->registerJs('
                $("#update-oi-'.$oi->id.'-button").click(function(e){
                    e.preventDefault();

                    var modalId = $(this).data("target");
                    $(modalId).modal("show").find(modalId + "-content").load($(this).data("url"));
                    
                    return false;
                });
            ');
        }
    }
    ?>
<?php endforeach; ?>

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
                "'.Url::to(['/rpmes/plan/download']).'?type=print&id=" + id, 
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
