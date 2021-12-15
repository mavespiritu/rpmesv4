<?php

namespace common\modules\procurement\controllers;

use Yii;
use common\modules\procurement\models\PrPr;
use common\modules\procurement\models\PrItem;
use common\modules\procurement\models\PrStockInventory;
use common\modules\procurement\models\PrTransactionHistory;
use common\modules\procurement\models\prBudgetVerification;
use common\modules\procurement\models\PrProcVerification;
use common\modules\procurement\models\PrMode;
use common\modules\procurement\models\PrProcurementType;
use common\modules\procurement\models\PrItemApproval;
use common\modules\procurement\models\PrPrSearch;
use common\modules\procurement\models\PrPpmp;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * PrPrController implements the CRUD actions for PrPr model.
 */
class PrPrController extends Controller
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
                    'submit-verification' => ['POST'],
                ],
            ],
        ];
    }

    public function actionStockList($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $out = ['results' => ['id' => '', 'name' => '']];
        if (!is_null($q)) {
            $items = PrStockInventory::find()
                ->select(['id', 'concat(stock_code," - ",description) as name'])
                ->where(['or', 
                    ['like', 'stock_code', $q],
                    ['like','article', $q],
                    ['like','description', $q],
                ])
                ->limit(20)
                ->asArray()
                ->all();

            $out['results'] = array_values($items);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'name' => PrStockInventory::find($id)->stock_code.' - '.PrStockInventory::find($id)->description];
        }
        return $out;
    }

    /**
     * Lists all PrPr models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PrPrSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PrPr model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $status = $model->getPrTransactionHistories()->orderBy(['id' => SORT_DESC])->one();

        return $this->render('view', [
            'model' => $model,
            'status' => $status,
        ]);
    }

    public function actionViewItemForm($id)
    {
        $model = $this->findModel($id);

        $status = $model->getPrTransactionHistories()->orderBy(['id' => SORT_DESC])->one();

        $itemModel = new PrItem();
        $itemModel->scenario = 'autoMode';

        $itemModel->pr_id = $model->id;
        $stockName = '';

        if ($itemModel->load(Yii::$app->request->post())) {
            $item = PrStockInventory::findOne(['id' => $itemModel->stock_inventory_id]);
            if($item)
            {
                $itemModel->unit = $item->unit;
                $itemModel->item = $item->article;
                $itemModel->description = $item->description;
                $itemModel->save();

            }
        }

        return $this->renderAjax('_view-item-form', [
            'model' => $model,
            'itemModel' => $itemModel,
            'stockName' => $stockName,
            'status' => $status,
        ]);
    }

    public function actionViewItems($id)
    {
        $model = $this->findModel($id);

        $status = $model->getPrTransactionHistories()->orderBy(['id' => SORT_DESC])->one();

        return $this->renderAjax('_view-item-list', [
            'model' => $model,
            'status' => $status,
        ]);
    }

    public function actionViewBasicInformation($id)
    {
        $model = $this->findModel($id);
        $status = $model->getPrTransactionHistories()->orderBy(['id' => SORT_DESC])->one();

        $itemModel = new PrItem();
        $itemModel->pr_id = $model->id;
        $stockName = '';

        if ($itemModel->load(Yii::$app->request->post())) {

            $item = PrStockInventory::findOne(['id' => $itemModel->stock_inventory_id]);
            if($item)
            {
                $itemModel->unit = $item->unit;
                $itemModel->item = $item->article;
                $itemModel->description = $item->description;
                $itemModel->save();

                \Yii::$app->getSession()->setFlash('success', 'Item Saved');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->renderAjax('_view-basic-information', [
            'model' => $model,
            'itemModel' => $itemModel,
            'stockName' => $stockName,
            'status' => $status,
        ]);
    }

    public function actionAddItem($id)
    {
        $model = $this->findModel($id);
        $stockModel = new PrStockInventory();
        $itemModel = new PrItem();

        $itemModel->scenario = 'manualMode';
        $stockModel->scenario = 'manualMode';

        if (Yii::$app->request->isAjax && $itemModel->load(Yii::$app->request->post())) {

            $stockModel->stock_code = '-';
            $stockModel->article = $itemModel->item;
            $stockModel->unit = $itemModel->unit;
            $stockModel->description = $itemModel->description;
            if($stockModel->save(false))
            {
                $itemModel->pr_id = $model->id;
                $itemModel->stock_inventory_id = $stockModel->id;
                $itemModel->save(false);
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($itemModel);
        }

        return $this->renderAjax('_add-item', [
            'model' => $model,
            'itemModel' => $itemModel,
        ]);
    }
    
    public function actionUpdateItem($id)
    {
        $itemModel = PrItem::findOne($id);
        $stockModel = new PrStockInventory();
        $model = $itemModel->pr;
        $status = $model->getPrTransactionHistories()->orderBy(['id' => SORT_DESC])->one();

        if (Yii::$app->request->isAjax && $itemModel->load(Yii::$app->request->post())) {

            if($itemModel->save())
            {
                $approval = $itemModel->getPrItemApprovals()->orderBy(['id' => SORT_DESC])->one();
                if($approval)
                {
                    $app = new PrItemApproval();
                    $app->item_id = $itemModel->id;
                    $app->status = 'FOR RE-CHECKING';
                    $app->action_taken_by = Yii::$app->user->id;
                    $app->date_of_action = date("Y-m-d H:i:s");
                    $app->remarks = 'Revision already made by the end user';
                    $app->save();
                }
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($itemModel);
        }

        return $this->renderAjax('_add-item', [
            'model' => $model,
            'itemModel' => $itemModel,
        ]);
    }

    public function actionDeleteItem($id)
    {
        $itemModel = PrItem::findOne($id);
        $model = $itemModel->pr;

        $itemModel->delete();
    }

    /**
     * Creates a new PrPr model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PrPr();
        $ppmpModel = new PrPpmp();

        $model->date_requested = date("Y-m-d");

        if ($model->load(Yii::$app->request->post()) && $ppmpModel->load(Yii::$app->request->post())) {
            if($model->save()){
                $ppmpModel->pr_id = $model->id;
                $ppmpModel->save();

                $transactionModel = new PrTransactionHistory();
                $transactionModel->pr_id = $model->id;
                $transactionModel->group = 'END-USER';
                $transactionModel->status = 'PENDING';
                $transactionModel->action_type = 'FOR ADDITION OF ITEM';
                $transactionModel->action_taken_by = Yii::$app->user->id;
                $transactionModel->date_of_action = date("Y-m-d H:i:s");
                $transactionModel->remarks = 'To do';
                $transactionModel->save();

                \Yii::$app->getSession()->setFlash('success', 'Record Saved');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'ppmpModel' => $ppmpModel,
        ]);
    }

    public function actionSubmitBudgetVerification($id)
    {
        $model = $this->findModel($id);

        $transactionModel = new PrTransactionHistory();
        $transactionModel->pr_id = $model->id;
        $transactionModel->group = 'END-USER';
        $transactionModel->status = 'PENDING';
        $transactionModel->action_type = 'FOR BUDGET VERIFICATION';
        $transactionModel->action_taken_by = Yii::$app->user->id;
        $transactionModel->date_of_action = date("Y-m-d H:i:s");
        $transactionModel->remarks = 'To do';
        $transactionModel->save();

        \Yii::$app->getSession()->setFlash('success', 'Purchase Request submitted for budget verification');
        return $this->redirect(['view', 'id' => $model->id]);

    }

    public function actionVerify($id, $group, $status)
    {
        $model = $this->findModel($id);
        $budgetModel = $model->prBudgetVerification ? $model->prBudgetVerification : new PrBudgetVerification();
        $procModel = $model->prProcVerification ? $model->prProcVerification : new PrProcVerification();

        $modes = PrMode::find()->all();
        $modes = ArrayHelper::map($modes, 'id', 'title');

        $procurementTypes = PrProcurementType::find()->all();
        $procurementTypes = ArrayHelper::map($procurementTypes, 'id', 'title');

        $verifyModel = $group == 'BUDGET' ? $budgetModel : $procModel;

        if(Yii::$app->request->isAjax && $verifyModel->load(Yii::$app->request->post()))
        {
            if($verifyModel->validate() == 1)
            {
                $verifyModel->pr_id = $model->id;
                if($verifyModel->save())
                {
                    $transactionModel = new PrTransactionHistory();
                    $transactionModel->pr_id = $model->id;
                    $transactionModel->group = $group;
                    $transactionModel->status = $status;
                    $transactionModel->action_type = $group.' VERIFIED';
                    $transactionModel->action_taken_by = Yii::$app->user->id;
                    $transactionModel->date_of_action = date("Y-m-d H:i:s");
                    $transactionModel->remarks = '';
                    if($transactionModel->save())
                    {
                        if($group == 'PROCUREMENT')
                        {
                            $items = $model->prItems;
                            if($items)
                            {
                                $i = 1;
                                foreach($items as $item):
                                    if($item->approval && ($item->approval->status != 'FOR REVISION' || $item->approval->status != 'DISAPPROVED'))
                                    {
                                        $item->item_no = $i;
                                        $item->save(false);

                                        $app = new PrItemApproval();
                                        $app->item_id = $item->id;
                                        $app->status = 'APPROVED';
                                        $app->action_taken_by = Yii::$app->user->id;
                                        $app->date_of_action = date("Y-m-d H:i:s");
                                        $app->remarks = 'Approved by the Procurement';
                                        $app->save();

                                        $i++;

                                    }else if(!$item->approval){

                                        $item->item_no = $i;
                                        $item->save(false);

                                        $app = new PrItemApproval();
                                        $app->item_id = $item->id;
                                        $app->status = 'APPROVED';
                                        $app->action_taken_by = Yii::$app->user->id;
                                        $app->date_of_action = date("Y-m-d H:i:s");
                                        $app->remarks = 'Approved by the Procurement';
                                        $app->save();

                                        $i++;
                                    }
                                endforeach;
                            }
                        }
                    }
                }
            }else{
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($verifyModel);
            }
        }
        

        return $this->renderAjax('_verify-'.$group, [
            'model' => $model,
            'modes' => $modes,
            'procurementTypes' => $procurementTypes,
            'verifyModel' => $verifyModel,
            'group' => $group
        ]);
    }

    public function actionApproveItem($id)
    {
        $model = PrItem::findOne(['id' => $id]);
        $approvalModel = new PrItemApproval();

        if($approvalModel->load(Yii::$app->request->post()))
        {
            $approvalModel->item_id = $model->id;
            $approvalModel->action_taken_by = Yii::$app->user->id;
            $approvalModel->date_of_action = date("Y-m-d H:i:s");
            $approvalModel->save();
        }
        
        return $this->renderAjax('_approve-item', [
            'model' => $model,
            'approvalModel' => $approvalModel,
        ]);
    }

    /**
     * Updates an existing PrPr model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $ppmpModel = $model->prPpmp ? $model->prPpmp : new PrPpmp();
        $ppmpModel->pr_id = $model->id;

        if ($model->load(Yii::$app->request->post()) && $ppmpModel->load(Yii::$app->request->post())) {

            $model->save();
            $ppmpModel->save();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'ppmpModel' => $ppmpModel,
        ]);
    }

    /**
     * Deletes an existing PrPr model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PrPr model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PrPr the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PrPr::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
