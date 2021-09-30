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
        'id' => 'ris-form',
        'enableAjaxValidation' => true,
    ]); ?>

    <?php if(Yii::$app->user->can('Administrator') || Yii::$app->user->can('Procurement')){ ?>
        <?php 
            $signatoryUrl = \yii\helpers\Url::to(['/v1/ris/signatory-list']);
            echo $form->field($model, 'office_id')->widget(Select2::classname(), [
            'data' => $offices,
            'options' => ['placeholder' => 'Select Division','multiple' => false, 'class'=>'office-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
            'pluginEvents'=>[
                'select2:select'=>'
                    function(){
                        $.ajax({
                            url: "'.$signatoryUrl.'",
                            data: {
                                    id: this.value,
                                }
                        }).done(function(result) {
                            $(".requested-by-select").html("").select2({ data:result, theme:"krajee", width:"100%",placeholder:"Select Staff", allowClear: true});
                            $(".requested-by-select").select2("val","");
                        });
                    }'

            ]
            ]);
        ?>
    <?php } ?>

    <?= $form->field($model, 'fund_cluster_id')->widget(Select2::classname(), [
        'data' => $fundClusters,
        'options' => ['placeholder' => 'Select Fund Cluster','multiple' => false, 'class'=>'fund-cluster-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ]);
    ?>

    <?= $form->field($model, 'purpose')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'date_required')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Enter date'],
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
            'startDate' => date("Y-m-d")
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
