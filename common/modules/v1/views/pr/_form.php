<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Url;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Pr */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pr-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
        'id' => 'pr-form',
        'enableAjaxValidation' => true,
    ]); ?>

    <p>Reminder: RIS must be approved to include items.</p>

    <?= $form->field($model, 'type')->widget(Select2::classname(), [
        'data' => $types,
        'options' => ['placeholder' => 'Select Type','multiple' => false, 'class'=>'type-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ])->label('Type') ?>
    
    <?= $form->field($model, 'office_id')->widget(Select2::classname(), [
        'data' => $offices,
        'options' => ['placeholder' => 'Select Division','multiple' => false, 'class'=>'office-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ]);
    ?>

<?= $form->field($model, 'year')->widget(Select2::classname(), [
        'data' => $years,
        'options' => ['placeholder' => 'Select Year','multiple' => false, 'class'=>'year-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ]);
    ?>

    <?= $form->field($model, 'fund_source_id')->widget(Select2::classname(), [
        'data' => $fundSources,
        'options' => ['placeholder' => 'Select Fund Source','multiple' => false, 'class'=>'fund-source-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ]);
    ?>

    <?= $form->field($model, 'fund_cluster_id')->widget(Select2::classname(), [
        'data' => $fundClusters,
        'options' => ['placeholder' => 'Select Fund Cluster','multiple' => false, 'class'=>'fund-cluster-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ]);
    ?>

    <?= $form->field($model, 'purpose')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'date_requested')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Enter date', 'autocomplete' => 'off'],
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
        ],
    ]) ?>

    <?= $form->field($model, 'requested_by')->widget(Select2::classname(), [
        'data' => $signatories,
        'options' => ['placeholder' => 'Select Staff','multiple' => false, 'class'=>'requested-by-select'],
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
