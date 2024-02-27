<?php

use yii\helpers\Html;
use frontend\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

$asset = AppAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\AgencySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'RPMES Guidelines';
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="plan-index">
    <div class="box box-solid">
        <div class="box-header with-border"><h3 class="box-title">Read the guidelines here</h3></div>
            <div class="box-body" style="height: calc(100vh - 200px); width: 100%;">
                <iframe src="<?= $asset->baseUrl ?>/docs/20231016_RPMES-Operational-Guidelines.pdf" width="100%" height="100%" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>
