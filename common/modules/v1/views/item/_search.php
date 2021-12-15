<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
use yii\helpers\Url;
use kartik\select2\Select2;
/* @var $model common\modules\v1\models\ItemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="item-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'procurement_mode_id')->widget(Select2::classname(), [
            'data' => $procurementModes,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'procurement-mode-select'],
            'pluginOptions' => [
                'allowClear' =>  false,
            ],
        ]);
    ?>

    <?= $form->field($model, 'category')->widget(Select2::classname(), [
            'data' => $categories,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class' => 'category-select'],
            'pluginOptions' => [
                'allowClear' =>  false,
            ],
        ]);
    ?>

    <?= $form->field($model, 'code') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'unit_of_measure') ?>

    <?= $form->field($model, 'cse')->widget(Select2::classname(), [
            'data' => ['Yes' => 'Yes', 'No' => 'No'],
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'cse-select'],
            'pluginOptions' => [
                'allowClear' =>  false,
            ],
        ]);
    ?>

    <?= $form->field($model, 'classification')->widget(Select2::classname(), [
            'data' => $classifications,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'classification-select'],
            'pluginOptions' => [
                'allowClear' =>  false,
            ],
        ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Clear', ['class' => 'btn btn-outline-secondary', 'onClick' => 'redirectItemPage()']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$script = '
    function redirectItemPage()
    {
        window.location.href = "'.Url::to(['/v1/item/']).'";
    }
';
$this->registerJs($script, View::POS_END);
?>