<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
/* @var $this yii\web\View */
/* @var $model common\modules\procurement\models\PrPr */

$this->title = 'Create Request';
$this->params['breadcrumbs'][] = ['label' => 'Purchase Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pr-pr-create">
    <!-- <div class="panel panel-default">
        <div class="panel-body"> -->
        	<div class="row">
        		<!-- <div class="col-md-3 col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <?= Nav::widget([
                            'options' => [
                                'class' => 'nav-pills nav-stacked',
                            ],
                            'items' => [
                                ['label' => '1. Create Request', 'url' => ['/procurement/pr-pr/create']],
                                ['label' => '2. Add Items', 'url' => ['/procurement/pr-pr/create-item'], 'options' => [
                                    'class' => 'disabled',
                                    'onclick' => 'return false;',
                                ]],
                            ]
                            ]) ?>
                        </div>
                    </div>
        		</div> -->
        		<div class="col-md-12 col-xs-12">
        			<?= $this->render('_form', [
				        'model' => $model,
                        'ppmpModel' => $ppmpModel,
				    ]) ?>
        		</div>
        	</div>
        <!-- </div>
    </div> -->
</div>
