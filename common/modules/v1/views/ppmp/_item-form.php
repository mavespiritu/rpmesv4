<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
use yii\web\View;
/* @var $model common\modules\v1\models\Ppmp */
/* @var $form yii\widgets\ActiveForm */
$itemsUrl = \yii\helpers\Url::to(['/v1/ppmp/item-list']);
$item_id = $itemModel->isNewRecord ? 0 : $itemModel->item_id;
?>

<div class="ppmp-item">

    <?php $form = ActiveForm::begin([
    	'options' => ['class' => 'disable-submit-buttons'],
        'id' => 'ppmp-item-form',
    ]); ?>
    <?= Html::hiddenInput('cost_per_unit', $itemModel->isNewRecord ? '' : $itemModel->item->cost_per_unit, ['id' => 'cost_per_unit']) ?>
    <?= $form->field($itemModel, 'activity_id')->hiddenInput(['value' => $activity->id])->label(false) ?>
    <?= $form->field($itemModel, 'fund_source_id')->hiddenInput(['value' => $fundSource->id])->label(false) ?>
    
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label class="control-label">Activity</label>
                <?= Html::textInput('activity_id', $activity->title, ['disabled' => 'disabled', 'class' => 'form-control']); ?>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label class="control-label">Fund Source</label>
                <?= Html::textInput('fund_source_id', $fundSource->code, ['disabled' => 'disabled', 'class' => 'form-control']); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?= $form->field($itemModel, 'sub_activity_id')->widget(Select2::classname(), [
                'data' => $subActivities,
                'options' => ['placeholder' => 'Select PPA', 'multiple' => false, 'class' => 'sub-activity-select', 'id' => 'ppmpitem-sub_activity_id-'.$itemModel->sub_activity_id],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                'pluginEvents'=>[
                    'select2:select'=>'
                        function(){
                            $.ajax({
                                url: "'.$itemsUrl.'",
                                data: {
                                        id: '.$model->id.',
                                        sub_activity_id: this.value,
                                        obj_id: $("#ppmpitem-obj_id-'.$itemModel->sub_activity_id.'").val(),
                                        item_id: '.$item_id.'
                                    }
                                
                            }).done(function(result) {
                                $(".item-select").html("").select2({ data:result, theme:"krajee", width:"100%",placeholder:"Select Item", allowClear: true});
                                $(".item-select").select2("val","");
                            });
                        }'

                ]
                ]);
            ?>
        </div>
        <div class="col-md-6 col-xs-12">
            <?= $form->field($itemModel, 'obj_id')->widget(Select2::classname(), [
                    'data' => $objects,
                    'options' => ['placeholder' => 'Select Object', 'multiple' => false, 'class' => 'obj-select', 'id' => 'ppmpitem-obj_id-'.$itemModel->sub_activity_id],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                    'pluginEvents' => [
                        'select2:select'=>'
                            function(){
                                $.ajax({
                                    url: "'.$itemsUrl.'",
                                    data: {
                                            id: '.$model->id.',
                                            sub_activity_id: $("#ppmpitem-sub_activity_id-'.$itemModel->sub_activity_id.'").val(),
                                            obj_id: this.value,
                                            item_id: '.$item_id.'
                                          }
                                    
                                }).done(function(result) {
                                    $(".item-select").html("").select2({ data:result, theme:"krajee", width:"100%",placeholder:"Select Item", allowClear: true});
                                    $(".item-select").select2("val","");
                                });
                            }'

                    ]
                ])->label('Object');
            ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <?= $form->field($itemModel, 'item_id')->widget(Select2::classname(), [
                    'data' => $items,
                    'options' => ['placeholder' => 'Select Item', 'multiple' => false, 'class' => 'item-select', 'id' => 'ppmpitem-item_id-'.$itemModel->sub_activity_id],
                    'pluginOptions' => [
                        'allowClear' =>  true,
                    ],
                    'pluginEvents' => [
                        'select2:select'=>'
                            function(){
                                updateItemDetails(this.value);
                            }'
                    ]
                ]);
            ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label class="control-label">Unit of Measure</label>
                <?= Html::textInput('unit_of_measure', $itemModel->isNewRecord ? '' : $itemModel->item->unit_of_measure, ['disabled' => 'disabled', 'class' => 'form-control', 'id' => 'ppmp-item-unit_of_measure']); ?>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label class="control-label">Cost Per Unit</label>
                <?= Html::textInput('cost_per_unit', $itemModel->isNewRecord ? '' : number_format($itemModel->item->cost_per_unit, 2), ['disabled' => 'disabled', 'class' => 'form-control', 'id' => 'ppmp-item_cost']); ?>
            </div>
        </div>
    </div>
    
    <label for="quantity" class="control-label">Quantity</label>
    <div class="row">
        <div class="col-md-6 col-xs-12">
        <table class="table table-responsive table-bordered" style="width: 100%;">
            <?php if($months){ ?>
                <?php $i = 0; ?>
                <?php foreach($months as $month){ ?>
                    <?php if($i < 6){ ?>
                        <?= $form->field($itemBreakdowns[$month->id], "[$month->id]month_id")->hiddenInput(['value' => $month->id])->label(false) ?>
                    <tr>
                        <th><?= $month->month ?></th>
                        <td><?= $form->field($itemBreakdowns[$month->id], "[$month->id]quantity")->textInput(['type' => 'number', 'maxlength' => true, 'min' => 0, 'onkeyup' => 'getTotal()', 'value' => $itemBreakdowns[$month->id]->quantity > 0 ? $itemBreakdowns[$month->id]->quantity : 0])->label(false) ?></td>
                    </tr>
                    <?php } ?>
                    <?php $i++ ?>
                <?php } ?>
            <?php } ?>
        </table>
        </div>
        <div class="col-md-6 col-xs-12">
        <table class="table table-responsive table-bordered" style="width: 100%;">
            <?php if($months){ ?>
                <?php $i = 0; ?>
                <?php foreach($months as $month){ ?>
                    <?php if($i > 5){ ?>
                        <?= $form->field($itemBreakdowns[$month->id], "[$month->id]month_id")->hiddenInput(['value' => $month->id])->label(false) ?>
                    <tr>
                        <th><?= $month->month ?></th>
                        <td><?= $form->field($itemBreakdowns[$month->id], "[$month->id]quantity")->textInput(['type' => 'number', 'maxlength' => true, 'min' => 0, 'onkeyup' => 'getTotal()', 'value' => $itemBreakdowns[$month->id]->quantity > 0 ? $itemBreakdowns[$month->id]->quantity : 0])->label(false) ?></td>
                    </tr>
                    <?php } ?>
                    <?php $i++ ?>
                <?php } ?>
            <?php } ?>
        </table>
        </div>
    </div>
    
    <span class="pull-right">Total</span><br>
    <p class="panel-title pull-right" style="font-size: 35px !important;" id="total-per-item"></p>
    <p class="clearfix"></p>
    
    <div class="form-group">
        <?= Html::submitButton('Save Item', ['class' => 'btn btn-success', 'data' => ['disabled-text' => 'Please Wait']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
  $script = '
    function loadItems(id, activity_id, fund_source_id)
    {
        $.ajax({
            url: "'.Url::to(['/v1/ppmp/load-items']).'",
            data: {
                id: id,
                activity_id: activity_id,
                fund_source_id: fund_source_id,
            },
            beforeSend: function(){
                $("#items").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                $("#items").empty();
                $("#items").hide();
                $("#items").fadeIn("slow");
                $("#items").html(data);
            },
            error: function (err) {
                console.log(err);
            }
        });
    }

    function getTotal()
    {
        var cost_per_unit = parseInt($("#cost_per_unit").val());
        var jan = parseInt($("#itembreakdown-1-quantity").val());
        var feb = parseInt($("#itembreakdown-2-quantity").val());
        var mar = parseInt($("#itembreakdown-3-quantity").val());
        var apr = parseInt($("#itembreakdown-4-quantity").val());
        var may = parseInt($("#itembreakdown-5-quantity").val());
        var jun = parseInt($("#itembreakdown-6-quantity").val());
        var jul = parseInt($("#itembreakdown-7-quantity").val());
        var aug = parseInt($("#itembreakdown-8-quantity").val());
        var sep = parseInt($("#itembreakdown-9-quantity").val());
        var oct = parseInt($("#itembreakdown-10-quantity").val());
        var nov = parseInt($("#itembreakdown-11-quantity").val());
        var dec = parseInt($("#itembreakdown-12-quantity").val());

        var total = jan + feb + mar + apr + may + jun + jul + aug + sep + oct + nov + dec;

        grandTotal = total * cost_per_unit;

        $("#total-per-item").empty();
        $("#total-per-item").html(number_format(grandTotal, 2, ".", ","));       
    }

    function updateItemDetails(id)
    {
        $.ajax({
            url: "'.Url::to(['/v1/ppmp/unit-of-measure']).'",
            data: {
                    id: id,
                  }
        }).done(function(result) {
            $("#ppmp-item-unit_of_measure").empty();
            $("#ppmp-item-unit_of_measure").fadeIn("slow");
            $("#ppmp-item-unit_of_measure").val(result);

        });

        $.ajax({
            url: "'.Url::to(['/v1/ppmp/cost']).'",
            data: {
                    id: id,
                  }
        }).done(function(result) {
            $("#ppmp-item_cost").empty();
            $("#ppmp-item_cost").fadeIn("slow");
            $("#ppmp-item_cost").val(result);
        });

        $.ajax({
            url: "'.Url::to(['/v1/ppmp/cost-per-unit']).'",
            data: {
                    id: id,
                  }
        }).done(function(result) {
            $("#cost_per_unit").empty();
            $("#cost_per_unit").val(result);
        });

        getTotal();
    }

    $(document).ready(function() {
        getTotal();
        $("#ppmp-item-form").on("beforeSubmit", function(e) {
            e.preventDefault();
            var activity_id = $("#ppmpitem-activity_id").val();
            var fund_source_id = $("#ppmpitem-fund_source_id").val();
            var sub_activity_id = $("#ppmpitem-sub_activity_id-'.$itemModel->sub_activity_id.'").val();
            
            var form = $(this);
            var formData = form.serialize();

            $.ajax({
                url: form.attr("action"),
                type: form.attr("method"),
                data: formData,
                success: function (data) {
                    $("#create-item-modal").modal("toggle");
                    $(".modal-backdrop").remove();
                    loadItems('.$model->id.',activity_id,fund_source_id);
                    loadPpmpTotal('.$model->id.');
                    form.enableSubmitButtons();
                },
                error: function (err) {
                    console.log(err);
                }
            });

            return false;
        });
    });
  ';
  $this->registerJs($script, View::POS_END);
?>