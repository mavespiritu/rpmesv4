<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'PPMP Monitoring';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="app-index">
    <div class="row">
        <div class="col-md-2 col-xs-12">
            <div class="box box-primary">
                <div class="box-header panel-title"><i class="fa fa-list"></i>Preview Report</div>
                <div class="box-body">
                <?= $this->render('_search',[
                    'model' => $model,
                    'stages' => $stages,
                    'years' => $years,
                    'offices' => $offices,
                    'fundSources' => $fundSources,
                    'orders' => $orders,
                ]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-10 col-xs-12">
            <div id="items"></div>
        </div>
    </div>
</div>
