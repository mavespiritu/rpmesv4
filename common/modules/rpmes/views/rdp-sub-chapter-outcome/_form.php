<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\RdpSubChapterOutcome */
/* @var $form yii\widgets\ActiveForm */

$subOutcomeUrl = \yii\helpers\Url::to(['/rpmes/rdp-sub-chapter-outcome/rdp-chapter-outcome-list']);
?>

<div class="rdp-sub-chapter-outcome-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
    ]); ?>

    <?= $form->field($model, 'rdp_chapter_id')->widget(Select2::classname(), [
            'data' => $chapters,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'chapter-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
            'pluginEvents'=>[
                'select2:select'=>'
                    function(){
                        $.ajax({
                            url: "'.$subOutcomeUrl.'",
                            data: {
                                id: this.value,
                            }
                            
                        }).done(function(result) {
                            $(".rdp-chapter-outcome-select").html("").select2({ data:result, theme:"krajee", width:"100%",placeholder:"No RDP Chapter Outcome", allowClear: true});
                            $(".rdp-chapter-outcome-select").select2("val","");
                        });
                    }'

            ]
        ]);
    ?>

    <?= $form->field($model, 'rdp_chapter_outcome_id')->widget(Select2::classname(), [
            'data' => $outcomes,
            'options' => ['multiple' => false, 'placeholder' => 'No RDP Chapter Outcome', 'class'=>'rdp-chapter-outcome-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
        ]);
    ?>

    <?= $form->field($model, 'level')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
