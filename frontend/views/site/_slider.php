<?php
use yii\bootstrap\Carousel;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\web\View;
?>
<div style="height: 90%; width: 100%; display: flex; justify-content: center; align-items: center;">
<center><?= 
    Carousel::widget([
        'items' => $images
    ]);
?></center>
</div>
<div class="row">
  <div class="col-md-12 col-xs-12">
    <div class="col-md-3 col-xs-12"><button class="btn btn-block btn-default" onclick="previousGraph('beneficiaries', '<?= $year ?>', '<?= $quarter ?>')"><i class="fa fa-backward"></i> Previous</button></div>
    <div class="col-md-6 col-xs-12">
      
    </div>
    <div class="col-md-3 col-xs-12"><button class="btn btn-block btn-default" onclick="nextGraph('employment', '<?= $year ?>', '<?= $quarter ?>')"><i class="fa fa-forward"></i> Next</button></div>
  </div>
</div>