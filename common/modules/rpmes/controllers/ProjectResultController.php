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

    /**
     * Lists all ProjectResult models.
     * @return mixed
     */
    public function actionIndex()
    {
        $projectResults = [];
        $accomplishment = [];
        $projectOutcome = [];
        $getData = [];

        $projectsModels = null;
        $projectsPages = null;
        $dueDate = null;
        $agency_id = null;
        
        $model = new Project();

        $model->scenario='projectResult';
        $model->year = date("Y");
        $model->agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : null;

        $years = ProjectResult::find()->select(['distinct(year) as year'])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];

        $agencies = Agency::find()->select(['id', 'code as title']);
        $agencies = Yii::$app->user->can('AgencyUser') ? $agencies->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $agencies;
        $agencies = $agencies->orderBy(['code' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        if($model->load(Yii::$app->request->get()))
        {
            $agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : $model->agency_id;

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

            $projectsPaging = Project::find();
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
            }
        }
        if(
            MultipleModel::loadMultiple($projectResults, Yii::$app->request->post()) &&
            MultipleModel::loadMultiple($accomplishment, Yii::$app->request->post()) &&
            MultipleModel::loadMultiple($projectOutcome, Yii::$app->request->post())
        )
        {

            $transaction = \Yii::$app->db->beginTransaction();
            $getData = Yii::$app->request->get('Project');

            try{
                if(!empty($projectResults))
                {
                    foreach($projectResults as $projectResult)
                    {
                        if(!($flag = $projectResult->save())){
                            $transaction->rollBack();
                            break;
                        }
                    }
                }

                if(!empty($accomplishment))
                {
                    foreach($accomplishment as $accomp)
                    {
                        if(!($flag = $accomp->save())){
                            $transaction->rollBack();
                            break;
                        }
                    }
                }

                if(!empty($projectOutcome))
                {
                    foreach($projectOutcome as $projectOut)
                    {
                        if(!($flag = $projectOut->save())){
                            $transaction->rollBack();
                            break;
                        }
                    }
                }

                if($flag)
                {
                    $transaction->commit();

                        \Yii::$app->getSession()->setFlash('success', 'Project Results Saved');
                        return Yii::$app->user->can('AgencyUser') ? isset($getData['page']) ? 
                            $this->redirect(['/rpmes/project-result/', 
                            'Project[year]' => $getData['year'],
                            'Project[quarter]' => $getData['quarter'],  
                            'page' => $getData['page'],
                        ]) : $this->redirect(['/rpmes/project-result/', 
                            'Project[year]' => $getData['year'], 
                            'Project[quarter]' => $getData['quarter'],
                        ]) : $this->redirect(['/rpmes/project-result/', 
                            'Project[year]' => $getData['year'], 
                            'Project[quarter]' => $getData['quarter'],
                            'Project[agency_id]' => $getData['agency_id'], 
                        ]);
                }
            }catch(\Exception $e){
                $transaction->rollBack();
            }
        }

        return $this->render('index', [
            'model' => $model,
            'quarters' => $quarters,
            'years' => $years,
            'agencies' => $agencies,
            'accomplishment' => $accomplishment,
            'projectResults' => $projectResults,
            'projectOutcome' => $projectOutcome,
            'projectsModels' => $projectsModels,
            'projectsPages' => $projectsPages,
            'getData' => $getData,
            'agency_id' => $agency_id
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
