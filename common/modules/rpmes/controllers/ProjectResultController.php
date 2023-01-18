<?php

namespace common\modules\rpmes\controllers;

use Yii;
use common\modules\rpmes\models\Agency;
use common\modules\rpmes\models\Plan;
use common\modules\rpmes\models\Project;
use common\modules\rpmes\models\ProjectResult;
use common\modules\rpmes\models\Accomplishment;
use common\modules\rpmes\models\PRojectOutcome;
use common\modules\rpmes\models\ProjectResultSearch;
use common\modules\rpmes\models\MultipleModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;

/**
 * ProjectResultController implements the CRUD actions for ProjectResult model.
 */
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
    public function actionIndex()
    {
        $resultModels = [];
        $outcomes = [];
        $project = null;
        
        $model = new Project();

        $model->scenario = Yii::$app->user->can('Administrator') ? 'projectResultAdmin' : 'projectResult';
       
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

        $projects = [];

        if(!empty($params))
        {
            $accomps = Accomplishment::find()->select(['project_id', 'IF(sum(COALESCE(action, 0)) > 0, 1, 0) as isCompleted'])->groupBy(['project_id'])->createCommand()->getRawSql();

            $projects = Project::find()
                    ->select(['id', 'concat(project_no,": ",title) as title'])
                    ->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');

            $projects = Yii::$app->user->can('Administrator') ? $params['Project']['agency_id'] != '' ? $projects->andWhere(['agency_id' => $params['Project']['agency_id']]) : $projects : $projects->andWhere(['agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]);

            $projects = $params['Project']['year'] != '' ? $projects->andWhere(['year' => $params['Project']['year']]) : $projects;
            $projects = $projects->andWhere(['draft' => 'No']);
            $projects = $projects->andWhere(['accomps.isCompleted' => 1]);
            
            $projects = $projects
                        ->asArray()
                        ->all();
            
            $projects = ArrayHelper::map($projects, 'id', 'title');
        }

        if($model->load(Yii::$app->request->get()))
        {
            $getData = Yii::$app->request->get('Project');

            $project = Project::findOne($model->id);

            $agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : $model->agency_id;

            $outcomes = ProjectOutcome::find()->where(['project_id' => $model->id])->all();

            if($outcomes)
            {
                foreach($outcomes as $outcome)
                {
                    $resultModel = ProjectResult::findOne(['year' => $model->year, 'quarter' => $model->quarter, 'project_id' => $model->id, 'project_outcome_id' => $outcome->id]) ? ProjectResult::findOne(['year' => $model->year, 'quarter' => $model->quarter, 'project_id' => $model->id, 'project_outcome_id' => $outcome->id]) : new ProjectResult();

                    $resultModel->year = $model->year;
                    $resultModel->quarter = $model->quarter;
                    $resultModel->project_id = $model->id;
                    $resultModel->project_outcome_id = $outcome->id;

                    $resultModels[$outcome->id] = $resultModel;
                }
            }

            /* $projectResultsPaging = ProjectResults::find();
            $projectsPaging->andWhere(['id' => $projectIDs]);
            $countProjects = clone $projectsPaging;
            $projectsPages = new Pagination(['totalCount' => $countProjects->count()]);
            $projectsModels = $projectsPaging->offset($projectsPages->offset)
                ->limit($projectsPages->limit)
                ->orderBy(['id' => SORT_ASC])
                ->all();

            $projects = Yii::$app->user->can('AgencyUser') ? Plan::find()
                        ->leftJoin('project', 'project.id = plan.project_id')
                        ->where(['project.draft' => 'No', 'project.agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C, 'plan.year' => $model->year])
                        ->all() : 
                        Plan::find()
                        ->leftJoin('project', 'project.id = plan.project_id')
                        ->where(['project.draft' => 'No', 'project.agency_id' => $model->agency_id, 'plan.year' => $model->year])
                        ->all();

            if($projects)
            {
                foreach($projects as $project)
                {

                    $resultModel = ProjectResult::findOne(['project_id' => $project->project_id, 'year' => $project->year, 'quarter' => $model->quarter]) ?
                    ProjectResult::findOne(['project_id' => $project->project_id, 'year' => $project->year, 'quarter' => $model->quarter]) : new ProjectResult();

                    $resultModel->project_id = $project->project_id;
                    $resultModel->year = $project->year;
                    $resultModel->quarter = $model->quarter;

                    $projectResults[$project->project_id] = $resultModel;

                    $accomplishmentAccomp = Accomplishment::findOne(['project_id' => $project->project_id, 'year' => $project->year, 'quarter' => $model->quarter]) ?
                    Accomplishment::findOne(['project_id' => $project->project_id, 'year' => $project->year, 'quarter' => $model->quarter]) : new Accomplishment();

                    $accomplishmentAccomp->project_id = $project->project_id;
                    $accomplishmentAccomp->year = $project->year;
                    $accomplishmentAccomp->quarter = $model->quarter;
                    $accomplishmentAccomp->action = $project->project->isCompleted == true ? 1 : 0;

                    $accomplishment[$project->project_id] = $accomplishmentAccomp;

                    $projectOutcomeModel = ProjectOutcome::findOne(['project_id' => $project->project_id, 'year' => $project->year]) ?
                    ProjectOutcome::findOne(['project_id' => $project->project_id, 'year' => $project->year]) : new ProjectOutcome();

                    $projectOutcomeModel->project_id = $project->project_id;
                    $projectOutcomeModel->year = $project->year;

                    $projectOutcome[$project->project_id] = $projectOutcomeModel;
                }
            } */
        }
        if(
            MultipleModel::loadMultiple($resultModels, Yii::$app->request->post()) /* &&
            MultipleModel::loadMultiple($projectResults, Yii::$app->request->post()) &&
            MultipleModel::loadMultiple($accomplishment, Yii::$app->request->post()) &&
            MultipleModel::loadMultiple($projectOutcome, Yii::$app->request->post()) */
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
                        if(!($flag = $resultModel->save())){
                            $transaction->rollBack();
                            break;
                        }
                    }
                }

                if($flag)
                {
                    $transaction->commit();

                        \Yii::$app->getSession()->setFlash('success', 'Project Results Saved');
                        return Yii::$app->user->can('AgencyUser') ? 
                            $this->redirect(['/rpmes/project-result/', 
                            'Project[year]' => $getData['year'], 
                            'Project[id]' => $getData['id'], 
                            'Project[quarter]' => $getData['quarter'],
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
            'projects' => $projects,
            'outcomes' => $outcomes,
            'resultModels' => $resultModels
            /* 'accomplishment' => $accomplishment,
            'projectResults' => $projectResults,
            'projectOutcome' => $projectOutcome,
            'projectsModels' => $projectsModels,
            'projectsPages' => $projectsPages,
            'getData' => $getData,
            'agency_id' => $agency_id */
        ]);
    }

    /**
     * Displays a single ProjectResult model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ProjectResult model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProjectResult();
        $projects = Project::find()->select(['project.id','CONCAT(agency.code,'.'": ",'.'project.title) as title','agency.code']);
        $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
        $projects = $projects->andWhere(['project.draft' => 'No']);
        $projects = $projects->orderBy(['agency.code' => SORT_ASC])->asArray()->all();
        $projects = ArrayHelper::map($projects, 'id', 'title');

        $years = Project::find()->select(['distinct(year) as year'])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');


        if ($model->load(Yii::$app->request->post())) {
            $model->submitted_by = Yii::$app->user->id;
            $model->date_submitted = date('Y-m-d H:i:s');
            $model->save();
            \Yii::$app->getSession()->setFlash('success', 'Record Saved');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'projects' => $projects,
            'years' => $years,
        ]);
    }

    /**
     * Updates an existing ProjectResult model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $projects = Project::find()->select(['project.id','CONCAT(agency.code,'.'": ",'.'project.title) as title','agency.code']);
        $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
        $projects = $projects->andWhere(['project.draft' => 'No']);
        $projects = $projects->orderBy(['agency.code' => SORT_ASC])->asArray()->all();
        $projects = ArrayHelper::map($projects, 'id', 'title');

        $years = Project::find()->select(['distinct(year) as year'])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');

        if ($model->load(Yii::$app->request->post())) {
            \Yii::$app->getSession()->setFlash('success', 'Record Updated');
            $model->save();
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'projects' => $projects,
            'years' => $years,
        ]);
    }

    /**
     * Deletes an existing ProjectResult model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \Yii::$app->getSession()->setFlash('success', 'Record Deleted');
        return $this->redirect(['index']);
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
}
