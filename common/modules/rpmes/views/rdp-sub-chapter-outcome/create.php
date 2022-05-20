<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\RdpSubChapterOutcome */

$this->title = 'Add New Record';
$this->params['breadcrumbs'][] = ['label' => 'RDP Sub-Chapter Outcomes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rdp-sub-chapter-outcome-create">
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $this->render('_form', [
                'model' => $model,
                'chapters' => $chapters,
                'outcomes' => $outcomes,
            ]) ?>
        </div>
    </div>
</div>
