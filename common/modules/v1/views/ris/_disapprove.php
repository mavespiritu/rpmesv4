<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Url;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ris */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ris-form">
    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
        'id' => 'ris-disapproval-form'
    ]); ?>

    <?= $form->field($model, 'disapproved_by')->widget(Select2::classname(), [
        'data' => $signatories,
        'options' => ['placeholder' => 'Select Disapprover','multiple' => false, 'class'=>'disapproved_by-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ]);
    ?>

    <?= $form->field($model, 'date_disapproved')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Enter date', 'autocomplete' => 'off'],
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
            'startDate' => date("Y-m-d")
        ],
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
