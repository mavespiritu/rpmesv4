<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ris */

$this->title = $model->status ? $model->ris_no.' ['.$model->status->status.']' : $model->ris_no;
$this->params['breadcrumbs'][] = ['label' => 'RIS', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$total = 0;
?>

<div class="ris-view">
    <?= $this->render('_menu', [
        'model' => $model
    ]) ?>

    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="box box-primary">
                <div class="box-header panel-title"><i class="fa fa-edit"></i> Add Supplemental Item</div>
                <div class="box-body">
                    <?= $this->render('_supplemental-item-form', [
                        'model' => $model,
                        'itemModel' => $itemModel,
                        'activities' => $activities,
                        'subActivities' => $subActivities,
                        'objects' => $objects,
                        'items' => $items,
                        'months' => $months,
                        'itemBreakdowns' => $itemBreakdowns,
                    ]); ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="box box-primary">
                <div class="box-header panel-title"><i class="fa fa-list"></i> Supplemental Items</div>
                <div class="box-body">
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Specification</th>
                                <th>Unit Cost</th>
                                <th>Quantity</th>
                                <td align=center><b>Total</b></td>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(!empty($supplementalItems)){ ?>
                            <?php foreach($supplementalItems as $idx => $suppItems){ ?>
                                <tr>
                                    <th colspan=5><i><?= $idx ?></i></th>
                                </tr>
                                <?php if(!empty($suppItems)){ ?>
                                    <?php foreach($suppItems as $item){ ?>
                                        <?= $this->render('_supplemental-item', [
                                            'model' => $model,
                                            'item' => $item,
                                            'specifications' => $specifications
                                        ]) ?>
                                        <?php $total += ($item['cost'] * $item['total']); ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        <?php }else{ ?>
                            <tr>
                                <td colspan=6 align=center>No supplemental items included</td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan=3 align=right><b>Grand Total</b></td>
                            <td>&nbsp;</td>
                            <td align=right><b><?= number_format($total, 2) ?></b></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan=3 align=right><b>Realigned</b></td>
                            <td>&nbsp;</td>
                            <td align=right><b><?= number_format($model->getItemsTotal('Realigned'), 2) ?></b></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan=3 align=right><b>Unused</b></td>
                            <td>&nbsp;</td>
                            <td align=right><b><?= number_format($model->getItemsTotal('Realigned') - $total, 2) ?></b></td>
                            <td>&nbsp;</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>