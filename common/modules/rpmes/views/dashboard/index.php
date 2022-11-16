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

    $this->title = 'Dashboard';
    $this->params['breadcrumbs'][] = $this->title;

    $HtmlHelper = new HtmlHelper();
?>
<style>
.highcharts-figure,
.highcharts-data-table table {
    min-width: 310px;
    max-width: 800px;
    margin: 1em auto;
}

#container {
    height: 400px;
}

.highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #ebebeb;
    margin: 10px auto;
    text-align: center;
    width: 100%;
    max-width: 500px;
}

.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}

.highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
}

.highcharts-data-table td,
.highcharts-data-table th,
.highcharts-data-table caption {
    padding: 0.5em;
}

.highcharts-data-table thead tr,
.highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}

.highcharts-data-table tr:hover {
    background: #f1f7ff;
}

</style>
<div class="dashboard-index">
    <div class="row">
        <div class="col-md-8 col-xs-12">
            <div class="box box-solid">
                <div class="box-body">
                    <h4>Upcoming Due Dates</h4>
                    <table class="table table-bordered table-condensed table-striped table-hover table-responsive">
                        <thead>
                            <tr>
                                <th>Report</th>
                                <th>Quarter</th>
                                <th>Due Date</th>
                                <th>Remarks</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Monitoring Plan</td>
                                <td>-</td>
                                <td><?= $monitoringPlan ? date("F j, Y", strtotime($monitoringPlan->due_date)) : 'Not set' ?></td>
                                <td><?= $monitoringPlan ? strtotime(date("Y-m-d")) <= strtotime($monitoringPlan->due_date) ? $HtmlHelper->time_elapsed_string($monitoringPlan->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($monitoringPlan->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/plan'])?></td>
                            </tr>
                            <tr>
                                <td>Accomplishment</td>
                                <td>1st Quarter</td>
                                <td><?= $accompQ1 ? date("F j, Y", strtotime($accompQ1->due_date)) : 'Not set' ?></td>
                                <td><?= $accompQ1 ? strtotime(date("Y-m-d")) <= strtotime($accompQ1->due_date) ? $HtmlHelper->time_elapsed_string($accompQ1->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($accompQ1->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/accomplishment', 'Project[year]' => date("Y"), 'Project[agency_id]' => Yii::$app->user->identity->userinfo->AGENCY_C, 'Project[quarter]' => 'Q1'])?></td>
                            </tr>
                            <tr>
                                <td>Accomplishment</td>
                                <td>2nd Quarter</td>
                                <td><?= $accompQ2 ? date("F j, Y", strtotime($accompQ2->due_date)) : 'Not set' ?></td>
                                <td><?= $accompQ2 ? strtotime(date("Y-m-d")) <= strtotime($accompQ2->due_date) ? $HtmlHelper->time_elapsed_string($accompQ2->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($accompQ2->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/accomplishment', 'Project[year]' => date("Y"), 'Project[agency_id]' => Yii::$app->user->identity->userinfo->AGENCY_C, 'Project[quarter]' => 'Q2'])?></td>
                            </tr>
                            <tr>
                                <td>Accomplishment</td>
                                <td>3rd Quarter</td>
                                <td><?= $accompQ3 ? date("F j, Y", strtotime($accompQ3->due_date)) : 'Not set' ?></td>
                                <td><?= $accompQ3 ? strtotime(date("Y-m-d")) <= strtotime($accompQ3->due_date) ? $HtmlHelper->time_elapsed_string($accompQ3->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($accompQ3->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/accomplishment', 'Project[year]' => date("Y"), 'Project[agency_id]' => Yii::$app->user->identity->userinfo->AGENCY_C, 'Project[quarter]' => 'Q3'])?></td>
                            </tr>
                            <tr>
                                <td>Accomplishment</td>
                                <td>4th Quarter</td>
                                <td><?= $accompQ4 ? date("F j, Y", strtotime($accompQ4->due_date)) : 'Not set' ?></td>
                                <td><?= $accompQ4 ? strtotime(date("Y-m-d")) <= strtotime($accompQ4->due_date) ? $HtmlHelper->time_elapsed_string($accompQ4->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($accompQ4->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/accomplishment', 'Project[year]' => date("Y"), 'Project[agency_id]' => Yii::$app->user->identity->userinfo->AGENCY_C, 'Project[quarter]' => 'Q4'])?></td>
                            </tr>
                            <tr>
                                <td>Project Exception</td>
                                <td>1st Quarter</td>
                                <td><?= $exceptionQ1 ? date("F j, Y", strtotime($exceptionQ1->due_date)) : 'Not set' ?></td>
                                <td><?= $exceptionQ1 ? strtotime(date("Y-m-d")) <= strtotime($exceptionQ1->due_date) ? $HtmlHelper->time_elapsed_string($exceptionQ1->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($exceptionQ1->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/project-exception', 'Project[year]' => date("Y"), 'Project[agency_id]' => Yii::$app->user->identity->userinfo->AGENCY_C, 'Project[quarter]' => 'Q1', 'Project[status]' => ''])?></td>
                            </tr>
                            <tr>
                                <td>Project Exception</td>
                                <td>2nd Quarter</td>
                                <td><?= $exceptionQ2 ? date("F j, Y", strtotime($exceptionQ2->due_date)) : 'Not set' ?></td>
                                <td><?= $exceptionQ2 ? strtotime(date("Y-m-d")) <= strtotime($exceptionQ2->due_date) ? $HtmlHelper->time_elapsed_string($exceptionQ2->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($exceptionQ2->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/project-exception', 'Project[year]' => date("Y"), 'Project[agency_id]' => Yii::$app->user->identity->userinfo->AGENCY_C, 'Project[quarter]' => 'Q2', 'Project[status]' => ''])?></td>
                            </tr>
                            <tr>
                                <td>Project Exception</td>
                                <td>3rd Quarter</td>
                                <td><?= $exceptionQ3 ? date("F j, Y", strtotime($exceptionQ3->due_date)) : 'Not set' ?></td>
                                <td><?= $exceptionQ3 ? strtotime(date("Y-m-d")) <= strtotime($exceptionQ3->due_date) ? $HtmlHelper->time_elapsed_string($exceptionQ3->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($exceptionQ3->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/project-exception', 'Project[year]' => date("Y"), 'Project[agency_id]' => Yii::$app->user->identity->userinfo->AGENCY_C, 'Project[quarter]' => 'Q3', 'Project[status]' => ''])?></td>
                            </tr>
                            <tr>
                                <td>Project Exception</td>
                                <td>4th Quarter</td>
                                <td><?= $exceptionQ4 ? date("F j, Y", strtotime($exceptionQ4->due_date)) : 'Not set' ?></td>
                                <td><?= $exceptionQ4 ? strtotime(date("Y-m-d")) <= strtotime($exceptionQ4->due_date) ? $HtmlHelper->time_elapsed_string($exceptionQ4->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($exceptionQ4->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/project-exception', 'Project[year]' => date("Y"), 'Project[agency_id]' => Yii::$app->user->identity->userinfo->AGENCY_C, 'Project[quarter]' => 'Q4', 'Project[status]' => ''])?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xs-12">
            <div class="box box-solid">
                <div class="box-body">
                    <h4>Recent Activities</h4>
                    <p>This section is under maintenance.</p>
                </div>
            </div>
            
        </div>
        <div class="col-md-12 col-xs-12">
            <div class="box box-solid">
                <div class="box-body">
                    <script src="https://code.highcharts.com/highcharts.js"></script>
                    <script src="https://code.highcharts.com/modules/exporting.js"></script>
                    <script src="https://code.highcharts.com/modules/export-data.js"></script>
                    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

                    <figure class="highcharts-figure">
                        <div id="container"></div>
                        <p class="highcharts-description">
                            Bar chart showing horizontal columns. This chart type is often
                            beneficial for smaller screens, as the user can scroll through the data
                            vertically, and axis labels are easy to read.
                        </p>
                    </figure>
                </div>
            </div>
            
        </div>
    </div>
</div>
<?php
$cars = array('Volvo', 'BMW', 'Toyota', 'Honda' ,'Ford','Mitsubishi','Suzuki', 'Morris Garage', 'Maserati');
$script = "
Highcharts.chart('container', {
    chart: {
        type: 'bar'
    },
    title: {
        text: 'Projects'
    },
    
    xAxis: {
        categories: ['".implode("','", $cars)."'],
        title: {
            text: null
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Projects',
            align: 'high'
        },
        labels: {
            overflow: 'justify'
        }
    },
    tooltip: {
        valueSuffix: 'projects'
    },
    plotOptions: {
        bar: {
            dataLabels: {
                enabled: true
            }
        }
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'top',
        x: -40,
        y: 80,
        floating: true,
        borderWidth: 1,
        backgroundColor:
            Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
        shadow: true
    },
    credits: {
        enabled: false
    },
    series: [{
        name: 'Year 2019',
        data: [631, 727, 3202, 721, 26, 500, 300, 12873, 1]
    }, {
        name: 'Year 2020',
        data: [814, 841, 3714, 726, 31, 500, 200, 21637, 1]
    }, {
        name: 'Year 2021',
        data: [1044, 944, 4170, 735, 40, 500, 300, 21876, 1]
    }, {
        name: 'Year 2022',
        data: [1276, 1007, 4561, 746, 42, 500, 200, 25387, 1]
    }]
});";

$this->registerJs($script, View::POS_END);
?>

