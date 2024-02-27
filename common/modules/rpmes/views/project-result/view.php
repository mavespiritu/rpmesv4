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

$this->title = 'Project Results Report for '.$model->year;
$this->params['breadcrumbs'][] = ['label' => 'RPMES Form 4: Project Results Report', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$successMessage = \Yii::$app->getSession()->getFlash('success');
?>

<?php
Modal::begin([
    'id' => 'update-modal',
    'size' => "modal-md",
    'header' => '<div id="update-modal-header"><h4>Update Project Results Report</h4></div>',
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
                <?= Html::a('<i class="fa fa-backward"></i> Go back to Project Results Reports', ['index'], [
                    'class' => 'btn btn-box-tool',
                ]) ?>
                <?= Yii::$app->user->can('AgencyUser') ? 
                        $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
                            count($model->plans) < 1 ? 
                                Html::a('<i class="fa fa-pencil"></i> Update Project Results Report', '#', [
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
                                Html::a('<i class="fa fa-trash"></i> Delete Project Results Report', ['delete', 'id' => $model->id], [
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
        <p style="color: <?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? 'black' : 'red' : 'black' ?>"><i class="fa  fa-info-circle"></i> <?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? 'Submission is open until '.date("F j, Y", strtotime($dueDate->due_date)).'.' : 'Submission is closed. The deadline of submission is '.date("F j, Y", strtotime($dueDate->due_date)).'.' : '' ?></p>
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
                    'attribute' => 'project.description',
                    'headerOptions' => [
                        'style' => 'width: 20%; background-color: #002060; color: white; font-weight: normal;'
                    ],
                ],
                [
                    'header' => 'Results/Outcome Indicator/Target',
                    'headerOptions' => [
                        'style' => 'width: 25%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        $ctr = range('a', 'z');
                        $indicators = $plan->project->getProjectOutcomes()->where(['year' => $model->year])->orderBy(['id' => SORT_ASC])->all();
                        $str = '';

                        if($indicators){
                            foreach($indicators as $i => $indicator){
                                $str .= '<p>'.strip_tags($ctr[$i].'.&nbsp;'.$indicator->outcome).'</p>';
                            }
                        }

                        return $str;
                    }
                ],
                [
                    'header' => 'Observed Results/Outcome/Impact',
                    'headerOptions' => [
                        'style' => 'width: 25%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                    ],
                    'format' => 'raw',
                    'value' => function($plan) use ($model){
                        $ctr = range('a', 'z');
                        $indicators = $plan->project->getProjectOutcomes()->where(['year' => $model->year])->orderBy(['id' => SORT_ASC])->all();
                        $str = '';

                        if($indicators){
                            foreach($indicators as $i => $indicator){
                                $str .= $indicator->getAccomplishment($model->year) ? '<p>'.strip_tags($ctr[$i].'.&nbsp;'.$indicator->getAccomplishment($model->year)->value).'</p>' : '<p>'.$ctr[$i].'.</p>';
                            }
                        }

                        return $str;
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
                "'.Url::to(['/rpmes/project-result/download']).'?type=print&id=" + id, 
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
