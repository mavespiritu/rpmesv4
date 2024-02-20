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
use yii\widgets\MaskedInput;
use kartik\select2\Select2;
use yii\widgets\LinkPager;
use dosamigos\switchery\Switchery;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Project */

$this->title = Yii::$app->user->can('Administrator') ? 'Provide NPMC Endorsement' : 'View NPMC Endorsement';
$this->params['breadcrumbs'][] = ['label' => 'RPMES Form 3: Project Exception Report', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Project Exception Report for '.$model->quarter.' '.$model->year, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$successMessage = \Yii::$app->getSession()->getFlash('success');

function renderSummary($page)
{
    $firstNumber = $page->offset + 1;
    $lastNumber = $page->pageCount - 1 == $page->page ? $page->totalCount : ($page->page + 1) * $page->limit;
    $total = $page->totalCount;
    return $total > 0 ? 'Showing <b>'.$firstNumber.'-'.$lastNumber.'</b> of <b>'.$total.'</b> items.' : 'Showing <b>0</b> of <b>'.$total.'</b> items.';
}
?>

<div class="plan-target-view">
    <div class="box box-solid">
        <div class="box-header with-border"><h3 class="box-title">Project Exception Report for <?= $model->quarter ?> <?= $model->year ?>: <?= $this->title ?></h3>
            <div class="box-tools pull-right">
                <?= Html::a('<i class="fa fa-backward"></i> Go back to Project Exception Report for '.$model->quarter.' '.$model->year, ['view', 'id' => $model->id], [
                    'class' => 'btn btn-box-tool',
                ]) ?>
            </div>  
        </div>
        <div class="box-body" style="height: calc(100vh - 210px);">
            <div class="summary"><?= renderSummary($exceptionsPages) ?></div>
            <div class="pull-right">
                <p><b>Accomplish findings by page (20 per page):</b>
                <?= LinkPager::widget(['pagination' => $exceptionsPages]); ?>
                </p>
                
            </div>
            <div class="clearfix"></div>
            <?php $form = ActiveForm::begin([
                'options' => ['id' => 'project-exception-review-form', 'class' => 'disable-submit-buttons'],
                
            ]); ?>
            <div class="project-exception-table-container" style="height: calc(100vh - 410px);">
                <table id="project-exception-table" class="table table-bordered table-responsive table-striped table-hover" cellspacing="0" style="min-width: 2000px;">
                    <thead>
                        <tr style="background-color: #002060; color: white; font-weight: normal;">
                            <td style="text-align: center;">#</td>
                            <td style="width: 10%; text-align: center;">Program/Project Title</td>
                            <td style="width: 10%; text-align: center;">For NPMC Action?</td>
                            <td style="width: 20%; text-align: center;">Requested action from NPMC</td>
                            <td style="width: 10%; text-align: center;">Findings</td>
                            <td style="width: 10%; text-align: center;">Typology</td>
                            <td style="width: 10%; text-align: center;">Issue Status</td>
                            <td style="width: 10%; text-align: center;">Reasons</td>
                            <td style="width: 10%; text-align: center;">Actions to be taken</td>
                            <td style="width: 10%; text-align: center;">Actions taken</td>
                            
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($exceptionsModels)){ ?>
                        <?php $i = $exceptionsPages->offset + 1; ?>
                        <?php foreach($exceptionsModels as $exception){ ?>
                            <tr>
                                <td><?= $i ?></td>
                                <td><?= $exception->project->project_no.': '.$exception->project->title ?></td>
                                <td align=center><?= $form->field($actions[$exception->id], "[$exception->id]for_npmc_action")->widget(Switchery::className(), [
                                    'options' => [
                                        'label' => false,
                                        'title' => 'for NPMC action?',
                                    ],
                                    'clientOptions' => [
                                        'color' => '#5fbeaa',
                                        'size' => 'small'
                                    ],
                                    'clientEvents' => [
                                        'change' => new JsExpression('function() {
                                            this.checked == true ? this.value = 1 : this.value = 0;
                                            updateProjectExceptionTable();
                                        }'),
                                    ]
                                ])->label(false) ?></td>
                                <td><?= $form->field($actions[$exception->id], "[$exception->id]requested_action")->textArea(['rows' => 3, 'style' => 'resize: none;', 'onKeyup' => 'updateProjectExceptionTable()'])->label(false) ?></td>
                                <td><?= strip_tags($exception->findings) ?></td>
                                <td align=center><?= $exception->typology ? $exception->typology->title : '' ?></td>
                                <td align=center><?= $exception->issue_status ?></td>
                                <td><?= strip_tags($exception->causes) ?></td>
                                <td><?= strip_tags($exception->action_taken) ?></td>
                                <td><?= strip_tags($exception->recommendations) ?></td>
                            </tr>
                            
                            <?php $i++ ?>
                        <?php } ?>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <br>
            <div class="form-group pull-right">
                <?= Yii::$app->user->can('Administrator') ?
                        $model->currentStatus == 'Submitted' ?
                            Html::submitButton('Save Review', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) :
                        '' :
                    '';
                ?>        
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>


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
    $(document).ready(function(){
        $(".project-exception-table-container").freezeTable({
            "scrollable": true,
            "scrollBar": true,
            "columnNum": 2,
        });
    });
');
?>

<?php
    $script = '
    function updateProjectExceptionTable()
    {
        $(".project-exception-table-container").freezeTable("update");
    }
    ';

    $this->registerJs($script, View::POS_END);
?>


<style>
    .pagination{
        margin-top: 0;
        padding-top: 0;
    }
</style>

