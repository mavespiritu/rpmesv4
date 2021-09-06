<?php 
    use yii\helpers\Html;
    use yii\web\View;
    use yii\helpers\Url;
?>

<h4>Item List</h4>
<table class="table table-bordered table-hover table-condensed table-striped">
    <thead>
        <tr>
            <th>Item No.</th>
            <th>Stock/Property No.</th>
            <th>Unit</th>
            <th colspan=4>Item Description</th>
            <th>Quantity</th>
            <th>Unit Cost</th>
            <th>Total Cost</th>
            <th>Status</th>
            <th style="width: 4%;">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td align=center colspan=12><b><?= $model->prPpmp ? $model->prPpmp->description : 'No description' ?></b></td>
        </tr>
        <?php if($model->prItems){ ?>
            <?php $total = 0; ?>
            <?php foreach($model->prItems as $item): ?>
                <tr>
                    <td style="background-color: <?= $item->approvalColor ?>"><?= $item->item_no != '' ? $item->item_no : '-' ?></td>
                    <td style="background-color: <?= $item->approvalColor ?>"><?= $item->stockInventory ? $item->stockInventory->stock_code : '-' ?></td>
                    <td style="background-color: <?= $item->approvalColor ?>"><?= $item->unit ?></td>
                    <td style="background-color: <?= $item->approvalColor ?>;" colspan=4><?= $item->description ?></td>
                    <td style="background-color: <?= $item->approvalColor ?>" align=center><?= $item->quantity ?></td>
                    <td style="background-color: <?= $item->approvalColor ?>" align=right><?= number_format($item->unit_cost, 2) ?></td>
                    <td style="background-color: <?= $item->approvalColor ?>" align=right><?= number_format($item->unit_cost*$item->quantity, 2) ?></td>
                    <td style="background-color: <?= $item->approvalColor ?>" align=center><?= $item->approvalStatus ?>
                        <?= $item->approval ? $item->approvalStatus == 'FOR REVISION' || $item->approvalStatus == 'DISAPPROVED' ? '<br><b>Remarks: '.$item->approval->remarks.'</b>' : '' : '' ?>
                    </td>
                    <td align=center>
                        <?php if(($status->status == 'PENDING' && $status->action_type == 'FOR ADDITION OF ITEM') && Yii::$app->user->can('EndUser') || $item->approvalStatus == 'FOR REVISION'){ ?>
                            <?= Html::button('<i class="glyphicon glyphicon-pencil"></i>', ['value' => Url::to(['/procurement/pr-pr/update-item', 'id' => $item->id]), 'class' => 'takeAction', 'style' => 'border: none; background: none; color: #8CD1FF; padding: 0; margin: 0;']) ?>
                            <a href="javascript:void(0);" onClick="deleteItem(<?= $item->id ?>)" data-method="post" data-confirm="Are you sure you want to delete this item?"><i class="glyphicon glyphicon-trash"></i></a>
                        <?php } ?>
                        <?php if($status->status == 'APPROVED' && $status->action_type == 'BUDGET VERIFIED' && Yii::$app->user->can('ProcurementStaff')){ ?>
                            <?= Html::button('<i class="fa fa-external-link"></i>', ['value' => Url::to(['/procurement/pr-pr/approve-item', 'id' => $item->id]), 'class' => 'takeAction', 'style' => 'border: none; background: none;']) ?>
                        <?php } ?>
                    </td>
                </tr>
                <?php $total+=$item->unit_cost*$item->quantity ?>
            <?php endforeach ?>
            <tr style="background-color: white;">
                <td colspan=9 align=right><b>TOTAL</b></td>
                <td align=right><b><?= number_format($total, 2) ?></b></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        <?php }else{ ?>
            <tr>
                <td colspan=12>No added items for this purchase request.</td>
            </tr>
        <?php } ?>
        <tr style="background-color: white;">
            <td align=center colspan=2><b>PURPOSE: </b></td>
            <td colspan=10><b><?= $model->purpose ?></b></td>
        </tr>
        <tr style="background-color: white;">
            <td>&nbsp;</td>
            <td colspan=5><b>REQUESTED BY:</b></td>
            <td colspan=6><b>APPROVED BY:</b></td>
        </tr>
        <tr style="background-color: white;">
            <td>Signature:</td>
            <td colspan=5>&nbsp;</td>
            <td colspan=6>&nbsp;</td>
        </tr>
        <tr style="background-color: white;">
            <td>Printed Name:</td>
            <td colspan=5 align="center"><b><?= strtoupper($model->requester) ?></b></td>
            <td colspan=6 align="center"><b><?= strtoupper($model->approver) ?></b></td>
        </tr>
        <tr style="background-color: white;">
            <td>Designation:</td>
            <td colspan=5 align="center"><?= $model->requester_designation ?></td>
            <td colspan=6 align="center"><?= $model->approver_designation ?></td>
        </tr>
        <tr style="background-color: white;">
            <td rowspan=2><b>Portion for Procurement Focal Person</b></td>
            <td align=center><b><?= $model->prPpmp ? $model->prPpmp->source : '' ?></b></td>
            <td>Version No.</td>
            <td>Item No.</td>
            <td>I certify that this request is included in our PPMP and the specifications & supporting documents are in order.</td>
            <td colspan=7>&nbsp;</td>
        </tr>
        <tr style="background-color: white;">
            <td align=center><b><?= $model->prPpmp ? $model->prPpmp->source_version : '' ?></b></td>
            <td align=center><b><?= $model->prPpmp ? $model->prPpmp->version_no : '' ?></b></td>
            <td align=center><b><?= $model->prPpmp ? $model->prPpmp->item_no : '' ?></b></td>
            <td colspan=2 align="center"><br><b><?= $model->procurementFocalPerson ?></b></td>
            <td colspan=7>&nbsp;</td>
        </tr>
        <tr style="background-color: white;">
            <td colspan=12>&nbsp;</td>
        </tr>
        <tr style="background-color: white;">
            <td colspan=4 align="center"><b>FOR BUDGET SECTION USE ONLY</b></td>
            <td colspan=8 align="center"><b>FOR PROCUREMENT UNIT SECTION ONLY</b></td>
        </tr>
        <tr style="background-color: white;">
            <td rowspan=3>Verified as to inclusion in WFP & appropriate fund source</td>
            <td colspan=2>Allotment:</td>
            <td>Verified By:</td>
            <td rowspan=3 colspan=2>Verified as to inclusion in APP/PPMP & completeness of specifications & supporting documents</td>
            <td colspan=3>Verified By:</td>
            <td colspan=3>Mode of Procurement</td>
        </tr>
        <tr style="background-color: white;">
            <td colspan=2 rowspan=2><?= $model->prBudgetVerification ? '<b>'.$model->prBudgetVerification->allotment.'</b>' : '<i>For Budget Verification</i>' ?></td>
            <td rowspan=2><br><b><?= $model->budgetVerifier ?></b></td>
            <td colspan=3 rowspan=2><br><b><?= $model->procurementVerifier ?></b></td>
            <td colspan=3><?= $model->prProcVerification ? '<b>'.$model->prProcVerification->mode->title.'</b>' : '<i>For Procurement Verification</i>' ?></td>
        </tr>
        <tr style="background-color: white;">
            <td colspan=2 align="center"><b>PMT</b></td>
            <td>&nbsp;</td>
        </tr>
    </tbody>
</table>
<?php if($status){ ?>
    <?php if($status->status == 'PENDING' && $status->action_type == 'FOR ADDITION OF ITEM' && Yii::$app->user->can('EndUser')){ ?>
        <?php if($model->prItems){ ?>
            <p>I certify that this request is included in our PPMP and the specifications & supporting documents are in order.</p>
            <?= Html::a('Submit for Budget Verification', ['/procurement/pr-pr/submit-budget-verification', 'id' => $model->id],[
                'data' => [
                    'confirm' => 'Are you sure you want to submit this purchase request for budget verification?',
                    'method' => 'post',
                ],
                'class' => 'btn btn-info btn-block'
            ]) ?>
        <?php } ?>
    <?php } ?>
    <?php if($status->status == 'PENDING' && $status->action_type == 'FOR BUDGET VERIFICATION' && Yii::$app->user->can('BudgetStaff')){ ?>
        <?php if($model->prItems){ ?>
            <div class="row">
                <div class="col-md-4 col-xs-12"> 
                    <?= Html::button('Verify/Approve PR', ['value' => Url::to(['/procurement/pr-pr/verify', 'id' => $model->id, 'group' => 'BUDGET', 'status' => 'APPROVED']), 'class' => 'takeAction btn btn-info btn-block']) ?>
                </div>
                <div class="col-md-4 col-xs-12">
                    <?= Html::button('Set PR as For Revision', ['value' => Url::to(['/procurement/pr-pr/for-revision', 'id' => $model->id, 'group' => 'BUDGET', 'status' => 'FOR REVISION']), 'class' => 'takeAction btn btn-warning btn-block']) ?>
                </div>
                <div class="col-md-4 col-xs-12">
                    <?= Html::button('Disapprove PR', ['value' => Url::to(['/procurement/pr-pr/disapprove', 'id' => $model->id, 'group' => 'BUDGET', 'status' => 'DISAPPROVED']), 'class' => 'takeAction btn btn-danger btn-block']) ?>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
    <?php if($status->status == 'APPROVED' && $status->action_type == 'BUDGET VERIFIED' && Yii::$app->user->can('ProcurementStaff')){ ?>
        <?php if($model->prItems){ ?>
            <div class="row">
                <div class="col-md-4 col-xs-12">
                    <?= Html::button('Verify/Approve PR', ['value' => Url::to(['/procurement/pr-pr/verify', 'id' => $model->id, 'group' => 'PROCUREMENT', 'status' => 'APPROVED']), 'class' => 'takeAction btn btn-info btn-block']) ?>
                </div>
                <div class="col-md-4 col-xs-12">
                    <?= Html::button('Set PR as For Revision', ['value' => Url::to(['/procurement/pr-pr/for-revision', 'id' => $model->id, 'group' => 'PROCUREMENT', 'status' => 'FOR REVISION']), 'class' => 'takeAction btn btn-warning btn-block']) ?>
                </div>
                <div class="col-md-4 col-xs-12">
                    <?= Html::button('Disapprove PR', ['value' => Url::to(['/procurement/pr-pr/disapprove', 'id' => $model->id, 'group' => 'PROCUREMENT', 'status' => 'DISAPPROVED']), 'class' => 'takeAction btn btn-danger btn-block']) ?>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
<?php } ?>
<?php
$script = '
    $( document ).ready(function() {
        $(".takeAction").click(function(){
          $("#genericModal").modal("show").find("#genericModalContent").load($(this).attr("value"));
        });
    });
    function updateItem(id)
    {
        $.ajax({
            url: "'.Url::to(['/procurement/pr-pr/update-item']).'?id=" + id,
            beforeSend: function(){
                $("#item-form").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                $("#item-form").empty();
                $("#item-form").hide();
                $("#item-form").fadeIn();
                $("#item-form").html(data);
            }
        }); 
    }

    function deleteItem(id)
    {
        $.ajax({
            url: "'.Url::to(['/procurement/pr-pr/delete-item']).'?id=" + id,
            beforeSend: function(){
                $("#item-list").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                viewItems('.$model->id.');
            }
        }); 
    }
';
$this->registerJs($script, View::POS_END);
?>