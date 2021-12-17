<?php 
    use yii\helpers\Html;
    use yii\helpers\Url;
    use frontend\assets\AppAsset;

    $asset = AppAsset::register($this);
?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <?= Html::img($asset->baseUrl.'/images/default_product_image.png') ?>
            </div>
            <div class="col-md-9 col-xs-12">
                <h4><?= $model->item->title ?>
                    <span class="pull-right"><?= Html::button('<i class="fa fa-shopping-cart"></i> Add to RIS', ['value' => Url::to(['/v1/ris/buy', 'id' => $item->id, 'item_id' => $model->id]), 'class' => 'btn btn-primary btn-sm btn-block buy-button']) ?></span>
                </h4>
                <br>
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <table class="table table-condensed">
                            <tr>
                                <th style="width:25%;">Activity:</th>
                                <td style="width:25%;"><?= $model->activity->title ?></td>
                                <th style="width:25%;">Remaining Qty (in PPMP):</th>
                                <td style="width:25%;"><?= $model->remainingQuantity ?></td>
                            </tr>
                            <tr>
                                <th>PAP:</th>
                                <td><?= $model->subActivity->title ?></td>
                                <th>Cost Per Unit:</th>
                                <td><?= number_format($model->cost, 2) ?></td>
                            </tr>
                            <tr>
                                <th>Fund Source:</th>
                                <td><?= $model->fundSource->code ?></td>
                                <th>Unit of Measure:</th>
                                <td><?= $model->item->unit_of_measure ?></td>
                            </tr>
                            <tr>
                                <th>Object:</th>
                                <td><?= $model->obj->objTitle ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>