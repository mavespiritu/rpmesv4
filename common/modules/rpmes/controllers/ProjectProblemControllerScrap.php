<?php

namespace common\modules\rpmes\controllers;

use Yii;
use common\modules\rpmes\models\ProjectProblem;
use common\modules\rpmes\models\Project;
use common\modules\rpmes\models\ProjectProblemSearch;
use common\modules\rpmes\models\Sector;
use common\modules\rpmes\models\SubSector;
use common\modules\rpmes\models\Agency;
use common\modules\rpmes\models\ProjectOutcome;
use common\modules\rpmes\models\Model;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * ProjectProblemController implements the CRUD actions for ProjectProblem model.
 */
class ProjectProblemController extends Controller
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
     * Lists all ProjectProblem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectProblemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProjectProblem model.
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
     * Creates a new ProjectProblem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $projects = Project::find()->where(['draft' => 'No'])->all();
        $projects = ArrayHelper::map($projects, 'id', 'title');

        $sectors = Sector::find()->all();
        $sectors = ArrayHelper::map($sectors, 'id', 'title');

        $subSectors = SubSector::find()->all();
        $subSectors = ArrayHelper::map($subSectors, 'id', 'title');

        $agencies = Agency::find()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $problemModels = [new ProjectProblem()];
        $problemModels = Model::createMultiple(ProjectProblem::classname());
        Model::loadMultiple($problemModels, Yii::$app->request->post()); 

        $valid = Model::validateMultiple($problemModels) && $valid; 

        if ($model->load(Yii::$app->request->post())) {

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
    
                        foreach ($problemModels as $problemModel) {
                            $problemModel->project_id = $model->project_id;
                            $problemModel->submitted_by = Yii::$app->user->id;
                            $problemModel->date_submitted = date('Y-m-d H:i:s');
                            if (! ($flag = $problemModel->save())) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        \Yii::$app->getSession()->setFlash('success', 'Record Saved');
                        return $this->redirect(['index']);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            } 
        }

        return $this->render('create', [
            'problemModels' => (empty($problemModels)) ? [new ProjectProblem] : $problemModels,
            'projects' => $projects,
            'sectors' => $sectors,
            'subSectors' => $subSectors,
            'agencies' => $agencies,
            'model' => $model,
            'projectModel' => $projectModel,
        ]);
    }

    /**
     * Updates an existing ProjectProblem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->submitted_by = Yii::$app->user->id;
            $model->date_submitted = date('Y-m-d H:i:s');
            $model->save();
            \Yii::$app->getSession()->setFlash('success', 'Record Updated');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProjectProblem model.
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
     * Finds the ProjectProblem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProjectProblem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProjectProblem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
