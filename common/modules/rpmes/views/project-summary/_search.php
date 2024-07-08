<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;
use yii\bootstrap\ButtonDropdown;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model common\modules\rpmes\models\ProjectFindingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-summary-search">

<?php $form = ActiveForm::begin([
    'id' => 'search-summary-accomplishment-form'
]); ?>

<div class="row">
    <div class="col-md-3 col-xs-12">
        <?= $form->field($model, 'year')->widget(Select2::classname(), [
            'data' => $years,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'year-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
            ])->label('Year');
        ?>
    </div>
    <div class="col-md-3 col-xs-12">
        <?= $form->field($model, 'quarter')->widget(Select2::classname(), [
            'data' => ['Q1' => 'Q1', 'Q2' => 'Q2', 'Q3' => 'Q3', 'Q4' => 'Q4'],
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'quarter-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
            ])->label('Quarter');
        ?>
    </div>
    <div class="col-md-3 col-xs-12">
        <?= $form->field($model, 'agency_id')->widget(Select2::classname(), [
            'data' => $agencies,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'agency-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
            ])->label('Agency');
        ?>
    </div>
    <div class="col-md-3 col-xs-12">
        <?= $form->field($model, 'sector_id')->widget(Select2::classname(), [
            'data' => $sectors,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'sector-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
            ])->label('Sector');
        ?>
    </div>
</div>
<div class="row">
    <div class="col-md-3 col-xs-12">
        <?= $form->field($model, 'mode_of_implementation_id')->widget(Select2::classname(), [
            'data' => $modes,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'mode-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
            ])->label('Mode of Implementation');
        ?>
    </div>
    <div class="col-md-3 col-xs-12">
        <?= $form->field($model, 'region_id')->widget(Select2::classname(), [
            'data' => $regions,
            'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'region-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
            'pluginEvents'=>[
                'select2:select select2:unselect'=>'
                    function(){
                        $.ajax({
                            url: "'.Url::to(['/rpmes/project/province-list-single']).'",
                            data: {
                                    id: this.value,
                                }
                        }).done(function(result) {
                            $(".province-select").html("").select2({ data:result, multiple:false, theme:"krajee", width:"100%",placeholder:"Select one", allowClear: true});
                            $(".province-select").select2("val","");
                        });
                    }'

            ]
            ]);
        ?>
    </div>
    <div class="col-md-3 col-xs-12">
        <?= $form->field($model, 'province_id')->widget(Select2::classname(), [
            'data' => $provinces,
            'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'province-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
            ]);
        ?>
    </div>
    <div class="col-md-3 col-xs-12">
        <?= $form->field($model, 'fund_source_id')->widget(Select2::classname(), [
            'data' => $fundSources,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'fund-source-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
            ])->label('Fund Source');
        ?>
    </div>
</div>
<div class="row">
    
    <div class="col-md-3 col-xs-12">
        <?= $form->field($model, 'grouping')->widget(Select2::classname(), [
            'data' => $sorts,
            'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'sort-select'],
            'pluginOptions' => [
                'allowClear' =>  true,
            ],
            ])->label('Grouping');
        ?>
    </div>
</div>

<div class="form-group pull-right">
    <?= Html::a('Reset', ['/rpmes/project-summary'], ['class' => 'btn btn-outline-secondary']) ?>
    <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
</div>
<div class="clearfix"></div>

<?php ActiveForm::end(); ?>

</div>
<?php
    $script = '
    $("#search-summary-accomplishment-form").on("beforeSubmit", function (e) {
        e.preventDefault();
     
        var form = $(this);
        var formData = form.serialize();
        
        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            beforeSend: function(){
                $("#summary-accomplishment-table").html("<div class=\"text-center\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                console.log(this.data);
                $("#summary-accomplishment-table").empty();
                $("#summary-accomplishment-table").hide();
                $("#summary-accomplishment-table").fadeIn("slow");
                $("#summary-accomplishment-table").html(data);
            },
            error: function (err) {
                console.log(err);
            }
        });      

        return false;
    });
    ';

    $this->registerJs($script, View::POS_END);
?>

