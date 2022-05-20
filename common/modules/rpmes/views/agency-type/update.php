<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\AgencyType */

$this->title = 'Update Record';
$this->params['breadcrumbs'][] = ['label' => 'Agency Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="agency-type-update">
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
