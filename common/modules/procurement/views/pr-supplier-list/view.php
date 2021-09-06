<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\procurement\models\PrSupplierList */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pr Supplier Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="pr-supplier-list-view">

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
            'service_type_id',
            'type',
            'business_name:ntext',
            'business_address:ntext',
            'contact_person',
            'landline',
            'mobile',
            'email_address:email',
            'philgeps_no',
            'bir_registration',
            'tin_no',
        ],
    ]) ?>

</div>
