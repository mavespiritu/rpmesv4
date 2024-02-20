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

$this->title = $model->draft == 'Yes' ? 'Adjust Targets' : 'View Targets';
$this->params['breadcrumbs'][] = ['label' => 'RPMES Form 1: Initial Project Report', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Monitoring Plan '.$model->year, 'url' => ['view', 'id' => $model->id]];
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
        <div class="box-header with-border"><h3 class="box-title">Monitoring Plan <?= $model->year ?>: <?= $this->title ?></h3>
            <div class="box-tools pull-right">
                <?= Html::a('<i class="fa fa-backward"></i> Go back to Monitoring Plan '.$model->year, ['view', 'id' => $model->id], [
                    'class' => 'btn btn-box-tool',
                ]) ?>
            </div>  
        </div>
        <div class="box-body" style="height: calc(100vh - 210px);">
            <div class="summary"><?= renderSummary($projectsPages) ?></div>
            <div class="pull-right">
                <p><b><?= $model->draft == 'Yes' ? 'Accomplish' : 'Browse' ?> projects by page (5 per page):</b>
                <?= LinkPager::widget(['pagination' => $projectsPages]); ?>
                </p>
                
            </div>
            <div class="clearfix"></div>
            <?php $form = ActiveForm::begin([
                'options' => ['id' => 'target-form', 'class' => 'disable-submit-buttons'],
                
            ]); ?>
            <div class="target-table-container" style="height: calc(100vh - 410px);">
                <table id="target-table" class="table table-bordered table-responsive table-striped table-hover" cellspacing="0" style="min-width: 3500px;">
                    <thead>
                        <tr style="background-color: #002060; color: white; font-weight: normal;">
                            <td rowspan=2>#</td>
                            <td rowspan=2 style="width: 8%;">Program/Project Title</td>
                            <td rowspan=2 style="width: 8%;">Default <br>Indicator</td>
                            <td rowspan=2 style="width: 4%;">Metrics</td>
                            <td rowspan=2 style="width: 5%;">Baseline <br>Accomplishment</td>
                            <td rowspan=2 style="width: 5%;">Baseline <br>Appropriations</td>
                            <td rowspan=2 style="width: 5%;">Baseline <br>Allotment</td>
                            <td colspan=12 style="text-align: center;">Monthly Target</td>
                            <td colspan=2 style="width: 10%; text-align: center;">Employment Generated</td>
                        </tr>
                        <tr style="background-color: #002060; color: white; font-weight: normal;">
                            <?php foreach($months as $month){ ?>
                                <td style="text-align: center;"><?= $month ?></td>
                            <?php } ?>
                            <td style="text-align: center;">Male</td>
                            <td style="text-align: center;">Female</td>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($projectsModels)){ ?>
                        <?php $i = $projectsPages->offset + 1; ?>
                        <?php foreach($projectsModels as $plan){ ?>
                            <?php $project = $plan->project; ?>
                            <tr>
                                <td><b><?= $i ?></b></td>
                                <td><b><?= $project->project_no.': '.$project->title ?></b></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <?php foreach($months as $month){ ?>
                                    <td>&nbsp;</td>
                                <?php } ?>
                                <td><?= $form->field($targets[$project->id]['maleEmployed'], "[$project->id][maleEmployed]annual")->widget(MaskedInput::classname(), [
                                    'options' => [
                                        'autocomplete' => 'off',
                                        'value' => $targets[$project->id]['maleEmployed']['annual'] != '' ? $targets[$project->id]['maleEmployed']['annual'] : 0,
                                        'onkeyup' => 'updateTargetTable()',
                                    ],
                                    'clientOptions' => [
                                        'alias' =>  'decimal',
                                        'removeMaskOnSubmit' => true,
                                        'groupSeparator' => ',',
                                        'autoGroup' => true
                                    ],
                                ])->label(false) ?></td>
                                <td><?= $form->field($targets[$project->id]['femaleEmployed'], "[$project->id][femaleEmployed]annual")->widget(MaskedInput::classname(), [
                                    'options' => [
                                        'autocomplete' => 'off',
                                        'value' => $targets[$project->id]['femaleEmployed']['annual'] != '' ? $targets[$project->id]['femaleEmployed']['annual'] : 0,
                                        'onkeyup' => 'updateTargetTable()',
                                    ],
                                    'clientOptions' => [
                                        'alias' =>  'decimal',
                                        'removeMaskOnSubmit' => true,
                                        'groupSeparator' => ',',
                                        'autoGroup' => true
                                    ],
                                ])->label(false) ?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td align=right>Financial Targets</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><?= $form->field($targets[$project->id]['financial'], "[$project->id][financial]allocation")->widget(MaskedInput::classname(), [
                                    'options' => [
                                        'autocomplete' => 'off',
                                        'value' => $targets[$project->id]['financial']['allocation'] != '' ? $targets[$project->id]['financial']['allocation'] : 0,
                                        'onkeyup' => 'updateTargetTable()',
                                    ],
                                    'clientOptions' => [
                                        'alias' =>  'decimal',
                                        'removeMaskOnSubmit' => true,
                                        'groupSeparator' => ',',
                                        'autoGroup' => true
                                    ],
                                ])->label(false) ?></td>
                                <td><?= $form->field($targets[$project->id]['financial'], "[$project->id][financial]releases")->widget(MaskedInput::classname(), [
                                    'options' => [
                                        'autocomplete' => 'off',
                                        'value' => $targets[$project->id]['financial']['releases'] != '' ? $targets[$project->id]['financial']['releases'] : 0,
                                        'onkeyup' => 'updateTargetTable()',
                                    ],
                                    'clientOptions' => [
                                        'alias' =>  'decimal',
                                        'removeMaskOnSubmit' => true,
                                        'groupSeparator' => ',',
                                        'autoGroup' => true
                                    ],
                                ])->label(false) ?></td>
                                <?php foreach($months as $mo => $month){ ?>
                                    <td><?= $form->field($targets[$project->id]['financial'], "[$project->id][financial]{$mo}")->widget(MaskedInput::classname(), [
                                    'options' => [
                                        'autocomplete' => 'off',
                                        'value' => $targets[$project->id]['financial'][$mo] != '' ? $targets[$project->id]['financial'][$mo] : 0,
                                        'onkeyup' => 'updateTargetTable()',
                                    ],
                                    'clientOptions' => [
                                        'alias' =>  'decimal',
                                        'removeMaskOnSubmit' => true,
                                        'groupSeparator' => ',',
                                        'autoGroup' => true
                                    ],
                                ])->label(false) ?></td>
                                <?php } ?>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td align=right>Physical Targets</td>
                                <td><?= $form->field($targets[$project->id]['physical'], "[$project->id][physical]indicator")->textInput(['onKeyup' => 'updateTargetTable()'])->label(false) ?></td>
                                <td>
                                <?= $form->field($targets[$project->id]['physical'], "[$project->id][physical]type")
                                    ->dropdownList($metrics, [
                                            'onchange' => '$("#projecttarget-'.$project->id.'-physical-type-hidden").val($(this).val()); updateTargetTable();',
                                        ]
                                    )
                                    ->label(false) ?>
                                    <?= Html::hiddenInput('ProjectTarget['.$project->id.'][physical][updatedType]', null, ['id' => 'projecttarget-'.$project->id.'-physical-type-hidden']) ?>
                                </td>
                                <td><?= $form->field($targets[$project->id]['physical'], "[$project->id][physical]baseline")->widget(MaskedInput::classname(), [
                                    'options' => [
                                        'autocomplete' => 'off',
                                        'value' => $targets[$project->id]['physical']['baseline'] != '' ? $targets[$project->id]['physical']['baseline'] : 0,
                                        'onkeyup' => 'updateTargetTable()',
                                    ],
                                    'clientOptions' => [
                                        'alias' =>  'decimal',
                                        'removeMaskOnSubmit' => true,
                                        'groupSeparator' => ',',
                                        'autoGroup' => true
                                    ],
                                ])->label(false) ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <?php foreach($months as $mo => $month){ ?>
                                    <td><?= $form->field($targets[$project->id]['physical'], "[$project->id][physical]{$mo}")->widget(MaskedInput::classname(), [
                                    'options' => [
                                        'autocomplete' => 'off',
                                        'value' => $targets[$project->id]['physical'][$mo] != '' ? $targets[$project->id]['physical'][$mo] : 0,
                                        'onkeyup' => 'updateTargetTable()',
                                    ],
                                    'clientOptions' => [
                                        'alias' =>  'decimal',
                                        'removeMaskOnSubmit' => true,
                                        'groupSeparator' => ',',
                                        'autoGroup' => true
                                    ],
                                ])->label(false) ?></td>
                                <?php } ?>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <?php if(!empty($oiTargets[$project->id])){ ?>
                                <?php $i = 1; ?>
                                <?php foreach($oiTargets[$project->id] as $idx => $oi){ ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td align=right>Output Indicator <?= $i++ ?>: <?= $oi->indicator ?></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;
                                        <?= Html::hiddenInput('ProjectExpectedOutput['.$project->id.']['.$oi->indicator.'][target]', $oi->target) ?></td>
                                        <td><?= $form->field($oiTargets[$project->id][$oi->indicator], "[$project->id][$oi->indicator]baseline")->widget(MaskedInput::classname(), [
                                            'options' => [
                                                'autocomplete' => 'off',
                                                'value' => $oiTargets[$project->id][$oi->indicator]['baseline'] != '' ? $oiTargets[$project->id][$oi->indicator]['baseline'] : 0,
                                                'onkeyup' => 'updateTargetTable()',
                                            ],
                                            'clientOptions' => [
                                                'alias' =>  'decimal',
                                                'removeMaskOnSubmit' => true,
                                                'groupSeparator' => ',',
                                                'autoGroup' => true
                                            ],
                                        ])->label(false) ?></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <?php foreach($months as $mo => $month){ ?>
                                            <td><?= $form->field($oiTargets[$project->id][$oi->indicator], "[$project->id][$oi->indicator]{$mo}")->widget(MaskedInput::classname(), [
                                                'options' => [
                                                    'autocomplete' => 'off',
                                                    'value' => $oiTargets[$project->id][$oi->indicator][$mo] != '' ? $oiTargets[$project->id][$oi->indicator][$mo] : 0,
                                                    'onkeyup' => 'updateTargetTable()',
                                                ],
                                                'clientOptions' => [
                                                    'alias' =>  'decimal',
                                                    'removeMaskOnSubmit' => true,
                                                    'groupSeparator' => ',',
                                                    'autoGroup' => true
                                                ],
                                            ])->label(false) ?></td>
                                        <?php } ?>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
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
                <?= Yii::$app->user->can('AgencyUser') ?
                        $model->currentStatus != 'Draft' || $model->currentStatus != 'For further validation' ? 
                            $projectsPages->totalCount > 0 ?
                                $dueDate ? 
                                    strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ?
                                        Html::submitButton('Save Targets', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) :
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
        $(".target-table-container").freezeTable({
            "scrollable": true,
            "scrollBar": true,
            "columnNum": 2,
        });
    });
');
?>

<?php
    $script = '
    function updateTargetTable()
    {
        $(".target-table-container").freezeTable("update");
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

