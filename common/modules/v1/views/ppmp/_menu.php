<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div>
    <div class="pull-left">
        <?php // Html::button('<i class="fa fa-plus"></i> Add Item', ['value' => Url::to(['/v1/ppmp/create-item']), 'class' => 'btn btn-app', 'id' => 'create-button']) ?>
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