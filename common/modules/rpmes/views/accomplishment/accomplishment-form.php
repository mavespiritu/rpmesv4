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

$this->title = $model->draft == 'Yes' ? 'Accomplish Form 2' : 'View Form 2';
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
                <table id="accomplishment-table" class="table table-bordered table-responsive table-striped table-hover" cellspacing="0" style="min-width: 3000px;">
                    <thead>
                        <tr style="background-color: #002060; color: white; font-weight: normal;">
                            <td style="width: 2%; text-align: center;" rowspan=3>#</td>
                            <td style="width: 8%; text-align: center;" rowspan=3>Program/Project Title</td>
                            <td style="text-align: center;" colspan=5>Financial Status <br> (in PHP exact figures)</td>
                            <td style="text-align: center;" colspan=3>Physical Status</td>
                            <td style="text-align: center;" colspan=4>Employment Generated</td>
                            <td style="width: 5%; text-align: center;" rowspan=3>Project is completed?</td>
                            <td style="width: 10%; text-align: center;" rowspan=3>Remarks</td>
                        </tr>
                        <tr style="background-color: #002060; color: white; font-weight: normal;">
                            <td style="width: 10%; text-align: center;" colspan=2>Appropriations</td>
                            <td style="width: 5%; text-align: center;" rowspan=2>Allotment</td>
                            <td style="width: 5%; text-align: center;" rowspan=2>Obligations</td>
                            <td style="width: 5%; text-align: center;" rowspan=2>Disbursements</td>
                            <td style="width: 5%; text-align: center;" rowspan=2>Indicator</td>
                            <td style="width: 5%; text-align: center;" rowspan=2>Target</td>
                            <td style="width: 5%; text-align: center;" rowspan=2>Actual</td>
                            <td style="width: 10%; text-align: center;" colspan=2>Male</td>
                            <td style="width: 10%; text-align: center;" colspan=2>Female</td>
                        </tr>
                        <tr style="background-color: #002060; color: white; font-weight: normal;">
                            <td style="width: 5%; text-align: center;">Target</td>
                            <td style="width: 5%; text-align: center;">Actual</td>
                            <td style="width: 5%; text-align: center;">Target</td>
                            <td style="width: 5%; text-align: center;">Actual</td>
                            <td style="width: 5%; text-align: center;">Target</td>
                            <td style="width: 5%; text-align: center;">Actual</td>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($projectsModels)){ ?>
                        <?php $i = $projectsPages->offset + 1; ?>
                        <?php foreach($projectsModels as $plan){ ?>
                            <?php $project = $plan->project; ?>
                            <?php $allComplete += $project->getIsCompleted($model->year)[$model->quarter] == true ? 1 : 0 ?>
                            <tr>
                                <td><?= $i ?></td>
                                <td><?= $project->project_no.': '.$project->title ?><br>
                                </td>
                                <td align=right><?= number_format($plan->project->getFinancialTargetPerQuarter($model->year)[$model->quarter], 2) ?></td>
                                <?= $project->getIsCompleted($model->year)[$model->quarter] == false ? '<td>'.$form->field($accomplishments[$project->id]['financial'], "[$project->id][financial]allocation")->widget(MaskedInput::classname(), [
                                    'options' => [
                                        'autocomplete' => 'off',
                                        'value' => $accomplishments[$project->id]['financial']['allocation'] != '' ? $accomplishments[$project->id]['financial']['allocation'] : 0,
                                        'onkeyup' => 'updateAccomplishmentTable()',
                                    ],
                                    'clientOptions' => [
                                        'alias' =>  'decimal',
                                        'removeMaskOnSubmit' => true,
                                        'groupSeparator' => ',',
                                        'autoGroup' => true
                                    ],
                                ])->label(false).'</td>' : '<td align=right>'.number_format(floatval($accomplishments[$project->id]['financial']['allocation']), 2).'</td>' ?>
                                <?= $project->getIsCompleted($model->year)[$model->quarter] == false ? '<td>'.$form->field($accomplishments[$project->id]['financial'], "[$project->id][financial]releases")->widget(MaskedInput::classname(), [
                                    'options' => [
                                        'autocomplete' => 'off',
                                        'value' => $accomplishments[$project->id]['financial']['releases'] != '' ? $accomplishments[$project->id]['financial']['releases'] : 0,
                                        'onkeyup' => 'updateAccomplishmentTable()',
                                    ],
                                    'clientOptions' => [
                                        'alias' =>  'decimal',
                                        'removeMaskOnSubmit' => true,
                                        'groupSeparator' => ',',
                                        'autoGroup' => true
                                    ],
                                ])->label(false).'</td>' : '<td align=right>'.number_format(floatval($accomplishments[$project->id]['financial']['releases']), 2).'</td>' ?>
                                <?= $project->getIsCompleted($model->year)[$model->quarter] == false ? '<td>'.$form->field($accomplishments[$project->id]['financial'], "[$project->id][financial]obligation")->widget(MaskedInput::classname(), [
                                    'options' => [
                                        'autocomplete' => 'off',
                                        'value' => $accomplishments[$project->id]['financial']['obligation'] != '' ? $accomplishments[$project->id]['financial']['obligation'] : 0,
                                        'onkeyup' => 'updateAccomplishmentTable()',
                                    ],
                                    'clientOptions' => [
                                        'alias' =>  'decimal',
                                        'removeMaskOnSubmit' => true,
                                        'groupSeparator' => ',',
                                        'autoGroup' => true
                                    ],
                                ])->label(false).'</td>' : '<td align=right>'.number_format(floatval($accomplishments[$project->id]['financial']['obligation']), 2).'</td>' ?>
                                <?= $project->getIsCompleted($model->year)[$model->quarter] == false ? '<td>'.$form->field($accomplishments[$project->id]['financial'], "[$project->id][financial]expenditures")->widget(MaskedInput::classname(), [
                                    'options' => [
                                        'autocomplete' => 'off',
                                        'value' => $accomplishments[$project->id]['financial']['expenditures'] != '' ? $accomplishments[$project->id]['financial']['expenditures'] : 0,
                                        'onkeyup' => 'updateAccomplishmentTable()',
                                    ],
                                    'clientOptions' => [
                                        'alias' =>  'decimal',
                                        'removeMaskOnSubmit' => true,
                                        'groupSeparator' => ',',
                                        'autoGroup' => true
                                    ],
                                ])->label(false).'</td>' : '<td align=right>'.number_format(floatval($accomplishments[$project->id]['financial']['expenditures']), 2).'</td>' ?>
                                <td><?= $project->getPhysicalTarget($model->year) ? $project->getPhysicalTarget($model->year)->type == 'Percentage' ? $project->getPhysicalTarget($model->year)->indicator.' (in %)' : $project->getPhysicalTarget($model->year)->indicator : '' ?></td>
                                <td align=center><?= $project->getPhysicalTarget($model->year) ? $project->getPhysicalTarget($model->year)->type == 'Percentage' ? number_format($project->getPhysicalTargetPerQuarter($model->year)[$model->quarter], 2) : number_format($project->getPhysicalTargetPerQuarter($model->year)[$model->quarter], 0) : 0 ?></td>
                                <?= $project->getIsCompleted($model->year)[$model->quarter] == true ? 
                                        $project->getPhysicalTarget($model->year) ? 
                                            $project->getPhysicalTarget($model->year)->type == 'Percentage' ?
                                            '<td align=center>'. number_format($accomplishments[$project->id]['physical']['value'], 2).'</td>' :
                                            '<td align=center>'. number_format($accomplishments[$project->id]['physical']['value'], 0).'</td>' :
                                        '<td align=center>'. number_format($accomplishments[$project->id]['physical']['value'], 0).'</td>' :
                                    '<td>'.$form->field($accomplishments[$project->id]['physical'], "[$project->id][physical]value")->widget(MaskedInput::classname(), [
                                        'options' => [
                                            'autocomplete' => 'off',
                                            'value' => $accomplishments[$project->id]['physical']['value'] != '' ? $accomplishments[$project->id]['physical']['value'] : 0,
                                            'onkeyup' => 'updateAccomplishmentTable()',
                                        ],
                                        'clientOptions' => [
                                            'alias' =>  'decimal',
                                            'removeMaskOnSubmit' => true,
                                            'groupSeparator' => ',',
                                            'autoGroup' => true
                                        ],
                                    ])->label(false).'</td>' ?>
                                <td align=center><?= number_format(floatval($project->getNewMalesEmployedTarget($model->year)), 0) ?></td>
                                <?= $project->getIsCompleted($model->year)[$model->quarter] == false ? '<td>'.$form->field($accomplishments[$project->id]['personEmployed'], "[$project->id][personEmployed]male")->widget(MaskedInput::classname(), [
                                    'options' => [
                                        'autocomplete' => 'off',
                                        'value' => $accomplishments[$project->id]['personEmployed']['male'] != '' ? $accomplishments[$project->id]['personEmployed']['male'] : 0,
                                        'onkeyup' => 'updateAccomplishmentTable()',
                                    ],
                                    'clientOptions' => [
                                        'alias' =>  'decimal',
                                        'removeMaskOnSubmit' => true,
                                        'groupSeparator' => ',',
                                        'autoGroup' => true
                                    ],
                                ])->label(false).'</td>' : '<td align=center>'.number_format(floatval($accomplishments[$project->id]['personEmployed']['male']), 0).'</td>' ?>
                                <td align=center><?= number_format($project->getNewFemalesEmployedTarget($model->year), 0) ?></td>
                                <?= $project->getIsCompleted($model->year)[$model->quarter] == false ? '<td>'.$form->field($accomplishments[$project->id]['personEmployed'], "[$project->id][personEmployed]female")->widget(MaskedInput::classname(), [
                                    'options' => [
                                        'autocomplete' => 'off',
                                        'value' => $accomplishments[$project->id]['personEmployed']['female'] != '' ? $accomplishments[$project->id]['personEmployed']['female'] : 0,
                                        'onkeyup' => 'updateAccomplishmentTable()',
                                    ],
                                    'clientOptions' => [
                                        'alias' =>  'decimal',
                                        'removeMaskOnSubmit' => true,
                                        'groupSeparator' => ',',
                                        'autoGroup' => true
                                    ],
                                ])->label(false).'</td>' : '<td align=center>'.number_format(floatval($accomplishments[$project->id]['personEmployed']['female'])   , 0).'</td>' ?>
                                <?= $project->getIsCompleted($model->year)[$model->quarter] == false ? '<td align=center>'.$form->field($accomplishments[$project->id]['accomplishment'], "[$project->id][accomplishment]action")->widget(Switchery::className(), [
                                        'options' => [
                                            'label' => false,
                                            'title' => 'Toggle if project is completed',
                                        ],
                                        'clientOptions' => [
                                            'color' => '#5fbeaa',
                                            'size' => 'small'
                                        ],
                                    'clientEvents' => [
                                            'change' => new JsExpression('function() {
                                                this.checked == true ? this.value = 1 : this.value = 0;
                                                updateAccomplishmentTable();
                                            }'),
                                        ]
                                    ])->label(false).'</td>' : '<td align=center>Yes</td>' ?>
                               <?= $project->getIsCompleted($model->year)[$model->quarter] == false ? '<td>'.$form->field($accomplishments[$project->id]['accomplishment'], "[$project->id][accomplishment]remarks")->textInput(['onKeyup' => 'updateAccomplishmentTable()'])->label(false).'</td>' : '<td>'.$accomplishments[$project->id]['accomplishment']['remarks'].'</td>' ?>
                            </tr>
                            <?php $i++ ?>
                        <?php } ?>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <br>
            <div class="form-group pull-right">
                <?= Yii::$app->user->can('AgencyUser') ?
                        $model->currentStatus == 'Draft' || $model->currentStatus == 'For further validation' ? 
                            $projectsPages->totalCount > 0 ?
                                $dueDate ? 
                                    strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ?
                                        $allComplete != $lastNumber ? 
                                            Html::submitButton('Save Form 2', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) :
                                        '' :
                                    '' :
                                '' :
                            '' :
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

