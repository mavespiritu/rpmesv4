<div id="heat"></div>
<script>

var map = AmCharts.makeChart( "heat", {
  "type": "map",
  "theme": "light",
  "colorSteps": 10,
  "responsive": {
    "enabled": true
  },
  "dataProvider": {
    "map": "philippinesLow",
    "areas": <?= $data ?>,
    "zoomLatitude": 17.1818,
    "zoomLongitude": 120.4092,
    "zoomLevel": 5,
  },

  "imagesSettings": {
    "color": "#ff0303",
  },

  "valueLegend": {
    "right": 10,
    "minValue": "Least",
    "maxValue": "Many"
  },

  "export": {
    "enabled": true,
    "bottom": 30,
    "minZoomLevel": 0.25,
    "gridHeight": 100,
    "gridAlpha": 0.1,
    "gridBackgroundAlpha": 0,
    "gridColor": "#FFFFFF",
    "draggerAlpha": 1,
    "buttonCornerRadius": 2,

  },
   "zoomControl": {
    "zoomControlEnabled": true,
    "homeButtonEnabled": true,
    "panControlEnabled": false,
    "minZoomLevel": 0.25,
    "gridHeight": 200,
    "gridAlpha": 0.1,
    "gridBackgroundAlpha": 0,
    "gridColor": "#FFFFFF",
    "draggerAlpha": 1,
    "buttonCornerRadius": 2,
    },
} );
</script>
<style>
#heat {
  width: 100%;
  height: 100%;
  position: 'sticky';
}
</style>