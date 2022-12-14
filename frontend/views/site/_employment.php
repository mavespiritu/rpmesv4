<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\web\View;
?>
<!-- Styles -->
<style>
#employment {
  width: 100%;
  height: 90%;
  position: 'sticky';
}
</style>


<!-- Chart code -->
<script>
am5.ready(function() {

// Create root element
// https://www.amcharts.com/docs/v5/getting-started/#Root_element
var root = am5.Root.new("employment");

// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
root.setThemes([
  am5themes_Animated.new(root)
]);

// Create chart
// https://www.amcharts.com/docs/v5/charts/xy-chart/
var chart = root.container.children.push(am5xy.XYChart.new(root, {
  panX: false,
  panY: false,
  wheelX: "panX",
  wheelY: "zoomX",
  layout: root.verticalLayout
}));

// Add scrollbar
// https://www.amcharts.com/docs/v5/charts/xy-chart/scrollbars/
chart.set("scrollbarX", am5.Scrollbar.new(root, {
  orientation: "horizontal"
}));

var data = <?= $data ?>;


// Create axes
// https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
  categoryField: "sector",
  renderer: am5xy.AxisRendererX.new(root, {}),
  tooltip: am5.Tooltip.new(root, {})
}));

xAxis.data.setAll(data);

var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
  min: 0,
  max: 100,
  numberFormat: "#.##'%'",
  strictMinMax: true,
  calculateTotals: true,
  renderer: am5xy.AxisRendererY.new(root, {})
}));


// Add legend
// https://www.amcharts.com/docs/v5/charts/xy-chart/legend-xy-series/
var legend = chart.children.push(am5.Legend.new(root, {
  centerX: am5.p50,
  x: am5.p50
}));


// Add series
// https://www.amcharts.com/docs/v5/charts/xy-chart/series/
function makeSeries(name, fieldName) {
  var series = chart.series.push(am5xy.ColumnSeries.new(root, {
    name: name,
    stacked: true,
    xAxis: xAxis,
    yAxis: yAxis,
    valueYField: fieldName,
    valueYShow: "valueYTotalPercent",
    categoryXField: "sector"
  }));

  series.columns.template.setAll({
    tooltipText: "{name}, {categoryX}:{valueYTotalPercent.formatNumber('#.##')}%",
    tooltipY: am5.percent(10)
  });
  series.data.setAll(data);

  // Make stuff animate on load
  // https://www.amcharts.com/docs/v5/concepts/animations/
  series.appear();

  series.bullets.push(function () {
    return am5.Bullet.new(root, {
      sprite: am5.Label.new(root, {
        text: "{valueYTotalPercent.formatNumber('#.##')}%",
        fill: root.interfaceColors.get("alternativeText"),
        centerY: am5.p50,
        centerX: am5.p50,
        populateText: true
      })
    });
  });

  legend.data.push(series);
}

makeSeries("Male", "male");
makeSeries("Female", "female");


// Make stuff animate on load
// https://www.amcharts.com/docs/v5/concepts/animations/
chart.appear(1000, 100);

}); // end am5.ready()
</script>

<!-- HTML -->
<h4 class="text-center">Employment Generated Per Sector By Sex</h4>
<div id="employment"></div>
<div class="row">
  <div class="col-md-12 col-xs-12">
    <div class="col-md-3 col-xs-12"><button class="btn btn-block btn-default" onclick="previousGraph('event', '<?= $year ?>', '<?= $quarter ?>', '<?= $agency_id ?>', '<?= $category_id ?>', '<?= $sector_id ?>', '<?= $sub_sector_id ?>', '<?= $province_id ?>', '<?= $fund_source_id ?>')"><i class="fa fa-backward"></i>Previous</button></div>
    <div class="col-md-6 col-xs-12">
      <button class="btn btn-block btn-default" id="employment-button" value="<?= Url::to(['/site/employment-data', 
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
    <div class="col-md-3 col-xs-12"><button class="btn btn-block btn-default" onclick="nextGraph('disbursement-by-category', '<?= $year ?>', '<?= $quarter ?>', '<?= $agency_id ?>', '<?= $category_id ?>', '<?= $sector_id ?>', '<?= $sub_sector_id ?>', '<?= $province_id ?>', '<?= $fund_source_id ?>')"><i class="fa fa-forward"></i>Next</button></div>
  </div>
</div>

<?php
  Modal::begin([
    'id' => 'employment-modal',
    'size' => "modal-md",
    'header' => '<div id="employment-modal-header"><h4>Employment Generated Per Sector By Sex</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="employment-modal-content"></div>';
  Modal::end();
?>

<?php
    $script = '
        $(document).ready(function(){
            $("#employment-button").click(function(){
              $("#employment-modal").modal("show").find("#employment-modal-content").load($(this).attr("value"));
            });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>
