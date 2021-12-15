<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\widgets\MaskedInput;
use yii\web\JsExpression;
use kartik\typeahead\Typeahead;
use yii\helpers\Url;
use yii\bootstrap\Modal;
/* @var $this yii\web\View */
/* @var $model common\modules\pos\models\PrItem */
/* @var $form yii\widgets\ActiveForm */
$stocksurl = \yii\helpers\Url::to(['/procurement/pr-pr/stock-list']);
?>

<div class="procms-item-form">

    <?php $form = ActiveForm::begin([
        'id' => 'item-add',
        'options' => ['class' => 'disable-submit-buttons'],
        'enableClientValidation' => true,
    ]); ?>

    <div class="row">
        <div class="col-md-4 col-xs-12">
            <?= $form->field($itemModel, 'stock_inventory_id')->widget(Select2::classname(), [
                    'initValueText' => strip_tags($stockName),
                    'options' => ['placeholder' => 'Search Item'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => $stocksurl,
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(item) { return item.name; }'),
                        'templateSelection' => new JsExpression('function (item) { return item.text == "" ? item.name : item.text; }'),
                    ],
                ])->label('Search Item'); 
            ?>
        </div>
        <div class="col-md-4 col-xs-12">
            <?= $form->field($itemModel, 'quantity')->textInput(['type' => 'number', 'min' => 1, 'placeholder' => 'Enter quantity']) ?>
        </div>
        <div class="col-md-4 col-xs-12">
            <?= $form->field($itemModel, 'unit_cost')->widget(MaskedInput::classname(), [
                'options' => [
                    'placeholder' => 'Enter amount',
                ],
                'clientOptions' => [
                    'alias' =>  'decimal',
                    'autoGroup' => true,
                ],
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="pull-right">
                <?= Html::submitButton('Save Item', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
                <a href="javascript: void(0);" onClick="viewItemForm(<?= $model->id?>);" class="btn btn-info">Clear</a>
            </div>
        </div>
    </div>
    <p>Note: If the item is not on the selection list, click <?= Html::button('<u>here</u>', ['value' => Url::to(['/procurement/pr-pr/add-item', 'id' => $model->id]), 'class' => 'takeAction', 'style' => 'border: none; background: none; color: #8CD1FF; padding: 0; margin: 0;']) ?> to add specification of item manually.</p>
    

    <?php ActiveForm::end(); ?>

</div>
<?php
    Modal::begin([
        'id' => 'genericModal',
        'size' => "modal-md",
        'header' => '<div id="genericModalHeader"></div>'
    ]);
    echo '<div id="genericModalContent"></div>';
    Modal::end();
?>
<?php
    $script = '
        $( document ).ready(function() {
            $(".takeAction").click(function(){
              $("#genericModal").modal("show").find("#genericModalContent").load($(this).attr("value"));
            });

            $("#item-add").on("beforeSubmit", function(e) {
                e.preventDefault();
                var form = $(this);
                var formData = form.serialize();
                $.ajax({
                    url: form.attr("action"),
                    type: form.attr("method"),
                    data: formData,
                    success: function (data) {
                        alert("Item has been saved");
                        viewItemForm('.$model->id.');
                        viewItems('.$model->id.');
                    },
                });
                return false;
            });
        });

';
$this->registerJs($script);
   
?>