<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\modules\procurement\models\PrPr */

$this->title = $model->prProcVerification ? $model->prProcVerification->pr_no : $model->dts_no;
$this->params['breadcrumbs'][] = ['label' => 'Purchase Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

?>
<div class="pr-pr-view">

    <p class="pull-right">
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary', 'data' => [
                'method' => 'post',
            ],]) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-md-9 col-xs-12">
            <h2>PR No.: <?= $model->prProcVerification ? '<u>'.$model->prProcVerification->pr_no.'</u>' : '&nbsp;&nbsp;<u>Needs Verification</u>' ?></h2>

            <?= $this->render('_menu', ['model' => $model]) ?>

            <div class="panel panel-default">
                <div class="panel-body">
                    <div id="main-content"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-xs-12">
            <h4>Reports Available</h4>
            <div class="row" style="margin-right: 20px;">
                <?php if($status->action_type != 'FOR ADDITION OF ITEM'){ ?>
                <div class="col-md-3 col-xs-12">
                    <?php if(($status->action_type != 'FOR ADDITION OF ITEM') && Yii::$app->user->can('EndUser')){ ?>
                        <?= Html::a('<i class="fa fa-file-o"></i> PR', ['#'],['class' => 'btn btn-app']) ?>
                    <?php } ?>
                </div>
                <div class="col-md-3 col-xs-12">
                    <?php if(($status->status == 'APPROVED' && $status->action_type == 'PROCUREMENT VERIFIED') && Yii::$app->user->can('ProcurementStaff')){ ?>
                        <?= Html::a('<i class="fa fa-file-o"></i> RFQ', ['#'],['class' => 'btn btn-app']) ?>
                    <?php } ?>
                </div>
                <div class="col-md-3 col-xs-12">
                    <?php if(($status->status == 'APPROVED' && $status->action_type == 'PROCUREMENT VERIFIED') && Yii::$app->user->can('ProcurementStaff')){ ?>
                        <?= Html::a('<i class="fa fa-file-o"></i> APR', ['#'],['class' => 'btn btn-app']) ?>
                    <?php } ?>
                </div>
                <div class="col-md-3 col-xs-12">
                    <?php if(($status->status == 'APPROVED' && $status->action_type == 'PROCUREMENT VERIFIED') && Yii::$app->user->can('ProcurementStaff')){ ?>
                        <?= Html::a('<i class="fa fa-file-o"></i> AOQ', ['#'],['class' => 'btn btn-app']) ?>
                    <?php } ?>
                </div>
                <?php }else{ ?>
                    <div class="col-md-12 col-xs-12">
                        <p>No reports available</p>
                    </div>
                <?php } ?>
            </div>
            <h4>Current Status</h4>
            <?= DetailView::widget([
                'model' => $status,
                'attributes' => [
                    'group',
                    'status',
                    'action_type',
                    'actionTakenByName',
                    'date_of_action',
                    'remarks',
                ],
            ]) ?>

<?php
    Modal::begin([
        'id' => 'genericModal',
        'size' => "modal-md",
        'header' => '<div id="genericModalHeader"></div>'
    ]);
    echo '<div id="genericModalContent"></div>';
    Modal::end();
?>
<?php
        $script = '
            $( document ).ready(function() {

                $(".takeAction").click(function(){
                  $("#genericModal").modal("show").find("#genericModalContent").load($(this).attr("value"));
                });
            });
';
$this->registerJs($script);
   
?>
