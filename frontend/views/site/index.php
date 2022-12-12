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

        <h3 align=center>The Region 1 RPMES Dashboard</h3>

        <div class="row">
            <div class="col-md-2 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?= $this->render('_search', [
                            'model' => $model,
                            'years' => $years,
                            'quarters' => $quarters,
                            'agencies' => $agencies,
                            'categories' => $categories,
                            'sectors' => $sectors,
                            'subSectors' => $subSectors,
                            'provinces' => $provinces,
                            'fundSources' => $fundSources,
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
                                <h4><i class='fa fa-map-marker'></i> Project Map</h4>
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
    function loadHeatMap(year, quarter, agency_id, category_id, sector_id, sub_sector_id, province_id, fund_source_id)
    {
        $.ajax({
            url: "'.Url::to(['/site/heat-map']).'?year=" + year + "&quarter=" + quarter + "&agency_id=" + agency_id + "&category_id=" + category_id + "&sector_id=" + sector_id + "&sub_sector_id=" + sub_sector_id + "&province_id=" + province_id + "&fund_source_id=" + fund_source_id,
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

    function loadEmployment(year, quarter, agency_id, category_id, sector_id, sub_sector_id, province_id, fund_source_id)
    {
        $.ajax({
            url: "'.Url::to(['/site/employment']).'?year=" + year + "&quarter=" + quarter + "&agency_id=" + agency_id + "&category_id=" + category_id + "&sector_id=" + sector_id + "&sub_sector_id=" + sub_sector_id + "&province_id=" + province_id + "&fund_source_id=" + fund_source_id,
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

    function loadDisbursementByCategory(year, quarter, agency_id, category_id, sector_id, sub_sector_id, province_id, fund_source_id)
    {
        $.ajax({
            url: "'.Url::to(['/site/disbursement-by-category']).'?year=" + year + "&quarter=" + quarter + "&agency_id=" + agency_id + "&category_id=" + category_id + "&sector_id=" + sector_id + "&sub_sector_id=" + sub_sector_id + "&province_id=" + province_id + "&fund_source_id=" + fund_source_id,
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

    function loadProjectImplementation(year, quarter, agency_id, category_id, sector_id, sub_sector_id, province_id, fund_source_id)
    {
        $.ajax({
            url: "'.Url::to(['/site/project-implementation']).'?year=" + year + "&quarter=" + quarter + "&agency_id=" + agency_id + "&category_id=" + category_id + "&sector_id=" + sector_id + "&sub_sector_id=" + sub_sector_id + "&province_id=" + province_id + "&fund_source_id=" + fund_source_id,
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

    function loadBeneficiaries(year, quarter, agency_id, category_id, sector_id, sub_sector_id, province_id, fund_source_id)
    {
        $.ajax({
            url: "'.Url::to(['/site/beneficiaries']).'?year=" + year + "&quarter=" + quarter + "&agency_id=" + agency_id + "&category_id=" + category_id + "&sector_id=" + sector_id + "&sub_sector_id=" + sub_sector_id + "&province_id=" + province_id + "&fund_source_id=" + fund_source_id,
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

    function previousGraph(action, year, quarter, agency_id, category_id, sector_id, sub_sector_id, province_id, fund_source_id)
    {
        if(action === "employment")
        {
            loadEmployment(year, quarter, agency_id, category_id, sector_id, sub_sector_id, province_id, fund_source_id);
        }
        else if(action === "disbursement-by-category")
        {
            loadDisbursementByCategory(year, quarter, agency_id, category_id, sector_id, sub_sector_id, province_id, fund_source_id);
        }
        else if(action === "project-implementation")
        {
            loadProjectImplementation(year, quarter, agency_id, category_id, sector_id, sub_sector_id, province_id, fund_source_id);
        }
        else if(action === "beneficiaries")
        {
            loadBeneficiaries(year, quarter, agency_id, category_id, sector_id, sub_sector_id, province_id, fund_source_id);
        }
    }

    function nextGraph(action, year, quarter, agency_id, category_id, sector_id, sub_sector_id, province_id, fund_source_id)
    {
        if(action === "employment")
        {
            loadEmployment(year, quarter, agency_id, category_id, sector_id, sub_sector_id, province_id, fund_source_id);
        }
        else if(action === "disbursement-by-category")
        {
            loadDisbursementByCategory(year, quarter, agency_id, category_id, sector_id, sub_sector_id, province_id, fund_source_id);
        }
        else if(action === "project-implementation")
        {
            loadProjectImplementation(year, quarter, agency_id, category_id, sector_id, sub_sector_id, province_id, fund_source_id);
        }
        else if(action === "beneficiaries")
        {
            loadBeneficiaries(year, quarter, agency_id, category_id, sector_id, sub_sector_id, province_id, fund_source_id);
        }
    }

    $(document).ready(function(){
        var result = "";
        var classesOfDivs = ["employment", "disbursement-by-category", "project-implementation", "beneficiaries"];
        var classnameSelected = classesOfDivs[Math.floor(Math.random()*classesOfDivs.length)];
        nextGraph(classnameSelected, "","","","","","","","");
        loadHeatMap("","","","","","","","");
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
        var agency_id = $("#submission-agency_id").val();
        var category_id = $("#submission-category_id").val();
        var sector_id = $("#submission-sector_id").val();
        var sub_sector_id = $("#submission-sub_sector_id").val();
        var province_id = $("#submission-province_id").val();
        var fund_source_id = $("#submission-fund_source_id").val();

        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            beforeSend: function(){
                $("#graphs-table").html("<div class=\"text-center\"><svg class=\"spinner\" width=\"30px\" height=\"30px\" viewBox=\"0 0 66 66\" xmlns=\"http://www.w3.org/2000/svg\"><circle class=\"path\" fill=\"none\" stroke-width=\"6\" stroke-linecap=\"round\" cx=\"33\" cy=\"33\" r=\"30\"></circle></svg></div>");
            },
            success: function (data) {
                loadHeatMap(year, quarter, agency_id, category_id, sector_id, sub_sector_id, province_id, fund_source_id);
                loadEmployment(year, quarter, agency_id, category_id, sector_id, sub_sector_id, province_id, fund_source_id);
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