<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;
use yii\bootstrap\ButtonDropdown;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ResolutionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="resolution-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="col-md-6 col-xs-12">
        <?= $form->field($searchModel, 'quarter')->dropDownList(['' => '', 'Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'])->label('Quarter *'); ?>
    </div>
    <div class="col-md-6 col-xs-12">
        <?= $form->field($searchModel, 'year')->widget(Select2::classname(), [
            'data' => $years,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'year-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
            ])->label('Year *');
        ?>
    </div>

    <div class="form-group pull-right">
            <?= Html::submitButton('Search Data', ['class' => 'btn btn-primary', 'style' => 'margin-top: 5px;']) ?>
            <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<br>
<br>
<br>
