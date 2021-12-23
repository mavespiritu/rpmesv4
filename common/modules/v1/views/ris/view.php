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
                    <!-- <table class="table bordered-table">
                        <tr>
                            <td>Division:</td>
                            <td colspan=4><u><?= $model->officeName ?></u></td>
                            <td rowspan=2>RIS No.</td>
                            <td rowspan=2 colspan=4><u><?= $model->ris_no ?></u</td>
                        </tr>
                        <tr>
                            <td>Office:</td>
                            <td colspan=4><u><?= $model->officeName ?></u></td>
                        </tr>
                        <tr>
                            <td colspan=5 align=center><b>Requisition</b></td>
                            <td rowspan=2 align=center><b>Stock Available?</b></td>
                            <td colspan=4 align=center><b>Issue</b></td>
                        </tr>
                        <tr>
                            <td>Stock No.</td>
                            <td>Unit</td>
                            <td>Description</td>
                            <td>Quantity</td>
                            <td>ABC</td>
                            <td>Quantity</td>
                            <td>Date Issue</td>
                            <td>Remarks</td>
                            <td>Fund Source</td>
                        </tr>
                    </table> -->
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
                                            'months' => $months
                                        ]),
                                        'active' => true,
                                    ],
                                    [
                                        'label' => '<i class="fa fa-shopping-cart"></i> RIS Items <span class="badge bg-green" id="badge-ris">'.$model->getRisItems()->count().'</span>',
                                        'content' => '',
                                        'linkOptions'=>['data-url' => Url::to(['/v1/ris/for-procurement', 'id' => $model->id])]
                                    ],
                                ],
                                'bordered'=>true,
                                'position'=>TabsX::POS_ABOVE,
                                'align'=>TabsX::ALIGN_RIGHT,
                                'encodeLabels'=>false
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
