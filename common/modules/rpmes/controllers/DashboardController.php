<?php

namespace common\modules\rpmes\controllers;

use Yii;
use common\models\Region;
use common\models\Province;
use common\models\Citymun;
use common\models\Barangay;
use common\modules\rpmes\models\DueDate;
use common\modules\rpmes\models\Project;
use common\modules\rpmes\models\Plan;
use common\modules\rpmes\models\ProjectTarget;
use common\modules\rpmes\models\ProjectRegion;
use common\modules\rpmes\models\ProjectProvince;
use common\modules\rpmes\models\ProjectCitymun;
use common\modules\rpmes\models\ProjectBarangay;
use common\modules\rpmes\models\ProjectCategory;
use common\modules\rpmes\models\ProjectSdgGoal;
use common\modules\rpmes\models\ProjectRdpChapter;
use common\modules\rpmes\models\ProjectRdpChapterOutcome;
use common\modules\rpmes\models\ProjectRdpSubChapterOutcome;
use common\modules\rpmes\models\ProjectExpectedOutput;
use common\modules\rpmes\models\ProjectOutcome;
use common\modules\rpmes\models\Agency;
use common\modules\rpmes\models\Program;
use common\modules\rpmes\models\Sector;
use common\modules\rpmes\models\SubSector;
use common\modules\rpmes\models\SubSectorPerSector;
use common\modules\rpmes\models\Category;
use common\modules\rpmes\models\KeyResultArea;
use common\modules\rpmes\models\ModeOfImplementation;
use common\modules\rpmes\models\FundSource;
use common\modules\rpmes\models\LocationScope;
use common\modules\rpmes\models\SdgGoal;
use common\modules\rpmes\models\RdpChapter;
use common\modules\rpmes\models\RdpChapterOutcome;
use common\modules\rpmes\models\RdpSubChapterOutcome;
use common\modules\rpmes\models\ProjectSearch;
use common\modules\rpmes\models\Model;
use common\modules\rpmes\models\MultipleModel;
use common\modules\rpmes\models\Submission;
use common\modules\rpmes\models\SubmissionLog;
use common\modules\rpmes\models\PhysicalAccomplishment;
use common\modules\rpmes\models\FinancialAccomplishment;
use common\modules\rpmes\models\PersonEmployedAccomplishment;
use common\modules\rpmes\models\BeneficiariesAccomplishment;
use common\modules\rpmes\models\GroupAccomplishment;
use common\modules\rpmes\models\Accomplishment;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\helpers\Json;
use yii\data\Pagination;

class DashboardController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['AgencyUser', 'Administrator', 'SuperAdministrator'],
                    ],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $logModel = new Project();
        $logModel->scenario = Yii::$app->user->can('Administrator') ? 'searchSubmissionLogAdmin' : 'searchSubmissionLog';
        $logModel->year = date("Y");

        $monitoringPlan = DueDate::findOne(['report' => 'Monitoring Plan', 'year' => date("Y")]);
        $accompQ1 = DueDate::findOne(['report' => 'Accomplishment', 'year' => date("Y"), 'quarter' => 'Q1']);
        $accompQ2 = DueDate::findOne(['report' => 'Accomplishment', 'year' => date("Y"), 'quarter' => 'Q2']);
        $accompQ3 = DueDate::findOne(['report' => 'Accomplishment', 'year' => date("Y"), 'quarter' => 'Q3']);
        $accompQ4 = DueDate::findOne(['report' => 'Accomplishment', 'year' => date("Y"), 'quarter' => 'Q4']);
        $exceptionQ1 = DueDate::findOne(['report' => 'Project Exception', 'year' => date("Y"), 'quarter' => 'Q1']);
        $exceptionQ2 = DueDate::findOne(['report' => 'Project Exception', 'year' => date("Y"), 'quarter' => 'Q2']);
        $exceptionQ3 = DueDate::findOne(['report' => 'Project Exception', 'year' => date("Y"), 'quarter' => 'Q3']);
        $exceptionQ4 = DueDate::findOne(['report' => 'Project Exception', 'year' => date("Y"), 'quarter' => 'Q4']);
        $projectResults = DueDate::findOne(['report' => 'Project Results', 'year' => date("Y")]);

        $monitoringPlanSubmission = Submission::findOne(['report' => 'Monitoring Plan', 'year' => date("Y"), 'agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]);
        $accompQ1Submission = Submission::findOne(['report' => 'Accomplishment', 'year' => date("Y"), 'quarter' => 'Q1', 'agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]);
        $accompQ2Submission = Submission::findOne(['report' => 'Accomplishment', 'year' => date("Y"), 'quarter' => 'Q2', 'agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]);
        $accompQ3Submission = Submission::findOne(['report' => 'Accomplishment', 'year' => date("Y"), 'quarter' => 'Q3', 'agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]);
        $accompQ4Submission = Submission::findOne(['report' => 'Accomplishment', 'year' => date("Y"), 'quarter' => 'Q4', 'agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]);
        $exceptionQ1Submission = Submission::findOne(['report' => 'Project Exception', 'year' => date("Y"), 'quarter' => 'Q1', 'agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]);
        $exceptionQ2Submission = Submission::findOne(['report' => 'Project Exception', 'year' => date("Y"), 'quarter' => 'Q2', 'agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]);
        $exceptionQ3Submission = Submission::findOne(['report' => 'Project Exception', 'year' => date("Y"), 'quarter' => 'Q3', 'agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]);
        $exceptionQ4Submission = Submission::findOne(['report' => 'Project Exception', 'year' => date("Y"), 'quarter' => 'Q4', 'agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]);
        $projectResultsSubmission = Submission::findOne(['report' => 'Project Results', 'year' => date("Y"), 'agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]);

        $years = Submission::find()->select(['distinct(year) as year'])->orderBy(['year' => SORT_DESC])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        $agencies = Agency::find()->select(['id', 'code as title']);
        $agencies = Yii::$app->user->can('AgencyUser') ? $agencies->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $agencies;
        $agencies = $agencies->orderBy(['code' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $projectCount = Yii::$app->user->can('AgencyUser') ? Project::find()->where(['agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C])->andWhere(['source_id' => null])->count() : Project::find()->andWhere(['source_id' => null])->count();
        $planCount = Yii::$app->user->can('AgencyUser') ? Submission::find()->where(['report' => 'Monitoring Plan', 'agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C])->count() : Submission::find()->where(['report' => 'Monitoring Plan'])->count();
        $accompCount = Yii::$app->user->can('AgencyUser') ? Submission::find()->where(['report' => 'Accomplishment', 'agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C])->count() : Submission::find()->where(['report' => 'Accomplishment'])->count();
        $exceptionCount = Yii::$app->user->can('AgencyUser') ? Submission::find()->where(['report' => 'Project Exception', 'agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C])->count() : Submission::find()->where(['report' => 'Project Exception'])->count();
        $resultCount = Yii::$app->user->can('AgencyUser') ? Submission::find()->where(['report' => 'Project Results', 'agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C])->count() : Submission::find()->where(['report' => 'Project Results'])->count();

        $logs = Yii::$app->user->can('AgencyUser') ? SubmissionLog::find()
                ->leftJoin('submission', 'submission.id = submission_log.submission_id')
                ->where(['submission.agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C])
                ->orderBy(['id' => SORT_DESC])
                ->limit(10)
                ->all() : SubmissionLog::find()
                ->leftJoin('submission', 'submission.id = submission_log.submission_id')
                ->where(['submission_log.user_id' => Yii::$app->user->id])
                ->orderBy(['id' => SORT_DESC])
                ->limit(10)
                ->all();

        return $this->render('index',[
            'monitoringPlan' => $monitoringPlan,
            'accompQ1' => $accompQ1,
            'accompQ2' => $accompQ2,
            'accompQ3' => $accompQ3,
            'accompQ4' => $accompQ4,
            'exceptionQ1' => $exceptionQ1,
            'exceptionQ2' => $exceptionQ2,
            'exceptionQ3' => $exceptionQ3,
            'exceptionQ4' => $exceptionQ4,
            'projectResults' => $projectResults,
            'monitoringPlanSubmission' => $monitoringPlanSubmission,
            'accompQ1Submission' => $accompQ1Submission,
            'accompQ2Submission' => $accompQ2Submission,
            'accompQ3Submission' => $accompQ3Submission,
            'accompQ4Submission' => $accompQ4Submission,
            'exceptionQ1Submission' => $exceptionQ1Submission,
            'exceptionQ2Submission' => $exceptionQ2Submission,
            'exceptionQ3Submission' => $exceptionQ3Submission,
            'exceptionQ4Submission' => $exceptionQ4Submission,
            'projectResultsSubmission' => $projectResultsSubmission,
            'years' => $years,
            'agencies' => $agencies,
            'logModel' => $logModel,
            'logs' => $logs,
            'projectCount' => $projectCount,
            'planCount' => $planCount,
            'accompCount' => $accompCount,
            'exceptionCount' => $exceptionCount,
            'resultCount' => $resultCount,
        ]);
    }

    public function actionSubmissionLog($year, $agency_id)
    {
        $agency_id = Yii::$app->user->can('Administrator') ? $agency_id : Yii::$app->user->identity->userinfo->AGENCY_C;

        $projectTotal = Project::find()->where(['draft' => 'No', 'year' => $year, 'agency_id' => $agency_id])->count();

        $monitoringPlan = Submission::findOne(['report' => 'Monitoring Plan', 'year' => $year, 'agency_id' => $agency_id]);
        $accompQ1 = Submission::findOne(['report' => 'Accomplishment', 'year' => $year, 'quarter' => 'Q1', 'agency_id' => $agency_id]);
        $accompQ2 = Submission::findOne(['report' => 'Accomplishment', 'year' => $year, 'quarter' => 'Q2', 'agency_id' => $agency_id]);
        $accompQ3 = Submission::findOne(['report' => 'Accomplishment', 'year' => $year, 'quarter' => 'Q3', 'agency_id' => $agency_id]);
        $accompQ4 = Submission::findOne(['report' => 'Accomplishment', 'year' => $year, 'quarter' => 'Q4', 'agency_id' => $agency_id]);
        $exceptionQ1 = Submission::findOne(['report' => 'Project Exception', 'year' => $year, 'quarter' => 'Q1', 'agency_id' => $agency_id]);
        $exceptionQ2 = Submission::findOne(['report' => 'Project Exception', 'year' => $year, 'quarter' => 'Q2', 'agency_id' => $agency_id]);
        $exceptionQ3 = Submission::findOne(['report' => 'Project Exception', 'year' => $year, 'quarter' => 'Q3', 'agency_id' => $agency_id]);
        $exceptionQ4 = Submission::findOne(['report' => 'Project Exception', 'year' => $year, 'quarter' => 'Q4', 'agency_id' => $agency_id]);

        return $this->renderAjax('submission_log',[
            'monitoringPlan' => $monitoringPlan,
            'accompQ1' => $accompQ1,
            'accompQ2' => $accompQ2,
            'accompQ3' => $accompQ3,
            'accompQ4' => $accompQ4,
            'exceptionQ1' => $exceptionQ1,
            'exceptionQ2' => $exceptionQ2,
            'exceptionQ3' => $exceptionQ3,
            'exceptionQ4' => $exceptionQ4,
            'projectTotal' => $projectTotal,
        ]);
    }
}