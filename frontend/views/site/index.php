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
        <?= $this->render('_dashboard-filter', [
                        'model' => $model,
                        'years' => $years,
                        'quarters' => $quarters,
                        'agencies' => $agencies,
                        'sectors' => $sectors,
                        'subSectors' => $subSectors,
                        'categories' => $categories,
                        'provinces' => $provinces,
                        'fundSources' => $fundSources,
                    ]) ?>

        <div class='row'>
            <div class='col-lg-6'>
                <h3 style='color: #3C8DBC;'><i class='fa fa-map-marker'></i>Heat Map</h3>

                <div id="heatmap-table">

                </div>
            </div>
            <div class='col-lg-6'>
                <h3 style='color: #3C8DBC;'><i class='fa fa-area-chart'></i>Statistics</h3>

                <div id="graphs-table">
                <?= $this->render('_graphs', [
                        'projectStatus' => $projectStatus,
                        'projectFinancial' => $projectFinancial,
                        'scriptEmployment' => $scriptEmployment,
                        'script' => $script,
                    ]) ?>

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
        if (x.style.display == "none") {
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