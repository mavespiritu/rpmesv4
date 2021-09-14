<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div>
    <div class="pull-left">
        <?= Html::a('<i class="fa fa-angle-double-left"></i> Back to PPMP List', ['/v1/ppmp/'], ['class' => 'btn btn-app']) ?>
    </div>
    <div class="pull-right">
        <?= Html::a('<i class="fa fa-trash"></i> Delete PPMP', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-app',
            'data' => [
                'confirm' => 'Deleting this PPMP will also delete all included items. Would you like to proceed?',
                'method' => 'post',
            ],
        ]) ?>
    </div>
    <div class="clearfix"></div>
</div>