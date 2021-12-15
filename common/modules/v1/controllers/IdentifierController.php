<?php

namespace common\modules\v1\controllers;

use Yii;
use common\modules\v1\models\CostStructure;
use common\modules\v1\models\OrganizationalOutcome;
use common\modules\v1\models\Program;
use common\modules\v1\models\SubProgram;
use common\modules\v1\models\Identifier;
use common\modules\v1\models\IdentifierSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
/**
 * IdentifierController implements the CRUD actions for Identifier model.
 */
class IdentifierController extends Controller
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
                        'roles' => ['Administrator'],
                    ],
                ],
            ],
        ];
    }

    public function actionOrganizationalOutcomeList($id)
    {
        $organizationalOutcomes = OrganizationalOutcome::find()->select([
            'id',
            'concat(code," - ",title) as title'
        ])
        ->where(['cost_structure_id' => $id])
        ->asArray()
        ->all();

        $arr = [];
        $arr[] = ['id'=>'','text'=>''];
        foreach($organizationalOutcomes as $organizationalOutcome){
            $arr[] = ['id' => $organizationalOutcome['id'] ,'text' => $organizationalOutcome['title']];
        }
        \Yii::$app->response->format = 'json';
        return $arr;
    }

    public function actionProgramList($id, $organizationalOutcomeId)
    {
        $programs = Program::find()->select([
            'id',
            'concat(code," - ",title) as title'
        ])
        ->where([
            'cost_structure_id' => $id,
            'organizational_outcome_id' => $organizationalOutcomeId,
        ])
        ->asArray()
        ->all();

        $arr = [];
        $arr[] = ['id'=>'','text'=>''];
        foreach($programs as $program){
            $arr[] = ['id' => $program['id'] ,'text' => $program['title']];
        }
        \Yii::$app->response->format = 'json';
        return $arr;
    }

    public function actionSubProgramList($id, $organizationalOutcomeId, $programId)
    {
        $subPrograms = SubProgram::find()->select([
            'id',
            'concat(code," - ",title) as title'
        ])
        ->where([
            'cost_structure_id' => $id,
            'organizational_outcome_id' => $organizationalOutcomeId,
            'program_id' => $programId,
        ])
        ->asArray()
        ->all();

        $arr = [];
        $arr[] = ['id'=>'','text'=>''];
        foreach($subPrograms as $subProgram){
            $arr[] = ['id' => $subProgram['id'] ,'text' => $subProgram['title']];
        }
        \Yii::$app->response->format = 'json';
        return $arr;
    }

    /**
     * Lists all Identifier models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new IdentifierSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Identifier model.
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
     * Creates a new Identifier model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Identifier();

        $costStructures = CostStructure::find()->select([
            'id',
            'concat(code," - ",title) as title'
        ])
        ->asArray()
        ->all();

        $costStructures = ArrayHelper::map($costStructures, 'id', 'title');
        $organizationalOutcomes = [];
        $programs = [];
        $subPrograms = [];

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', 'Record Saved');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'costStructures' => $costStructures,
            'organizationalOutcomes' => $organizationalOutcomes,
            'programs' => $programs,
            'subPrograms' => $subPrograms,
        ]);
    }

    /**
     * Updates an existing Identifier model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $costStructures = CostStructure::find()->select([
            'id',
            'concat(code," - ",title) as title'
        ])
        ->asArray()
        ->all();

        $costStructures = ArrayHelper::map($costStructures, 'id', 'title');

        $organizationalOutcomes = OrganizationalOutcome::find()->select([
            'id',
            'concat(code," - ",title) as title'
        ])
        ->where(['cost_structure_id' => $model->cost_structure_id])
        ->asArray()
        ->all();

        $organizationalOutcomes = ArrayHelper::map($organizationalOutcomes, 'id', 'title');

        $programs = Program::find()->select([
            'id',
            'concat(code," - ",title) as title'
        ])
        ->where([
            'cost_structure_id' => $model->cost_structure_id,
            'organizational_outcome_id' => $model->organizational_outcome_id,
        ])
        ->asArray()
        ->all();

        $programs = ArrayHelper::map($programs, 'id', 'title');

        $subPrograms = SubProgram::find()->select([
            'id',
            'concat(code," - ",title) as title'
        ])
        ->where([
            'cost_structure_id' => $model->cost_structure_id,
            'organizational_outcome_id' => $model->organizational_outcome_id,
            'program_id' => $model->program_id,
        ])
        ->asArray()
        ->all();

        $subPrograms = ArrayHelper::map($subPrograms, 'id', 'title');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', 'Record Updated');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'costStructures' => $costStructures,
            'organizationalOutcomes' => $organizationalOutcomes,
            'programs' => $programs,
            'subPrograms' => $subPrograms,
        ]);
    }

    /**
     * Deletes an existing Identifier model.
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
     * Finds the Identifier model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Identifier the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Identifier::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
