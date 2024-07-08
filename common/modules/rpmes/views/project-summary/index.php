<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use common\components\helpers\HtmlHelper;
use yii\bootstrap\ButtonDropdown;
use faryshta\disableSubmitButtons\Asset as DisableButtonAsset;
DisableButtonAsset::register($this);
use yii\web\View;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\rpmes\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'RPMES Form 5: Summary of Physical and Financial Accomplishment including Project Results';
$this->params['breadcrumbs'][] = $this->title;

$successMessage = \Yii::$app->getSession()->getFlash('success');
?>
<div class="project-status-index">
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">List of Projects with Physical and Financial Accomplishment</h3>
        </div>
        <div class="box-body">
            <?= $this->render('_search', [
                'model' => $model,
                'years' => $years,
                'agencies' => $agencies,
                'sectors' => $sectors,
                'modes' => $modes,
                'regions' => $regions,
                'provinces' => $provinces,
                'citymuns' => $citymuns,
                'fundSources' => $fundSources,
                'sorts' => $sorts,
            ]) ?>
            <div id="summary-accomplishment-table"></div>
            <?php GridView::widget([
                'options' => [
                    'class' => 'table-responsive',
                ],
                'tableOptions' => [
                    'class' => 'table table-bordered table-striped table-hover',
                ],
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => [
                            'style' => 'background-color: #002060; color: white; font-weight: normal;'
                        ],
                    ],
                    [
                        'attribute' => 'year',
                        'header' => 'Year',
                        'headerOptions' => [
                            'style' => 'width: 5%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'attribute' => 'quarter',
                        'header' => 'Quarter',
                        'headerOptions' => [
                            'style' => 'width: 5%; background-color: #002060; color: white; font-weight: normal;'
                        ]
                    ],
                    [
                        'header' => '
                                    (a) Project No. <br>
                                    (b) Program/Project Title <br>
                                    (c) Province <br>
                                    (d) City/Municipality <br>
                                    (e) Barangay <br>
                                    (f) Implementing Agency <br>
                                    (g) Start and End Date <br>
                                    (h) Sector <br>
                                    (i) Fund Source <br>
                                    (j) Funding Agency <br>
                                    (k) Total Cost <br>
                                    ',
                        'headerOptions' => [
                            'style' => 'width: 15%; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'format' => 'raw',
                        'value' => function($model){
                            return 
                                '(a) '.$model->project->project_no.'<br>'.
                                '(b) '.$model->project->title.'<br>'.
                                '(c) '.$model->project->provinceTitle.'<br>'.
                                '(d) '.$model->project->citymunTitle.'<br>'.
                                '(e) '.$model->project->barangayTitle.'<br>'.
                                '(f) '.$model->project->agency->code.'<br>'.
                                '(g) '.date("F j, Y", strtotime($model->project->start_date)).' - '.date("F j, Y", strtotime($model->project->completion_date)).'<br>'.
                                '(h) '.$model->project->sector->title.'<br>'.
                                '(i) '.implode(', ', array_column($model->project->getProjectHasFundSources()->select(['fund_source.title'])->leftJoin('fund_source', 'fund_source.id = project_has_fund_sources.fund_source_id')->createCommand()->queryAll(), 'title')).'<br>'.
                                '(j) '.implode(', ', array_column($model->project->getProjectHasFundSources()->select(['agency'])->createCommand()->queryAll(), 'agency')).'<br>'.
                                '(k) '.number_format($model->project->cost, 2).'<br>'
                            ;
                        }
                    ],
                    [
                        'header' => 'Appropriations',
                        'headerOptions' => [
                            'style' => 'width: 10%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'contentOptions' => [
                            'style' => 'text-align: right;'
                        ],
                        'format' => 'raw',
                        'value' => function($model){
                            return number_format($model->project->getNewAccomplishedAppropriationsForQuarter($model->year)[$model->quarter], 2);
                        }
                    ],
                    [
                        'header' => 'Allotment',
                        'headerOptions' => [
                            'style' => 'width: 10%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'contentOptions' => [
                            'style' => 'text-align: right;'
                        ],
                        'format' => 'raw',
                        'value' => function($model){
                            return number_format($model->project->getNewAccomplishedAllotmentForQuarter($model->year)[$model->quarter], 2);
                        }
                    ],
                    [
                        'header' => 'Obligations',
                        'headerOptions' => [
                            'style' => 'width: 10%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'contentOptions' => [
                            'style' => 'text-align: right;'
                        ],
                        'format' => 'raw',
                        'value' => function($model){
                            return number_format($model->project->getNewAccomplishedObligationForQuarter($model->year)[$model->quarter], 2);
                        }
                    ],
                    [
                        'header' => 'Disbursements',
                        'headerOptions' => [
                            'style' => 'width: 10%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'contentOptions' => [
                            'style' => 'text-align: right;'
                        ],
                        'format' => 'raw',
                        'value' => function($model){
                            return number_format($model->project->getNewAccomplishedDisbursementForQuarter($model->year)[$model->quarter], 2);
                        }
                    ],
                    [
                        'header' => 'Funding Support (%)',
                        'headerOptions' => [
                            'style' => 'width: 5%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'contentOptions' => [
                            'style' => 'text-align: center;'
                        ],
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->project->getNewAccomplishedAppropriationsForQuarter($model->year)[$model->quarter] > 0 ? number_format(($model->project->getNewAccomplishedAllotmentForQuarter($model->year)[$model->quarter] / $model->project->getNewAccomplishedAppropriationsForQuarter($model->year)[$model->quarter]) * 100, 2) : 0;
                        }
                    ],
                    [
                        'header' => 'Funding Utilization (%)',
                        'headerOptions' => [
                            'style' => 'width: 5%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'contentOptions' => [
                            'style' => 'text-align: center;'
                        ],
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->project->getNewAccomplishedAllotmentForQuarter($model->year)[$model->quarter] > 0 ? number_format(($model->project->getNewAccomplishedDisbursementForQuarter($model->year)[$model->quarter] / $model->project->getNewAccomplishedAllotmentForQuarter($model->year)[$model->quarter]) * 100, 2) : 0;
                        }
                    ],
                    [
                        'header' => 'Target <br> OWPA (%)',
                        'headerOptions' => [
                            'style' => 'width: 5%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'contentOptions' => [
                            'style' => 'text-align: center;'
                        ],
                        'format' => 'raw',
                        'value' => function($model){
                            return number_format($model->project->getTargetOwpa($model->year)[$model->quarter], 2);
                        }
                    ],
                    [
                        'header' => 'Actual <br> OWPA (%)',
                        'headerOptions' => [
                            'style' => 'width: 5%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'contentOptions' => [
                            'style' => 'text-align: center;'
                        ],
                        'format' => 'raw',
                        'value' => function($model){
                            return number_format($model->project->getActualOwpa($model->year)[$model->quarter], 2);
                        }
                    ],
                    [
                        'header' => 'Slippage <br> (%)',
                        'headerOptions' => [
                            'style' => 'width: 5%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'contentOptions' => [
                            'style' => 'text-align: center;'
                        ],
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->project->getSlippage($model->year)[$model->quarter] > -10 && $model->project->getSlippage($model->year)[$model->quarter] < 10 ? number_format($model->project->getSlippage($model->year)[$model->quarter], 2) : '<font style="color:red">'.number_format($model->project->getSlippage($model->year)[$model->quarter], 2).'</font>';
                        }
                    ],
                    [
                        'header' => 'EG-Male',
                        'headerOptions' => [
                            'style' => 'width: 5%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'contentOptions' => [
                            'style' => 'text-align: center;'
                        ],
                        'format' => 'raw',
                        'value' => function($model){
                            return number_format($model->project->getMalesEmployedActual($model->year)[$model->quarter], 0);
                        }
                    ],
                    [
                        'header' => 'EG-Female',
                        'headerOptions' => [
                            'style' => 'width: 5%; text-align: center; background-color: #002060; color: white; font-weight: normal;'
                        ],
                        'contentOptions' => [
                            'style' => 'text-align: center;'
                        ],
                        'format' => 'raw',
                        'value' => function($model){
                            return number_format($model->project->getFemalesEmployedActual($model->year)[$model->quarter], 0);
                        }
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>

<?php
if ($successMessage) {
    $this->registerJs("
        $(document).ready(function() {
            // Display the flash message
            $('.alert-success').fadeIn();

            // Hide the flash message after 5 seconds
            setTimeout(function() {
                $('.alert-success').fadeOut();
            }, 5000);
        });
    ");
}
?>