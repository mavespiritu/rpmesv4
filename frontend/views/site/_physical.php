<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\web\View;
?>
<!-- Styles -->
<style>
#physical {
  width: 100%;
  height: 90%;
}
</style>

<!-- Chart code -->
<script>
am5.ready(function() {



}); // end am5.ready()
</script>

<!-- HTML -->
<h4 class="text-center">Physical Status</h4>
<div id="physical"></div>
<div class="row">
  <div class="col-md-12 col-xs-12">
    <div class="col-md-3 col-xs-12"><button class="btn btn-block btn-default" onclick="previousGraph('beneficiaries', '<?= $year ?>', '<?= $quarter ?>')"><i class="fa fa-backward"></i>Previous</button></div>
    <div class="col-md-6 col-xs-12">
      <button class="btn btn-block btn-default" id="physical-button" value="<?= Url::to(['/site/physical-data', 
                'year' => $year,
                'quarter' => $quarter
      ]) ?>">View Tabular Data</button>
    </div>
    <div class="col-md-3 col-xs-12"><button class="btn btn-block btn-default" onclick="nextGraph('image-slider', '<?= $year ?>', '<?= $quarter ?>')"><i class="fa fa-forward"></i>Next</button></div>
  </div>
</div>

<?php
  Modal::begin([
    'id' => 'physical-modal',
    'size' => "modal-md",
    'header' => '<div id="physical-modal-header"><h4>Physical Status</h4></div>',
    'options' => ['tabindex' => false],
  ]);
  echo '<div id="phsyical-modal-content"></div>';
  Modal::end();
?>

<?php
    $script = '
        $(document).ready(function(){
            $("#physical-button").click(function(){
              $("#physical-modal").modal("show").find("#physical-modal-content").load($(this).attr("value"));
            });
        });     
    ';

    $this->registerJs($script, View::POS_END);
?>