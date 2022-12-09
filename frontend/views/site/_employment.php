<div id="employmentChart"></div>

<?php
    $script = "
        am5.ready(function() {
            var root = am5.Root.new('financialChart');
            
            root.setThemes([
              am5themes_Animated.new(root)
            ]);
            
            var root1 = am5.Root.new('employmentChart');
            root1.setThemes([
              am5themes_Animated.new(root1)
            ]);
            var chart1 = root1.container.children.push(
              am5xy.XYChart.new(root1, {
                panX: false,
                panY: false,
                wheelX: 'panX',
                wheelY: 'zoomX',
                layout: root1.horizontalLayout,
                arrangeTooltips: false
              })
            );
            root1.numberFormatter.set('numberFormat', '#.#s".'"'."%');
            var legend1 = chart1.children.push(
              am5.Legend.new(root1, {
                centerY: am5.p50,
                y: am5.p50,
                //useDefaultMarker: true,
                layout: root1.verticalLayout
              })
            );
            legend1.markers.template.setAll({
              width: 50,
              height: 50
            })
            var data1 = [".$scriptEmployment."];
            var yAxis = chart1.yAxes.push(
              am5xy.CategoryAxis.new(root1, {
                categoryField: 'category',
                renderer: am5xy.AxisRendererY.new(root1, {
                  inversed: true,
                  cellStartLocation: 0.1,
                  cellEndLocation: 0.9
                })
              })
            );
            var yRenderer = yAxis.get('renderer');
            yRenderer.grid.template.setAll({
              visible: false
            });
            yAxis.data.setAll(data1);
            var xAxis = chart1.xAxes.push(
              am5xy.ValueAxis.new(root1, {
                calculateTotals: true,
                min: -100,
                max: 100,
                renderer: am5xy.AxisRendererX.new(root1, {
                  minGridDistance: 80
                })
              })
            );
            var xRenderer = xAxis.get('renderer');
            xRenderer.grid.template.setAll({
              visible: false
            });
            var rangeDataItem = xAxis.makeDataItem({
              value: 0
            });
            var range = xAxis.createAxisRange(rangeDataItem);
            range.get('grid').setAll({
              stroke: am5.color(0xeeeeee),
              strokeOpacity: 1,
              location: 1,
              visible: true
            });
            function createSeries(field, name, color, icon, inlegend) {
              var series1 = chart1.series.push(
                am5xy.ColumnSeries.new(root1, {
                  xAxis: xAxis,
                  yAxis: yAxis,
                  name: name,
                  valueXField: field,
                  categoryYField: 'category',
                  sequencedInterpolation: true,
                  fill: color,
                  stroke: color,
                  clustered: false
                })
              );
            
              series1.columns.template.setAll({
                height: 50,
                fillOpacity: 0,
                strokeOpacity: 0
              });
              if (icon) {
                series1.columns.template.set('fillPattern', am5.PathPattern.new(root1, {
                  color: color,
                  repetition: 'repeat-x',
                  width: 50,
                  height: 50,
                  fillOpacity: 0,
                  svgPath: icon
                }));
              }
            
              series1.data.setAll(data1);
              series1.appear();
            
              if (inlegend) {
                legend1.data.push(series1);
              }
            
              return series1;
            }
            var femaleColor = am5.color(0xf25f5c);
            var maleColor = am5.color(0x247ba0);
            var placeholderColor = am5.color(0xeeeeee);
            var maleIcon = 'M 25.1 10.7 c 2.1 0 3.7 -1.7 3.7 -3.7 c 0 -2.1 -1.7 -3.7 -3.7 -3.7 c -2.1 0 -3.7 1.7 -3.7 3.7 C 21.4 9 23 10.7 25.1 10.7 z M 28.8 11.5 H 25.1 h -3.7 c -2.8 0 -4.7 2.5 -4.7 4.8 V 27.7 c 0 2.2 3.1 2.2 3.1 0 V 17.2 h 0.6 v 28.6 c 0 3 4.2 2.9 4.3 0 V 29.3 h 0.7 h 0.1 v 16.5 c 0.2 3.1 4.3 2.8 4.3 0 V 17.2 h 0.5 v 10.5 c 0 2.2 3.2 2.2 3.2 0 V 16.3 C 33.5 14 31.6 11.5 28.8 11.5 z';
            var femaleIcon = 'M 18.4 15.1 L 15.5 25.5 c -0.6 2.3 2.1 3.2 2.7 1 l 2.6 -9.6 h 0.7 l -4.5 16.9 H 21.3 v 12.7 c 0 2.3 3.2 2.3 3.2 0 V 33.9 h 1 v 12.7 c 0 2.3 3.1 2.3 3.1 0 V 33.9 h 4.3 l -4.6 -16.9 h 0.8 l 2.6 9.6 c 0.7 2.2 3.3 1.3 2.7 -1 l -2.9 -10.4 c -0.4 -1.2 -1.8 -3.3 -4.2 -3.4 h -4.7 C 20.1 11.9 18.7 13.9 18.4 15.1 z M 28.6 7.2 c 0 -2.1 -1.6 -3.7 -3.7 -3.7 c -2 0 -3.7 1.7 -3.7 3.7 c 0 2.1 1.6 3.7 3.7 3.7 C 27 10.9 28.6 9.2 28.6 7.2 z';
            createSeries('maleMax', 'Male', placeholderColor, maleIcon, false);
            createSeries('male', 'Male', maleColor, maleIcon, true);
            createSeries('femaleMax', 'Female', placeholderColor, femaleIcon, false);
            createSeries('female', 'Female', femaleColor, femaleIcon, true);
            chart1.appear(1000, 100);
                    });";

    $this->registerJs($script, View::POS_END);
?>

<style>
#employmentChart {
  width: 100%;
  height: 500px;
}
</style>