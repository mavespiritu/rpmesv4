<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use dosamigos\ckeditor\CKEditor;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\Acknowledgment */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'options' => ['class' => 'disable-submit-buttons'],
]); ?>


<?= $form->field($model, 'remarks')->widget(CKEditor::className(), [
    'preset' => 'basic'
])->label('Remarks') ?>

<div class="pull-right">
    <?= Html::submitButton('Send Form 1 for further validation', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
</div>
<div class="clearfix"></div>

<?php ActiveForm::end(); ?>
