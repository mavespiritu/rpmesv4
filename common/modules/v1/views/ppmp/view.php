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
                    <table class="table table-responsive">
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
                    <table class="table table-responsive">
                        <tr>
                            <th>Total</th>
                            <td align="right"><div id="ppmp-total"><?= number_format($model->total, 2) ?></div></td>
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

        function number_format (number, decimals, dec_point, thousands_sep) {
            number = (number + "").replace(/[^0-9+\-Ee.]/g, "");
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === "undefined") ? "," : thousands_sep,
                dec = (typeof dec_point === "undefined") ? "." : dec_point,
                s = "",
                toFixedFix = function (n, prec) {
                    var k = Math.pow(10, prec);
                    return "" + Math.round(n * k) / k;
                };
            s = (prec ? toFixedFix(n, prec) : "" + Math.round(n)).split(".");
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || "").length < prec) {
                s[1] = s[1] || "";
                s[1] += new Array(prec - s[1].length + 1).join("0");
            }
            return s.join(dec);
        }
    ';

    $this->registerJs($script, View::POS_END);
?>