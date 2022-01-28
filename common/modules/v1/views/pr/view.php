<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Pr */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Prs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="pr-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'pr_no',
            'office_id',
            'section_id',
            'unit_id',
            'fund_source_id',
            'fund_cluster_id',
            'purpose:ntext',
            'requested_by',
            'date_requested',
            'approved_by',
            'date_approved',
            'type',
        ],
    ]) ?>

</div>
