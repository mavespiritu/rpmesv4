<?php
use yii\helpers\Html;
use frontend\assets\AppAsset;
use yii\web\View;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Url;

?>

<h4><i class="fa fa-search"></i> Search Filter</h4>
<?php $form = ActiveForm::begin([
    'id' => 'search-dashboard-form'
]); ?>

<?= $form->field($model, 'year')->widget(Select2::classname(), [
                'data' => $years,
                'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'year-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Year *');
            ?>

<?= $form->field($model, 'quarter')->widget(Select2::classname(), [
                'data' => $quarters,
                'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'quarter-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Quarter *');
            ?>

<div class="form-group pull-right">
    <?= Html::submitButton('Generate Data', ['class' => 'btn btn-primary', 'style' => 'margin-top: 5px;']) ?>
</div>

<?php ActiveForm::end(); ?>