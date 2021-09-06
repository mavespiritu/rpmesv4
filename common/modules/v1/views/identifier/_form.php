<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\v1\models\Identifier */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="identifier-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <?php 
        $organizationaloutcomesurl = \yii\helpers\Url::to(['/v1/identifier/organizational-outcome-list']);
        echo $form->field($model, 'cost_structure_id')->widget(Select2::classname(), [
            'data' => $costStructures,
            'options' => ['placeholder' => 'Select Cost Structure','multiple' => false,'class'=>'cost-structure-select'],
            'pluginOptions' => [
                'allowClear' => true
            ],
            'pluginEvents'=>[
                'select2:select'=>'
                    function(){
                        var vals = this.value;
                        $.ajax({
                            url: "'.$organizationaloutcomesurl.'",
                            data: {id: vals}
                            
                        }).done(function(result) {
                            var h;
                            $(".organizational-outcome-select").html("").select2({ data:result, theme:"krajee", width:"100%",placeholder:"Select Organizational Outcome", allowClear: true,});
                            $(".organizational-outcome-select").select2("val","");

                            $(".program-select").html("").select2({ data:{}, theme:"krajee", width:"100%",placeholder:"Select Program", allowClear: true,});
                            $(".program-select").select2("val","");

                            $(".sub-program-select").html("").select2({ data:{}, theme:"krajee", width:"100%",placeholder:"Select Sub Program", allowClear: true,});
                            $(".sub-program-select").select2("val","");
                        });
                    }'

            ]
        ]);
    ?>

    <?php 
        $programsurl = \yii\helpers\Url::to(['/v1/identifier/program-list']);
        echo $form->field($model, 'organizational_outcome_id')->widget(Select2::classname(), [
            'data' => $organizationalOutcomes,
            'options' => ['placeholder' => 'Select Organizational Outcome','multiple' => false,'class'=>'organizational-outcome-select'],
            'pluginOptions' => [
                'allowClear' => true
            ],
            'pluginEvents'=>[
                'select2:select'=>'
                    function(){
                        $.ajax({
                            url: "'.$programsurl.'",
                            data: {id: $("#identifier-cost_structure_id").val() , organizationalOutcomeId: this.value}
                            
                        }).done(function(result) {
                            var h;
                            $(".program-select").html("").select2({ data:result, theme:"krajee", width:"100%",placeholder:"Select Program", allowClear: true,});
                            $(".program-select").select2("val","");

                            $(".sub-program-select").html("").select2({ data:{}, theme:"krajee", width:"100%",placeholder:"Select Sub Program", allowClear: true,});
                            $(".sub-program-select").select2("val","");
                        });
                    }'

            ]
        ]);
    ?>

    <?php 
        $subProgramsurl = \yii\helpers\Url::to(['/v1/identifier/sub-program-list']);
        echo $form->field($model, 'program_id')->widget(Select2::classname(), [
            'data' => $programs,
            'options' => ['placeholder' => 'Select Program','multiple' => false,'class'=>'program-select'],
            'pluginOptions' => [
                'allowClear' => true
            ],
            'pluginEvents'=>[
                'select2:select'=>'
                    function(){
                        $.ajax({
                            url: "'.$subProgramsurl.'",
                            data: {
                                    id: $("#identifier-cost_structure_id").val(), 
                                    organizationalOutcomeId: $("#identifier-organizational_outcome_id").val(), 
                                    programId: this.value
                                }
                            
                        }).done(function(result) {
                            var h;
                            $(".sub-program-select").html("").select2({ data:result, theme:"krajee", width:"100%",placeholder:"Select Sub Program", allowClear: true,});
                            $(".sub-program-select").select2("val","");
                        });
                    }'

            ]
        ]);
    ?>

    <?= $form->field($model, 'sub_program_id')->widget(Select2::classname(), [
        'data' => $subPrograms,
        'options' => ['placeholder' => 'Select Sub Program','multiple' => false, 'class'=>'sub-program-select'],
        'pluginOptions' => [
            'allowClear' =>  true,
        ],
        ]);
    ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
