<?php

namespace common\modules\v1\controllers;

use Yii;
use common\modules\v1\models\Pr;
use common\modules\v1\models\PrSearch;
use common\modules\v1\models\AppropriationItem;
use common\modules\v1\models\Activity;
use common\modules\v1\models\SubActivity;
use common\modules\v1\models\FUndSource;
use common\modules\v1\models\Ris;
use common\modules\v1\models\Month;
use common\modules\v1\models\Ppmp;
use common\modules\v1\models\Obj;
use common\modules\v1\models\Item;
use common\modules\v1\models\PpmpItem;
use common\modules\v1\models\ItemCost;
use common\modules\v1\models\ItemBreakdown;
use common\modules\v1\models\PpmpItemSearch;
use common\modules\v1\models\FundCluster;
use common\modules\v1\models\ProcurementMode;
use common\modules\v1\models\Signatory;
use common\modules\v1\models\RisItem;
use common\modules\v1\models\RisItemSpec;
use common\modules\v1\models\RisItemSpecValue;
use common\modules\v1\models\RisSource;
use common\modules\v1\models\RisSearch;
use common\modules\v1\models\ForContractItem;
use common\modules\v1\models\Settings;
use common\modules\v1\models\Model;
use common\modules\v1\models\MultipleModel;
use common\modules\v1\models\Transaction;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use markavespiritu\user\models\Office;
use yii\widgets\ActiveForm;
use yii\web\Response;
use kartik\mpdf\Pdf;

/**
 * PrController implements the CRUD actions for Pr model.
 */
class PrController extends Controller
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
                        'roles' => ['ProcurementStaff', 'Administrator'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Pr models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PrSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $types = [
            'Supply' => 'Goods',
            'Service' => 'Service/Contract',
        ];

        $fundSources = FundSource::find()->all();
        $fundSources = ArrayHelper::map($fundSources, 'id', 'code');

        $offices = Office::find()->all();
        $offices = ArrayHelper::map($offices, 'abbreviation', 'abbreviation');

        $procurementModes = ProcurementMode::find()->all();
        $procurementModes = ArrayHelper::map($procurementModes, 'id', 'title');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'types' => $types,
            'fundSources' => $fundSources,
            'offices' => $offices,
            'procurementModes' => $procurementModes,
        ]);
    }

    /**
     * Displays a single Pr model.
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

    public function actionHome($id)
    {
        return $this->renderAjax('_home', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionItems($id)
    {
        $model = $this->findModel($id);

        

        return $this->renderAjax('_items', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Pr model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Pr();

        $approver = Settings::findOne(['title' => 'PR Approver']);
        $model->approved_by = $approver ? $approver->value : '';

        $offices = Office::find()->all();
        $offices = ArrayHelper::map($offices, 'abbreviation', 'abbreviation');

        $fundSources = FundSource::find()->all();
        $fundSources = ArrayHelper::map($fundSources, 'id', 'code');

        $fundClusters = FundCluster::find()->all();
        $fundClusters = ArrayHelper::map($fundClusters, 'id', 'title');

        $signatories = Signatory::find()->all();
        $signatories = ArrayHelper::map($signatories, 'emp_id', 'name');

        $years = Ppmp::find()->select(['distinct(year) as year'])->where(['stage' => 'Final'])->orderBy(['year' => SORT_DESC])->asArray()->all();
        $years = ArrayHelper::map($years, 'year', 'year');

        $procurementModes = ProcurementMode::find()->all();
        $procurementModes = ArrayHelper::map($procurementModes, 'id', 'title');

        $types = [
            'Supply' => 'Goods',
            'Service' => 'Service/Contract',
        ];

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {

            $lastPr = Pr::find()->orderBy(['id' => SORT_DESC])->one();
            $lastNumber = $lastPr ? intval(substr($lastPr->pr_no, -3)) : '001';
            $pr_no = $lastPr ? substr(date("Y"), -2).'-'.date("md").'-'.str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT) : substr(date("Y"), -2).'-'.date("md").'-'.$lastNumber;
            $model->pr_no = $pr_no;
            $model->created_by = Yii::$app->user->identity->userinfo->EMP_N; 
            $model->date_created = date("Y-m-d"); 
            $model->save(false);

            \Yii::$app->getSession()->setFlash('success', 'Record Saved');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->renderAjax('create', [
            'model' => $model,
            'offices' => $offices,
            'fundSources' => $fundSources,
            'fundClusters' => $fundClusters,
            'signatories' => $signatories,
            'types' => $types,
            'years' => $years,
            'procurementModes' => $procurementModes
        ]);
    }

    /**
     * Updates an existing Pr model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $offices = Office::find()->all();
        $offices = ArrayHelper::map($offices, 'abbreviation', 'abbreviation');

        $fundSources = FundSource::find()->all();
        $fundSources = ArrayHelper::map($fundSources, 'id', 'code');

        $fundClusters = FundCluster::find()->all();
        $fundClusters = ArrayHelper::map($fundClusters, 'id', 'title');

        $signatories = Signatory::find()->all();
        $signatories = ArrayHelper::map($signatories, 'emp_id', 'name');

        $years = Ppmp::find()->select(['distinct(year) as year'])->where(['stage' => 'Final'])->orderBy(['year' => SORT_DESC])->asArray()->all();
        $years = ArrayHelper::map($years, 'year', 'year');

        $procurementModes = ProcurementMode::find()->all();
        $procurementModes = ArrayHelper::map($procurementModes, 'id', 'title');

        $types = [
            'Supply' => 'Goods',
            'Service' => 'Service/Contract',
        ];

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            \Yii::$app->getSession()->setFlash('success', 'Record Updated');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->renderAjax('update', [
            'model' => $model,
            'offices' => $offices,
            'fundSources' => $fundSources,
            'fundClusters' => $fundClusters,
            'signatories' => $signatories,
            'types' => $types,
            'years' => $years,
            'procurementModes' => $procurementModes
        ]);
    }

    /**
     * Deletes an existing Pr model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if($model->delete())
        {
            $statuses = Transaction::deleteAll(['model' => 'Pr', 'model_id' => $id]);
        }
        
        \Yii::$app->getSession()->setFlash('success', 'Record Deleted');
        return $this->redirect(['index']);
    }

    /**
     * Finds the Pr model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pr the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pr::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
