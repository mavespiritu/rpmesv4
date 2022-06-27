<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectProblemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-problem-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="col-md-6 col-xs-12">
        <?= $form->field($model, 'project_id')->widget(Select2::classname(), [
                'data' => $projects,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'agency-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
            ])->label('Agency *');
        ?>
    </div>
    <br>
    <div class="form-group col-md-6 col-xs-12">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
