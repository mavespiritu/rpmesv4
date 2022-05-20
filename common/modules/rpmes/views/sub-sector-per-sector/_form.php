<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\SubSectorPerSector */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sub-sector-per-sector-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <?= $form->field($model, 'sector_id')->widget(Select2::classname(), [
            'data' => $sectors,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'sector-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
        ]);
    ?>

    <?= $form->field($model, 'sub_sector_id')->widget(Select2::classname(), [
            'data' => $subSectors,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'sub-sector-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
        ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
