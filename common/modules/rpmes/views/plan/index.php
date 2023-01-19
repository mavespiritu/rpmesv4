<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use common\components\helpers\HtmlHelper;
use yii\bootstrap\ButtonDropdown;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\AgencySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Form 1: Initial Project Report';
$this->params['breadcrumbs'][] = $this->title;

$HtmlHelper = new HtmlHelper();
function renderSummary($page)
{
    $firstNumber = $page->offset + 1;
    $lastNumber = $page->pageCount - 1 == $page->page ? $page->totalCount : ($page->page + 1) * $page->limit;
    $total = $page->totalCount;
    return 'Showing <b>'.$firstNumber.'-'.$lastNumber.'</b> of <b>'.$total.'</b> items.';
}

$year = $model->year != '' ? $model->year : '';
$agency_id = $model->agency_id != '' ? $model->agency_id : '';
$category_id = $categoryModel->category_id != '' ? $categoryModel->category_id : '';
$fund_source_id = $model->fund_source_id != '' ? $model->fund_source_id : '';
$sector_id = $model->sector_id != '' ? $model->sector_id : '';
$sub_sector_id = $model->sub_sector_id != '' ? $model->sub_sector_id : '';
$region_id = !empty($regionModel->region_id) ? json_encode($regionModel->region_id) : '';
$province_id = !empty($provinceModel->province_id) ? json_encode($provinceModel->province_id) : '';
$period = $model->period != '' ? $model->period : '';
$data_type = $model->data_type != '' ? $model->data_type : '';
$project_no = $model->project_no != '' ? $model->project_no : '';
$title = $model->title != '' ? $model->title : '';
?>
<div class="plan-index">
    <div class="alert alert-<?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? 'info' : 'danger' : '' ?>"><i class="fa fa-exclamation-circle"></i> <?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? $HtmlHelper->time_elapsed_string($dueDate->due_date).' to go before the deadline of submission of monitoring plan. Due date is '.date("F j, Y", strtotime($dueDate->due_date)) 
    : 'Submission of monitoring plan has ended '.$HtmlHelper->time_elapsed_string($dueDate->due_date).' ago. Due date is '.date("F j, Y", strtotime($dueDate->due_date)) : '' ?></div>
    <?php if(Yii::$app->user->can('AgencyUser')){ ?>
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
    <br>
    <?php } ?>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Monitoring Plan</h3>
                </div>
                <div class="box-body">
                <?= $this->render('_search', [
                    'model' => $model,
                    'regionModel' => $regionModel,
                    'provinceModel' => $provinceModel,
                    'categoryModel' => $categoryModel,
                    'years' => $years,
                    'agencies' => $agencies,
                    'programs' => $programs,
                    'sectors' => $sectors,
                    'subSectors' => $subSectors,
                    'modes' => $modes,
                    'fundSources' => $fundSources,
                    'categories' => $categories,
                    'scopes' => $scopes,
                    'regions' => $regions,
                    'provinces' => $provinces,
                    'categories' => $categories,
                    'kras' => $kras,
                    'goals' => $goals,
                    'chapters' => $chapters,
                    'periods' => $periods,
                    'dataTypes' => $dataTypes,
                ]) ?>
                <hr>
                <?php $form = ActiveForm::begin([
                    'id' => 'monitoring-project-form',
                    'options' => ['class' => 'disable-submit-buttons'],
                ]); ?>
                <div class="pull-right">
                    <?= ButtonDropdown::widget([
                        'label' => '<i class="fa fa-download"></i> Export',
                        'encodeLabel' => false,
                        'options' => ['class' => 'btn btn-success btn-sm'],
                        'dropdown' => [
                            'items' => [
                                [
                                    'label' => 'Excel', 
                                    'encodeLabel' => false, 
                                    'url' => Url::to(['/rpmes/plan/download-monitoring-plan', 
                                        'type' => 'excel', 
                                        'year' => $year,
                                        'agency_id' => $agency_id,
                                        'category_id' => $category_id,
                                        'fund_source_id' => $fund_source_id,
                                        'sector_id' => $sector_id,
                                        'sub_sector_id' => $sub_sector_id,
                                        'region_id' => $region_id,
                                        'province_id' => $province_id,
                                        'period' => $period,
                                        'data_type' => $data_type,
                                        'project_no' => $project_no,
                                        'title' => $title,
                                    ])
                                ],
                                [   'label' => 'PDF', 
                                    'encodeLabel' => false, 
                                    'url' => Url::to(['/rpmes/plan/download-monitoring-plan',
                                        'type' => 'pdf', 
                                        'year' => $year,
                                        'agency_id' => $agency_id,
                                        'category_id' => $category_id,
                                        'fund_source_id' => $fund_source_id,
                                        'sector_id' => $sector_id,
                                        'sub_sector_id' => $sub_sector_id,
                                        'region_id' => $region_id,
                                        'province_id' => $province_id,
                                        'period' => $period,
                                        'data_type' => $data_type,
                                        'project_no' => $project_no,
                                        'title' => $title,
                                    ])
                                ],
                            ],
                        ],
                    ]) ?>
                    <?= Html::button('<i class="fa fa-print"></i> Print', [
                        'onClick' => 'printFormOneReport(
                            "'.$year.'",
                            "'.$agency_id.'",
                            "'.$category_id.'",
                            "'.$fund_source_id.'",
                            "'.$sector_id.'",
                            "'.$sub_sector_id.'",
                            "'.str_replace('"', '\'', json_encode($regionModel->region_id)).'",
                            "'.str_replace('"', '\'', json_encode($provinceModel->province_id)).'",
                            "'.$period.'",
                            "'.$data_type.'",
                            "'.$project_no.'",
                            "'.$title.'"
                        )', 'class' => 'btn btn-danger btn-sm']) ?>
                </div>
                <div class="clearfix"></div>
                <br>
                <div class="summary"><?= renderSummary($projectsPages) ?></div>
                <div class="monitoring-project-table" style="height: 800px;">
                    <table class="table table-condensed table-striped table-hover table-responsive table-bordered" cellspacing="0" style="min-width: 3000px;">
                        <thead>
                            <tr>
                                <td rowspan=3 align=center style="vertical-align: bottom;"><input type=checkbox name="monitoring-projects" class="check-monitoring-projects" /></td>
                                <td rowspan=3 style="width: 5%;">&nbsp;</td>
                                <td rowspan=3 colspan=2 style="width: 10%;">
                                    <b>
                                    (a) Project ID <br>
                                    (b) Name of Project <br>
                                    (c) Location <br>
                                    (d) Sector/Sub-Sector <br>
                                    (e) Funding Source <br>
                                    (f) Mode of Implementation <br>
                                    (g) Project Schedule <br>
                                    (h) Project Profile
                                    </b>
                                </td>
                                <td rowspan=3 align=center style="width: 5%;"><b>Unit of Measure</b></td>
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
                            <?php $financialTotal = 0; ?>
                            <?php $physicalTotal = 0; ?>
                            <?php $maleEmployedTotal = 0; ?>
                            <?php $femaleEmployedTotal = 0; ?>
                            <?php $beneficiaryTotal = 0; ?>
                            <?php $groupTotal = 0; ?>
                            <!-- <tr>
                                <td colspan=5 align=right><b>Grand Total</b></td>
                                <?php foreach($quarters as $quarter => $q){ ?>
                                    <td align=right><b><?= isset($totals['financials'][$quarter]) ? number_format($totals['financials'][$quarter], 2) : number_format(0, 2) ?></b></td>
                                    <?php $financialTotal += isset($totals['financials'][$quarter]) ? $totals['financials'][$quarter] : 0; ?>
                                <?php } ?>
                                <td align=right><b><?= number_format($financialTotal, 2) ?></b></td>
                                <?php foreach($quarters as $quarter => $q){ ?>
                                    <td align=right><b><?= isset($totals['physicals'][$quarter]) ? number_format($totals['physicals'][$quarter], 0) : 0 ?></b></td>
                                    <?php $physicalTotal += isset($totals['physicals'][$quarter]) ? $totals['physicals'][$quarter] : 0; ?>
                                <?php } ?>
                                <td align=right><b><?= number_format($physicalTotal, 0) ?></b></td>
                                <?php foreach($quarters as $quarter => $q){ ?>
                                    <td align=right><b><?= isset($totals['maleEmployed'][$quarter]) ? number_format($totals['maleEmployed'][$quarter], 0) : 0 ?></b></td>
                                    <td align=right><b><?= isset($totals['femaleEmployed'][$quarter]) ? number_format($totals['femaleEmployed'][$quarter], 0) : 0 ?></b></td>
                                    <?php $maleEmployedTotal += isset($totals['maleEmployed'][$quarter]) ? $totals['maleEmployed'][$quarter] : 0; ?>
                                    <?php $femaleEmployedTotal += isset($totals['femaleEmployed'][$quarter]) ? $totals['femaleEmployed'][$quarter] : 0; ?>
                                <?php } ?>
                                <td align=right><b><?= number_format($maleEmployedTotal, 0) ?></b></td>
                                <td align=right><b><?= number_format($femaleEmployedTotal, 0) ?></b></td>
                                <?php foreach($quarters as $quarter => $q){ ?>
                                    <td align=right><b><?= isset($totals['beneficiaries'][$quarter]) ? number_format($totals['beneficiaries'][$quarter], 0) : 0 ?></b></td>
                                    <td align=right><b><?= isset($totals['groupBeneficiaries'][$quarter]) ? number_format($totals['groupBeneficiaries'][$quarter], 0) : 0 ?></b></td>
                                    <?php $beneficiaryTotal += isset($totals['beneficiaries'][$quarter]) ? $totals['beneficiaries'][$quarter] : 0; ?>
                                    <?php $groupTotal += isset($totals['groupBeneficiaries'][$quarter]) ? $totals['groupBeneficiaries'][$quarter] : 0; ?>
                                <?php } ?>
                                <td align=right><b><?= number_format($beneficiaryTotal, 0) ?></b></td>
                                <td align=right><b><?= number_format($groupTotal, 0) ?></b></td>
                            </tr> -->
                        </thead>
                        <tbody>
                        <?php if($projectsModels){ ?>
                            <?php $idx = $projectsPages->offset; ?>
                            <?php foreach($projectsModels as $model){ ?>
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
</div>
<?php
    $script = '
    function printFormOneReport(year, agency_id, category_id, fund_source_id, sector_id, sub_sector_id, region_id, province_id, period, data_type, project_no, title)
    {
        var printWindow = window.open(
            "'.Url::to(['/rpmes/plan/download-monitoring-plan']).'?type=print&year=" + year +  "&agency_id=" + agency_id + "&category_id=" + category_id + "&fund_source_id=" + fund_source_id + "&sector_id=" + sector_id + "&sub_sector_id=" + sub_sector_id + "&region_id=" + region_id + "&province_id=" + province_id + "&period=" + period + "&data_type=" + data_type+ "&project_no=" + project_no+ "&title=" + title, 
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
