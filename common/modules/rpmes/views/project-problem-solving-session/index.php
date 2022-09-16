<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use common\components\helpers\HtmlHelper;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\ProjectProblemSolvingSessionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project Problem Solving Sessions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-problem-solving-session-index">
    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Add New', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Summary of Project Problem Solving Sessions</h3>
                </div>
                <div class="box-body">
                    <?= $this->render('_search', [
                        'model' => $model,
                        'quarters' => $quarters,
                        'years' => $years,
                        'agencies' => $agencies,
                        'sectors' => $sectors,
                        'regions' => $regions,
                        'provinces' => $provinces,
                    ]) ?>
                    <br><br>
                    <hr>
                    <div id="project-problem-solving-session-table"></div>
                </div>
            </div>
        </div>
    </div>
</div>
