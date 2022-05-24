<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use common\components\helpers\HtmlHelper;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\AgencySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Monitoring Plan';
$this->params['breadcrumbs'][] = $this->title;

$HtmlHelper = new HtmlHelper();
function renderSummary($page)
{
    $firstNumber = $page->offset + 1;
    $lastNumber = $page->pageCount - 1 == $page->page ? $page->totalCount : ($page->page + 1) * $page->limit;
    $total = $page->totalCount;
    return 'Showing <b>'.$firstNumber.'-'.$lastNumber.'</b> of <b>'.$total.'</b> items.';
}
?>
<div class="plan-index">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Monitoring Plan</h3>
                </div>
                <div class="box-body">
                <div class="alert alert-<?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? 'info' : 'danger' : '' ?>"><i class="fa fa-exclamation-circle"></i> <?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? $HtmlHelper->time_elapsed_string($dueDate->due_date).' to go before the deadline of submission of monitoring plan. Due date is '.date("F j, Y", strtotime($dueDate->due_date)) 
                : 'Submission of monitoring plan has ended '.$HtmlHelper->time_elapsed_string($dueDate->due_date).' ago. Due date is '.date("F j, Y", strtotime($dueDate->due_date)) : '' ?></div>
                <?= $this->render('_search', [
                    'model' => $model,
                    'regionModel' => $regionModel,
                    'provinceModel' => $provinceModel,
                    'citymunModel' => $citymunModel,
                    'years' => $years,
                    'agencies' => $agencies,
                    'programs' => $programs,
                    'sectors' => $sectors,
                    'subSectors' => $subSectors,
                    'modes' => $modes,
                    'fundSources' => $fundSources,
                    'scopes' => $scopes,
                    'regions' => $regions,
                    'provinces' => $provinces,
                    'citymuns' => $citymuns,
                    'categories' => $categories,
                    'kras' => $kras,
                    'goals' => $goals,
                    'chapters' => $chapters,
                ]) ?>
                <hr>
                <?php $form = ActiveForm::begin([
                    'id' => 'monitoring-project-form',
                    'options' => ['class' => 'disable-submit-buttons'],
                ]); ?>
                <div class="summary"><?= renderSummary($projectsPages) ?></div>
                <div class="monitoring-project-table" style="height: 800px;">
                    <table class="table table-condensed table-striped table-hover table-responsive table-bordered" cellspacing="0" style="min-width: 3 000px;">
                        <thead>
                            <tr>
                                <td rowspan=3 align=center style="vertical-align: bottom;"><input type=checkbox name="monitoring-projects" class="check-monitoring-projects" /></td>
                                <td rowspan=3>&nbsp;</td>
                                <td rowspan=3 colspan=2 style="width: 20%;">
                                    <b>
                                    (a) Project ID <br>
                                    (b) Name of Project <br>
                                    (c) Location <br>
                                    (d) Sector/Sub-Sector <br>
                                    (e) Funding Source <br>
                                    (f) Mode of Implementation <br>
                                    (g) Project Schedule
                                    </b>
                                </td>
                                <td rowspan=3 align=center style="width: 10%;"><b>Unit of Measure</b></td>
                                <td colspan=<?= count($quarters) + 1?> align=center><b>Financial Requirements</b></td>
                                <td colspan=<?= count($quarters) + 1?> align=center><b>Physical Targets</b></td>
                                <td colspan=<?= (count($quarters) * count($genders)) + 2?> align=center><b>Employment Generated</b></td>
                                <td colspan=<?= (count($quarters) + 1) * 2 ?> align=center><b>Target Beneficiaries</b></td>
                            </tr>
                            <tr>
                                <?php if($quarters){ ?>
                                    <?php foreach($quarters as $q => $quarter){ ?>
                                        <td align=center rowspan=2><b><?= $q ?></b></td>
                                    <?php } ?>
                                <?php } ?>
                                <td align=center rowspan=2><b>Total</b></td>
                                <?php if($quarters){ ?>
                                    <?php foreach($quarters as $q => $quarter){ ?>
                                        <td align=center rowspan=2><b><?= $q ?></b></td>
                                    <?php } ?>
                                <?php } ?>
                                <td align=center rowspan=2><b>Total</b></td>
                                <?php if($quarters){ ?>
                                    <?php foreach($quarters as $q => $quarter){ ?>
                                        <td align=center colspan=2><b><?= $q ?></b></td>
                                    <?php } ?>
                                <?php } ?>
                                <td align=center colspan=2><b>Total</b></td>
                                <?php if($quarters){ ?>
                                    <?php foreach($quarters as $q => $quarter){ ?>
                                        <td align=center colspan=2><b><?= $q ?></b></td>
                                    <?php } ?>
                                <?php } ?>
                                <td align=center colspan=2><b>Total</b></td>
                            </tr>
                            <tr>
                                <?php if($quarters){ ?>
                                    <?php foreach($quarters as $quarter){ ?>
                                        <?php if($genders){ ?>
                                            <?php foreach($genders as $g => $gender){ ?>
                                                <td align=center><b><?= $g ?></b></td>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                                <?php if($genders){ ?>
                                    <?php foreach($genders as $g => $gender){ ?>
                                        <td align=center><b><?= $g ?></b></td>
                                    <?php } ?>
                                <?php } ?>
                                <?php if($quarters){ ?>
                                    <?php foreach($quarters as $quarter){ ?>
                                        <td align=center><b>I</b></td>
                                        <td align=center><b>G</b></td>
                                    <?php } ?>
                                <?php } ?>
                                <td align=center><b>I</b></td>
                                <td align=center><b>G</b></td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if($projectsModels){ ?>
                            <?php $idx = $projectsPages->offset; ?>
                            <?php foreach($projectsModels as $idx => $model){ ?>
                                <?= $this->render('_project', [
                                    'idx' => $idx,
                                    'model' => $model,
                                    'form' => $form,
                                    'projectIds' => $projectIds,
                                    'dueDate' => $dueDate
                                ]) ?>
                                <?php $idx++ ?>
                            <?php } ?>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <br>
                <div class="pull-left"><?= LinkPager::widget(['pagination' => $projectsPages]); ?></div>
                <div class="pull-right"> 
                    <?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? Html::submitButton('Delete Selected', ['class' => 'btn btn-danger', 'id' => 'delete-selected-monitoring-project-button', 'data' => ['disabled-text' => 'Please Wait'], 'data' => [
                        'method' => 'get',
                    ], 'disabled' => true]) : '' : '' ?>
                </div>
                <div class="clearfix"></div>
                <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php if(Yii::$app->user->can('AgencyUser')){ ?>
    <br>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Monitoring Plan Submission</h3>
                </div>
                <div class="box-body">
                <?= $this->render('_submit', [
                        'submissionModel' => $submissionModel,
                        'agencies' => $agencies,
                        'projectCount' => $projectCount,
                        'dueDate' => $dueDate
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
<?php
    $script = '
    function enableMonitoringButtons()
    {
        if($("#monitoring-project-form input:checkbox:checked").length > 0)
        {
            $("#delete-selected-monitoring-project-button").attr("disabled", false);
        }else{
            $("#delete-selected-monitoring-project-button").attr("disabled", true);
        }
    }

    $(".check-monitoring-projects").click(function(){
        $(".check-monitoring-project").not(this).prop("checked", this.checked);
        enableMonitoringButtons();
    });
    
    $(".check-monitoring-project").click(function() {
        enableMonitoringButtons();
    });

    $("#delete-selected-monitoring-project-button").on("click", function(e) {
        var checkedVals = $(".check-monitoring-project:checkbox:checked").map(function() {
            return this.value;
        }).get();

        var ids = checkedVals.join(",");

        e.preventDefault();

        var con = confirm("Are you sure you want to remove this projects?");
        if(con == true)
        {
            var form = $("#monitoring-project-form");
            var formData = form.serialize();

            $.ajax({
                url: form.attr("action"),
                type: "GET",
                data: {id: ids},
                success: function (data) {
                    console.log(data);
                    form.enableSubmitButtons();
                    $.growl.notice({ title: "Success!", message: "The selected projects has been deleted" });
                },
                error: function (err) {
                    console.log(err);
                }
            }); 
        }

        return false;
    });

    $(document).ready(function(){
        $(".check-monitoring-project").removeAttr("checked");
        enableMonitoringButtons();
        $(".monitoring-project-table").freezeTable({
            "scrollable": true,
            "columnNum": 3
        });
    });
    ';

    $this->registerJs($script, View::POS_END);
?>