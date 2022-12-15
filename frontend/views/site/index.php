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

<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<div class='site-index'>

        <h3 align=center>The Region 1 RPMES Dashboard</h3>

        <div class="row">
            <div class="col-md-2 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?= $this->render('_search', [
                            'model' => $model,
                            'years' => $years,
                            'quarters' => $quarters,
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-10 col-xs-12">
                <div class="row">
                    <div class="col-md-8 col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h4><i class='fa fa-area-chart'></i> Statistics</h4>
                                <div id="statistics" style="height: 70vh;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h4><i class='fa fa-map-marker'></i> Project Distribution per Province</h4>
                                <div id="map" style="height: 70vh;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $script = '
    function loadHeatMap(year, quarter)
    {
        $.ajax({
            url: "'.Url::to(['/site/heat-map']).'?year=" + year + "&quarter=" + quarter,
            beforeSend: function(){
                $("#map").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) { 
                $("#map").empty();
                $("#map").hide();
                $("#map").fadeIn("slow");
                $("#map").html(data);
            }
        });
    }

    function loadEmployment(year, quarter)
    {
        $.ajax({
            url: "'.Url::to(['/site/employment']).'?year=" + year + "&quarter=" + quarter,
            beforeSend: function(){
                $("#statistics").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) { 
                $("#statistics").empty();
                $("#statistics").hide();
                $("#statistics").fadeIn("slow");
                $("#statistics").html(data);
            }
        });
    }

    function loadDisbursementByCategory(year, quarter)
    {
        $.ajax({
            url: "'.Url::to(['/site/disbursement-by-category']).'?year=" + year + "&quarter=" + quarter,
            beforeSend: function(){
                $("#statistics").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) { 
                $("#statistics").empty();
                $("#statistics").hide();
                $("#statistics").fadeIn("slow");
                $("#statistics").html(data);
            }
        });
    }

    function loadProjectImplementation(year, quarter)
    {
        $.ajax({
            url: "'.Url::to(['/site/project-implementation']).'?year=" + year + "&quarter=" + quarter,
            beforeSend: function(){
                $("#statistics").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) { 
                $("#statistics").empty();
                $("#statistics").hide();
                $("#statistics").fadeIn("slow");
                $("#statistics").html(data);
            }
        });
    }

    function loadBeneficiaries(year, quarter)
    {
        $.ajax({
            url: "'.Url::to(['/site/beneficiaries']).'?year=" + year + "&quarter=" + quarter,
            beforeSend: function(){
                $("#statistics").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) { 
                $("#statistics").empty();
                $("#statistics").hide();
                $("#statistics").fadeIn("slow");
                $("#statistics").html(data);
            }
        });
    }

    function loadPhysical(year, quarter)
    {
        $.ajax({
            url: "'.Url::to(['/site/physical']).'?year=" + year + "&quarter=" + quarter,
            beforeSend: function(){
                $("#statistics").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) { 
                $("#statistics").empty();
                $("#statistics").hide();
                $("#statistics").fadeIn("slow");
                $("#statistics").html(data);
            }
        });
    }

    function loadImageSlider(year, quarter)
    {
        $.ajax({
            url: "'.Url::to(['/site/image-slider']).'?year=" + year + "&quarter=" + quarter,
            beforeSend: function(){
                $("#statistics").html("<div class=\"text-center\" style=\"margin-top: 50px;\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) { 
                $("#statistics").empty();
                $("#statistics").hide();
                $("#statistics").fadeIn("slow");
                $("#statistics").html(data);
            }
        });
    }

    function previousGraph(action, year, quarter)
    {
        if(action === "employment")
        {
            loadEmployment(year, quarter);
        }
        else if(action === "disbursement-by-category")
        {
            loadDisbursementByCategory(year, quarter);
        }
        else if(action === "project-implementation")
        {
            loadProjectImplementation(year, quarter);
        }
        else if(action === "beneficiaries")
        {
            loadBeneficiaries(year, quarter);
        }
        else if(action === "physical")
        {
            loadPhysical(year, quarter);
        }
        else if(action === "image-slider")
        {
            loadImageSlider(year, quarter);
        }
    }

    function nextGraph(action, year, quarter)
    {
        if(action === "employment")
        {
            loadEmployment(year, quarter);
        }
        else if(action === "disbursement-by-category")
        {
            loadDisbursementByCategory(year, quarter);
        }
        else if(action === "project-implementation")
        {
            loadProjectImplementation(year, quarter);
        }
        else if(action === "beneficiaries")
        {
            loadBeneficiaries(year, quarter);
        }
        else if(action === "physical")
        {
            loadPhysical(year, quarter);
        }
        else if(action === "image-slider")
        {
            loadImageSlider(year, quarter);
        }
    }

    $(document).ready(function(){
        var result = "";
        var classesOfDivs = ["employment", "disbursement-by-category", "project-implementation", "beneficiaries", "physical", "event"];
        var classnameSelected = classesOfDivs[Math.floor(Math.random()*classesOfDivs.length)];
        //nextGraph(classnameSelected, "","");
        loadImageSlider("","");
        loadHeatMap("","");
    });
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
        
        var year = $("#submission-year").val();
        var quarter = $("#submission-quarter").val();
        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            beforeSend: function(){
                $("#graphs-table").html("<div class=\"text-center\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                loadHeatMap(year, quarter);
                loadEmployment(year, quarter);
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