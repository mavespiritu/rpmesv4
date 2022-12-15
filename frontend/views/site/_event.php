<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\web\View;
use slavkovrn\imagecarousel\ImageCarouselWidget;
?>

<style>
* {box-sizing: border-box}
body {font-family: Verdana, sans-serif; margin:0}
.mySlides {display: none}
img {vertical-align: middle;}

/* Slideshow container */
.slideshow-container {
  max-width: 1000px;
  position: relative;
  margin: auto;
}

/* Next & previous buttons */
.prev, .next {
  cursor: pointer;
  position: absolute;
  top: 50%;
  width: auto;
  padding: 16px;
  margin-top: -22px;
  color: white;
  font-weight: bold;
  font-size: 18px;
  transition: 0.6s ease;
  border-radius: 0 3px 3px 0;
  user-select: none;
}

/* Position the "next button" to the right */
.next {
  right: 0;
  border-radius: 3px 0 0 3px;
}

/* On hover, add a black background color with a little bit see-through */
.prev:hover, .next:hover {
  background-color: rgba(0,0,0,0.8);
}

/* Caption text */
.text {
  color: #f2f2f2;
  font-size: 15px;
  padding: 8px 12px;
  position: absolute;
  bottom: 8px;
  width: 100%;
  text-align: center;
}

/* Number text (1/3 etc) */
.numbertext {
  color: #f2f2f2;
  font-size: 12px;
  padding: 8px 12px;
  position: absolute;
  top: 0;
}

/* The dots/bullets/indicators */
.dot {
  cursor: pointer;
  height: 15px;
  width: 15px;
  margin: 0 2px;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
  transition: background-color 0.6s ease;
}

.active, .dot:hover {
  background-color: #717171;
}

/* Fading animation */
.fade {
  animation-name: fade;
  animation-duration: 1.5s;
}

@keyframes fade {
  from {opacity: .4} 
  to {opacity: 1}
}

/* On smaller screens, decrease text size */
@media only screen and (max-width: 300px) {
  .prev, .next,.text {font-size: 11px}
}
</style>

<script>
let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}    
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";  
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";  
  dots[slideIndex-1].className += " active";
}
</script>

<!-- HTML -->
<h4 class="text-center">Events</h4>
<div id="event">
<div class="slideshow-container">

<?php if(!empty($images)){ ?>
  <?php $idx = 1; ?>
  <?php foreach($images as $image){ ?>
        <div class="mySlides fade">
          <div class="numbertext">1 / 3</div>
          <img src="<?= Yii::getAlias('@eventUrlPath').'/'.$image['image'] ?>" style="width:100%">
        </div>
  <?php $idx ++ ?>
  <?php } ?>
<?php } ?>

<a class="prev" onclick="plusSlides(-1)">❮</a>
<a class="next" onclick="plusSlides(1)">❯</a>

</div>
<br>

<div style="text-align:center">
<?php if(!empty($images)){ ?>
  <?php $idy = 1; ?>
  <?php foreach($images as $image){ ?>
        <span class="dot" onclick="currentSlide(<?= $idy ?>)"></span> 
  <?php $idx ++ ?>
  <?php } ?>
<?php } ?>
</div>

</div>
<div class="row">
  <div class="col-md-12 col-xs-12">
    <div class="col-md-6 col-xs-12"><button class="btn btn-block btn-default" onclick="previousGraph('beneficiaries', '<?= $year ?>', '<?= $quarter ?>', '<?= $agency_id ?>', '<?= $category_id ?>', '<?= $sector_id ?>', '<?= $sub_sector_id ?>', '<?= $province_id ?>', '<?= $fund_source_id ?>')"><i class="fa fa-backward"></i> Previous</button></div>
    
    <div class="col-md-6 col-xs-12"><button class="btn btn-block btn-default" onclick="nextGraph('employment', '<?= $year ?>', '<?= $quarter ?>', '<?= $agency_id ?>', '<?= $category_id ?>', '<?= $sector_id ?>', '<?= $sub_sector_id ?>', '<?= $province_id ?>', '<?= $fund_source_id ?>')"><i class="fa fa-forward"></i> Next</button></div>
  </div>
</div>

