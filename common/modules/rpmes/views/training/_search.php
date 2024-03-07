<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;
use yii\bootstrap\ButtonDropdown;

/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\TrainingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pull-left">
    <?= Yii::$app->user->can('Administrator') ? Html::a('<i class="fa fa-plus"></i> Add New Record', ['create'],['class' => 'btn btn-success', 'id' => 'create-button']) : '' ?>

    <?= Yii::$app->user->can('Administrator') ? Html::button('<i class="fa fa-print"></i> Generate Form 9', ['value' => Url::to(['/rpmes/training/generate']), 'class' => 'btn btn-default', 'id' => 'generate-button']) : '' ?>
</div>

<div class="pull-right">
    <div class="project-problem-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'globalSearch')->textInput(['style' => 'border-top: none !important; border-left: none !important; border-right: none !important;', 'placeholder' => 'Search Records'])->label(false) ?>

    <?php ActiveForm::end(); ?>

    </div>
</div>
<div class="clearfix"></div>

<?php
  Modal::begin([
    'id' => 'generate-modal',
    'size' => "modal-sm",
    'header' => '<div id="generate-modal-header"><h4>Generate Form 9</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="generate-modal-content"></div>';
  Modal::end();
?>
<?php
    $script = '
        $(document).ready(function(){
            $("#generate-button").click(function(){
                $("#generate-modal").modal("show").find("#generate-modal-content").load($(this).attr("value"));
              });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>
