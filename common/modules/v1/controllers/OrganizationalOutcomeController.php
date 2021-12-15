<?php

namespace common\modules\v1\controllers;

use Yii;
use common\modules\v1\models\CostStructure;
use common\modules\v1\models\OrganizationalOutcome;
use common\modules\v1\models\OrganizationalOutcomeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * OrganizationalOutcomeController implements the CRUD actions for OrganizationalOutcome model.
 */
class OrganizationalOutcomeController extends Controller
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

    /**
     * Lists all OrganizationalOutcome models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrganizationalOutcomeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $costStructures = CostStructure::find()->select([
            'id',
            'concat(code," - ",title) as title'
        ])
        ->asArray()
        ->all();

        $costStructures = ArrayHelper::map($costStructures, 'id', 'title');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'costStructures' => $costStructures,
        ]);
    }

    /**
     * Displays a single OrganizationalOutcome model.
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
     * Creates a new OrganizationalOutcome model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrganizationalOutcome();

        $costStructures = CostStructure::find()->select([
            'id',
            'concat(code," - ",title) as title'
        ])
        ->asArray()
        ->all();

        $costStructures = ArrayHelper::map($costStructures, 'id', 'title');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', 'Record Saved');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'costStructures' => $costStructures,
        ]);
    }

    /**
     * Updates an existing OrganizationalOutcome model.
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', 'Record Updated');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'costStructures' => $costStructures,
        ]);
    }

    /**
     * Deletes an existing OrganizationalOutcome model.
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
     * Finds the OrganizationalOutcome model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrganizationalOutcome the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrganizationalOutcome::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
