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

    $this->title = 'Home';
    $this->params['breadcrumbs'][] = $this->title;

    $HtmlHelper = new HtmlHelper();
?>
<div class="dashboard-index">
    <div class="row">
        <div class="col-md-8 col-xs-12">
            <div class="box box-solid">
                <div class="box-body">
                    <h4>Upcoming Due Dates</h4>
                    <table class="table table-bordered table-condensed table-striped table-hover table-responsive">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Report</th>
                                <th style="width: 20%;">Quarter</th>
                                <th style="width: 20%;">Due Date</th>
                                <th style="width: 20%;">Remarks</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Form 1: Initial Project Report</td>
                                <td>-</td>
                                <td><?= $monitoringPlan ? date("F j, Y", strtotime($monitoringPlan->due_date)) : 'Not set' ?></td>
                                <td><?= $monitoringPlan ? strtotime(date("Y-m-d")) <= strtotime($monitoringPlan->due_date) ? $HtmlHelper->time_elapsed_string($monitoringPlan->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($monitoringPlan->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/plan'])?></td>
                            </tr>
                            <tr>
                                <td>Form 2: Physical and Financial Accomplishment Report</td>
                                <td>1st Quarter</td>
                                <td><?= $accompQ1 ? date("F j, Y", strtotime($accompQ1->due_date)) : 'Not set' ?></td>
                                <td><?= $accompQ1 ? strtotime(date("Y-m-d")) <= strtotime($accompQ1->due_date) ? $HtmlHelper->time_elapsed_string($accompQ1->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($accompQ1->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/accomplishment'])?></td>
                            </tr>
                            <tr>
                                <td>Form 2: Physical and Financial Accomplishment Report</td>
                                <td>2nd Quarter</td>
                                <td><?= $accompQ2 ? date("F j, Y", strtotime($accompQ2->due_date)) : 'Not set' ?></td>
                                <td><?= $accompQ2 ? strtotime(date("Y-m-d")) <= strtotime($accompQ2->due_date) ? $HtmlHelper->time_elapsed_string($accompQ2->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($accompQ2->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/accomplishment'])?></td>
                            </tr>
                            <tr>
                                <td>Form 2: Physical and Financial Accomplishment Report</td>
                                <td>3rd Quarter</td>
                                <td><?= $accompQ3 ? date("F j, Y", strtotime($accompQ3->due_date)) : 'Not set' ?></td>
                                <td><?= $accompQ3 ? strtotime(date("Y-m-d")) <= strtotime($accompQ3->due_date) ? $HtmlHelper->time_elapsed_string($accompQ3->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($accompQ3->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/accomplishment'])?></td>
                            </tr>
                            <tr>
                                <td>Form 2: Physical and Financial Accomplishment Report</td>
                                <td>4th Quarter</td>
                                <td><?= $accompQ4 ? date("F j, Y", strtotime($accompQ4->due_date)) : 'Not set' ?></td>
                                <td><?= $accompQ4 ? strtotime(date("Y-m-d")) <= strtotime($accompQ4->due_date) ? $HtmlHelper->time_elapsed_string($accompQ4->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($accompQ4->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/accomplishment'])?></td>
                            </tr>
                            <tr>
                                <td>Form 3: Project Exception Report</td>
                                <td>1st Quarter</td>
                                <td><?= $exceptionQ1 ? date("F j, Y", strtotime($exceptionQ1->due_date)) : 'Not set' ?></td>
                                <td><?= $exceptionQ1 ? strtotime(date("Y-m-d")) <= strtotime($exceptionQ1->due_date) ? $HtmlHelper->time_elapsed_string($exceptionQ1->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($exceptionQ1->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/project-exception'])?></td>
                            </tr>
                            <tr>
                                <td>Form 3: Project Exception Report</td>
                                <td>2nd Quarter</td>
                                <td><?= $exceptionQ2 ? date("F j, Y", strtotime($exceptionQ2->due_date)) : 'Not set' ?></td>
                                <td><?= $exceptionQ2 ? strtotime(date("Y-m-d")) <= strtotime($exceptionQ2->due_date) ? $HtmlHelper->time_elapsed_string($exceptionQ2->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($exceptionQ2->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/project-exception'])?></td>
                            </tr>
                            <tr>
                                <td>Form 3: Project Exception Report</td>
                                <td>3rd Quarter</td>
                                <td><?= $exceptionQ3 ? date("F j, Y", strtotime($exceptionQ3->due_date)) : 'Not set' ?></td>
                                <td><?= $exceptionQ3 ? strtotime(date("Y-m-d")) <= strtotime($exceptionQ3->due_date) ? $HtmlHelper->time_elapsed_string($exceptionQ3->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($exceptionQ3->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/project-exception'])?></td>
                            </tr>
                            <tr>
                                <td>Form 3: Project Exception Report</td>
                                <td>4th Quarter</td>
                                <td><?= $exceptionQ4 ? date("F j, Y", strtotime($exceptionQ4->due_date)) : 'Not set' ?></td>
                                <td><?= $exceptionQ4 ? strtotime(date("Y-m-d")) <= strtotime($exceptionQ4->due_date) ? $HtmlHelper->time_elapsed_string($exceptionQ4->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($exceptionQ4->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/project-exception'])?></td>
                            </tr>
                            <tr>
                                <td>Form 4: Project Results Report</td>
                                <td>-</td>
                                <td><?= $monitoringPlan ? date("F j, Y", strtotime($monitoringPlan->due_date)) : 'Not set' ?></td>
                                <td><?= $monitoringPlan ? strtotime(date("Y-m-d")) <= strtotime($monitoringPlan->due_date) ? $HtmlHelper->time_elapsed_string($monitoringPlan->due_date).' to go' : 'Ended '.$HtmlHelper->time_elapsed_string($monitoringPlan->due_date).' ago' : 'Not set' ?></td>
                                <td align=center><?= Html::a('Accomplish Now', ['/rpmes/project-result'])?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- <div class="box box-solid">
                <div class="box-body">
                    <h4>Submission Log</h4>
                    <?= $this->render('_search-submission', [
                        'years' => $years,
                        'agencies' => $agencies,
                        'logModel' => $logModel,
                    ])?>
                    <br>
                    <div id="submission-log"></div>
                </div>
            </div> -->
        </div>
        <div class="col-md-4 col-xs-12">
            <div class="box box-solid">
                <div class="box-body">
                    <h4>Recent Activities</h4>
                    <?php if($logs){ ?>
                        <?php foreach($logs as $log){ ?>
                            <?php if($log->status == 'Submitted'){ ?>
                                <p><?= $log->actor ?> submitted the Agency <?= $log->submission->report ?> <?= $log->submission->year ?> Report last <?= $log->datetime ?></p>
                            <?php }else if($log->status == 'For further validation'){ ?>
                                <p><?= $log->actor ?> has requested to revise the Agency <?= $log->submission->report ?> <?= $log->submission->year ?> Report last <?= $log->datetime ?></p>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
