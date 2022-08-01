<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-search">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
        'method' => 'get'
    ]); ?>

    <div class="row">
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'year')->widget(Select2::classname(), [
                'data' => $years,
                'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'year-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Year *');
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'quarter')->widget(Select2::classname(), [
                'data' => $quarters,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'quarter-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Quarter *');
            ?>
        </div>
        <?php if(Yii::$app->user->can('Administrator') || Yii::$app->user->can('SuperAdministrator')){ ?>
            <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'agency_id')->widget(Select2::classname(), [
                    'data' => $agencies,
                    'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'agency-select'],
                    'pluginOptions' => [
                        'allowClear' =>  true,
                    ],
                ])->label('Agency *');
            ?>
            </div>
        <?php } ?>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'sector_id')->widget(Select2::classname(), [
                'data' => $sectors,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'sector-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'region_id')->widget(Select2::classname(), [
                'data' => $regions,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'region-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                'pluginEvents'=>[
                    'select2:select select2:unselect'=>'
                        function(){
                            $.ajax({
                                url: "'.Url::to(['/rpmes/project/province-list-single']).'",
                                data: {
                                        id: this.value,
                                    }
                            }).done(function(result) {
                                $(".province-select").html("").select2({ data:result, multiple:false, theme:"krajee", width:"100%",placeholder:"Select one", allowClear: true});
                                $(".province-select").select2("val","");
                            });
                        }'
    
                ]
                ]);
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'province_id')->widget(Select2::classname(), [
                'data' => $provinces,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'province-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>
        </div>
    </div>
    <div class="pull-right"><?= Html::submitButton('Generate Form', ['class' => 'btn btn-primary', 'style' => 'margin-top: 5px;', 'data' => ['disabled-text' => 'Please Wait']]) ?></div>
    <div class="clearfix"></div>
    <?php ActiveForm::end(); ?>

</div>
