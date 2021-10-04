<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ris */

$this->title = $model->ris_no;
$this->params['breadcrumbs'][] = ['label' => 'RIS', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ris-view">
    <?= $this->render('_menu', [
        'model' => $model
    ]) ?>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header panel-title"><i class="fa fa-edit"></i> Select Items</div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <?= TabsX::widget([
                                'items'=> [
                                    [
                                        'label' => '<i class="fa fa-list"></i> PPMP Items',
                                        'content' => $this->render('_home',[
                                            'model' => $model,
                                            'appropriationItemModel' => $appropriationItemModel,
                                            'activities' => $activities,
                                            'subActivities' => $subActivities,
                                            'fundSources' => $fundSources,
                                        ]),
                                        'active' => true,
                                    ],
                                    [
                                        'label' => '<i class="fa fa-list"></i> For Procurement',
                                        'content' => '',
                                        'linkOptions'=>['data-url' => Url::to(['/v1/ris/ris-items', 'id' => $model->id])]
                                    ],
                                ],
                                'bordered'=>true,
                                'position'=>TabsX::POS_ABOVE,
                                'encodeLabels'=>false
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
