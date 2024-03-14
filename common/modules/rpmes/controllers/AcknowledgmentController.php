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
use common\modules\rpmes\models\PhysicalAccomplishment;
use common\modules\rpmes\models\FinancialAccomplishment;
use common\modules\rpmes\models\PersonEmployedAccomplishment;
use common\modules\rpmes\models\BeneficiariesAccomplishment;
use common\modules\rpmes\models\Accomplishment;
use common\modules\rpmes\models\Acknowledgment;
use common\modules\rpmes\models\Settings;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\helpers\Json;
use yii\data\Pagination;

class AcknowledgmentController extends \yii\web\Controller
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
                'only' => ['monitoring-plan', 'monitoring-report', 'project-exception', 'project-results'],
                'rules' => [
                    [
                        'actions' => ['monitoring-plan', 'monitoring-report', 'project-exception', 'project-results'],
                        'allow' => true,
                        'roles' => ['Administrator', 'SuperAdministrator'],
                    ],
                ],
            ],
        ];
    }
    public function actionMonitoringPlan()
    {
        $model = new Submission();
        $model->scenario = 'acknowledgmentMonitoringPlan';

        $submissions = null;
        $agencyIDs = null;
        $getData = [];

        $years = Submission::find()->select(['distinct(year) as year'])->orderBy(['year' => SORT_DESC])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        $agencies = Agency::find()->select(['id', 'code as title'])->orderBy(['code' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        if($model->load(Yii::$app->request->get()))
        {
            $getData = Yii::$app->request->get('Submission');
            $submissions = Agency::find();

            $agencyIDs = Plan::find()->where(['year' => $model->year])->asArray()->all();
            $agencyIDs = ArrayHelper::map($agencyIDs, 'agency_id', 'agency_id');

            $submissions = $submissions->andWhere(['id' => $agencyIDs]);

            if($model->agency_id != '')
            {
                $submissions = $submissions->andWhere(['id' => $model->agency_id]);
            }

            $submissions = Yii::$app->user->can('AgencyUser') ? $submissions->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $submissions;

            $submissions = $submissions->orderBy(['code' => SORT_ASC])->all();

        }

        return $this->render('monitoring-plan', [
            'model' => $model,
            'years' => $years,
            'agencies' => $agencies,
            'submissions' => $submissions,
            'getData' => $getData,
        ]);

    }

    public function actionAcknowledgeMonitoringPlan($id)
    {
        $officeTitle = Settings::findOne(['Agency Title Long']);
        $officeAddress = Settings::findOne(['Agency Address']);
        $officeHead = Settings::findOne(['Agency Head']);
        $officeTitleShort = Settings::findOne(['Agency Title Short']);
        $submission = Submission::findOne(['id' => $id]);
        $agency = Agency::findOne(['id' => $submission->agency_id]);
        $model = Acknowledgment::findOne(['submission_id' => $submission->id]) ? Acknowledgment::findOne(['submission_id' => $submission->id]) : new Acknowledgment();
        $lastAcknowledgment = Acknowledgment::find()->orderBy(['id' => SORT_DESC])->one();
        $lastNumber = $lastAcknowledgment ? intval($lastAcknowledgment->id) + 1 : '1';
        $model->submission_id = $submission->id;
        $model->control_no = $model->isNewRecord ? 'NEDARO1-QOP-03-'.date("Y").'001'.$lastNumber : $model->control_no;
        $model->recipient_name = $agency->head;
        $model->recipient_designation = $agency->head_designation;
        $model->recipient_office = $agency->title;
        $model->recipient_address = $agency->address;
        $model->acknowledged_by = Yii::$app->user->id;

        if($model->load(Yii::$app->request->post()))
        {
            $model->save();

            \Yii::$app->getSession()->setFlash('success', 'Acknowledgment has been saved successfully');
            return $this->redirect(['/rpmes/acknowledgment/monitoring-plan', 'Submission[year]' => $submission->year]);
        }

        return $this->renderAjax('_monitoring-plan-form', [
            'model' => $model,
            'submission' => $submission,
            'agency' => $agency,
            'officeTitle' => $officeTitle,
            'officeAddress' => $officeAddress,
            'officeHead' => $officeHead,
            'officeTitleShort' => $officeTitleShort,
        ]);
    }

    public function actionPrintMonitoringPlan($id)
    {
        $acknowledgment = Acknowledgment::findOne(['id' => $id]);
        $submission = Submission::findOne(['id' => $acknowledgment->submission_id]);
        $officeTitle = Settings::findOne(['Agency Title Long']);
        $officeAddress = Settings::findOne(['Agency Address']);
        $officeHead = Settings::findOne(['Agency Head']);
        $officeTitleShort = Settings::findOne(['Agency Title Short']);

        return $this->renderAjax('_monitoring-plan', [
            'acknowledgment' => $acknowledgment,
            'submission' => $submission,
            'officeTitle' => $officeTitle,
            'officeAddress' => $officeAddress,
            'officeHead' => $officeHead,
            'officeTitleShort' => $officeTitleShort,
        ]);
    }

    public function actionMonitoringReport()
    {
        $model = new Submission();
        $model->scenario = 'acknowledgmentMonitoringPlan';

        $submissions = null;
        $agencyIDs = null;
        $getData = [];
        $quarters = ['Q1' => 'First Quarter', 'Q2' => 'Second Quarter', 'Q3' => 'Third Quarter', 'Q4' => 'Fourth Quarter'];

        $years = Submission::find()->select(['distinct(year) as year'])->orderBy(['year' => SORT_DESC])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        $agencies = Agency::find()->select(['id', 'code as title'])->orderBy(['code' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');
        
        if($model->load(Yii::$app->request->get()))
        {
            $getData = Yii::$app->request->get('Submission');
            $submissions = Agency::find();

            $agencyIDs = Plan::find()->where(['year' => $model->year])->asArray()->all();
            $agencyIDs = ArrayHelper::map($agencyIDs, 'agency_id', 'agency_id');

            $submissions = $submissions->andWhere(['id' => $agencyIDs]);

            if($model->agency_id != '')
            {
                $submissions = $submissions->andWhere(['id' => $model->agency_id]);
            }

            $submissions = Yii::$app->user->can('AgencyUser') ? $submissions->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $submissions;

            $submissions = $submissions->orderBy(['code' => SORT_ASC])->all();

        }

        return $this->render('monitoring-report', [
            'model' => $model,
            'years' => $years,
            'agencies' => $agencies,
            'submissions' => $submissions,
            'getData' => $getData,
            'quarters' => $quarters,
        ]);
    }
    
    public function actionAcknowledgeMonitoringReport($id)
    {
        $officeTitle = Settings::findOne(['Agency Title Long']);
        $officeAddress = Settings::findOne(['Agency Address']);
        $officeHead = Settings::findOne(['Agency Head']);
        $officeTitleShort = Settings::findOne(['Agency Title Short']);
        $submission = Submission::findOne(['id' => $id]);
        $agency = Agency::findOne(['id' => $submission->agency_id]);
        $model = Acknowledgment::findOne(['submission_id' => $submission->id]) ? Acknowledgment::findOne(['submission_id' => $submission->id]) : new Acknowledgment();
        $lastAcknowledgment = Acknowledgment::find()->orderBy(['id' => SORT_DESC])->one();
        $lastNumber = $lastAcknowledgment ? intval($lastAcknowledgment->id) + 1 : '1';
        $model->submission_id = $submission->id;
        $model->control_no = $model->isNewRecord ? 'NEDARO1-QOP-03-'.date("Y").'001'.$lastNumber : $model->control_no;
        $model->recipient_name = $agency->head;
        $model->recipient_designation = $agency->head_designation;
        $model->recipient_office = $agency->title;
        $model->recipient_address = $agency->address;
        $model->acknowledged_by = Yii::$app->user->id;

        if($model->load(Yii::$app->request->post()))
        {
            $model->save();

            \Yii::$app->getSession()->setFlash('success', 'Acknowledgment has been saved successfully');
            return $this->redirect(['/rpmes/acknowledgment/monitoring-report', 'Submission[year]' => $submission->year]);
        }

        return $this->renderAjax('_monitoring-report-form', [
            'model' => $model,
            'submission' => $submission,
            'agency' => $agency,
            'officeTitle' => $officeTitle,
            'officeAddress' => $officeAddress,
            'officeHead' => $officeHead,
            'officeTitleShort' => $officeTitleShort,
        ]);
    }

    public function actionPrintMonitoringReport($id)
    {
        $acknowledgment = Acknowledgment::findOne(['id' => $id]);
        $submission = Submission::findOne(['id' => $acknowledgment->submission_id]);
        $officeTitle = Settings::findOne(['Agency Title Long']);
        $officeAddress = Settings::findOne(['Agency Address']);
        $officeHead = Settings::findOne(['Agency Head']);
        $officeTitleShort = Settings::findOne(['Agency Title Short']);

        return $this->renderAjax('_monitoring-report', [
            'acknowledgment' => $acknowledgment,
            'submission' => $submission,
            'officeTitle' => $officeTitle,
            'officeAddress' => $officeAddress,
            'officeHead' => $officeHead,
            'officeTitleShort' => $officeTitleShort,
        ]);
    }

    public function actionProjectException()
    {
        $model = new Submission();
        $model->scenario = 'acknowledgmentMonitoringPlan';

        $submissions = null;
        $agencyIDs = null;
        $getData = [];
        $quarters = ['Q1' => 'First Quarter', 'Q2' => 'Second Quarter', 'Q3' => 'Third Quarter', 'Q4' => 'Fourth Quarter'];

        $years = Submission::find()->select(['distinct(year) as year'])->orderBy(['year' => SORT_DESC])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        $agencies = Agency::find()->select(['id', 'code as title'])->orderBy(['code' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');
        
        if($model->load(Yii::$app->request->get()))
        {
            $getData = Yii::$app->request->get('Submission');
            $submissions = Agency::find();

            $agencyIDs = Plan::find()->where(['year' => $model->year])->asArray()->all();
            $agencyIDs = ArrayHelper::map($agencyIDs, 'agency_id', 'agency_id');

            $submissions = $submissions->andWhere(['id' => $agencyIDs]);

            if($model->agency_id != '')
            {
                $submissions = $submissions->andWhere(['id' => $model->agency_id]);
            }

            $submissions = Yii::$app->user->can('AgencyUser') ? $submissions->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $submissions;

            $submissions = $submissions->orderBy(['code' => SORT_ASC])->all();

        }

        return $this->render('project-exception', [
            'model' => $model,
            'years' => $years,
            'agencies' => $agencies,
            'submissions' => $submissions,
            'getData' => $getData,
            'quarters' => $quarters,
        ]);
    }

    public function actionAcknowledgeProjectException($id)
    {
        $officeTitle = Settings::findOne(['Agency Title Long']);
        $officeAddress = Settings::findOne(['Agency Address']);
        $officeHead = Settings::findOne(['Agency Head']);
        $officeTitleShort = Settings::findOne(['Agency Title Short']);
        $submission = Submission::findOne(['id' => $id]);
        $agency = Agency::findOne(['id' => $submission->agency_id]);
        $model = Acknowledgment::findOne(['submission_id' => $submission->id]) ? Acknowledgment::findOne(['submission_id' => $submission->id]) : new Acknowledgment();
        $lastAcknowledgment = Acknowledgment::find()->orderBy(['id' => SORT_DESC])->one();
        $lastNumber = $lastAcknowledgment ? intval($lastAcknowledgment->id) + 1 : '1';
        $model->submission_id = $submission->id;
        $model->control_no = $model->isNewRecord ? 'NEDARO1-QOP-03-'.date("Y").'001'.$lastNumber : $model->control_no;
        $model->recipient_name = $agency->head;
        $model->recipient_designation = $agency->head_designation;
        $model->recipient_office = $agency->title;
        $model->recipient_address = $agency->address;
        $model->acknowledged_by = Yii::$app->user->id;

        if($model->load(Yii::$app->request->post()))
        {
            $model->save();

            \Yii::$app->getSession()->setFlash('success', 'Acknowledgment has been saved successfully');
            return $this->redirect(['/rpmes/acknowledgment/project-exception', 'Submission[year]' => $submission->year]);
        }

        return $this->renderAjax('_project-exception-form', [
            'model' => $model,
            'submission' => $submission,
            'agency' => $agency,
            'officeTitle' => $officeTitle,
            'officeAddress' => $officeAddress,
            'officeHead' => $officeHead,
            'officeTitleShort' => $officeTitleShort,
        ]);
    }
    public function actionPrintProjectException($id)
    {
        $acknowledgment = Acknowledgment::findOne(['id' => $id]);
        $submission = Submission::findOne(['id' => $acknowledgment->submission_id]);
        $officeTitle = Settings::findOne(['Agency Title Long']);
        $officeAddress = Settings::findOne(['Agency Address']);
        $officeHead = Settings::findOne(['Agency Head']);
        $officeTitleShort = Settings::findOne(['Agency Title Short']);

        return $this->renderAjax('_project-exception', [
            'acknowledgment' => $acknowledgment,
            'submission' => $submission,
            'officeTitle' => $officeTitle,
            'officeAddress' => $officeAddress,
            'officeHead' => $officeHead,
            'officeTitleShort' => $officeTitleShort,
        ]);
    }

    public function actionProjectResults()
    {
        $model = new Submission();
        $model->scenario = 'acknowledgmentMonitoringPlan';

        $submissions = null;
        $agencyIDs = null;
        $getData = [];

        $years = Submission::find()->select(['distinct(year) as year'])->orderBy(['year' => SORT_DESC])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        $agencies = Agency::find()->select(['id', 'code as title'])->orderBy(['code' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        if($model->load(Yii::$app->request->get()))
        {
            $getData = Yii::$app->request->get('Submission');
            $submissions = Agency::find();

            $agencyIDs = Plan::find()->where(['year' => $model->year])->asArray()->all();
            $agencyIDs = ArrayHelper::map($agencyIDs, 'agency_id', 'agency_id');

            $submissions = $submissions->andWhere(['id' => $agencyIDs]);

            if($model->agency_id != '')
            {
                $submissions = $submissions->andWhere(['id' => $model->agency_id]);
            }

            $submissions = Yii::$app->user->can('AgencyUser') ? $submissions->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $submissions;

            $submissions = $submissions->orderBy(['code' => SORT_ASC])->all();

        }

        return $this->render('project-results', [
            'model' => $model,
            'years' => $years,
            'agencies' => $agencies,
            'submissions' => $submissions,
            'getData' => $getData,
        ]);

    }

    public function actionAcknowledgeProjectResults($id)
    {
        $officeTitle = Settings::findOne(['Agency Title Long']);
        $officeAddress = Settings::findOne(['Agency Address']);
        $officeHead = Settings::findOne(['Agency Head']);
        $officeTitleShort = Settings::findOne(['Agency Title Short']);
        $submission = Submission::findOne(['id' => $id]);
        $agency = Agency::findOne(['id' => $submission->agency_id]);
        $model = Acknowledgment::findOne(['submission_id' => $submission->id]) ? Acknowledgment::findOne(['submission_id' => $submission->id]) : new Acknowledgment();
        $lastAcknowledgment = Acknowledgment::find()->orderBy(['id' => SORT_DESC])->one();
        $lastNumber = $lastAcknowledgment ? intval($lastAcknowledgment->id) + 1 : '1';
        $model->submission_id = $submission->id;
        $model->control_no = $model->isNewRecord ? 'NEDARO1-QOP-03-'.date("Y").'001'.$lastNumber : $model->control_no;
        $model->recipient_name = $agency->head;
        $model->recipient_designation = $agency->head_designation;
        $model->recipient_office = $agency->title;
        $model->recipient_address = $agency->address;
        $model->acknowledged_by = Yii::$app->user->id;

        if($model->load(Yii::$app->request->post()))
        {
            $model->save();

            \Yii::$app->getSession()->setFlash('success', 'Acknowledgment has been saved successfully');
            return $this->redirect(['/rpmes/acknowledgment/project-results', 'Submission[year]' => $submission->year]);
        }

        return $this->renderAjax('_project-results-form', [
            'model' => $model,
            'submission' => $submission,
            'agency' => $agency,
            'officeTitle' => $officeTitle,
            'officeAddress' => $officeAddress,
            'officeHead' => $officeHead,
            'officeTitleShort' => $officeTitleShort,
        ]);
    }

    public function actionPrintProjectResults($id)
    {
        $acknowledgment = Acknowledgment::findOne(['id' => $id]);
        $submission = Submission::findOne(['id' => $acknowledgment->submission_id]);
        $officeTitle = Settings::findOne(['Agency Title Long']);
        $officeAddress = Settings::findOne(['Agency Address']);
        $officeHead = Settings::findOne(['Agency Head']);
        $officeTitleShort = Settings::findOne(['Agency Title Short']);

        return $this->renderAjax('_monitoring-plan', [
            'acknowledgment' => $acknowledgment,
            'submission' => $submission,
            'officeTitle' => $officeTitle,
            'officeAddress' => $officeAddress,
            'officeHead' => $officeHead,
            'officeTitleShort' => $officeTitleShort,
        ]);
    }

    public function actionDeleteSubmission($id, $report)
    {
        $model = Submission::findOne(['id' => $id, 'report' => $report]);
        $submission = $model;
        if($model->delete())
        {
            \Yii::$app->getSession()->setFlash('success', 'Submission has been deleted successfully');
            return $report == 'Monitoring Plan' ? $this->redirect(['/rpmes/acknowledgment/monitoring-plan', 'Submission[year]' => $submission->year]) : $this->redirect(['/rpmes/acknowledgment/monitoring-report', 'Submission[year]' => $submission->year]);
        }
    }
}
