<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;
use yii\bootstrap\ButtonDropdown;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Form 6: Reports on the Status of Projects Encountering Implementation Problems';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-status-index">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Reports on the Status of Projects Encountering Implementation Problems</h3>
                </div>
                <div class="box-body">
                    <?= $this->render('_search', [
                        'model' => $model,
                        'quarters' => $quarters,
                        'years' => $years,
                        'agencies' => $agencies,
                        'sectors' => $sectors,
                        'regions' => $regions,
                        'provinces' => $provinces,
                    ]) ?>
                    <hr>
                    <div id="project-status-table"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $script = '
        function printFormSixReport(year,quarter,agency_id,sector_id,region_id,province_id)
        {
            var printWindow = window.open(
                "'.Url::to(['/rpmes/project-status/print-form-six']).'?year=" + year + "&quarter=" + quarter + "&agency_id=" + agency_id + "&sector_id=" + sector_id + "&region_id=" + region_id + "&province_id=" + province_id, 
                "Print",
                "left=200", 
                "top=200", 
                "width=650", 
                "height=500", 
                "toolbar=0", 
                "resizable=0"
                );
                printWindow.addEventListener("load", function() {
                    printWindow.print();
                    setTimeout(function() {
                    printWindow.close();
                }, 1);
                }, true);
        }
    ';

    $this->registerJs($script, View::POS_END);
