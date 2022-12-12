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
var chart = root.container.children.push(
  am5xy.XYChart.new(root, {
    panX: false,
    panY: false,
    wheelX: "panX",
    wheelY: "zoomX",
    layout: root.horizontalLayout,
    arrangeTooltips: false
  })
);

// Use only absolute numbers
root.numberFormatter.set("numberFormat", "#.#s'%");

// Add legend
// https://www.amcharts.com/docs/v5/charts/xy-chart/legend-xy-series/
var legend = chart.children.push(
  am5.Legend.new(root, {
    centerY: am5.p50,
    y: am5.p50,
    //useDefaultMarker: true,
    layout: root.verticalLayout
  })
);

legend.markers.template.setAll({
  width: 50,
  height: 50
})

// Data
var data = <?= $data ?>;

// Create axes
// https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
var yAxis = chart.yAxes.push(
  am5xy.CategoryAxis.new(root, {
    categoryField: "category",
    renderer: am5xy.AxisRendererY.new(root, {
      inversed: true,
      cellStartLocation: 0.1,
      cellEndLocation: 0.9
    })
  })
);

var yRenderer = yAxis.get("renderer");
yRenderer.grid.template.setAll({
  visible: false
});

yAxis.data.setAll(data);

var xAxis = chart.xAxes.push(
  am5xy.ValueAxis.new(root, {
    calculateTotals: true,
    min: -100,
    max: 100,
    renderer: am5xy.AxisRendererX.new(root, {
      minGridDistance: 80
    })
  })
);

var xRenderer = xAxis.get("renderer");
xRenderer.grid.template.setAll({
  visible: false
});

var rangeDataItem = xAxis.makeDataItem({
  value: 0
});

var range = xAxis.createAxisRange(rangeDataItem);

range.get("grid").setAll({
  stroke: am5.color(0xeeeeee),
  strokeOpacity: 1,
  location: 1,
  visible: true
});

// Add series
// https://www.amcharts.com/docs/v5/charts/xy-chart/series/
function createSeries(field, name, color, icon, inlegend) {
  var series = chart.series.push(
    am5xy.ColumnSeries.new(root, {
      xAxis: xAxis,
      yAxis: yAxis,
      name: name,
      valueXField: field,
      categoryYField: "category",
      sequencedInterpolation: true,
      fill: color,
      stroke: color,
      clustered: false
    })
  );

  series.columns.template.setAll({
    height: 50,
    fillOpacity: 0,
    strokeOpacity: 0
  });
  
  if (icon) {
    series.columns.template.set("fillPattern", am5.PathPattern.new(root, {
      color: color,
      repetition: "repeat-x",
      width: 50,
      height: 50,
      fillOpacity: 0,
      svgPath: icon
    }));
  }

  series.data.setAll(data);
  series.appear();

  if (inlegend) {
    legend.data.push(series);
  }

  return series;
}

var femaleColor = am5.color(0xf25f5c);
var maleColor = am5.color(0x247ba0);
var placeholderColor = am5.color(0xeeeeee);

var maleIcon = "M 25.1 10.7 c 2.1 0 3.7 -1.7 3.7 -3.7 c 0 -2.1 -1.7 -3.7 -3.7 -3.7 c -2.1 0 -3.7 1.7 -3.7 3.7 C 21.4 9 23 10.7 25.1 10.7 z M 28.8 11.5 H 25.1 h -3.7 c -2.8 0 -4.7 2.5 -4.7 4.8 V 27.7 c 0 2.2 3.1 2.2 3.1 0 V 17.2 h 0.6 v 28.6 c 0 3 4.2 2.9 4.3 0 V 29.3 h 0.7 h 0.1 v 16.5 c 0.2 3.1 4.3 2.8 4.3 0 V 17.2 h 0.5 v 10.5 c 0 2.2 3.2 2.2 3.2 0 V 16.3 C 33.5 14 31.6 11.5 28.8 11.5 z";
var femaleIcon = "M 18.4 15.1 L 15.5 25.5 c -0.6 2.3 2.1 3.2 2.7 1 l 2.6 -9.6 h 0.7 l -4.5 16.9 H 21.3 v 12.7 c 0 2.3 3.2 2.3 3.2 0 V 33.9 h 1 v 12.7 c 0 2.3 3.1 2.3 3.1 0 V 33.9 h 4.3 l -4.6 -16.9 h 0.8 l 2.6 9.6 c 0.7 2.2 3.3 1.3 2.7 -1 l -2.9 -10.4 c -0.4 -1.2 -1.8 -3.3 -4.2 -3.4 h -4.7 C 20.1 11.9 18.7 13.9 18.4 15.1 z M 28.6 7.2 c 0 -2.1 -1.6 -3.7 -3.7 -3.7 c -2 0 -3.7 1.7 -3.7 3.7 c 0 2.1 1.6 3.7 3.7 3.7 C 27 10.9 28.6 9.2 28.6 7.2 z";

createSeries("maleMax", "Male", placeholderColor, maleIcon, false);
createSeries("male", "Male", maleColor, maleIcon, true);
createSeries("femaleMax", "Female", placeholderColor, femaleIcon, false);
createSeries("female", "Female", femaleColor, femaleIcon, true);


// Make stuff animate on load
// https://www.amcharts.com/docs/v5/concepts/animations/
chart.appear(1000, 100);

}); // end am5.ready()
</script>

<!-- HTML -->
<h4 class="text-center">Employment Generated Per Category By Sex</h4>
<div id="employment"></div>
<div class="row">
  <div class="col-md-12 col-xs-12">
    <div class="col-md-3 col-xs-12"><button class="btn btn-block btn-default" onclick="previousGraph('project-implementation', '<?= $year ?>', '<?= $quarter ?>', '<?= $agency_id ?>', '<?= $category_id ?>', '<?= $sector_id ?>', '<?= $sub_sector_id ?>', '<?= $province_id ?>', '<?= $fund_source_id ?>')"><i class="fa fa-backward"></i> Previous Graph</button></div>
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
    <div class="col-md-3 col-xs-12"><button class="btn btn-block btn-default" onclick="nextGraph('disbursement-by-category', '<?= $year ?>', '<?= $quarter ?>', '<?= $agency_id ?>', '<?= $category_id ?>', '<?= $sector_id ?>', '<?= $sub_sector_id ?>', '<?= $province_id ?>', '<?= $fund_source_id ?>')"><i class="fa fa-forward"></i> Next Graph</button></div>
  </div>
</div>

<?php
  Modal::begin([
    'id' => 'employment-modal',
    'size' => "modal-md",
    'header' => '<div id="employment-modal-header"><h4>Employment Generated Per Category By Sex</h4></div>',
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