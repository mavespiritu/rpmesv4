<?php

namespace common\modules\rpmes\controllers;

use Yii;
use common\models\Region;
use common\models\Province;
use common\models\Citymun;
use common\models\Barangay;
use common\modules\rpmes\models\DueDate;
use common\modules\rpmes\models\Project;
use common\modules\rpmes\models\ProjectException;
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
use common\modules\rpmes\models\ProjectHasFundSources;
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
use common\modules\rpmes\models\SubmissionSearch;
use common\modules\rpmes\models\Model;
use common\modules\rpmes\models\MultipleModel;
use common\modules\rpmes\models\Submission;
use common\modules\rpmes\models\SubmissionLog;
use common\modules\rpmes\models\PhysicalAccomplishment;
use common\modules\rpmes\models\FinancialAccomplishment;
use common\modules\rpmes\models\PersonEmployedAccomplishment;
use common\modules\rpmes\models\BeneficiariesAccomplishment;
use common\modules\rpmes\models\GroupAccomplishment;
use common\modules\rpmes\models\ExpectedOutputAccomplishment;
use common\modules\rpmes\models\OutcomeAccomplishment;
use common\modules\rpmes\models\Accomplishment;
use common\modules\rpmes\models\PlanSearch;
use common\modules\rpmes\models\Typology;
use common\modules\rpmes\models\Acknowledgment;
use common\modules\rpmes\models\Settings;
use markavespiritu\user\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\db\Query;
use yii\helpers\Json;
use yii\data\Pagination;
use kartik\mpdf\Pdf;
use yii\web\Response;

class ProjectResultController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'view', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionProjectList($agency_id, $year)
    {
        $accomps = Accomplishment::find()->select(['project_id', 'IF(sum(COALESCE(action, 0)) > 0, 1, 0) as isCompleted'])->groupBy(['project_id'])->createCommand()->getRawSql();

        $projects = Project::find()
                    ->select(['id', 'concat(project_no,": ",title) as title'])
                    ->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');
        
        $projects = Yii::$app->user->can('Administrator') ? $agency_id != '' ? $projects->andWhere(['agency_id' => $agency_id]) : $projects : $projects->andWhere(['agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]);

        $projects = $year != '' ? $projects->andWhere(['year' => $year]) : $projects;
        $projects = $projects->andWhere(['draft' => 'No']);
        $projects = $projects->andWhere(['accomps.isCompleted' => 1]);
        
        $projects = $projects
                    ->asArray()
                    ->all();

        $arr = [];
        $arr[] = ['id'=>'','text'=>''];
        foreach($projects as $project){
            $arr[] = ['id' => $project['id'] ,'text' => $project['title']];
        }
        \Yii::$app->response->format = 'json';
        return $arr;
    }

    /**
     * Lists all ProjectResult models.
     * @return mixed
     */
    /* public function actionIndex()
    {
        $resultModels = [];
        $outcomes = [];
        $getData = [];
        $project = null;
        $outcomesPages = null;
        
        $model = new Project();

        $model->scenario = 'projectResult';
       
        $model->agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : null;

        $years = Project::find()->select(['distinct(year) as year'])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];

        $agencies = Agency::find()->select(['id', 'code as title']);
        $agencies = Yii::$app->user->can('AgencyUser') ? $agencies->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $agencies;
        $agencies = $agencies->orderBy(['code' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $params = Yii::$app->request->queryParams;

        if($model->load(Yii::$app->request->get()))
        {
            $getData = Yii::$app->request->get('Project');

            $project = Project::findOne($model->id);

            $agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : $model->agency_id;

            $accomps = Accomplishment::find()->select(['project_id', 'IF(sum(COALESCE(action, 0)) > 0, 1, 0) as isCompleted'])->groupBy(['project_id'])->createCommand()->getRawSql();

            $outcomesPaging = ProjectOutcome::find()
                    ->leftJoin('project', 'project.id = project_outcome.project_id')
                    ->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id')
                    ->andWhere([
                        'project.agency_id' => $model->agency_id,
                        'project.year' => $model->year,
                        'project.draft' => 'No',
                        'accomps.isCompleted' => 1
                    ]);
            
            $countOutcomes = clone $outcomesPaging;
            $outcomesPages = new Pagination(['totalCount' => $countOutcomes->count()]);
            $outcomes = $outcomesPaging->offset($outcomesPages->offset)
                ->limit($outcomesPages->limit)
                ->orderBy(['project.id' => SORT_ASC, 'project_outcome.id' => SORT_ASC])
                ->all();

            if($outcomes)
            {
                foreach($outcomes as $outcome)
                {
                    $resultModel = ProjectResult::findOne(['year' => $model->year, 'quarter' => $model->quarter, 'project_id' => $outcome->project->id, 'project_outcome_id' => $outcome->id]) ? ProjectResult::findOne(['year' => $model->year, 'quarter' => $model->quarter, 'project_id' => $outcome->project->id, 'project_outcome_id' => $outcome->id]) : new ProjectResult();

                    $resultModel->year = $model->year;
                    $resultModel->quarter = $model->quarter;
                    $resultModel->project_id = $outcome->project->id;
                    $resultModel->project_outcome_id = $outcome->id;

                    $resultModels[$outcome->id] = $resultModel;
                }
            }

        }
        if(
            MultipleModel::loadMultiple($resultModels, Yii::$app->request->post())
        )
        {
            $getData = Yii::$app->request->get('Project');

            $transaction = \Yii::$app->db->beginTransaction();

            try{
                if(!empty($resultModels))
                {
                    foreach($resultModels as $resultModel)
                    {
                        $resultModel->submitted_by = Yii::$app->user->id;
                        $resultModel->date_submitted = date("Y-m-d H:i:s");
                        if(!($flag = $resultModel->save(false))){
                            $transaction->rollBack();
                            break;
                        }
                    }
                }

                if($flag)
                {
                    $transaction->commit();

                        \Yii::$app->getSession()->setFlash('success', 'Project Results Saved');
                        return isset($getData['page']) ? 
                            $this->redirect(['/rpmes/project-result/',
                            'Project[agency_id]' => $getData['agency_id'], 
                            'Project[year]' => $getData['year'], 
                            'Project[id]' => $getData['id'], 
                            'Project[quarter]' => $getData['quarter'],
                            'Project[page]' => $getData['page'],
                        ]) : $this->redirect(['/rpmes/project-result/', 
                            'Project[agency_id]' => $getData['agency_id'], 
                            'Project[year]' => $getData['year'], 
                            'Project[id]' => $getData['id'], 
                            'Project[quarter]' => $getData['quarter'],
                            
                        ]);
                }
            }catch(\Exception $e){
                $transaction->rollBack();
            }
        }

        return $this->render('index', [
            'model' => $model,
            'project' => $project,
            'quarters' => $quarters,
            'years' => $years,
            'agencies' => $agencies,
            'outcomes' => $outcomes,
            'resultModels' => $resultModels,
            'outcomesPages' => $outcomesPages,
            'getData' => $getData
        ]);
    } */

    public function actionIndex()
    {
        $searchModel = new SubmissionSearch();
        $searchModel->report = 'Project Results';

        if(Yii::$app->user->can('AgencyUser'))
        {
            $searchModel->agency_id = Yii::$app->user->identity->userinfo->AGENCY_C;
        }

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Submission();
        $model->scenario = Yii::$app->user->can('Administrator') ? 'createProjectResultsReportAdmin' : 'createProjectResultsReport';

        $model->report = 'Project Results';
        $model->agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : $model->agency_id;
        $model->draft = 'Yes';

        $agencies = Agency::find()->select(['id', 'concat(title," (",code,")") as title'])->orderBy(['title' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            \Yii::$app->getSession()->setFlash('success', 'Record created');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->renderAjax('create', [
            'model' => $model,
            'agencies' => $agencies,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = Submission::findOne($id);

        if(!Yii::$app->user->can('Administrator')){
            if($model->agency_id != Yii::$app->user->identity->userinfo->AGENCY_C){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        $model->scenario = Yii::$app->user->can('Administrator') ? 'createProjectResultsReportAdmin' : 'createProjectResultsReport';

        $agencies = Agency::find()->select(['id', 'concat(title," (",code,")") as title'])->orderBy(['title' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');


        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            \Yii::$app->getSession()->setFlash('success', 'Record updated');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->renderAjax('update', [
            'model' => $model,
            'agencies' => $agencies,
        ]);
    }

    public function actionDelete($id)
    {
        $model = Submission::findOne($id);

        if(!Yii::$app->user->can('Administrator')){
            if($model->agency_id != Yii::$app->user->identity->userinfo->AGENCY_C){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        if(Yii::$app->request->post())
        {
            $model->delete();

            \Yii::$app->getSession()->setFlash('success', 'Record deleted');

            return $this->redirect(['index']);
        }
    }

    public function actionView($id)
    {
        $model = Submission::findOne($id);

        $planSubmission = Submission::findOne([
            'year' => $model->year,
            'agency_id' => $model->agency_id,
            'report' => 'Monitoring Plan',
            'draft' => 'No',
        ]);

        $projectIDs = $planSubmission ? $planSubmission->plans ? ArrayHelper::map($planSubmission->plans, 'project_id', 'project_id') : [] : [];

        $projectIDs = ArrayHelper::map($planSubmission->plans, 'project_id', 'project_id');

        $dueDate = DueDate::findOne(['year' => $model->year, 'report' => 'Project Results']);

        if(!Yii::$app->user->can('Administrator')){
            if($model->agency_id != Yii::$app->user->identity->userinfo->AGENCY_C){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
        
        $searchModel = new PlanSearch();
        $searchModel->submission_id = $planSubmission ? $planSubmission->id : $searchModel->submission_id;

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dueDate' => $dueDate,
        ]);
    }

    public function actionAccomplishForm($id)
    {
        $model = Submission::findOne($id);

        $planSubmission = Submission::findOne([
            'year' => $model->year,
            'agency_id' => $model->agency_id,
            'report' => 'Monitoring Plan',
            'draft' => 'No',
        ]);

        $dueDate = DueDate::findOne(['year' => $model->year, 'report' => 'Project Results']);

        if(!Yii::$app->user->can('Administrator')){
            if($model->agency_id != Yii::$app->user->identity->userinfo->AGENCY_C){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if(!$planSubmission){
            throw new ForbiddenHttpException('No included projects in monitoring plan');
        }

        $outcomeIndicators = [];

        $projectIDs = $planSubmission ? $planSubmission->plans ? ArrayHelper::map($planSubmission->plans, 'project_id', 'project_id') : [] : [];

        $projectsPaging = Plan::find();
        $projectsPaging 
            ->andWhere(['project_id' => $projectIDs])
            ->andWhere(['submission_id' => $planSubmission->id]);
        $countProjects = clone $projectsPaging;
        $projectsPages = new Pagination([
            'totalCount' => $countProjects->count(),
            'pageSize' => 5
        ]);
        $projectsModels = $projectsPaging->offset($projectsPages->offset)
            ->limit($projectsPages->limit)
            ->orderBy(['project_id' => SORT_ASC])
            ->all();

        if($projectsModels){
            foreach($projectsModels as $plan){
                $outcomes = $plan->project->getProjectOutcomes()->where([
                    'year' => $model->year
                ])
                ->orderBy(['id' => SORT_ASC])
                ->all();

                if($outcomes){
                    foreach($outcomes as $eo){
                        $outcome = OutcomeAccomplishment::findOne([
                            'project_id' => $plan->project_id,
                            'outcome_id' => $eo->id,
                            'year' => $model->year,
                        ]) ? OutcomeAccomplishment::findOne([
                            'project_id' => $plan->project_id,
                            'outcome_id' => $eo->id,
                            'year' => $model->year,
                        ]) : new OutcomeAccomplishment();
                        
                        $outcome->project_id = $plan->project_id;
                        $outcome->outcome_id = $eo->id;
                        $outcome->year = $model->year;

                        $outcomeIndicators[$plan->project_id][$eo->id] = $outcome;
                    }
                }
            }
        }

        if(Yii::$app->request->post()){
            $postData = Yii::$app->request->post();
            $getData = Yii::$app->request->get();

            $projectModels = $postData['OutcomeAccomplishment'];

            if(!empty($projectModels)){
                foreach($projectModels as $projectID => $oModels){
                    if(!empty($oModels)){
                        foreach($oModels as $oID => $oModel){
                            $outcome = OutcomeAccomplishment::findOne([
                                'project_id' => $projectID,
                                'outcome_id' => $oID,
                                'year' => $model->year,
                            ]) ? OutcomeAccomplishment::findOne([
                                'project_id' => $projectID,
                                'outcome_id' => $oID,
                                'year' => $model->year,
                            ]) : new OutcomeAccomplishment();

                            $outcome->project_id = $projectID;
                            $outcome->outcome_id = $oID;
                            $outcome->year = $model->year;
                            $outcome->value = isset($oModel['value']) ? $oModel['value'] : '';
                            $outcome->save(false);
                        }
                    }
                }
            }

            \Yii::$app->getSession()->setFlash('success', 'Project results were saved successfully');
                    return isset($getData['page']) ? 
                        $this->redirect(['accomplish-form', 
                            'id' => $model->id, 
                            'page' => $getData['page'],
                        ]) : $this->redirect(['accomplish-form', 
                            'id' => $model->id,
                        ]);
        }

        return $this->render('accomplishment-form', [
            'model' => $model,
            'projectsModels' => $projectsModels,
            'projectsPages' => $projectsPages,
            'outcomeIndicators' => $outcomeIndicators,
            'dueDate' => $dueDate,
        ]);
    }

    public function actionSubmit($id)
    {
        if(Yii::$app->request->post())
        {
            $model = Submission::findOne($id);

            $postData = Yii::$app->request->post();

            $model->submitted_by = Yii::$app->user->id;
            $model->date_submitted = date("Y-m-d H:i:s");
            $model->draft = 'No';
            $model->save(false);

            $logModel = new SubmissionLog();
            $logModel->submission_id = $model->id;
            $logModel->user_id = Yii::$app->user->id;
            $logModel->status = 'Submitted';

            if($logModel->save(false))
            {
                \Yii::$app->getSession()->setFlash('success', 'Project Results Report for '.$model->year.' has been submitted successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
    }

    public function actionDownload(
        $id,
        $type,
    )
    {
        $model = Submission::findOne($id);

        $planSubmission = Submission::findOne([
            'year' => $model->year,
            'agency_id' => $model->agency_id,
            'report' => 'Monitoring Plan',
            'draft' => 'No',
        ]);

        $projectIDs = $planSubmission ? $planSubmission->plans ? ArrayHelper::map($planSubmission->plans, 'project_id', 'project_id') : [] : [];

        $projectIDs = ArrayHelper::map($planSubmission->plans, 'project_id', 'project_id');

        if(!Yii::$app->user->can('Administrator')){
            if($model->agency_id != Yii::$app->user->identity->userinfo->AGENCY_C){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        $projects = Project::find()
                    ->select([
                        'project.id',
                        'project.title as title',
                        'project.description as objective',
                    ]);
        $projects = $projects->andWhere(['project.draft' => 'No']);
        $projects = $projects->andWhere(['project.source_id' => null]);
        $projects = $projects->andWhere(['project.agency_id' => $model->agency_id]);
        $projects = $projects->andWhere(['project.id' => $projectIDs]);
        $projects = $projects 
                    ->asArray()
                    ->all();

        $outcomes = [];

        $outcomeValues = ProjectOutcome::find()
                    ->andWhere([
                        'year' => $model->year,
                    ])
                    ->andWhere(['project_id' => $projectIDs])
                    ->orderBy(['id' => SORT_ASC])
                    ->all();

        if(!empty($outcomeValues)){
            foreach($outcomeValues as $outcome){
                $outcomes[$outcome['project_id']][] = $outcome;
            }
        }

        $filename = date("YmdHis").'_'.$model->agency->code.'_'.$model->year.'_'.'RPMES_Form_4';

        if($type == 'excel')
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=".$filename.".xls");
            return $this->renderPartial('_report-file', [
                'model' => $model,
                'type' => $type,
                'projects' => $projects,
                'outcomes' => $outcomes
            ]);
        }else if($type == 'pdf')
        {
            $content = $this->renderPartial('_report-file', [
                'model' => $model,
                'type' => $type,
                'projects' => $projects,
                'outcomes' => $outcomes
            ]);

            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_LEGAL, 
                'orientation' => Pdf::ORIENT_LANDSCAPE, 
                'destination' => Pdf::DEST_DOWNLOAD, 
                'filename' => $filename.'.pdf', 
                'content' => $content,  
                'marginLeft' => 11.4,
                'marginRight' => 11.4,
                'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
                'cssInline' => 'table{font-family: "Arial";border-collapse: collapse;}thead{font-size: 14px;text-align: center;vertical-align: middle;background-color: #002060;color: white;}thead tr{background-color: #002060;color: white;}td{font-size: 14px;border: 1px solid black;vertical-align: middle;}th{text-align: center;border: 1px solid black;vertical-align: middle;}h1,h2,h3,h4,h5,h6{text-align: center;font-weight: bolder;}', 
                ]);
        
                $response = Yii::$app->response;
                $response->format = \yii\web\Response::FORMAT_RAW;
                $headers = Yii::$app->response->headers;
                $headers->add('Content-Type', 'application/pdf');
                return $pdf->render();
        }else if($type == 'print')
        {
            return $this->renderAjax('_report-file', [
                'model' => $model,
                'type' => $type,
                'projects' => $projects,
                'outcomes' => $outcomes
            ]);
        }
    }

    /**
     * Finds the ProjectResult model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProjectResult the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProjectResult::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAcknowledge($id)
    {
        $officeTitle = Settings::findOne(['Agency Title Long']);
        $officeAddress = Settings::findOne(['Agency Address']);
        $officeHead = Settings::findOne(['Agency Head']);
        $officeTitleShort = Settings::findOne(['Agency Title Short']);
        $submission = Submission::findOne($id);
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

        if($model->load(Yii::$app->request->post()))
        {
            $model->acknowledged_by = Yii::$app->user->id;
            if($model->save()){
                $logModel = new SubmissionLog();
                $logModel->submission_id = $submission->id;
                $logModel->user_id = Yii::$app->user->id;
                $logModel->status = 'Acknowledged';

                if($logModel->save())
                {
                    \Yii::$app->getSession()->setFlash('success', 'This report has been acknowledged successfully');
                    return $this->redirect(['view', 'id' => $submission->id]);
                }
            }
        }

        return $this->renderAjax('_acknowledgment-form', [
            'model' => $model,
            'submission' => $submission,
            'agency' => $agency,
            'officeTitle' => $officeTitle,
            'officeAddress' => $officeAddress,
            'officeHead' => $officeHead,
            'officeTitleShort' => $officeTitleShort,
        ]);
    }

    public function actionRevert($id)
    {
        if(!Yii::$app->user->can('Administrator')){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $submission = Submission::findOne($id);

        $model = new SubmissionLog();
        $model->scenario = 'forFurtherValidation';
        $model->submission_id = $submission->id;
        $model->user_id = Yii::$app->user->id;
        $model->status = 'For further validation';

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            \Yii::$app->getSession()->setFlash('success', 'This report has been sent successfully for further validation');
            return $this->redirect(['view', 'id' => $submission->id]);

            
        }

        return $this->renderAjax('_revert-form', [
            'model' => $model,
        ]);
    }
}
