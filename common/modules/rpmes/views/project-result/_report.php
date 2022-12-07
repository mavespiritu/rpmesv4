<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;
use yii\bootstrap\ButtonDropdown;
use dosamigos\switchery\Switchery;
use yii\web\JsExpression;
use common\components\helpers\HtmlHelper;
use yii\widgets\LinkPager;
use dosamigos\ckeditor\CKEditor;
use kartik\date\DatePicker;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\DueDateSearch */
/* @var $form yii\widgets\ActiveForm */
$HtmlHelper = new HtmlHelper();
function renderSummary($page)
{
    $firstNumber = $page->offset + 1;
    $lastNumber = $page->pageCount - 1 == $page->page ? $page->totalCount : ($page->page + 1) * $page->limit;
    $total = $page->totalCount;
    return 'Showing <b>'.$firstNumber.'-'.$lastNumber.'</b> of <b>'.$total.'</b> items.';
}
?>
<div class="project-result-table" style="height: 600px;">
    </h5>
    <?php $form = ActiveForm::begin([
        'options' => ['id' => 'project-result-form', 'class' => 'disable-submit-buttons'],
    ]); ?>
    <div class="summary"><?= renderSummary($projectsPages) ?></div>
    <div class="project-result-form project-result-table">
        <table id="project-result-table" class="table table-condensed table-responsive table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <td align=center><b>#</td>
                    <td align=center><b>Project Title</td>
                    <td align=center><b>Objective</td>
                    <td align=center><b>Results Indicator</td>
                    <td align=center><b>Target</td>
                    <td align=center><b>Observed Results</td>
                    <td align=center><b>Is Project Completed?</td>
                </tr>   
            </thead>
            <tbody>
            <?php if(!empty($projectsModels)){ ?>
                <?php $idx = 1; ?>
                    <?php foreach($projectsModels as $model){ ?>
                        <tr>
                            <td align=center><?= $idx ?></td>
                            <td align=left> 
                                    (a) <?= $model->project_no ?> <br>
                                    (b) <?= $model->title ?> <br>
                                    (c) <?= $model->startDate ?> to <?= $model->completionDate ?> <br>
                                    (d) <?= $model->location ?> <br>
                                    (e) <?= $model->fundSourceTitle ?> <br>
                            </td>
                            <td align=center>
                                <?= $form->field($projectResults[$model->id], "[$model->id]objective")->textArea(['rows' => '3', 'style' => 'resize: none;',
                                            'disabled' => $model->isCompleted == true ? true : false])->label(false) ?>
                            </td>
                            <td align=center>
                                <?= $form->field($projectOutcome[$model->id], "[$model->id]performance_indicator")->textArea(['rows' => '3', 'style' => 'resize: none;',
                                            'disabled' => $model->isCompleted == true ? true : false])->label(false) ?>
                            </td>
                            <td align=center>
                                <?= $form->field($projectOutcome[$model->id], "[$model->id]target")->textArea(['rows' => '3', 'style' => 'resize: none;',
                                            'disabled' => $model->isCompleted == true ? true : false])->label(false) ?>
                            </td>
                            <td align=center>
                                <?= $form->field($projectResults[$model->id], "[$model->id]observed_results")->textArea(['rows' => '3', 'style' => 'resize: none;',
                                            'disabled' => $model->isCompleted == true ? true : false])->label(false) ?>
                            </td>
                            <td align=center>
                                    <?= $form->field($accomplishment[$model->id], "[$model->id]action")->widget(Switchery::className(), [
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
                                                enableInputFields(this.value, '.$model->id.');
                                            }'),
                                        ]
                                    ])->label(false) ?>
                                </td>
                        </tr>
                        <?php $idx ++ ?>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <div>
            <div class="pull-right"><?= LinkPager::widget(['pagination' => $projectsPages]); ?></div>
            <div class="pull-left">
                <?= !empty($projectResults) ? Html::submitButton('Save Project Results', ['class' => 'btn btn-primary', 'style' => 'margin-top: 20px;', 'data' => ['disabled-text' => 'Please Wait']]) : '' ?>
            </div>
            <div class="clearfix"></div>
        </div>

    <?php ActiveForm::end(); ?>
    <hr>
</div>
<?php
    $script = '
    function updateProjectResultTable(){
        $(".project-result-table").freezeTable("update");
    }
    function enableInputFields(toggle, id)
    {
        if(toggle == 1)
        {
            $("#projectresult-"+id+"-objective").prop("disabled", true);
            $("#projectoutcome-"+id+"-performance_indicator").prop("disabled", true);
            $("#projectoutcome-"+id+"-target").prop("disabled", true);
            $("#projectresult-"+id+"-observed_results").prop("disabled", true);
        }else{
            $("#projectresult-"+id+"-objective").prop("disabled", false);
            $("#projectoutcome-"+id+"-performance_indicator").prop("disabled", false);
            $("#projectoutcome-"+id+"-target").prop("disabled", false);
            $("#projectresult-"+id+"-observed_results").prop("disabled", false);
        }
    }
    $(document).ready(function(){
        $(".project-result-table").freezeTable({
            "scrollable": true,
            "columnNum": 2
        });
    });
    ';

    $this->registerJs($script, View::POS_END);
?>

