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
        <div class="col-md-9 col-xs-12">
            <div class="box box-solid">
                <div class="box-body" style="height: calc(100vh - 180px); scroll; padding: 20px;">
                    <div class="pull-left">
                        <h4><b>Hello, <?= ucwords(strtolower(Yii::$app->user->identity->userinfo->fullName)) ?></b><br>
                        <small>Today is <?= date("l, j F Y") ?></small>
                        </h4>
                    </div>
                    <div class="pull-right">
                        <?= Html::a('Add New Project', ['/rpmes/project/create'],['class' => 'btn btn-success', 'style' => 'background-color: black !important;']) ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-md-8 col-xs-12">
                            <h4><b>Form Submissions</b><br>
                            <small>For CY <?= date("Y") ?></small>
                            </h4>
                            <table class="table table-bordered table-condensed table-striped table-hover table-responsive">
                                <thead>
                                    <tr style='background-color: #002060; color: white; font-weight: normal;'>
                                        <td style="width: 25%;">Report</td>
                                        <td style="width: 15%;">Quarter</td>
                                        <td style="width: 20%;">Due Date</td>
                                        <td style="width: 10%;">Status</td>
                                        <td style="width: 20%;">NEDA Remarks</td>
                                        <td style="width: 10%;">&nbsp;</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Form 1: Initial Project Report</td>
                                        <td>-</td>
                                        <td><?= $monitoringPlan ? date("F j, Y", strtotime($monitoringPlan->due_date)) : 'Not set' ?></td>
                                        <td><?= $monitoringPlanSubmission ? $monitoringPlanSubmission->currentStatus : 'No submission' ?></td>
                                        <td align=center><?= $monitoringPlanSubmission ? $monitoringPlanSubmission->currentSubmissionLog ? $monitoringPlanSubmission->currentSubmissionLog->remarks : '' : '' ?></td>
                                        <td align=center><?= $monitoringPlanSubmission ? Html::a('View Report', ['/rpmes/plan/view', 'id' => $monitoringPlanSubmission->id],['class' => 'btn btn-default btn-sm']) : Html::a('Create Report', ['/rpmes/plan'],['class' => 'btn btn-success btn-sm'])?></td>
                                    </tr>
                                    <tr>
                                        <td rowspan=4 style="vertical-align: middle;">Form 2: Physical and Financial Accomplishment Report</td>
                                        <td>1st Quarter</td>
                                        <td><?= $accompQ1 ? date("F j, Y", strtotime($accompQ1->due_date)) : 'Not set' ?></td>
                                        <td><?= $accompQ1Submission ? $accompQ1Submission->currentStatus : 'No submission' ?></td>
                                        <td align=center><?= $accompQ1Submission ? $accompQ1Submission->currentSubmissionLog ? $accompQ1Submission->currentSubmissionLog->remarks : '' : '' ?></td>
                                        <td align=center><?= $accompQ1Submission ? Html::a('View Report', ['/rpmes/accomplishment/view', 'id' => $accompQ1Submission->id],['class' => 'btn btn-default btn-sm']) : Html::a('Create Report', ['/rpmes/accomplishment'],['class' => 'btn btn-success btn-sm'])?></td>
                                    </tr>
                                    <tr>
                                        <td>2nd Quarter</td>
                                        <td><?= $accompQ2 ? date("F j, Y", strtotime($accompQ2->due_date)) : 'Not set' ?></td>
                                        <td><?= $accompQ2Submission ? $accompQ2Submission->currentStatus : 'No submission' ?></td>
                                        <td align=center><?= $accompQ2Submission ? $accompQ2Submission->currentSubmissionLog ? $accompQ2Submission->currentSubmissionLog->remarks : '' : '' ?></td>
                                        <td align=center><?= $accompQ2Submission ? Html::a('View Report', ['/rpmes/accomplishment/view', 'id' => $accompQ2Submission->id],['class' => 'btn btn-default btn-sm']) : Html::a('Create Report', ['/rpmes/accomplishment'],['class' => 'btn btn-success btn-sm'])?></td>
                                    </tr>
                                    <tr>
                                        <td>3rd Quarter</td>
                                        <td><?= $accompQ3 ? date("F j, Y", strtotime($accompQ3->due_date)) : 'Not set' ?></td>
                                        <td><?= $accompQ3Submission ? $accompQ3Submission->currentStatus : 'No submission' ?></td>
                                        <td align=center><?= $accompQ3Submission ? $accompQ3Submission->currentSubmissionLog ? $accompQ3Submission->currentSubmissionLog->remarks : '' : '' ?></td>
                                        <td align=center><?= $accompQ3Submission ? Html::a('View Report', ['/rpmes/accomplishment/view', 'id' => $accompQ3Submission->id],['class' => 'btn btn-default btn-sm']) : Html::a('Create Report', ['/rpmes/accomplishment'],['class' => 'btn btn-success btn-sm'])?></td>
                                    </tr>
                                    <tr>
                                        <td>4th Quarter</td>
                                        <td><?= $accompQ4 ? date("F j, Y", strtotime($accompQ4->due_date)) : 'Not set' ?></td>
                                        <td><?= $accompQ4Submission ? $accompQ4Submission->currentStatus : 'No submission' ?></td>
                                        <td align=center><?= $accompQ4Submission ? $accompQ4Submission->currentSubmissionLog ? $accompQ4Submission->currentSubmissionLog->remarks : '' : '' ?></td>
                                        <td align=center><?= $accompQ4Submission ? Html::a('View Report', ['/rpmes/accomplishment/view', 'id' => $accompQ4Submission->id],['class' => 'btn btn-default btn-sm']) : Html::a('Create Report', ['/rpmes/accomplishment'],['class' => 'btn btn-success btn-sm'])?></td>
                                    </tr>
                                    <tr>
                                        <td rowspan=4 style="vertical-align: middle;">Form 3: Project Exception Report</td>
                                        <td>1st Quarter</td>
                                        <td><?= $exceptionQ1 ? date("F j, Y", strtotime($exceptionQ1->due_date)) : 'Not set' ?></td>
                                        <td><?= $exceptionQ1Submission ? $exceptionQ1Submission->currentStatus : 'No submission' ?></td>
                                        <td align=center><?= $exceptionQ1Submission ? $exceptionQ1Submission->currentSubmissionLog ? $exceptionQ1Submission->currentSubmissionLog->remarks : '' : '' ?></td>
                                        <td align=center><?= $exceptionQ1Submission ? Html::a('View Report', ['/rpmes/project-exception/view', 'id' => $exceptionQ1Submission->id],['class' => 'btn btn-default btn-sm']) : Html::a('Create Report', ['/rpmes/project-exception'],['class' => 'btn btn-success btn-sm'])?></td>
                                    </tr>
                                    <tr>
                                        <td>2nd Quarter</td>
                                        <td><?= $exceptionQ2 ? date("F j, Y", strtotime($exceptionQ2->due_date)) : 'Not set' ?></td>
                                        <td><?= $exceptionQ2Submission ? $exceptionQ2Submission->currentStatus : 'No submission' ?></td>
                                        <td align=center><?= $exceptionQ2Submission ? $exceptionQ2Submission->currentSubmissionLog ? $exceptionQ2Submission->currentSubmissionLog->remarks : '' : '' ?></td>
                                        <td align=center><?= $exceptionQ2Submission ? Html::a('View Report', ['/rpmes/project-exception/view', 'id' => $exceptionQ2Submission->id],['class' => 'btn btn-default btn-sm']) : Html::a('Create Report', ['/rpmes/project-exception'],['class' => 'btn btn-success btn-sm'])?></td>
                                    </tr>
                                    <tr>
                                        <td>3rd Quarter</td>
                                        <td><?= $exceptionQ3 ? date("F j, Y", strtotime($exceptionQ3->due_date)) : 'Not set' ?></td>
                                        <td><?= $exceptionQ3Submission ? $exceptionQ3Submission->currentStatus : 'No submission' ?></td>
                                        <td align=center><?= $exceptionQ3Submission ? $exceptionQ3Submission->currentSubmissionLog ? $exceptionQ3Submission->currentSubmissionLog->remarks : '' : '' ?></td>
                                        <td align=center><?= $exceptionQ3Submission ? Html::a('View Report', ['/rpmes/project-exception/view', 'id' => $exceptionQ3Submission->id],['class' => 'btn btn-default btn-sm']) : Html::a('Create Report', ['/rpmes/project-exception'],['class' => 'btn btn-success btn-sm'])?></td>
                                    </tr>
                                    <tr>
                                        <td>4th Quarter</td>
                                        <td><?= $exceptionQ4 ? date("F j, Y", strtotime($exceptionQ4->due_date)) : 'Not set' ?></td>
                                        <td><?= $exceptionQ4Submission ? $exceptionQ4Submission->currentStatus : 'No submission' ?></td>
                                        <td align=center><?= $exceptionQ4Submission ? $exceptionQ4Submission->currentSubmissionLog ? $exceptionQ4Submission->currentSubmissionLog->remarks : '' : '' ?></td>
                                        <td align=center><?= $exceptionQ4Submission ? Html::a('View Report', ['/rpmes/project-exception/view', 'id' => $exceptionQ4Submission->id],['class' => 'btn btn-default btn-sm']) : Html::a('Create Report', ['/rpmes/project-exception'],['class' => 'btn btn-success btn-sm'])?></td>
                                    </tr>
                                    <tr>
                                        <td>Form 4: Project Results Report</td>
                                        <td>-</td>
                                        <td><?= $projectResults ? date("F j, Y", strtotime($projectResults->due_date)) : 'Not set' ?></td>
                                        <td><?= $projectResultsSubmission ? $projectResultsSubmission->currentStatus : 'No submission' ?></td>
                                        <td align=center><?= $projectResultsSubmission ? $projectResultsSubmission->currentSubmissionLog ? $projectResultsSubmission->currentSubmissionLog->remarks : '' : '' ?></td>
                                        <td align=center><?= $projectResultsSubmission ? Html::a('View Report', ['/rpmes/project-result/view', 'id' => $projectResultsSubmission->id],['class' => 'btn btn-default btn-sm']) : Html::a('Create Report', ['/rpmes/project-result'],['class' => 'btn btn-success btn-sm'])?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-4 col-xs-12">
                            <h4><b>Statistics</b><br>
                            <small>as of CY <?= date("Y") ?></small>
                            </h4>
                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <div class="box box-solid">
                                        <div class="box-body">
                                            <h5>Projects</h5>
                                            <h1 class="text-center"><b><?= number_format($projectCount, 0) ?></b></h1>
                                            <p class="text-center" style="font-size: 12px; color: gray;">registered in the system</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-12">
                                    <div class="box box-solid">
                                        <div class="box-body">
                                            <h5>Form 1 Reports</h5>
                                            <h1 class="text-center"><b><?= number_format($planCount, 0) ?></b></h1>
                                            <p class="text-center" style="font-size: 12px; color: gray;"><?= $planCount > 1 ? 'annual submissions' : 'annual submission' ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12">
                                    <div class="box box-solid">
                                        <div class="box-body">
                                            <h5>Form 2 Reports</h5>
                                            <h1 class="text-center"><b><?= number_format($accompCount, 0) ?></b></h1>
                                            <p class="text-center" style="font-size: 12px; color: gray;"><?= $accompCount > 1 ? 'quarterly submissions' : 'quarterly submission' ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-12">
                                    <div class="box box-solid">
                                        <div class="box-body">
                                            <h5>Form 3 Reports</h5>
                                            <h1 class="text-center"><b><?= number_format($exceptionCount, 0) ?></b></h1>
                                            <p class="text-center" style="font-size: 12px; color: gray;"><?= $exceptionCount > 1 ? 'quarterly submissions' : 'quarterly submission' ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12">
                                    <div class="box box-solid">
                                        <div class="box-body">
                                            <h5>Form 4 Reports</h5>
                                            <h1 class="text-center"><b><?= number_format($resultCount, 0) ?></b></h1>
                                            <p class="text-center" style="font-size: 12px; color: gray;"><?= $resultCount > 1 ? 'annual submissions' : 'annual submission' ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
        <div class="col-md-3 col-xs-12">
            <h4><b>Recent Activities</b></h4>
            <div style="height: calc(100vh - 220px); overflow-y: scroll; padding-right: 20px;">
            <?php if($logs){ ?>
                <?php foreach($logs as $log){ ?>
                    <div class="box box-solid">
                        <div class="box-body">
                        <?php if($log->status == 'Submitted'){ ?>
                            <p><?= Yii::$app->user->can('Administrator') ? ucwords(strtolower($log->actor)) : 'You' ?> submitted the Agency <?= $log->submission->report ?> <?= $log->submission->year ?> Report<?= Yii::$app->user->can('Administrator') ? ' of '.$log->submission->agency->title : '' ?></p>
                            <small class="pull-left"><?= date("F j, Y", strtotime($log->datetime)) ?></small>
                            <small class="pull-right"><?= date("h:i:s A", strtotime($log->datetime)) ?></small>
                            <small class="clearfix"></small>
                        <?php }else if($log->status == 'For further validation'){ ?>
                            <p><?= Yii::$app->user->can('Administrator') ? 'You' : ucwords(strtolower($log->actor)).' has' ?> requested to revise the Agency <?= $log->submission->report ?> <?= $log->submission->year ?> Report<?= Yii::$app->user->can('Administrator') ? ' of '.$log->submission->agency->title : '' ?></p>
                            <small class="pull-left"><?= date("F j, Y", strtotime($log->datetime)) ?></small>
                            <small class="pull-right"><?= date("h:i:s A", strtotime($log->datetime)) ?></small>
                            <small class="clearfix"></small>
                        <?php }else if($log->status == 'Acknowledged'){ ?>
                            <p><?= Yii::$app->user->can('Administrator') ? 'You' : ucwords(strtolower($log->actor)).' of NEDA RO1' ?> acknowledged <?= Yii::$app->user->can('Administrator') ? 'the' : 'your' ?> Agency <?= $log->submission->report ?> <?= $log->submission->year ?> Report<?= Yii::$app->user->can('Administrator') ? ' of '.$log->submission->agency->title : '' ?></p>
                            <small class="pull-left"><?= date("F j, Y", strtotime($log->datetime)) ?></small>
                            <small class="pull-right"><?= date("h:i:s A", strtotime($log->datetime)) ?></small>
                            <small class="clearfix"></small>
                        <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
            </div>
        </div>
    </div>
</div>
