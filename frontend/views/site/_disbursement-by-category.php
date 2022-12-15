<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\web\View;
?>
<!-- Styles -->
<style>
#disbursement {
  width: 100%;
  height: 90%;
}
</style>

<!-- Chart code -->
<script>
am5.ready(function() {

// Create root element
// https://www.amcharts.com/docs/v5/getting-started/#Root_element
var root = am5.Root.new("disbursement");

// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
root.setThemes([
  am5themes_Animated.new(root)
]);

// Create chart
// https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/
var chart = root.container.children.push(
  am5percent.PieChart.new(root, {
    endAngle: 270
  })
);

// Create series
// https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Series
var series = chart.series.push(
  am5percent.PieSeries.new(root, {
    valueField: "value",
    categoryField: "sector",
    endAngle: 270
  })
);

var bgColor = root.interfaceColors.get("background");

series.slices.template.setAll({
  tooltipText:
    "{sector}: {valuePercentTotal.formatNumber('0.00')}% ({value})"
});

series.states.create("hidden", {
  endAngle: -90
});

// Set data
// https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Setting_data
series.data.setAll(<?= $data ?>);

series.appear(1000, 100);

}); // end am5.ready()
</script>

<!-- HTML -->
<h4 class="text-center">Total Expenditure Per Sector</h4>
<div id="disbursement"></div>
<div class="row">
  <div class="col-md-12 col-xs-12">
    <div class="col-md-3 col-xs-12"><button class="btn btn-block btn-default" onclick="previousGraph('employment', '<?= $year ?>', '<?= $quarter ?>', '<?= $agency_id ?>', '<?= $category_id ?>', '<?= $sector_id ?>', '<?= $sub_sector_id ?>', '<?= $province_id ?>', '<?= $fund_source_id ?>')"><i class="fa fa-backward"></i> Previous</button></div>
    <div class="col-md-6 col-xs-12">
      <button class="btn btn-block btn-default" id="disbursement-button" value="<?= Url::to(['/site/disbursement-by-category-data', 
                'year' => $year,
                'quarter' => $quarter, 
                'agency_id' => $agency_id, 
                'category_id' => $category_id, 
                'sector_id' => $sector_id, 
                'sub_sector_id' => $sub_sector_id, 
                'province_id' => $province_id, 
                'fund_source_id' => $fund_source_id
      ]) ?>">View Tabular Data</button>
    </div>
    <div class="col-md-3 col-xs-12"><button class="btn btn-block btn-default" onclick="nextGraph('project-implementation', '<?= $year ?>', '<?= $quarter ?>', '<?= $agency_id ?>', '<?= $category_id ?>', '<?= $sector_id ?>', '<?= $sub_sector_id ?>', '<?= $province_id ?>', '<?= $fund_source_id ?>')"><i class="fa fa-forward"></i> Next</button></div>
  </div>
</div>

<?php
  Modal::begin([
    'id' => 'disbursement-modal',
    'size' => "modal-md",
    'header' => '<div id="disbursement-modal-header"><h4>Total Expenditures Per Sector</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="disbursement-modal-content"></div>';
  Modal::end();
?>

<?php
    $script = '
        $(document).ready(function(){
            $("#disbursement-button").click(function(){
              $("#disbursement-modal").modal("show").find("#disbursement-modal-content").load($(this).attr("value"));
            });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>