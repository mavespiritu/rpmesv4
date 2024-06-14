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
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Project */

$this->title = $model->draft == 'Yes' ? 'Accomplish Form 2 OI/s' : 'View Form 2 OI/s';
$this->params['breadcrumbs'][] = ['label' => 'RPMES Form 2: Physical and Financial Accomplishment Report', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Accomplishment Report for '.$model->quarter.' '.$model->year, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$successMessage = \Yii::$app->getSession()->getFlash('success');
$allComplete = 0;

$lastNumber = $projectsPages->pageCount - 1 == $projectsPages->page ? $projectsPages->totalCount : ($projectsPages->page + 1) * $projectsPages->limit;

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
        <div class="box-header with-border"><h3 class="box-title">Accomplishment Report for <?= $model->quarter ?> <?= $model->year ?>: <?= $this->title ?></h3>
            <div class="box-tools pull-right">
                <?= Html::a('<i class="fa fa-backward"></i> Go back to Accomplishment Report for '.$model->quarter.' '.$model->year, ['view', 'id' => $model->id], [
                    'class' => 'btn btn-box-tool',
                ]) ?>
            </div>  
        </div>
        <div class="box-body" style="min-height: calc(100vh - 210px);">
            <p style="color: <?= $model->currentStatus != 'For further validation' ? $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? 'black' : 'red' : 'red' : 'red' ?>"><i class="fa  fa-info-circle"></i> <?= $model->currentStatus != 'For further validation' ? $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? 'Submission is open until '.date("F j, Y", strtotime($dueDate->due_date)).'.' : 'Submission is closed. The deadline of submission is '.date("F j, Y", strtotime($dueDate->due_date)).'.' : 'No set due date. Contact the administrator for due date setup.' : 'Your submission has been reverted for further validation. Please see remarks for your guidance: ' ?><div style="color: red !important;"><?= $model->currentSubmissionLog ? $model->currentSubmissionLog->remarks : '' ?> <?=  $model->currentStatus == 'For further validation' ? $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? 'Re-submit not later than '.date("F j, Y", strtotime($dueDate->due_date)).'.' : '' : '' : '' ?> </div></p>
            <div class="summary"><?= renderSummary($projectsPages) ?></div>
            <div class="pull-right">
                <p><b><?= $model->draft == 'Yes' ? 'Accomplish' : 'Browse' ?> projects by page (5 per page):</b>
                <?= LinkPager::widget(['pagination' => $projectsPages]); ?>
                </p>
                
            </div>
            <div class="clearfix"></div>
            <?php $form = ActiveForm::begin([
                'options' => ['id' => 'accomplishment-form', 'class' => 'disable-submit-buttons'],
                
            ]); ?>
            <div class="accomplishment-table-container" style="height: calc(100vh - 380px);">
                <table id="accomplishment-table" class="table table-bordered table-responsive table-striped table-hover" cellspacing="0" style="min-width: 1000px;">
                    <thead>
                        <tr style="background-color: #002060; color: white; font-weight: normal;">
                            <td style="width: 5%; text-align: center;">#</td>
                            <td style="width: 10%; text-align: center;">Program/Project Title</td>
                            <td style="width: 10%; text-align: center;">Output Indicator</td>
                            <td style="width: 10%; text-align: center;">End-of-Project Target</td>
                            <td style="width: 10%; text-align: center;">Target-to-Date</td>
                            <td style="width: 15%; text-align: center;">Actual-to-Date</td>
                            <td style="width: 10%; text-align: center;">Actual<br>Male</td>
                            <td style="width: 10%; text-align: center;">Actual<br>Female</td>
                            
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($projectsModels)){ ?>
                        <?php $i = $projectsPages->offset + 1; ?>
                        <?php foreach($projectsModels as $plan){ ?>
                            <?php $project = $plan->project; ?>
                            <?php $allComplete += $project->getIsCompleted($model->year)[$model->quarter] == true ? 1 : 0 ?>
                            <tr>
                                <td><b><?= $i ?></b></td>
                                <td colspan=7><b><?= $project->project_no.': '.$project->title ?></b></td>
                            </tr>
                            
                            <?php if($project->getProjectExpectedOutputs()->where([
                                    'year' => $model->year
                                ])
                                ->orderBy(['id' => SORT_ASC])
                                ->all()){ ?>
                                <?php foreach($project->getProjectExpectedOutputs()->where([
                                        'year' => $model->year
                                    ])
                                    ->orderBy(['id' => SORT_ASC])
                                    ->all() as $eo){ ?>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td><?= $eo->indicator ?></td>
                                            <td align=center><?= number_format($eo->getEndOfProjectTarget($model->year), 0) ?></td>
                                            <td align=center><?= number_format($eo->getPhysicalTargetPerQuarter($model->year)[$model->quarter], 0) ?></td>
                                            <?php if($project->getIsCompleted($model->year)[$model->quarter] == true){ ?>
                                                    <?= $eo->indicator != 'number of individual beneficiaries served' ? '<td align=center>'.number_format(floatval($outputIndicators[$project->id][$eo->id]['value']), 0).'</td>' : '<td>&nbsp;</td>' ?>
                                            <?php }else{ ?>
                                                    <?= $eo->indicator != 'number of individual beneficiaries served' ? '<td>'.$form->field($outputIndicators[$project->id][$eo->id], "[$project->id][$eo->id]value")->widget(MaskedInput::classname(), [
                                                        'options' => [
                                                            'autocomplete' => 'off',
                                                            'value' => $outputIndicators[$project->id][$eo->id]['value'] != '' ? $outputIndicators[$project->id][$eo->id]['value'] : 0,
                                                            'onkeyup' => 'updateAccomplishmentTable()',
                                                        ],
                                                        'clientOptions' => [
                                                            'alias' =>  'decimal',
                                                            'removeMaskOnSubmit' => true,
                                                            'groupSeparator' => ',',
                                                            'autoGroup' => true
                                                        ],
                                                    ])->label(false).'</td>' : '<td>&nbsp;</td>' ?>
                                            <?php } ?>
                               
                                            <?php if($project->getIsCompleted($model->year)[$model->quarter] == true){ ?>
                                                    <?= $eo->indicator == 'number of individual beneficiaries served' ? '<td align=center>'.number_format(floatval($outputIndicators[$project->id][$eo->id]['male']), 0).'</td>' : '<td>&nbsp;</td>' ?>
                                            <?php }else{ ?>
                                                    <?= $eo->indicator == 'number of individual beneficiaries served' ? '<td>'.$form->field($outputIndicators[$project->id][$eo->id], "[$project->id][$eo->id]male")->widget(MaskedInput::classname(), [
                                                'options' => [
                                                    'autocomplete' => 'off',
                                                    'value' => $outputIndicators[$project->id][$eo->id]['male'] != '' ? $outputIndicators[$project->id][$eo->id]['male'] : 0,
                                                    'onkeyup' => 'updateAccomplishmentTable()',
                                                ],
                                                'clientOptions' => [
                                                    'alias' =>  'decimal',
                                                    'removeMaskOnSubmit' => true,
                                                    'groupSeparator' => ',',
                                                    'autoGroup' => true
                                                ],
                                            ])->label(false).'</td>' : '<td>&nbsp;</td>' ?>
                                            <?php } ?>
                                            
                                            <?php if($project->getIsCompleted($model->year)[$model->quarter] == true){ ?>
                                                    <?= $eo->indicator == 'number of individual beneficiaries served' ? '<td align=center>'.number_format(floatval($outputIndicators[$project->id][$eo->id]['female']), 0).'</td>' : '<td>&nbsp;</td>' ?>
                                            <?php }else{ ?>
                                                    <?= $eo->indicator == 'number of individual beneficiaries served' ? '<td>'.$form->field($outputIndicators[$project->id][$eo->id], "[$project->id][$eo->id]female")->widget(MaskedInput::classname(), [
                                                'options' => [
                                                    'autocomplete' => 'off',
                                                    'value' => $outputIndicators[$project->id][$eo->id]['female'] != '' ? $outputIndicators[$project->id][$eo->id]['female'] : 0,
                                                    'onkeyup' => 'updateAccomplishmentTable()',
                                                ],
                                                'clientOptions' => [
                                                    'alias' =>  'decimal',
                                                    'removeMaskOnSubmit' => true,
                                                    'groupSeparator' => ',',
                                                    'autoGroup' => true
                                                ],
                                            ])->label(false).'</td>' : '<td>&nbsp;</td>' ?>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>
                            <?php } ?>
                            
                            <?php $i++ ?>
                        <?php } ?>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <br>
            <div class="form-group pull-right">
                <?= !Yii::$app->user->can('Administrator') ?
                        $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
                            $projectsPages->totalCount > 0 ?
                                $dueDate ? 
                                    strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ?
                                        $allComplete != $lastNumber ? 
                                            Html::submitButton('Save Form 2 OI/s', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) :
                                        '' :
                                    '' :
                                '' :
                            '' :
                        '' :
                    Html::submitButton('Save Form 2 OI/s', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]);
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
        $(".accomplishment-table-container").freezeTable({
            "scrollable": true,
            "scrollBar": true,
            "columnNum": 2,
        });
    });
');
?>

<?php
    $script = '
    function updateAccomplishmentTable()
    {
        $(".accomplishment-table-container").freezeTable("update");
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

