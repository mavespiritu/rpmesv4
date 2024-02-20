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

$this->title = 'Project Exception Report for '.$model->quarter.' '.$model->year;
$this->params['breadcrumbs'][] = ['label' => 'RPMES Form 3: Project Exception Report', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$successMessage = \Yii::$app->getSession()->getFlash('success');
?>

<?php
Modal::begin([
    'id' => 'update-modal',
    'size' => "modal-md",
    'header' => '<div id="update-modal-header"><h4>Update Project Exception Report</h4></div>',
    'options' => ['tabindex' => false],
]);
echo '<div id="update-modal-content"></div>';
Modal::end();
?>

<?php foreach ($dataProvider->models as $plan): ?>
    <?php
    $modelID = $plan->project->id;
    Modal::begin([
        'id' => 'create-findings-modal-'.$modelID,
        'size' => "modal-lg",
        'header' => '<div id="create-findings-modal-'.$modelID.'-header"><h4>Add Findings</h4></div>',
        'options' => ['tabindex' => false],
    ]);
    echo '<div id="create-findings-modal-'.$modelID.'-content"></div>';
    Modal::end();

    ?>
    <?php
        $exceptions = $plan->project->getProjectExceptionsPerQuarter($model->year, $model->quarter);

        if($exceptions)
        {
            foreach($exceptions as $exception){
                Modal::begin([
                    'id' => 'update-findings-modal-'.$exception->id,
                    'size' => "modal-lg",
                    'header' => '<div id="update-findings-modal-'.$exception->id.'-header"><h4>Update Findings</h4></div>',
                    'options' => ['tabindex' => false],
                ]);
                echo '<div id="update-findings-modal-'.$exception->id.'-content"></div>';
                Modal::end();
            }
        }
        ?>
<?php endforeach; ?>

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
                <?= Html::a('<i class="fa fa-backward"></i> Go back to Project Exception Reports', ['index'], [
                    'class' => 'btn btn-box-tool',
                ]) ?>
                <?= Yii::$app->user->can('AgencyUser') ? 
                        $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
                            count($model->plans) < 1 ? 
                                Html::a('<i class="fa fa-pencil"></i> Update Project Exception Report', '#', [
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
                                Html::a('<i class="fa fa-trash"></i> Delete Project Exception Report', ['delete', 'id' => $model->id], [
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
                        'style' => 'width: 10%; background-color: #002060; color: white; font-weight: normal;'
                    ]
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
                        return $plan->project->getSlippage($model->year)[$model->quarter] > -10 && $plan->project->getSlippage($model->year)[$model->quarter] < 10 ? number_format($plan->project->getSlippage($model->year)[$model->quarter], 2) : '<font style="color:red">'.number_format($plan->project->getSlippage($model->year)[$model->quarter], 2).'</font>';
                    }
                ],
                [
                    'header' => 'Findings',
                    'headerOptions' => [
                        'style' => 'width: 15%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        $ctr = range('a', 'z');
                        $exceptions = $plan->project->getProjectExceptionsPerQuarter($model->year, $model->quarter);
                        $str = '';

                        if($exceptions){
                            foreach($exceptions as $i => $exception){
                                $str .= '<p>'.strip_tags($ctr[$i].'.&nbsp;'.$exception->findings);
                                $str.= '<br>';
                                $str .= Yii::$app->user->can('AgencyUser') ? 
                                            $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
                                                count($model->plans) < 1 ? 
                                                    Html::a('Update', '#', [
                                                        'class' => 'btn btn-link',
                                                        'id' => 'update-findings-'.$exception->id.'-button',
                                                        'data-toggle' => 'modal',
                                                        'data-target' => '#update-findings-modal-'.$exception->id,
                                                        'data-url' => Url::to(['update-findings', 'id' => $model->id, 'project_id' => $plan->project->id, 'exception_id' => $exception->id, 'page' => isset(Yii::$app->request->queryParams['page']) ? Yii::$app->request->queryParams['page'] : 1]),
                                                    ]) : 
                                                '' : 
                                            '' : 
                                        '';
                                $str .= Yii::$app->user->can('AgencyUser') ? 
                                            $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
                                                count($model->plans) < 1 ? 
                                                '&nbsp;|&nbsp;'.Html::a('Delete', ['delete-findings', 'id' => $model->id, 'exception_id' => $exception->id, 'page' => isset(Yii::$app->request->queryParams['page']) ? Yii::$app->request->queryParams['page'] : 1], [
                                                        'class' => 'btn btn-link',
                                                        'data' => [
                                                            'confirm' => 'Are you sure want to delete this findings?',
                                                            'method' => 'post',
                                                        ],
                                                    ]) : 
                                                '' : 
                                            '' : 
                                        '';
                                $str .= '</p>';
                            }
                        }

                        return $str;
                    }
                ],
                [
                    'header' => 'Typology',
                    'headerOptions' => [
                        'style' => 'width: 10%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        $ctr = range('a', 'z');
                        $exceptions = $plan->project->getProjectExceptionsPerQuarter($model->year, $model->quarter);
                        $str = '';

                        if($exceptions){
                            foreach($exceptions as $i => $exception){
                                $str .= $exception->typology ? '<p class="pull-left">'.$ctr[$i].'.&nbsp;'.$exception->typology->title.'</p>' : '';
                            }
                        }

                        return $str;
                    }
                ],
                [
                    'header' => 'Issue Status',
                    'headerOptions' => [
                        'style' => 'width: 10%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        $ctr = range('a', 'z');
                        $exceptions = $plan->project->getProjectExceptionsPerQuarter($model->year, $model->quarter);
                        $str = '';

                        if($exceptions){
                            foreach($exceptions as $i => $exception){
                                $str .= '<p class="pull-left">'.$ctr[$i].'.&nbsp;'.$exception->issue_status.'</p>';
                            }
                        }

                        return $str;
                    }
                ],
                [
                    'header' => 'Reasons',
                    'headerOptions' => [
                        'style' => 'width: 10%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        $ctr = range('a', 'z');
                        $exceptions = $plan->project->getProjectExceptionsPerQuarter($model->year, $model->quarter);
                        $str = '';

                        if($exceptions){
                            foreach($exceptions as $i => $exception){
                                $str .= '<p class="pull-left">'.$ctr[$i].'.&nbsp;'.$exception->causes.'</p>';
                            }
                        }

                        return $str;
                    }
                ],
                [
                    'header' => 'Actions taken',
                    'headerOptions' => [
                        'style' => 'width: 15%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        $ctr = range('a', 'z');
                        $exceptions = $plan->project->getProjectExceptionsPerQuarter($model->year, $model->quarter);
                        $str = '';

                        if($exceptions){
                            foreach($exceptions as $i => $exception){
                                $str .= '<p class="pull-left">'.$ctr[$i].'.&nbsp;'.$exception->action_taken.'</p>';
                            }
                        }

                        return $str;
                    }
                ],
                [
                    'header' => 'Actions to be taken',
                    'headerOptions' => [
                        'style' => 'width: 15%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        $ctr = range('a', 'z');
                        $exceptions = $plan->project->getProjectExceptionsPerQuarter($model->year, $model->quarter);
                        $str = '';

                        if($exceptions){
                            foreach($exceptions as $i => $exception){
                                $str .= '<p class="pull-left">'.$ctr[$i].'.&nbsp;'.$exception->recommendations.'</p>';
                            }
                        }

                        return $str;
                    }
                ],
                [
                    'header' => '&nbsp;',
                    'headerOptions' => [
                        'style' => 'width: 5%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'contentOptions' => [
                        'style' => 'text-align: center'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        $modalID = $plan->project->id;
                        return  Yii::$app->user->can('AgencyUser') ? 
                                    $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
                                        count($model->plans) < 1 ? 
                                            Html::a('Add findings', '#', [
                                                'class' => 'btn btn-xs btn-block btn-success',
                                                'id' => 'create-findings-'.$modalID.'-button',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#create-findings-modal-'.$modalID,
                                                'data-url' => Url::to(['create-findings', 'id' => $model->id, 'project_id' => $plan->project->id, 'page' => isset(Yii::$app->request->queryParams['page']) ? Yii::$app->request->queryParams['page'] : 1]),
                                            ]) : 
                                        '' : 
                                    '' : 
                                '';
                    }
                ],
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
$this->registerJs('
    $(".create-findings-button").click(function(e){
        e.preventDefault();

        var modalId = $(this).data("target");
        $(modalId).modal("show").find(modalId + "-content").load($(this).data("url"));
        
        return false;
    });
');
?>

<?php foreach ($dataProvider->models as $plan): ?>
    <?php
    $this->registerJs('
        $("#create-findings-'.$plan->project->id.'-button").click(function(e){
            e.preventDefault();

            var modalId = $(this).data("target");
            $(modalId).modal("show").find(modalId + "-content").load($(this).data("url"));
            
            return false;
        });');
    ?>

    <?php
    $exceptions = $plan->project->getProjectExceptionsPerQuarter($model->year, $model->quarter);

    if($exceptions)
    {
        foreach($exceptions as $exception){
            $this->registerJs('
                $("#update-findings-'.$exception->id.'-button").click(function(e){
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
                "'.Url::to(['/rpmes/project-exception/download']).'?type=print&id=" + id, 
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
