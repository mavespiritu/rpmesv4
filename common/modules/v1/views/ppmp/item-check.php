<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;
use yii\bootstrap\Modal;
/* @var $model common\modules\v1\models\Ppmp */

$this->title = $model->status ? $model->title.' ['.$model->status->status.']' : $model->title.' - Item Check';
$this->params['breadcrumbs'][] = ['label' => 'PPMPs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ppmp-view">
    <?= $this->render('_menu', [
        'model' => $model
    ]) ?>
    <div class="box box-primary">
        <div class="box-header panel-title"><i class="fa fa-list"></i>Manage Items</div>
        <div class="box-body">
            <table class="table table-condensed table-responsive">
                <thead>
                    <tr>
                        <th>Activity</th>
                        <th>PAP</th>
                        <th>Item</th>
                        <th>Unit of Measure</th>
                        <th>Cost</th>
                        <th>Quantity Entry Count</th>
                        <th>Fund Source</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(!empty($items)){ ?>
                    <?php foreach($items as $item){ ?>
                        <?= $this->render('_item-check', [
                            'model' => $model,
                            'item' => $item
                        ]) ?>
                    <?php } ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
