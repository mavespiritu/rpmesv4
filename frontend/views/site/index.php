<?php
use yii\helpers\Html;
use frontend\assets\AppAsset;
use yii\web\View;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Url;

$appAsset = frontend\assets\AppAsset::register($this);
/* @var $this yii\web\View */

$this->title = 'eRPMES';
?>
<style>
#filterForm {
  display : none;
}
</style>
<div class='site-index'>

        <h2 align=center>Welcome to eRPMES Dashboard!</h2>

    <div class='body-content container'>
    <button onclick="showHideFilter()" class='btn btn-lg btn-primary' align=right>&#8595; Filter</button>
<div id="filterForm">
<hr>
<?php $form = ActiveForm::begin([
    'id' => 'search-dashboard-form'
]); ?>
    <div class="row">
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'year')->widget(Select2::classname(), [
                'data' => $years,
                'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'year-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Year *');
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?= $form->field($model, 'quarter')->widget(Select2::classname(), [
                'data' => $quarters,
                'options' => ['multiple' => false, 'placeholder' => 'Select One', 'class'=>'quarter-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ])->label('Quarter *');
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
            <?= $form->field($model, 'category_id')->widget(Select2::classname(), [
                'data' => $categories,
                'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'category-select'],
                'pluginOptions' => [
                    'allowClear' =>  true,
                ],
                ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-xs-12">
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
        </div>
        <div class="col-md-3 col-xs-12">
                <?= $form->field($model, 'sub_sector_id')->widget(Select2::classname(), [
                    'data' => $subSectors,
                    'options' => ['multiple' => false, 'placeholder' => 'Select one', 'class'=>'sub-sector-select'],
                    'pluginOptions' => [
                        'allowClear' =>  true,
                    ],
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
    <div class="form-group pull-right">
        <?= Html::submitButton('Generate Data', ['class' => 'btn btn-primary', 'style' => 'margin-top: 5px;']) ?>
    </div>
    <div class="clearfix"></div>
</div>

<hr>
<?php ActiveForm::end(); ?>

        <div class='row'>
            <div class='col-lg-6'>
                <h3 style='color: #3C8DBC;'><i class='fa fa-map-marker'></i>Heat Map</h3>

                <div id="heatmap-table">

                </div>
            </div>
            <div class='col-lg-6'>
                <h3 style='color: #3C8DBC;'><i class='fa fa-area-chart'></i>Statistics</h3>

                <div id="graphs-table">

            </div>
        </div>
        <div class='row'>
            <div class='col-lg-12'>
                <h3 style='color: #3C8DBC;'><i class='fa fa-folder-open'></i>Events</h3>

                <p>Enroll your projects to keep track of progress physically and financially using the RPMES Forms.</p>
            </div>
        </div>
        <hr>
        <h2 class='text-center'>About</h2>
        <br>
        <div class='row'>
            <div class='col-lg-4'>
                <p align=center><?= Html::a('Contact Us', ['/site/contact'],['class' => 'btn btn-lg btn-primary'])?></p>
            </div>
            <div class='col-lg-4'>
                <p align=center><?= Html::a('Visit RDC Website', ['https://ilocos.neda.gov.ph/rdc-2/'],['class' => 'btn btn-lg btn-primary'])?></p>
            </div>
            <div class='col-lg-4'>
                <p align=center><?= Html::a('Like us on Facebook', ['https://www.facebook.com/nedaregion1'],['class' => 'btn btn-lg btn-primary'],['target' => '_blank'])?></p>
            </div>
        </div>
    </div>
</div>
<?php
    $script = '

    function showHideFilter() {
        var x = document.getElementById("filterForm");
        if (x.style.display === "none") {
          x.style.display = "block";
        } else {
          x.style.display = "none";
        }
      }

    $("#search-dashboard-form").on("beforeSubmit", function (e) {
        e.preventDefault();
     
        var form = $(this);
        var formData = form.serialize();
        
        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            beforeSend: function(){
                $("#graphs-table").html("<div class=\"text-center\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                console.log(this.data);
                $("#graphs-table").empty();
                $("#graphs-table").hide();
                $("#graphs-table").fadeIn("slow");
                $("#graphs-table").html(data);
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