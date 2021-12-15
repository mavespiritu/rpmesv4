<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Appropriation Monitoring';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="app-index">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header panel-title"><i class="fa fa-list"></i>Preview Report</div>
                <div class="box-body">
                <?= $this->render('_search',[
                    'model' => $model,
                    'stages' => $stages,
                    'years' => $years,
                ]) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row"> 
        <div class="col-md-12 col-xs-12">
            <div id="items"></div>
        </div>
    </div>
</div>
