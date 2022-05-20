<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\RdpSubChapterOutcome */

$this->title = 'Update Record';
$this->params['breadcrumbs'][] = ['label' => 'RDP Sub-Chapter Outcomes', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="rdp-sub-chapter-outcome-update">
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
