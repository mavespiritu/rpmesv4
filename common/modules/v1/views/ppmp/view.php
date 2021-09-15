<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;
/* @var $model common\modules\v1\models\Ppmp */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'PPMPs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ppmp-view">
    <?= $this->render('_menu', [
        'model' => $model
    ]) ?>
    <div class="row">
        <div class="col-md-9 col-xs-12">
            <div class="box box-primary">
                <div class="box-header panel-title"><i class="fa fa-list"></i>Manage Items</div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <?= $this->render('_load-items', [
                                'model' => $model,
                                'appropriationItemModel' => $appropriationItemModel,
                                'activities' => $activities,
                                'fundSources' => $fundSources,
                            ]) ?>
                            <div id="item-details"></div>
                        </div>
                    </div>
                    <hr style="opacity: 0.3" />
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <div id="items"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-xs-12">
            <div class="box box-primary">
                <div class="box-header panel-title"><i class="fa fa-bar-chart"></i> Summary</div>
                <div class="box-body">
                    <table class="table table-responsive table-condensed table-hover">
                        <tr>
                            <th>Reference</th>
                            <td align="right"><?= $model->reference ? $model->reference->title : 'No cited reference' ?></td>
                        </tr>
                        <tr>
                            <th>Approved Budget</th>
                            <td align="right"><?= $model->reference ? number_format($model->reference->total, 2) : '0.00' ?></td>
                        </tr>
                    </table>
                    <br>
                    <p class="panel-title"><i class="fa fa-bar-chart"></i> This PPMP</p><br>
                    <table class="table table-responsive table-condensed table-hover">
                        <tr>
                            <th>Total</th>
                            <td align="right"><div id="ppmp-total"><?= number_format($model->total, 2) ?></div></td>
                        </tr>
                        <tr>
                            <td align="right" style="font-size: 12px;">Original</td>
                            <td align="right" style="font-size: 12px;"><div id="original-total"><?= number_format($model->originalTotal, 2) ?></div></td>
                        </tr>
                        <tr>
                            <td align="right" style="font-size: 12px;">Supplemental</td>
                            <td align="right" style="font-size: 12px;"><div id="supplemental-total"><?= number_format($model->supplementalTotal, 2) ?></div></td>
                        </tr>
                        <tr>
                            <th>Ongoing Procurement</th>
                            <td align="right"><?= number_format(0, 2) ?></td>
                        </tr>
                        <tr>
                            <th>Obligated</th>
                            <td align="right"><?= number_format(0, 2) ?></td>
                        </tr>
                    </table>
                    <div id="item-summary"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $script = '
        function loadPpmpTotal(id)
        {
            $.ajax({
                url: "'.Url::to(['/v1/ppmp/load-ppmp-total']).'",
                data: {
                    id: id,
                },
                success: function (data) {
                    $("#ppmp-total").empty();
                    $("#ppmp-total").hide();
                    $("#ppmp-total").fadeIn("slow");
                    $("#ppmp-total").html(data);
                },
                error: function (err) {
                    console.log(err);
                }
            });
        }

        function loadOriginalTotal(id)
        {
            $.ajax({
                url: "'.Url::to(['/v1/ppmp/load-original-total']).'",
                data: {
                    id: id,
                },
                success: function (data) {
                    $("#original-total").empty();
                    $("#original-total").hide();
                    $("#original-total").fadeIn("slow");
                    $("#original-total").html(data);
                },
                error: function (err) {
                    console.log(err);
                }
            });
        }

        function loadSupplementalTotal(id)
        {
            $.ajax({
                url: "'.Url::to(['/v1/ppmp/load-supplemental-total']).'",
                data: {
                    id: id,
                },
                success: function (data) {
                    $("#supplemental-total").empty();
                    $("#supplemental-total").hide();
                    $("#supplemental-total").fadeIn("slow");
                    $("#supplemental-total").html(data);
                },
                error: function (err) {
                    console.log(err);
                }
            });
        }

        function loadItemSummary(id)
        {
            $.ajax({
                url: "'.Url::to(['/v1/ppmp/load-item-summary']).'",
                data: {
                    id: id,
                },
                success: function (data) {
                    $("#item-summary").empty();
                    $("#item-summary").hide();
                    $("#item-summary").fadeIn("slow");
                    $("#item-summary").html(data);
                },
                error: function (err) {
                    console.log(err);
                }
            });
        }

        function loadItems(id, activity_id, fund_source_id)
        {
            $.ajax({
                url: "'.Url::to(['/v1/ppmp/load-items']).'",
                data: {
                    id: id,
                    activity_id: activity_id,
                    fund_source_id: fund_source_id,
                },
                beforeSend: function(){
                    $("#items").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
                },
                success: function (data) {
                    $("#items").empty();
                    $("#items").hide();
                    $("#items").fadeIn("slow");
                    $("#items").html(data);
                },
                error: function (err) {
                    console.log(err);
                }
            });
        }

        $(document).ready(function(){
            loadItemSummary('.$model->id.');
        });
    ';

    $this->registerJs($script, View::POS_END);
?>