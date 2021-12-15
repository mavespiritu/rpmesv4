<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ris */

$this->title = $model->ris_no;
$this->params['breadcrumbs'][] = ['label' => 'RIS', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ris-view">
    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-responsive table-condensed'], 
        'attributes' => [
            'ris_no',
            'officeName',
            'fundClusterName',
            'purpose:ntext',
            'date_required',
            'creatorName',
            'date_created',
            'requesterName',
            'date_requested',
            'approved_by',
            'date_approved',
            'issued_by',
            'date_issued',
            'received_by',
            'date_received',
        ],
    ]) ?>
</div>
