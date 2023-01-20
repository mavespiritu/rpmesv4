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
        throw new NotFoundHttpException('This site is temporary down');
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
