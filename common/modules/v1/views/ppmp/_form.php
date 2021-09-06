<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Ppmp */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="ppmp-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
        'id' => 'ppmp-form',
        'enableAjaxValidation' => true,
    ]); ?>

    <?php if(Yii::$app->user->can('Administrator')){ ?>
        <?= $form->field($model, 'office_id')->widget(Select2::classname(), [
            'data' => $offices,
            'options' => ['placeholder' => 'Select Division','multiple' => false, 'class'=>'office-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
            ]);
        ?>
    <?php } ?>

    <?= $form->field($model, 'year')->textInput(['type' => 'number', 'min' => date("Y"), 'autocomplete' => 'off']) ?>
    
    <?= $form->field($model, 'stage')->widget(Select2::classname(), [
        'data' => [ 'Indicative' => 'Indicative', 'Adjusted' => 'Adjusted', 'Final' => 'Final', ],
        'options' => ['placeholder' => 'Select Stage','multiple' => false, 'class'=>'stage-select'],
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
