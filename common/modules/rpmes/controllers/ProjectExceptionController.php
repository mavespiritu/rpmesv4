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
use common\modules\rpmes\models\ProjectException;
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
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\helpers\Json;
use yii\data\Pagination;

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
    }

    public function actionSubmit()
    {
        if(Yii::$app->request->post())
        {
            $postData = Yii::$app->request->post();
            
            $submissionModel = new Submission();
            $submissionModel->agency_id = $postData['agency_id'];
            $submissionModel->report = 'Project Exception';
            $submissionModel->year = $postData['year'];
            $submissionModel->quarter = $postData['quarter'];
            $submissionModel->submitted_by = Yii::$app->user->id;
            $submissionModel->draft = 'No';

            if($submissionModel->save())
            {
                \Yii::$app->getSession()->setFlash('success', 'Project Exception '.$postData['quarter'].' '.$postData['year'].' has been submitted.');
                return $this->redirect(['/rpmes/project-exception']);
            }
        }
    }
}
