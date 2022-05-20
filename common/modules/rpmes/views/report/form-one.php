<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;
    use yii\grid\GridView;
    use yii\widgets\LinkPager;
    use common\components\helpers\HtmlHelper;
    use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
    DisableButtonAsset::register($this);
    use yii\web\View;

    $this->title = 'RPMES Form 1: Initial Project Report';
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="form-one-index">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Generate Report</h3>
                </div>
                <div class="box-body">
                    <?= $this->render('_search-form-one', [
                        'model' => $model,
                        'years' => $years,
                        'fundSources' => $fundSources,
                        'regions' => $regions,
                        'provinces' => $provinces,
                        'citymuns' => $citymuns,
                        'sectors' => $sectors,
                        'subSectors' => $subSectors,
                        'agencies' => $agencies,
                    ]) ?>
                    <hr>
                    <div id="form-one-table"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $script = '
        function printFormOneReport(year, fund_source_id, sector_id, sub_sector_id, region_id, province_id, citymun_id, agency_id)
        {
            var printWindow = window.open(
                "'.Url::to(['/rpmes/report/print-form-one']).'?year=" + year +  "&fund_source_id=" + fund_source_id + "&sector_id=" + sector_id + "&sub_sector_id=" + sub_sector_id + "&region_id=" + region_id + "&province_id=" + province_id + "&citymun_id=" + citymun_id + "&agency_id=" + agency_id, 
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