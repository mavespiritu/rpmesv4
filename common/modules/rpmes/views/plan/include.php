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
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Project */

\yii\web\YiiAsset::register($this);

?>

<?php $form = ActiveForm::begin([
    'id' => 'include-project-form',
    'options' => ['class' => 'disable-submit-buttons'],
]); ?>

<table id="included-projects-table" class="table table-responsive table-bordered table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th style="width: 18%;">Project No.</th>
            <th style="width: 18%;">Program/Project Title</th>
            <th style="width: 18%;">Sector</th>
            <th style="width: 18%;">Mode of Implementation</th>
            <th style="width: 18%;">Project Profile</th>
            <td align=center><?= $model->draft == 'Yes' ? '<input type="checkbox" class="check-all-included-projects" />' : '' ?></td>
        </tr>
    </thead>
    <tbody>
    <?php if($projects){ ?>
        <?php $i = 1; ?>
        <?php foreach($projects as $project){ ?>
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
            $model->draft == 'Yes' ? 
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