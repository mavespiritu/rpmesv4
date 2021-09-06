<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use kartik\select2\Select2;
    use yii\widgets\DetailView;
    use yii\web\View;
    use yii\widgets\MaskedInput;
    use yii\helpers\Url;
?>
<div id="appropriation-allocation-form">
    <h4>Allocation Form</h4>
    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-responsive table-condensed table-bordered'],
        'attributes' => [
            [
                'label' => 'Object',
                'attribute' => 'appropriationObj.obj.objectTitle'
            ],
            [
                'label' => 'Program',
                'attribute' => 'appropriationPap.pap.codeAndTitle'
            ],
            [
                'label' => 'Fund Source',
               'attribute' => 'appropriationPap.fundSource.code'
            ],
            [
                'label' => 'Allocated',
                'attribute' => 'amount',
                'value' => function($model){ return number_format($model->amount, 2); }
            ]
        ],
    ]) ?>
    <table class="table table-responsive" style="border: none;">
        <thead>
            <tr>
                <th>Division</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
        <?php if($offices){ ?>
            <?php foreach($offices as $office){ ?>
                <tr>
                    <td><?= $office->abbreviation ?></td>
                    <td>
                        <?php $form = ActiveForm::begin([
                            'options' => ['class' => 'disable-submit-buttons'],
                            'id' => $office->id.'-form',
                            'enableClientValidation' => true,
                            'enableAjaxValidation' => true,
                            'validationUrl'=>['/v1/nep/validate-amount'],
                            'validateOnSubmit' => true,
                        ]); ?>
                        <?= $form->field($items[$office->id], 'appropriation_item_id')->hiddenInput(['value' => $items[$office->id]->appropriation_item_id])->label(false) ?>
                        <?= $form->field($items[$office->id], 'office_id')->hiddenInput(['value' => $items[$office->id]->office_id])->label(false) ?>
                        <div class="flex-start">
                            <div style="margin-right: 10px; width: 60%;">
                                <?= $form->field($items[$office->id], 'amount')->widget(MaskedInput::classname(), [
                                    'options' => [
                                        'id' => $office->id.'-input',
                                        'autocomplete' => 'off'
                                    ],
                                    'clientOptions' => [
                                        'alias' =>  'decimal',
                                        'removeMaskOnSubmit' => true,
                                        'groupSeparator' => ',',
                                        'autoGroup' => true
                                    ],
                                ])->label(false) ?>
                            </div>
                            <div>
                                <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-block', 'data' => ['disabled-text' => 'Please Wait']]) ?>
                            </div>
                            <div id="<?= $office->id ?>-alert-container"></div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </td>
                </tr>
                <?php
                $script = '
                    $(document).ready(function() {
                        $("#'.$office->id.'-form").on("beforeSubmit", function(e) {
                            e.preventDefault();
                            var form = $(this);
                            var formData = form.serialize();

                            $.ajax({
                                url: form.attr("action"),
                                type: form.attr("method"),
                                data: formData,
                                success: function (data) {
                                    $("#'.$office->id.'-alert-container").empty();
                                    $("#'.$office->id.'-alert-container").hide();
                                    $("#'.$office->id.'-alert-container").fadeIn("slow");
                                    $("#'.$office->id.'-alert-container").html("<p>&nbsp;&nbsp;&nbsp;Record Saved.</p>");
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
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>
