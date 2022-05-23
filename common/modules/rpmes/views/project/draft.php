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
/* @var $searchModel common\modules\rpmes\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Saved Projects';
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
<div class="draft-index">
    <?php $form = ActiveForm::begin([
        'id' => 'draft-project-form',
        'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>
    <div class="alert alert-<?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? 'info' : 'danger' : '' ?>"><i class="fa fa-exclamation-circle"></i> <?= $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? $HtmlHelper->time_elapsed_string($dueDate->due_date).' to go before the deadline of submission of monitoring plan. Due date is '.date("F j, Y", strtotime($dueDate->due_date)) 
    : 'Submission of monitoring plan has ended '.$HtmlHelper->time_elapsed_string($dueDate->due_date).' ago. Due date is '.date("F j, Y", strtotime($dueDate->due_date)) : '' ?></div>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Saved Projects</h3>
        </div>
        <div class="box-body">
            <div class="summary"><?= renderSummary($projectsPages) ?></div>
            <div class="draft-project-table" style="height: 600px;">
                <table class="table table-condensed table-striped table-hover table-responsive table-bordered" cellspacing="0" style="min-width: 3000px;">
                    <thead>
                        <tr>
                            <td rowspan=3 align=center style="vertical-align: bottom;"><input type=checkbox name="draft-projects" class="check-draft-projects" /></td>
                            <td rowspan=3>&nbsp;</td>
                            <td rowspan=3 colspan=2 style="width: 15%;">
                                <b>
                                (a) Name of Project <br>
                                (b) Location <br>
                                (c) Sector/Sub-Sector <br>
                                (d) Funding Source <br>
                                (e) Mode of Implementation <br>
                                (f) Project Schedule
                                </b>
                            </td>
                            <td rowspan=3 align=center style="width: 5%;"><b>Unit of Measure</b></td>
                            <td colspan=<?= count($quarters) + 1?> align=center><b>Financial Requirements</b></td>
                            <td colspan=<?= count($quarters) + 1?> align=center><b>Physical Targets</b></td>
                            <td colspan=<?= (count($quarters) * count($genders)) + 2?> align=center><b>Employment Generated</b></td>
                            <td colspan=<?= count($quarters) + 1?> align=center><b>Target Beneficiaries</b></td>
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
                        <?php foreach($projectsModels as $model){ ?>
                            <?= $this->render('_draft-project', [
                                'idx' => $idx,
                                'model' => $model,
                                'form' => $form,
                                'projectIds' => $projectIds,
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
                <?= Html::submitButton('Delete Selected', ['class' => 'btn btn-danger', 'id' => 'delete-selected-project-button', 'data' => ['disabled-text' => 'Please Wait'], 'data' => [
                    'method' => 'get',
                ], 'disabled' => true]) ?>
                <?php /* $dueDate ? strtotime(date("Y-m-d")) <= strtotime($dueDate->due_date) ? Html::submitButton('Submit to Monitoring Plan', ['class' => 'btn btn-success', 'id' => 'submit-draft-project-button', 'data' => ['disabled-text' => 'Please Wait'], 'data' => [
                    'method' => 'post',
                ], 'disabled' => true]) : '' : '' */ ?>
            </div>
            <div class="clearfix"></div>
            <br>
        </div>
    </div>
<?php ActiveForm::end(); ?>
</div>
<?php
    $script = '
    function enableDraftButtons()
    {
        if($("#draft-project-form input:checkbox:checked").length > 0)
        {
            $("#submit-draft-project-button").attr("disabled", false);
            $("#delete-selected-project-button").attr("disabled", false);
        }else{
            $("#submit-draft-project-button").attr("disabled", true);
            $("#delete-selected-project-button").attr("disabled", true);
        }
    }

    $(".check-draft-projects").click(function(){
        $(".check-draft-project").not(this).prop("checked", this.checked);
        enableDraftButtons();
    });

    $(".check-draft-project").click(function() {
        enableDraftButtons();
      });

    $("#delete-selected-project-button").on("click", function(e) {
        var checkedVals = $(".check-draft-project:checkbox:checked").map(function() {
            return this.value;
        }).get();

        var ids = checkedVals.join(",");

        e.preventDefault();

        var con = confirm("Are you sure you want to remove this projects?");
        if(con == true)
        {
            var form = $("#draft-project-form");
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

    $("#submit-draft-project-button").on("click", function(e) {
        e.preventDefault();

        var con = confirm("Are you sure you want to submit this projects?");
        if(con == true)
        {
            var form = $("#draft-project-form");
            var formData = form.serialize();

            $.ajax({
                url: form.attr("action"),
                type: "POST",
                data: formData,
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
        $(".check-draft-project").removeAttr("checked");
        $(".draft-project-table").freezeTable({
            "scrollable": true,
            "columnNum": 3
        });
        enableDraftButtons();
    });
    ';

    $this->registerJs($script, View::POS_END);
?>