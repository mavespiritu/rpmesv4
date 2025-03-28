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

    $this->title = 'Summary of Monitoring Plans';
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="monitoring-plan-index">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Summary: Monitoring Plans</h3>
                </div>
                <div class="box-body">
                    <?= $this->render('_search-monitoring-plan', [
                        'model' => $model,
                        'years' => $years,
                        'sorts' => $sorts,
                        'agencies' => $agencies,
                        'sectors' => $sectors,
                        'categories' => $categories,
                        'fundSources' => $fundSources,
                        'periods' => $periods,
                        'regions' => $regions,
                        'provinces' => $provinces
                    ]) ?>
                    <hr>
                    <div id="summary-monitoring-plan-table"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $script = '
        function printSummary(model, year, agency_id)
        {
            var printWindow = window.open(
                "'.Url::to(['/rpmes/summary/download-monitoring-plan']).'?type=print&model=" + model + "&year=" + year + "&agency_id=" + agency_id, 
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
?>