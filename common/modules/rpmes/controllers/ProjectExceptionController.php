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
use common\modules\rpmes\models\Accomplishment;
use common\modules\rpmes\models\PlanSearch;
use common\modules\rpmes\models\Typology;
use common\modules\rpmes\models\Settings;
use common\modules\rpmes\models\Acknowledgment;
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

class ProjectExceptionController extends \yii\web\Controller
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
                'only' => ['index', 'create', 'update', 'delete', 'view', 'review'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'view'],
                        'allow' => true,
                        'roles' => ['AgencyUser', 'Administrator', 'SuperAdministrator'],
                    ],
                    [
                        'actions' => ['review'],
                        'allow' => true,
                        'roles' => ['AgencyUser', 'Administrator', 'SuperAdministrator'],
                    ],
                ],
            ],
        ];
    }

    /* public function actionIndex()
    {
        $exceptions = [];
        $getData = [];

        $projectsModels = null;
        $projectsPages = null;
        $submissionModel = null;
        $dueDate = null;
        $agency_id = null;
        
        $model = new Project();
        $model->scenario = Yii::$app->user->can('AgencyUser') ? 'projectExceptionUser' : 'projectExceptionAdmin';
        $model->year = date("Y");
        $model->agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : null;

        $agencies = Agency::find()->select(['id', 'code as title']);
        $agencies = Yii::$app->user->can('AgencyUser') ? $agencies->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $agencies;
        $agencies = $agencies->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $years = Project::find()->select(['distinct(year) as year'])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];
        $statuses = ['Behind Schedule' => 'Behind Schedule', 'Ahead of Schedule' => 'Ahead of Schedule'];

        if($model->load(Yii::$app->request->get()))
        {   
            $agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : $model->agency_id;

            $submissionModel = Submission::findOne(['agency_id' => $model->agency_id, 'report' => 'Project Exception', 'year' => $model->year, 'quarter' => $model->quarter]) ?
                               Submission::findOne(['agency_id' => $model->agency_id, 'report' => 'Project Exception', 'year' => $model->year, 'quarter' => $model->quarter]) : new Submission();
            $dueDate = DueDate::findOne(['report' => 'Project Exception', 'quarter' => $model->quarter, 'year' => $model->year]);
            if(Yii::$app->user->can('AgencyUser')){
                if(Yii::$app->user->identity->userinfo->AGENCY_C != $model->agency_id)
                {
                    throw new ForbiddenHttpException('Not allowed to access');
                }
            }

            $getData = Yii::$app->request->get('Project');

            $projectIDs = Yii::$app->user->can('AgencyUser') ? Plan::find()
                        ->select(['plan.project_id as id'])
                        ->leftJoin('project', 'project.id = plan.project_id')
                        ->where(['project.draft' => 'No', 'project.agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C, 'plan.year' => $model->year])
                        ->asArray()
                        ->all() : 
                        Plan::find()
                        ->select(['plan.project_id as id'])
                        ->leftJoin('project', 'project.id = plan.project_id')
                        ->where(['project.draft' => 'No', 'project.agency_id' => $model->agency_id, 'plan.year' => $model->year])
                        ->asArray()
                        ->all();
            $projectIDs = ArrayHelper::map($projectIDs, 'id', 'id');

            $projects = Yii::$app->user->can('AgencyUser') ? Plan::find()
                        ->leftJoin('project', 'project.id = plan.project_id')
                        ->where(['project.draft' => 'No', 'project.agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C, 'plan.year' => $model->year])
                        ->all() : 
                        Plan::find()
                        ->leftJoin('project', 'project.id = plan.project_id')
                        ->where(['project.draft' => 'No', 'project.agency_id' => $model->agency_id, 'plan.year' => $model->year])
                        ->all();

            $selectedIDs = [];
            
            if($model->status != '')
            {
                if($projects)
                {
                    foreach($projects as $project)
                    {
                        if(($project->project->getImplementationStatus($model->quarter) == $model->status) && $project->project->isCompleted == false)
                        {
                            $exceptionModel = ProjectException::findOne(['project_id' => $project->project_id, 'year' => $model->year, 'quarter' => $model->quarter]) ? 
                            ProjectException::findOne(['project_id' => $project->project_id, 'year' => $model->year, 'quarter' => $model->quarter]) : new ProjectException();
                            $exceptionModel->project_id = $project->project_id;
                            $exceptionModel->year = $model->year;
                            $exceptionModel->quarter = $model->quarter;
                            $exceptionModel->submitted_by = Yii::$app->user->id;
                            $exceptions[$project->project_id] = $exceptionModel;
                            $selectedIDs[] = $project->project_id;
                        }
                    }
                }
            }else{
                if($projects)
                {
                    foreach($projects as $project)
                    {
                        if((($project->project->getPhysicalSlippage($model->quarter) <= -15) || ($project->project->getPhysicalSlippage($model->quarter) >= 15)) && $project->project->isCompleted == false)
                        {
                            $exceptionModel = ProjectException::findOne(['project_id' => $project->project_id, 'year' => $model->year, 'quarter' => $model->quarter]) ? 
                            ProjectException::findOne(['project_id' => $project->project_id, 'year' => $model->year, 'quarter' => $model->quarter]) : new ProjectException();
                            $exceptionModel->project_id = $project->project_id;
                            $exceptionModel->year = $model->year;
                            $exceptionModel->quarter = $model->quarter;
                            $exceptionModel->submitted_by = Yii::$app->user->id;
                            $exceptions[$project->project_id] = $exceptionModel;
                            $selectedIDs[] = $project->project_id;
                        }
                    }
                }
            }

            $projectsPaging = Project::find();
            $projectsPaging->andWhere(['id' => $selectedIDs]);
            $countProjects = clone $projectsPaging;
            $projectsPages = new Pagination(['totalCount' => $countProjects->count()]);
            $projectsModels = $projectsPaging->offset($projectsPages->offset)
                ->limit($projectsPages->limit)
                ->orderBy(['id' => SORT_ASC])
                ->all();
        }

        if(
            MultipleModel::loadMultiple($exceptions, Yii::$app->request->post())
        )
        {

            $transaction = \Yii::$app->db->beginTransaction();
            $getData = Yii::$app->request->get('Project');

            try{
                if(!empty($exceptions))
                {
                    foreach($exceptions as $exception)
                    {
                        if(!($flag = $exception->save())){
                            $transaction->rollBack();
                            break;
                        }
                    }
                }

                if($flag)
                {
                    $transaction->commit();

                        \Yii::$app->getSession()->setFlash('success', 'Accomplishment Saved');
                        return Yii::$app->user->can('AgencyUser') ? isset($getData['page']) ? 
                            $this->redirect(['/rpmes/project-exception/', 
                            'Project[year]' => $getData['year'], 
                            'Project[quarter]' => $getData['quarter'], 
                            'Project[status]' => $getData['status'], 
                            'page' => $getData['page'],
                        ]) : $this->redirect(['/rpmes/project-exception/', 
                            'Project[year]' => $getData['year'], 
                            'Project[quarter]' => $getData['quarter'], 
                            'Project[status]' => $getData['status'], 
                        ]) : $this->redirect(['/rpmes/project-exception/', 
                            'Project[year]' => $getData['year'], 
                            'Project[agency_id]' => $getData['agency_id'], 
                            'Project[quarter]' => $getData['quarter'], 
                            'Project[status]' => $getData['status'], 
                        ]);
                }
            }catch(\Exception $e){
                $transaction->rollBack();
            }
        }

        return $this->render('index',[
            'model' => $model,
            'years' => $years,
            'agencies' => $agencies,
            'quarters' => $quarters,
            'statuses' => $statuses,
            'exceptions' => $exceptions,
            'projectsModels' => $projectsModels,
            'projectsPages' => $projectsPages,
            'getData' => $getData,
            'dueDate' => $dueDate,
            'submissionModel' => $submissionModel,
            'agency_id' => $agency_id
        ]);
    } */

    public function actionIndex()
    {
        $searchModel = new SubmissionSearch();
        $searchModel->report = 'Project Exception';

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
        $model->scenario = Yii::$app->user->can('Administrator') ? 'createProjectExceptionReportAdmin' : 'createProjectExceptionReport';

        $model->report = 'Project Exception';
        $model->agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : $model->agency_id;
        $model->draft = 'Yes';

        $agencies = Agency::find()->select(['id', 'concat(title," (",code,")") as title'])->orderBy(['title' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];

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
            'quarters' => $quarters,
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

        $model->scenario = Yii::$app->user->can('Administrator') ? 'createProjectExceptionReportAdmin' : 'createProjectExceptionReport';

        $agencies = Agency::find()->select(['id', 'concat(title," (",code,")") as title'])->orderBy(['title' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];

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
            'quarters' => $quarters,
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

        $slippages = [];

        $dueDate = DueDate::findOne(['year' => $model->year, 'quarter' => $model->quarter, 'report' => 'Project Exception']);

        $months = [
            'jan' => 'January',
            'feb' => 'February',
            'mar' => 'March',
            'apr' => 'April',
            'may' => 'May',
            'jun' => 'June',
            'jul' => 'July',
            'aug' => 'August',
            'sep' => 'September',
            'oct' => 'October',
            'nov' => 'November',
            'dec' => 'December',
        ];

        $monthsWithoutJanuary = [
            'feb' => 'Feb',
            'mar' => 'Mar',
            'apr' => 'Apr',
            'may' => 'May',
            'jun' => 'Jun',
            'jul' => 'Jul',
            'aug' => 'Aug',
            'sep' => 'Sep',
            'oct' => 'Oct',
            'nov' => 'Nov',
            'dec' => 'Dec',
        ];

        $monthsWithoutDecember = [
            'jan' => 'Jan',
            'feb' => 'Feb',
            'mar' => 'Mar',
            'apr' => 'Apr',
            'may' => 'May',
            'jun' => 'Jun',
            'jul' => 'Jul',
            'aug' => 'Aug',
            'sep' => 'Sep',
            'oct' => 'Oct',
            'nov' => 'Nov',
        ];

        $quarters = [
            'Q1' => [
                'jan' => 'Jan',
                'feb' => 'Feb',
                'mar' => 'Mar',
            ],
            'Q2' => [
                'jan' => 'Jan',
                'feb' => 'Feb',
                'mar' => 'Mar',
                'apr' => 'Apr',
                'may' => 'May',
                'jun' => 'Jun',
            ],
            'Q3' => [
                'jan' => 'Jan',
                'feb' => 'Feb',
                'mar' => 'Mar',
                'apr' => 'Apr',
                'may' => 'May',
                'jun' => 'Jun',
                'jul' => 'Jul',
                'aug' => 'Aug',
                'sep' => 'Sep',
            ],
            'Q4' => [
                'jan' => 'Jan',
                'feb' => 'Feb',
                'mar' => 'Mar',
                'apr' => 'Apr',
                'may' => 'May',
                'jun' => 'Jun',
                'jul' => 'Jul',
                'aug' => 'Aug',
                'sep' => 'Sep',
                'oct' => 'Oct',
                'nov' => 'Nov',
                'dec' => 'Dec',
            ]
        ];

        if(!Yii::$app->user->can('Administrator')){
            if($model->agency_id != Yii::$app->user->identity->userinfo->AGENCY_C){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $model->year, 'project_id' => $projectIDs])->createCommand()->getRawSql();

        $physicalTotal = 'IF(project.data_type <> "Default",';
        foreach(array_reverse($monthsWithoutJanuary) as $mo => $month){
            $physicalTotal .= 'IF(COALESCE(physicalTargets.'.$mo.', 0) <= 0,';
        }
        $physicalTotal .= 'COALESCE(physicalTargets.jan, 0)';
        foreach($monthsWithoutJanuary as $mo => $month){
            $physicalTotal .= ', COALESCE(physicalTargets.'.$mo.', 0))';
        }
        $physicalTotal .= ',';
        foreach($monthsWithoutDecember as $mo => $month){
            $physicalTotal .= 'COALESCE(physicalTargets.'.$mo.', 0) +';
        }
        $physicalTotal .= 'COALESCE(physicalTargets.dec, 0))';

        $physicalAccomplishment = PhysicalAccomplishment::find()->where([
            'year' => $model->year,
            'quarter' => $model->quarter,
            'project_id' => $projectIDs
        ])
        ->createCommand()->getRawSql();

        $targetOwpa = [];

        foreach ($quarters as $q => $mos) {
            $targetOwpa[$q] = 'IF(physicalTargets.type = "Numerical", 
                                IF('.$physicalTotal.' > 0, ';

            $con = 'COALESCE(physicalTargets.baseline, 0) + ';

            foreach ($mos as $mo => $month) {
                $con .= $month === end($mos) ? 'COALESCE(physicalTargets.'.$mo.', 0)' : 'COALESCE(physicalTargets.'.$mo.', 0) + ';
            }

            $targetOwpa[$q] .= '(('.$con.')/('.$physicalTotal.')*100)';
            $targetOwpa[$q] .= ',('.$con.'/('.$physicalTotal.'))*100), '.$con.')';
        }     

        $actualOwpa = 'IF(physicalTargets.type = "Numerical", 
                            IF('.$physicalTotal.' > 0,
                                (COALESCE(physicalAccomplishment.value, 0)/'.$physicalTotal.')*100, 
                            0), 
                        COALESCE(physicalAccomplishment.value,0))';

        $projects = Project::find()
                    ->select([
                        'project.id',
                        'COALESCE('.$actualOwpa.', 0) - COALESCE('.$targetOwpa[$model->quarter].', 0) as slippage',
                    ]);

        $projects = $projects->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
        $projects = $projects->leftJoin(['physicalAccomplishment' => '('.$physicalAccomplishment.')'], 'physicalAccomplishment.project_id = project.id');                                                          
        $projects = $projects->andWhere(['project.draft' => 'No']);
        $projects = $projects->andWhere(['project.source_id' => null]);
        $projects = $projects->andWhere(['project.agency_id' => $model->agency_id]);
        $projects = $projects 
                    ->asArray()
                    ->all();

        if(!empty($projects)){
            foreach($projects as $project){
                if($project['slippage'] <= -10 || $project['slippage'] >= 10){
                    $slippages[] = $project;
                }
            }
        }

        $slippages = ArrayHelper::map($slippages, 'id', 'id');

        
        
        $searchModel = new PlanSearch();
        $searchModel->submission_id = $planSubmission ? $planSubmission->id : $searchModel->submission_id;
        $searchModel->project_id = $slippages;

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dueDate' => $dueDate,
            'quarters' => $quarters,
        ]);
    }

    public function actionCreateFindings($id, $project_id, $page)
    {
        $model = Submission::findOne($id);
        $project = Project::findOne($project_id);

        $exceptionModel = new ProjectException();
        $exceptionModel->project_id = $project->id;
        $exceptionModel->year = $model->year;
        $exceptionModel->quarter = $model->quarter;

        $typologies = Typology::find()->all();
        $typologies = ArrayHelper::map($typologies, 'id', 'title');

        if (Yii::$app->request->isAjax && $exceptionModel->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($exceptionModel);
        }

        if($exceptionModel->load(Yii::$app->request->post())){

            $exceptionModel->submitted_by = Yii::$app->user->id;

            if($exceptionModel->save())
            {
                \Yii::$app->getSession()->setFlash('success', 'Findings for project no. '.$project->project_no.' has been saved successfully.');
                return $page > 1 ? 
                        $this->redirect(['view', 
                            'id' => $model->id, 
                            'page' => $page,
                        ]) : $this->redirect(['view', 
                            'id' => $model->id,
                        ]);
            }
        }

        return $this->renderAjax('_findings-form',[
            'model' => $model,
            'project' => $project,
            'exceptionModel' => $exceptionModel,
            'typologies' => $typologies,
            'action' => 'create',
        ]);
    }

    public function actionUpdateFindings($id, $project_id, $exception_id, $page)
    {
        $model = Submission::findOne($id);
        $project = Project::findOne($project_id);

        $exceptionModel = ProjectException::findOne($exception_id);

        $typologies = Typology::find()->all();
        $typologies = ArrayHelper::map($typologies, 'id', 'title');

        if (Yii::$app->request->isAjax && $exceptionModel->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($exceptionModel);
        }

        if($exceptionModel->load(Yii::$app->request->post())){
            $getData = Yii::$app->request->get();

            $exceptionModel->submitted_by = Yii::$app->user->id;

            if($exceptionModel->save())
            {
                \Yii::$app->getSession()->setFlash('success', 'Findings for project no. '.$project->project_no.' has been updated successfully.');
                return $page > 1 ? 
                        $this->redirect(['view', 
                            'id' => $model->id, 
                            'page' => $page,
                        ]) : $this->redirect(['view', 
                            'id' => $model->id,
                        ]);
            }
        }

        return $this->renderAjax('_findings-form',[
            'model' => $model,
            'project' => $project,
            'exceptionModel' => $exceptionModel,
            'typologies' => $typologies,
            'action' => 'update'
        ]);
    }

    public function actionDeleteFindings($id, $exception_id, $page)
    {

        $model = Submission::findOne($id);

        $exception = ProjectException::findOne($exception_id);

        if(Yii::$app->request->post())
        {
            $getData = Yii::$app->request->get();

            if($exception->delete())
            {
                \Yii::$app->getSession()->setFlash('success', 'Finding has been deleted successfully.');
                return $page > 1 ? 
                        $this->redirect(['view', 
                            'id' => $model->id, 
                            'page' => $page,
                        ]) : $this->redirect(['view', 
                            'id' => $model->id,
                        ]);
            }
        }
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
                \Yii::$app->getSession()->setFlash('success', 'Project Exception Report for '.$model->quarter.' '.$model->year.' has been submitted successfully.');
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

        $slippages = [];

        $months = [
            'jan' => 'January',
            'feb' => 'February',
            'mar' => 'March',
            'apr' => 'April',
            'may' => 'May',
            'jun' => 'June',
            'jul' => 'July',
            'aug' => 'August',
            'sep' => 'September',
            'oct' => 'October',
            'nov' => 'November',
            'dec' => 'December',
        ];

        $monthsWithoutJanuary = [
            'feb' => 'Feb',
            'mar' => 'Mar',
            'apr' => 'Apr',
            'may' => 'May',
            'jun' => 'Jun',
            'jul' => 'Jul',
            'aug' => 'Aug',
            'sep' => 'Sep',
            'oct' => 'Oct',
            'nov' => 'Nov',
            'dec' => 'Dec',
        ];

        $monthsWithoutDecember = [
            'jan' => 'Jan',
            'feb' => 'Feb',
            'mar' => 'Mar',
            'apr' => 'Apr',
            'may' => 'May',
            'jun' => 'Jun',
            'jul' => 'Jul',
            'aug' => 'Aug',
            'sep' => 'Sep',
            'oct' => 'Oct',
            'nov' => 'Nov',
        ];

        $quarters = [
            'Q1' => [
                'jan' => 'Jan',
                'feb' => 'Feb',
                'mar' => 'Mar',
            ],
            'Q2' => [
                'jan' => 'Jan',
                'feb' => 'Feb',
                'mar' => 'Mar',
                'apr' => 'Apr',
                'may' => 'May',
                'jun' => 'Jun',
            ],
            'Q3' => [
                'jan' => 'Jan',
                'feb' => 'Feb',
                'mar' => 'Mar',
                'apr' => 'Apr',
                'may' => 'May',
                'jun' => 'Jun',
                'jul' => 'Jul',
                'aug' => 'Aug',
                'sep' => 'Sep',
            ],
            'Q4' => [
                'jan' => 'Jan',
                'feb' => 'Feb',
                'mar' => 'Mar',
                'apr' => 'Apr',
                'may' => 'May',
                'jun' => 'Jun',
                'jul' => 'Jul',
                'aug' => 'Aug',
                'sep' => 'Sep',
                'oct' => 'Oct',
                'nov' => 'Nov',
                'dec' => 'Dec',
            ]
        ];

        $targets = [];

        if(!Yii::$app->user->can('Administrator')){
            if($model->agency_id != Yii::$app->user->identity->userinfo->AGENCY_C){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $model->year, 'project_id' => $projectIDs])->createCommand()->getRawSql();

        $provinceTitles = ProjectProvince::find()
                    ->select(['project_id', 'GROUP_CONCAT(DISTINCT tblprovince.province_m ORDER BY tblprovince.province_m ASC SEPARATOR ", <br>") as title'])
                    ->leftJoin('tblprovince', 'tblprovince.province_c = project_province.province_id')
                    ->leftJoin('project', 'project.id = project_province.project_id')
                    ->where(['project.draft' => 'No'])
                    ->groupBy(['project_province.project_id'])
                    ->createCommand()->getRawSql();

        $citymunTitles = ProjectCitymun::find()
                    ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat(tblcitymun.citymun_m,", ",tblprovince.province_m) ORDER BY tblcitymun.citymun_m ASC, tblprovince.province_m ASC SEPARATOR ", <br>") as title'])
                    ->leftJoin('tblcitymun', 'tblcitymun.province_c = project_citymun.province_id and tblcitymun.citymun_c = project_citymun.citymun_id')
                    ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
                    ->leftJoin('project', 'project.id = project_citymun.project_id')
                    ->where(['project.draft' => 'No'])
                    ->groupBy(['project_citymun.project_id'])
                    ->createCommand()->getRawSql();
        
        $barangayTitles = ProjectBarangay::find()
                    ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat(tblbarangay.barangay_m,", ",tblcitymun.citymun_m,", ",tblprovince.province_m) ORDER BY tblbarangay.barangay_m ASC, tblcitymun.citymun_m ASC, tblprovince.province_m ASC SEPARATOR ", <br>") as title'])
                    ->leftJoin('tblbarangay', 'tblbarangay.province_c = project_barangay.province_id and tblbarangay.citymun_c = project_barangay.citymun_id and tblbarangay.barangay_c = project_barangay.barangay_id')
                    ->leftJoin('tblcitymun', 'tblcitymun.province_c = project_barangay.province_id and tblcitymun.citymun_c = project_barangay.citymun_id')
                    ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
                    ->leftJoin('project', 'project.id = project_barangay.project_id')
                    ->where(['project.draft' => 'No'])
                    ->groupBy(['project_barangay.project_id'])
                    ->createCommand()->getRawSql();

        $physicalTotal = 'IF(project.data_type <> "Default",';
        foreach(array_reverse($monthsWithoutJanuary) as $mo => $month){
            $physicalTotal .= 'IF(COALESCE(physicalTargets.'.$mo.', 0) <= 0,';
        }
        $physicalTotal .= 'COALESCE(physicalTargets.jan, 0)';
        foreach($monthsWithoutJanuary as $mo => $month){
            $physicalTotal .= ', COALESCE(physicalTargets.'.$mo.', 0))';
        }
        $physicalTotal .= ',';
        foreach($monthsWithoutDecember as $mo => $month){
            $physicalTotal .= 'COALESCE(physicalTargets.'.$mo.', 0) +';
        }
        $physicalTotal .= 'COALESCE(physicalTargets.dec, 0))';

        $physicalAccomplishment = PhysicalAccomplishment::find()->where([
            'year' => $model->year,
            'quarter' => $model->quarter,
            'project_id' => $projectIDs
        ])
        ->createCommand()->getRawSql();

        $targetOwpa = [];

        foreach ($quarters as $q => $mos) {
            $targetOwpa[$q] = 'IF(physicalTargets.type = "Numerical", 
                                IF('.$physicalTotal.' > 0, ';

            $con = 'COALESCE(physicalTargets.baseline, 0) + ';

            foreach ($mos as $mo => $month) {
                $con .= $month === end($mos) ? 'COALESCE(physicalTargets.'.$mo.', 0)' : 'COALESCE(physicalTargets.'.$mo.', 0) + ';
            }

            $targetOwpa[$q] .= '(('.$con.')/('.$physicalTotal.')*100)';
            $targetOwpa[$q] .= ',('.$con.'/('.$physicalTotal.'))*100), '.$con.')';
        }     

        $actualOwpa = 'IF(physicalTargets.type = "Numerical", 
                            IF('.$physicalTotal.' > 0,
                                (COALESCE(physicalAccomplishment.value, 0)/'.$physicalTotal.')*100, 
                            0), 
                        COALESCE(physicalAccomplishment.value,0))';

        $projectsWithSlippage = Project::find()
                    ->select([
                        'project.id',
                        'COALESCE('.$actualOwpa.', 0) - COALESCE('.$targetOwpa[$model->quarter].', 0) as slippage',
                    ]);

        $projectsWithSlippage = $projectsWithSlippage->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
        $projectsWithSlippage = $projectsWithSlippage->leftJoin(['physicalAccomplishment' => '('.$physicalAccomplishment.')'], 'physicalAccomplishment.project_id = project.id');                                                       
        $projectsWithSlippage = $projectsWithSlippage->andWhere(['project.draft' => 'No']);
        $projectsWithSlippage = $projectsWithSlippage->andWhere(['project.source_id' => null]);
        $projectsWithSlippage = $projectsWithSlippage->andWhere(['project.agency_id' => $model->agency_id]);
        $projectsWithSlippage = $projectsWithSlippage 
                    ->asArray()
                    ->all();

        if(!empty($projectsWithSlippage)){
            foreach($projectsWithSlippage as $project){
                if($project['slippage'] <= -10 || $project['slippage'] >= 10){
                    $slippages[] = $project;
                }
            }
        }

        $slippages = ArrayHelper::map($slippages, 'id', 'id');

        $projects = Project::find()
                    ->select([
                        'project.id',
                        'project.title as title',
                        'agency.code as agencyTitle',
                        'sector.title as sectorTitle',
                        'provinceTitles.title as provinceTitle',
                        'citymunTitles.title as citymunTitle',
                        'barangayTitles.title as barangayTitle',
                        'COALESCE('.$actualOwpa.', 0) - COALESCE('.$targetOwpa[$model->quarter].', 0) as slippage',
                    ]);

        $projects = $projects->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
        $projects = $projects->leftJoin(['physicalAccomplishment' => '('.$physicalAccomplishment.')'], 'physicalAccomplishment.project_id = project.id');                                             
        $projects = $projects->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
        $projects = $projects->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id');
        $projects = $projects->leftJoin(['barangayTitles' => '('.$barangayTitles.')'], 'barangayTitles.project_id = project.id');             
        $projects = $projects->leftJoin('sector', 'sector.id = project.sector_id');
        $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
        $projects = $projects->andWhere(['project.draft' => 'No']);
        $projects = $projects->andWhere(['project.source_id' => null]);
        $projects = $projects->andWhere(['project.agency_id' => $model->agency_id]);
        $projects = $projects->andWhere(['project.id' => $slippages]);
        $projects = $projects 
                    ->asArray()
                    ->all();

        $exceptions = [];

        $exceptionValues = ProjectException::find()
                    ->andWhere([
                        'year' => $model->year,
                        'quarter' => $model->quarter,
                    ])
                    ->andWhere(['project_id' => $slippages])
                    ->orderBy(['id' => SORT_ASC])
                    ->all();

        if(!empty($exceptionValues)){
            foreach($exceptionValues as $exception){
                $exceptions[$exception['project_id']][] = $exception;
            }
        }

        $filename = date("YmdHis").'_'.$model->agency->code.'_'.$model->quarter.'_'.$model->year.'_'.'RPMES_Form_3';

        if($type == 'excel')
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=".$filename.".xls");
            return $this->renderPartial('_report-file', [
                'model' => $model,
                'type' => $type,
                'projects' => $projects,
                'exceptions' => $exceptions
            ]);
        }else if($type == 'pdf')
        {
            $content = $this->renderPartial('_report-file', [
                'model' => $model,
                'type' => $type,
                'projects' => $projects,
                'exceptions' => $exceptions
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
                'exceptions' => $exceptions
            ]);
        }
    }

    public function actionEndorse($id)
    {
        $model = Submission::findOne($id);

        $planSubmission = Submission::findOne([
            'year' => $model->year,
            'agency_id' => $model->agency_id,
            'report' => 'Monitoring Plan',
            'draft' => 'No',
        ]);

        $projectIDs = $planSubmission ? $planSubmission->plans ? ArrayHelper::map($planSubmission->plans, 'project_id', 'project_id') : [] : [];

        if(!Yii::$app->user->can('Administrator')){
            if($model->agency_id != Yii::$app->user->identity->userinfo->AGENCY_C){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        $exceptionsPaging = ProjectException::find()
                            ->andWhere(['year' => $model->year])
                            ->andWhere(['quarter' => $model->quarter])
                            ->andWhere(['project_id' => $projectIDs]);
                    
        $countExceptions = clone $exceptionsPaging;
        $exceptionsPages = new Pagination(['totalCount' => $countExceptions->count()]);
        $exceptionsModels = $exceptionsPaging->offset($exceptionsPages->offset)
            ->limit($exceptionsPages->limit)
            ->orderBy(['project_id' => SORT_DESC, 'id' => SORT_ASC])
            ->all();

        $actions = [];

        if($exceptionsModels){
            foreach($exceptionsModels as $exception){
                $exception->scenario = 'review';
                $actions[$exception->id] = $exception;
            }
        }

        if(Yii::$app->request->post()){
            $postData = Yii::$app->request->post();
            $getData = Yii::$app->request->get();
            
            if(!empty($postData['ProjectException'])){
                foreach($postData['ProjectException'] as $exID => $exception){
                    $exceptionModel = ProjectException::findOne($exID);

                    $exceptionModel->for_npmc_action = $exception['for_npmc_action'];
                    $exceptionModel->requested_action = $exception['requested_action'];

                    $exceptionModel->save(false);
                }
            }

            \Yii::$app->getSession()->setFlash('success', 'Reviews were saved successfully');
                return isset($getData['page']) ? 
                    $this->redirect(['endorse', 
                        'id' => $model->id, 
                        'page' => $getData['page'],
                    ]) : $this->redirect(['endorse', 
                        'id' => $model->id,
                    ]);
        }

        return $this->render('endorse', [
            'model' => $model,
            'exceptionsModels' => $exceptionsModels,
            'exceptionsPages' => $exceptionsPages,
            'actions' => $actions,
        ]);
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
