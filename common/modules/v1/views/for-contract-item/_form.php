<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\ForContractItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="for-contract-item-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <?= $form->field($model, 'item_id')->widget(Select2::classname(), [
            'data' => $items,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'item-select'],
            'pluginOptions' => [
                'allowClear' =>  false,
            ],
        ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
