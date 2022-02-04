<?php

namespace common\modules\v1\controllers;

use Yii;
use common\modules\v1\models\Pr;
use common\modules\v1\models\PrSearch;
use common\modules\v1\models\PrItem;
use common\modules\v1\models\PrItemSpec;
use common\modules\v1\models\PrItemCost;
use common\modules\v1\models\Supplier;
use common\modules\v1\models\PrItemSpecValue;
use common\modules\v1\models\PrItemSearch;
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
        $model->scenario = 'selectRis';

        $statusIDs = Transaction::find()->select(['max(id) as id'])->where(['model' => 'Ris'])->groupBy(['model_id'])->asArray()->all();
        $statusIDs = ArrayHelper::map($statusIDs, 'id', 'id');
        $status = Transaction::find()->where(['in', 'id', $statusIDs])->createCommand()->getRawSql();

        $rises = Ris::find()
                ->select([
                    'ppmp_ris.id as id',
                    'concat("RIS No. ",ppmp_ris.ris_no," (",ppmp_ris.purpose,")") as title',
                    'tbloffice.abbreviation as groupTitle'
                ])
                ->leftJoin(['status' => '('.$status.')'], 'status.model_id = ppmp_ris.id')
                ->leftJoin('tbloffice', 'tbloffice.abbreviation = ppmp_ris.office_id')
                ->andWhere(['status.status' => 'Approved'])
                ->andWhere(['SUBSTRING(ppmp_ris.ris_no, 1, 2)' => substr($model->pr_no, 0, 2)])
                ->asArray()
                ->all();
        
        $rises = ArrayHelper::map($rises, 'id', 'title', 'groupTitle');

        return $this->renderAjax('_items', [
            'model' => $model,
            'rises' => $rises
        ]);
    }

    public function actionLoadRisItems($id, $ris_id)
    {
        $model = $this->findModel($id);

        $existingItems = PrItem::find()->where(['pr_id' => $model->id])->asArray()->all();
        $existingItems = ArrayHelper::map($existingItems, 'ris_item_id', 'ris_item_id');

        $ris = Ris::findOne($ris_id);
        
        $specifications = [];

        $items = RisItem::find()
                ->select([
                    'ppmp_ris_item.id as id',
                    'ppmp_ris_item.ris_id as ris_id',
                    'ppmp_item.id as stockNo',
                    'concat(
                        ppmp_cost_structure.code,"",
                        ppmp_organizational_outcome.code,"",
                        ppmp_program.code,"",
                        ppmp_sub_program.code,"",
                        ppmp_identifier.code,"",
                        ppmp_pap.code,"000-",
                        ppmp_activity.code," - ",
                        ppmp_activity.title," - ",
                        ppmp_sub_activity.title
                    ) as prexc',
                    'ppmp_activity.id as activityId',
                    'ppmp_activity.title as activityTitle',
                    'ppmp_sub_activity.id as subActivityId',
                    'ppmp_sub_activity.title as subActivityTitle',
                    'ppmp_item.title as itemTitle',
                    'ppmp_item.unit_of_measure as unitOfMeasure',
                    'ppmp_ppmp_item.cost as cost',
                    'sum(quantity) as total',
                    'ppmp_ris_item.type'
                ])
                ->leftJoin('ppmp_ppmp_item', 'ppmp_ppmp_item.id = ppmp_ris_item.ppmp_item_id')
                ->leftJoin('ppmp_item', 'ppmp_item.id = ppmp_ppmp_item.item_id')
                ->leftJoin('ppmp_activity', 'ppmp_activity.id = ppmp_ppmp_item.activity_id')
                ->leftJoin('ppmp_sub_activity', 'ppmp_sub_activity.id = ppmp_ppmp_item.sub_activity_id')
                ->leftJoin('ppmp_pap', 'ppmp_pap.id = ppmp_activity.pap_id')
                ->leftJoin('ppmp_identifier', 'ppmp_identifier.id = ppmp_pap.identifier_id')
                ->leftJoin('ppmp_sub_program', 'ppmp_sub_program.id = ppmp_pap.sub_program_id')
                ->leftJoin('ppmp_program', 'ppmp_program.id = ppmp_pap.program_id')
                ->leftJoin('ppmp_organizational_outcome', 'ppmp_organizational_outcome.id = ppmp_pap.organizational_outcome_id')
                ->leftJoin('ppmp_cost_structure', 'ppmp_cost_structure.id = ppmp_pap.cost_structure_id')
                ->andWhere([
                    'ris_id' => $ris->id,
                ])
                ->andWhere(['in', 'ppmp_ris_item.type', ['Original', 'Supplemental']])
                ->andWhere(['not in', 'ppmp_ris_item.id', $existingItems])
                ->groupBy(['ppmp_item.id', 'ppmp_activity.id', 'ppmp_sub_activity.id', 'ppmp_ris_item.cost'])
                ->asArray()
                ->all();

        $risItems = [];
        $prItems = [];

        if(!empty($items))
        {
            foreach($items as $item)
            {
                $risItems[$item['prexc']][] = $item;
                $prItem = new PrItem();
                $prItem->pr_id = $model->id;
                $prItem->ris_id = $ris->id;
                $prItem->item_id = $item['stockNo'];
                $prItem->activity_id = $item['activityId'];
                $prItem->sub_activity_id = $item['subActivityId'];
                $prItem->cost = $item['cost'];
                $prItem->type = $item['type'];
                
                $prItems[$item['id']] = $prItem;

                $spec = RisItemSpec::findOne([
                    'ris_id' => $item['ris_id'],
                    'activity_id' => $item['activityId'],
                    'sub_activity_id' => $item['subActivityId'],
                    'item_id' => $item['stockNo'],
                    'cost' => $item['cost'],
                    'type' => $item['type'],
                ]);

                if($spec)
                {
                    $specifications[$item['id']] = $spec;
                }
            }
        }

        if(MultipleModel::loadMultiple($prItems, Yii::$app->request->post()))
        {
            if(!empty($prItems))
            {
                foreach($prItems as $prItem)
                {
                    $includedItems = RisItem::find()
                        ->leftJoin('ppmp_ppmp_item', 'ppmp_ppmp_item.id = ppmp_ris_item.ppmp_item_id')
                        ->leftJoin('ppmp_item', 'ppmp_item.id = ppmp_ppmp_item.item_id')
                        ->leftJoin('ppmp_activity', 'ppmp_activity.id = ppmp_ppmp_item.activity_id')
                        ->leftJoin('ppmp_sub_activity', 'ppmp_sub_activity.id = ppmp_ppmp_item.sub_activity_id')
                        ->where([
                            'ppmp_ris_item.ris_id' => $ris->id,
                            'ppmp_activity.id' => $prItem->activity_id,
                            'ppmp_sub_activity.id' => $prItem->sub_activity_id,
                            'ppmp_item.id' => $prItem->item_id,
                            'ppmp_ris_item.cost' => $prItem->cost,
                            'ppmp_ris_item.type' => $prItem->type,
                        ])
                        ->all();

                    if($includedItems)
                    {
                        foreach($includedItems as $item)
                        {
                            $prItemModel = new PrItem();
                            $prItemModel->pr_id = $model->id;
                            $prItemModel->ris_id = $ris->id;
                            $prItemModel->ris_item_id = $item->id;
                            $prItemModel->ppmp_item_id = $item->ppmp_item_id;
                            $prItemModel->month_id = $item->month_id;
                            $prItemModel->cost = $item->cost;
                            $prItemModel->quantity = $item->quantity;
                            $prItemModel->type = $item->type;
                            $prItemModel->save();
                        }
                    }
                    
                }
            }
        }

        return $this->renderAjax('_items-ris_items', [
            'model' => $model,
            'risItems' => $risItems,
            'prItems' => $prItems,
            'specifications' => $specifications,
            'ris' => $ris
        ]);
    }

    public function actionPrItems($id)
    {
        $model = $this->findModel($id);
        $prItems = [];
        $specifications = [];
        $items = PrItem::find()
                ->select([
                    'ppmp_pr_item.id as id',
                    's.id as ris_item_spec_id',
                    'ppmp_item.id as item_id',
                    'ppmp_item.title as item',
                    'ppmp_item.unit_of_measure as unit',
                    'ppmp_pr_item.cost as cost',
                    'sum(ppmp_pr_item.quantity) as total',
                    'ppmp_supplier.business_name as supplier',
                    'ppmp_pr_item_cost.cost as abc',
                ])
                ->leftJoin('ppmp_ris', 'ppmp_ris.id = ppmp_pr_item.ris_id')
                ->leftJoin('ppmp_ris_item', 'ppmp_ris_item.id = ppmp_pr_item.ris_item_id')
                ->leftJoin('ppmp_ppmp_item', 'ppmp_ppmp_item.id = ppmp_pr_item.ppmp_item_id')
                ->leftJoin('ppmp_ris_item_spec s', 's.ris_id = ppmp_ris.id and 
                                                    s.activity_id = ppmp_ppmp_item.activity_id and 
                                                    s.sub_activity_id = ppmp_ppmp_item.sub_activity_id and 
                                                    s.item_id = ppmp_ppmp_item.item_id and 
                                                    s.cost = ppmp_pr_item.cost and 
                                                    s.type = ppmp_pr_item.type')
                ->leftJoin('ppmp_item', 'ppmp_item.id = ppmp_ppmp_item.item_id')
                ->leftJoin('ppmp_pr_item_cost', 'ppmp_pr_item_cost.pr_item_id = ppmp_pr_item.id')
                ->leftJoin('ppmp_supplier', 'ppmp_supplier.id = ppmp_pr_item_cost.supplier_id')
                ->andWhere([
                    'ppmp_pr_item.pr_id' => $model->id,
                ])
                ->groupBy(['ppmp_item.id', 's.id', 'ppmp_pr_item.cost'])
                ->asArray()
                ->all();

        if(!empty($items))
        {
            foreach($items as $item)
            {
                $prItem = new PrItem();
                $prItem->id = $item['id'];
                $prItems[$item['id']] = $prItem;

                $specs = RisItemSpec::findOne(['id' => $item['ris_item_spec_id']]);
                $specifications[$item['id']] = $specs;
            }
        }

        return $this->renderAjax('_items-pr_items', [
            'model' => $model,
            'items' => $items,
            'prItems' => $prItems,
            'specifications' => $specifications,
        ]);
    }

    public function actionCreateSpecification($pr_id, $item_id, $cost)
    {
        $model = $this->findModel($pr_id);
        $item = Item::findOne($item_id);

        $spec = new PrItemSpec();
        $spec->pr_id = $model->id;
        $spec->item_id = $item->id;
        $spec->cost = $cost;

        $specValues = [new RisItemSpecValue];

        $specs = RisItemSpec::find()->where(['item_id' => $item['item_id'], 'cost' => $item['cost']])->all();

        if($spec->load(Yii::$app->request->post()))
        {
            $specValues = Model::createMultiple(PrItemSpecValue::className());
            Model::loadMultiple($specValues, Yii::$app->request->post());

            // validate all models
            $valid = $spec->validate();
            $valid = Model::validateMultiple($specValues) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();

                try {
                    if ($flag = $spec->save(false)) {
                        foreach ($specValues as $specValue) {
                            $specValue->pr_item_spec_id = $spec->id;
                            if (! ($flag = $specValue->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->renderAjax('_specification-form', [
            'model' => $model,
            'item' => $item,
            'specs' => $specs,
            'specValues' => (empty($specValues)) ? [new RisItemSpecValue] : $specValues,
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
