<?php

namespace common\modules\v1\controllers;

use Yii;
use common\modules\v1\models\Ris;
use common\modules\v1\models\FundCluster;
use common\modules\v1\models\Signatory;
use common\modules\v1\models\RisSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use markavespiritu\user\models\Office;
use yii\widgets\ActiveForm;
use yii\web\Response;
/**
 * RisController implements the CRUD actions for Ris model.
 */
class RisController extends Controller
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

    public function actionSignatoryList($id)
    {
        $signatories = Signatory::find()->where(['office_id' => $id])->all();

        $arr = [];
        $arr[] = ['id'=>'','text'=>''];
        foreach($signatories as $signatory){
            $arr[] = ['id' => $signatory->id ,'text' => $signatory->name];
        }
        \Yii::$app->response->format = 'json';
        return $arr;
    }

    /**
     * Lists all Ris models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RisSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Ris model.
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

    public function actionInfo($id)
    {
        return $this->renderAjax('_info', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Ris model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Ris();

        $model->scenario = (Yii::$app->user->can('Administrator') || Yii::$app->user->can('Procurement')) ? 'isAdmin' : 'isUser';

        $offices = Office::find()->all();
        $offices = ArrayHelper::map($offices, 'id', 'abbreviation');

        $fundClusters = FundCluster::find()->all();
        $fundClusters = ArrayHelper::map($fundClusters, 'id', 'title');

        $signatories = (Yii::$app->user->can('Administrator') || Yii::$app->user->can('Procurement')) ? [] : Signatory::find()->where(['office_id' => Yii::$app->user->identity->userinfo->OFFICE_C])->all();
        $signatories = ArrayHelper::map($signatories, 'id', 'name');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            $lastRis = Ris::find()->where(['date_created' => date("Y-m-d")])->orderBy(['id' => SORT_DESC])->one();
            $ris_no = $lastRis ? substr(date("Y"), -2).'-'.date("md").'-'.str_pad(substr($lastRis->ris_no, -1) + 1, 3, '0', STR_PAD_LEFT) : substr(date("Y"), -2).'-'.date("md").'-001';
            $model->ris_no = $ris_no;
            $model->date_requested = $model->date_required; 
            $model->created_by = Yii::$app->user->id; 
            $model->date_created = date("Y-m-d"); 
            $model->save();

            \Yii::$app->getSession()->setFlash('success', 'Record Saved');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->renderAjax('create', [
            'model' => $model,
            'offices' => $offices,
            'fundClusters' => $fundClusters,
            'signatories' => $signatories,
        ]);
    }

    /**
     * Updates an existing Ris model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->scenario = (Yii::$app->user->can('Administrator') || Yii::$app->user->can('Procurement')) ? 'isAdmin' : 'isUser';

        $offices = Office::find()->all();
        $offices = ArrayHelper::map($offices, 'id', 'abbreviation');

        $fundClusters = FundCluster::find()->all();
        $fundClusters = ArrayHelper::map($fundClusters, 'id', 'title');

        $signatories = (Yii::$app->user->can('Administrator') || Yii::$app->user->can('Procurement')) ? Signatory::find()->where(['office_id' => $model->office_id])->all() : Signatory::find()->where(['office_id' => Yii::$app->user->identity->userinfo->OFFICE_C])->all();
        $signatories = ArrayHelper::map($signatories, 'id', 'name');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', 'Record Updated');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->renderAjax('update', [
            'model' => $model,
            'offices' => $offices,
            'fundClusters' => $fundClusters,
            'signatories' => $signatories,
        ]);
    }

    /**
     * Deletes an existing Ris model.
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
     * Finds the Ris model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ris the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ris::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
