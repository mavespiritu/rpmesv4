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
<div class='site-index'>

    <div class='jumbotron'>
        <h2>Welcome to eRPMES!</h2>
    </div>
    <div class='body-content container'>
    <div class="acknowledgment-monitoring-report-search">

<?php $form = ActiveForm::begin([
    'id' => 'search-summary-monitoring-report-form'
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
            ]);
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

<div class="form-group pull-right">
        <?= Html::submitButton('Generate Data', ['class' => 'btn btn-primary', 'style' => 'margin-top: 5px;']) ?>
</div>
<div class="clearfix"></div>
<?php ActiveForm::end(); ?>
        <div class='row'>
            <div class='col-lg-4'>
                <h3 style='color: #3C8DBC;'><i class='fa fa-folder-open'></i>Events</h3>

                <p>Enroll your projects to keep track of progress physically and financially using the RPMES Forms.</p>
            </div>
            <div class='col-lg-6'>
                <h3 style='color: #3C8DBC;'><i class='fa fa-map-marker'></i>Heat Map</h3>

                <p>
                    <script src="https://code.highcharts.com/highcharts.js"></script>
                    <script src="https://code.highcharts.com/highcharts-more.js"></script>
                    <script src="https://code.highcharts.com/modules/exporting.js"></script>
                    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

                    <figure class="highcharts-figure">
                        <div id="container"></div>
                        <p class="highcharts-description">
                            Project count per Municipality depending on Provinces and Regionwide Projects
                        </p>
                    </figure>
                </p>
                <div id="container"></div>
            </div>
            <div class='col-lg-2'>
                <h3 style='color: #3C8DBC;'><i class='fa fa-area-chart'></i>Statistics</h3>

                <p>Your data and statistics will be showcased through graphs and charts.</p>
            </div>
        </div>
        <br>
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