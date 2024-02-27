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

$this->title = 'Include Projects';
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

<div class="plan-include-view">
    <div class="box box-solid">
        <div class="box-header with-border"><h3 class="box-title">Monitoring Plan <?= $model->year ?>: <?= $this->title ?></h3>
            <div class="box-tools pull-right">
                <?= Html::a('<i class="fa fa-backward"></i> Go back to Monitoring Plan '.$model->year, ['view', 'id' => $model->id], [
                    'class' => 'btn btn-box-tool',
                ]) ?>
            </div>  
        </div>
        <div class="box-body" style="min-height: calc(100vh - 210px);">
            <p style="color: <?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? 'black' : 'red' : 'black' ?>"><small><i class="fa  fa-info-circle"></i> <?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? 'Submission is open until '.date("F j, Y", strtotime($dueDate->due_date)).'.' : 'Submission is closed. The deadline of submission is '.date("F j, Y", strtotime($dueDate->due_date)).'.' : '' ?></small></p>
            <div class="summary"><?= renderSummary($projectsPages) ?></div>
            <div class="pull-right">
                <p><b><?= 'Include' ?> projects by page (20 per page):</b>
                <?= LinkPager::widget(['pagination' => $projectsPages]); ?>
                </p>
            </div>
            <div class="clearfix"></div>
            <?php $form = ActiveForm::begin([
                'id' => 'include-project-form',
                'options' => ['class' => 'disable-submit-buttons'],
            ]); ?>

                <table id="included-projects-table" class="table table-responsive table-bordered table-hover">
                    <thead>
                        <tr style="background-color: #002060; color: white; font-weight: normal;">
                            <th>#</th>
                            <th style="width: 10%;">Project No.</th>
                            <th style="width: 28%;">Program/Project Title</th>
                            <th style="width: 18%;">Sector</th>
                            <th style="width: 18%;">Mode of Implementation</th>
                            <th style="width: 18%;">Project Profile</th>
                            <td align=center><?= $model->currentStatus != 'Draft' || $model->currentStatus != 'For further validation' ? '<input type="checkbox" class="check-all-included-projects" />' : '' ?></td>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($projectsModels)){ ?>
                        <?php $i = $projectsPages->offset + 1; ?>
                        <?php foreach($projectsModels as $project){ ?>
                            <tr>
                                <?= $this->render('_project', [
                                    'i' => $i,
                                    'model' => $model,
                                    'projects' => $projects,
                                    'project' => $project,
                                    'form' => $form
                                ]); ?>
                            </tr>

                            <?php $i++ ?>
                        <?php } ?>
                    <?php } ?>
                    </tbody>
                </table>

                <div class="form-group pull-right"> 
                    <?= Yii::$app->user->can('AgencyUser') ? 
                            $model->currentStatus != 'Draft' || $model->currentStatus != 'For further validation' ? 
                                count($projects) > 0 ?
                                    $dueDate ? 
                                        strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? 
                                            Html::submitButton('Include Selected', ['class' => 'btn btn-success', 'id' => 'include-project-button', 'data' => ['disabled-text' => 'Please Wait', 'method' => 'post', 'confirm' => 'Are you sure you want to include selected projects to this monitoring plan?'], 'disabled' => true]) : 
                                        '' : 
                                    '' : 
                                '' : 
                            '' : 
                        '' ?>
                </div>
                <div class="clearfix"></div>

                <?php ActiveForm::end(); ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

<?php
// check all checkboxes
$this->registerJs(
    new JsExpression('
        $(".check-all-included-projects").change(function() {
            $(".check-included-project").prop("checked", $(this).prop("checked"));
            $("#included-projects-table tr").toggleClass("isChecked", $(".check-included-project").is(":checked"));
            toggleBoldStyle();
            enableRemoveButton();
        });

        $("tr").click(function() {
            var inp = $(this).find(".check-included-project");
            var tr = $(this).closest("tr");
            inp.prop("checked", !inp.is(":checked"));
         
            tr.toggleClass("isChecked", inp.is(":checked"));
            toggleBoldStyle();
            enableRemoveButton();
        });

        function toggleBoldStyle() {
            $("#included-projects-table tr").removeClass("bold-style"); // Remove bold style from all rows
            $("#included-projects-table .isChecked").addClass("bold-style"); // Add bold style to selected rows
            enableRemoveButton();
        }

        function enableRemoveButton()
        {
            $("#include-project-form input:checkbox:checked").length > 0 ? $("#include-project-button").attr("disabled", false) : $("#include-project-button").attr("disabled", true);
        }

        $(document).ready(function(){
            $(".check-included-project").removeAttr("checked");
            enableRemoveButton();
        });
    ')
);

?>

<style>
.isChecked {
  background-color: #F5F5F5;
}
.bold-style {
    font-weight: bold;
}
tr{
  background-color: white;
}
/* click-through element */
.check-project {
  pointer-events: none;
}
</style>