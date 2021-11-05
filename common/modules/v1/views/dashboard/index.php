<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\View;
/* @var $this yii\web\View */
/* @var $searchModel common\modules\v1\models\PpmpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="dashboard-index">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header panel-title"><i class="fa fa-search"></i>Search Filter</div>
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
    <div id="prexc-summary"></div>
</div>

<?php
    $script = '
        $(document).ready(function(){
           
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>