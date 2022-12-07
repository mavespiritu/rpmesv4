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

<?= $form->field($model, 'agency_id')->widget(Select2::classname(), [
                'data' => $agencies,
                'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'agency-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Agency');
            ?>
    
<?= $form->field($model, 'category_id')->widget(Select2::classname(), [
                'data' => $categories,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'category-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>

<?= $form->field($model, 'sector_id')->widget(Select2::classname(), [
                    'data' => $sectors,
                    'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'sector-select'],
                    'pluginOptions' => [
                        'allowClear' =>  true,
                    ],
                    'pluginEvents'=>[
                        'select2:select'=>'
                            function(){
                                $.ajax({
                                    url: "'.Url::to(['/rpmes/project/sub-sector-list']).'",
                                    data: {
                                            id: this.value,
                                        }
                                }).done(function(result) {
                                    $(".sub-sector-select").html("").select2({ data:result, theme:"krajee", width:"100%",placeholder:"Select one", allowClear: true});
                                    $(".sub-sector-select").select2("val","");
                                });
                            }'
                        
                    ]
                    ]);
                ?>

<?= $form->field($model, 'sub_sector_id')->widget(Select2::classname(), [
                    'data' => $subSectors,
                    'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'sub-sector-select'],
                    'pluginOptions' => [
                        'allowClear' =>  true,
                    ],
                    ]);
                ?>
    
<?= $form->field($model, 'province_id')->widget(Select2::classname(), [
                'data' => $provinces,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'province-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>

<?= $form->field($model, 'fund_source_id')->widget(Select2::classname(), [
                'data' => $fundSources,
                'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'fund-source-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Fund Source');
            ?>

<div class="form-group pull-right">
    <?= Html::submitButton('Generate Data', ['class' => 'btn btn-primary', 'style' => 'margin-top: 5px;']) ?>
</div>

<?php ActiveForm::end(); ?>