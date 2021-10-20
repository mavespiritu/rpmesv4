<?php

namespace common\modules\v1\controllers;

use Yii;
use common\modules\v1\models\Month;
use common\modules\v1\models\Pap;
use common\modules\v1\models\Obj;
use common\modules\v1\models\Appropriation;
use common\modules\v1\models\AppropriationPap;
use common\modules\v1\models\AppropriationObj;
use common\modules\v1\models\FundSource;
use common\modules\v1\models\AppropriationItem;
use common\modules\v1\models\Activity;
use common\modules\v1\models\SubActivity;
use common\modules\v1\models\Ppmp;
use common\modules\v1\models\PpmpItem;
use common\modules\v1\models\PpmpItemSearch;
use common\modules\v1\models\PpmpCondition;
use common\modules\v1\models\Item;
use common\modules\v1\models\ItemCost;
use common\modules\v1\models\ObjectItem;
use common\modules\v1\models\ItemBreakdown;
use common\modules\v1\models\PpmpSearch;
use common\modules\v1\models\Settings;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\widgets\ActiveForm;
use markavespiritu\user\models\Office;
use yii\db\Query;
use yii\helpers\Url;
use kartik\mpdf\Pdf;

class BudgetMonitoringController extends \yii\web\Controller
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
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['Accounting', 'Administrator'],
                    ],
                ],
            ],
        ];
    }

    function sumElement(array $elements, $key)
    {
        $sum = 0;
        foreach($elements as $element){
            if (isset($element['children'])){
                $sum += $this->sumElement($element['children'], $key);
            }else{
                $sum += isset($element[$key]) ? $element[$key] : 0;
            }
        }
        return $sum;
    }

    function recursive(array $elements, $parentId = null, $stage, $year, $activity_id) {
        $branch = array();
        $offices = Office::find()->orderBy(['abbreviation' => SORT_ASC])->all();
        $fundSources = FundSource::find()->all();
        $activity = Activity::findOne($activity_id);

        foreach ($elements as $element) {
            if ($element['obj_id'] == $parentId) {
                $children = $this->recursive($elements, $element['id'], $stage, $year, $activity_id);

                if($fundSources)
                {
                    foreach($fundSources as $fundSource)
                    {
                        $element['source'.$fundSource->code] = $this->getAppropriationPerObjectPerFundSource($stage, $year, $element['id'], $activity->pap_id, $fundSource->id);
                    }
                }

                if($offices)
                {
                    foreach($offices as $office)
                    {
                        if($fundSources)
                        {
                            foreach($fundSources as $fundSource)
                            {
                                $element['ppmp'.$office->abbreviation.$fundSource->code] = $this->getObjectPerFundSource($stage, $year, $activity->id, $element['id'], $office->id, $fundSource->id);
                            }
                        }
                    }
                }

                if ($children) {
                    $element['children'] = $children;
                }

                $branch[] = $element;
            }
        }   
    
        return $branch;
    }

    function finalRecursive(array $elements)
    {
        $branch = array();
        $offices = Office::find()->orderBy(['abbreviation' => SORT_ASC])->all();
        $fundSources = FundSource::find()->all();

        foreach ($elements as $element) {
            if(isset($element['children']))
            {
                $children = $this->finalRecursive($element['children']);

                if($fundSources)
                {
                    foreach($fundSources as $fundSource)
                    {
                        $element['source'.$fundSource->code] = $this->sumElement($children, 'source'.$fundSource->code);
                    }
                }

                if($offices)
                {
                    foreach($offices as $office)
                    {
                        if($fundSources)
                        {
                            foreach($fundSources as $fundSource)
                            {
                                $element['ppmp'.$office->abbreviation.$fundSource->code] = $this->sumElement($children, 'ppmp'.$office->abbreviation.$fundSource->code);
                            }
                        }
                    }
                }

                if ($children) {
                    $element['children'] = $children;
                }
            }
            
            $branch[] = $element;
        }
    
        return $branch;
    }

    function getAppropriationPerObjectPerFundSource($stage, $year, $obj_id, $pap_id, $fund_source_id)
    {
        $appropriation = AppropriationItem::find()
            ->select(['amount'])
            ->leftJoin('ppmp_appropriation', 'ppmp_appropriation.id = ppmp_appropriation_item.appropriation_id');

        if($stage == 'Indicative')
        {
            $appropriation = $appropriation->andWhere(['ppmp_appropriation.type' => 'GAA', 'ppmp_appropriation.year' => $year - 1]);
        }
        else if($stage == 'Adjusted')
        {
            $appropriation = $appropriation->andWhere(['ppmp_appropriation.type' => 'NEP', 'ppmp_appropriation.year' => $year]);
        }
        else if($stage == 'Final')
        {
            $appropriation = $appropriation->andWhere(['ppmp_appropriation.type' => 'GAA', 'ppmp_appropriation.year' => $year]);
        }

        if($obj_id != '')
        {
            $appropriation = $appropriation->andWhere(['ppmp_appropriation_item.obj_id' => $obj_id]);
        }

        if($pap_id != '')
        {
            $appropriation = $appropriation->andWhere(['ppmp_appropriation_item.pap_id' => $pap_id]);
        }

        if($fund_source_id != '')
        {
            $appropriation = $appropriation->andWhere(['ppmp_appropriation_item.fund_source_id' => $fund_source_id]);
        }

        $appropriation = $appropriation
                        ->asArray()
                        ->one();
        
        return isset($appropriation['amount']) ? $appropriation['amount'] : 0;
    }

    function getObjectPerFundSource($stage, $year, $activity_id, $obj_id, $office_id, $fund_source_id)
    {
        $quantities = ItemBreakdown::find()
            ->select([
                'ppmp_item_id',
                'sum(quantity) as total'
            ])
            ->leftJoin('ppmp_ppmp_item', 'ppmp_ppmp_item.id = ppmp_ppmp_item_breakdown.ppmp_item_id')
            ->leftJoin('ppmp_item', 'ppmp_item.id = ppmp_ppmp_item.item_id')
            ->leftJoin('ppmp_ppmp', 'ppmp_ppmp.id = ppmp_ppmp_item.ppmp_id');
        
        $items = PpmpItem::find()
            ->select([
                'SUM(quantities.total * cost) as total'
            ])
            ->leftJoin('ppmp_ppmp', 'ppmp_ppmp.id = ppmp_ppmp_item.ppmp_id');

            if($year != ''){
                $quantities = $quantities->andWhere(['ppmp_ppmp.year' => $year]);
                $items = $items->andWhere(['ppmp_ppmp.year' => $year]);
            }

            if($stage != ''){
                $quantities = $quantities->andWhere(['ppmp_ppmp.stage' => $stage]);
                $items = $items->andWhere(['ppmp_ppmp.stage' => $stage]);
            }

            if($office_id != ''){
                $quantities = $quantities->andWhere(['ppmp_ppmp.office_id' => $office_id]);
                $items = $items->andWhere(['ppmp_ppmp.office_id' => $office_id]);
            }

            if($activity_id != ''){
                $quantities = $quantities->andWhere(['ppmp_ppmp_item.activity_id' => $activity_id]);
                $items = $items->andWhere(['ppmp_ppmp_item.activity_id' => $activity_id]);
            }

            if($obj_id != ''){
                $quantities = $quantities->andWhere(['ppmp_ppmp_item.obj_id' => $obj_id]);
                $items = $items->andWhere(['ppmp_ppmp_item.obj_id' => $obj_id]);
            }

            if($fund_source_id != ''){
                $quantities = $quantities->andWhere(['ppmp_ppmp_item.fund_source_id' => $fund_source_id]);
                $items = $items->andWhere(['ppmp_ppmp_item.fund_source_id' => $fund_source_id]);
            }

            $quantities = $quantities
            ->andWhere(['ppmp_ppmp_item.type' => 'Original'])
            ->groupBy(['ppmp_item_id'])
            ->createCommand()
            ->getRawSql();

            $items = $items->leftJoin(['quantities' => '('.$quantities.')'], 'quantities.ppmp_item_id = ppmp_ppmp_item.id')
                           ->asArray()
                           ->one();

            return isset($items['total']) ? $items['total'] : 0;
    }

    public function actionIndex()
    {
        $model = new AppropriationItem();
        $model->scenario = 'loadBudgetMonitoring';

        $stages = [
            'Indicative' => 'Indicative',
            'Adjusted' => 'Adjusted',
            'Final' => 'Final',
        ];

        $years = Ppmp::find()->select(['distinct(year) as year'])->asArray()->all();
        $years = ArrayHelper::map($years, 'year', 'year');

        $activities = Activity::find()
                     ->select([
                         'ppmp_activity.id as id',
                         'ppmp_activity.pap_id as pap_id',
                         'ppmp_activity.title as text',
                         'p.title as groupTitle'
                     ])
                     ->leftJoin(['p' => '(SELECT id, code, title from ppmp_pap)'], 'p.id = ppmp_activity.pap_id')
                     ->asArray()
                     ->all();

        $activities = ArrayHelper::map($activities, 'id', 'text', 'groupTitle');

        if($model->load(Yii::$app->request->post()))
        {
            $objects = Obj::find()->asArray()->all();
            $data = $this->finalRecursive($this->recursive($objects, null, $model->stage, $model->year, $model->activity_id));

            $activity = Activity::findOne($model->activity_id);
            $fundSources = FundSource::find()->all();
            $offices = Office::find()->orderBy(['abbreviation' => SORT_ASC])->all();
            $postData = Yii::$app->request->post('AppropriationItem');

            //echo "<pre>"; print_r($data); exit;
            
            return $this->renderAjax('view',[
                'data' => $data,
                'activity' => $activity,
                'fundSources' => $fundSources,
                'offices' => $offices,
                'year' => $model->year,
                'stage' => $model->stage,
                'postData' => $postData,
            ]);
        }

        return $this->render('index',[
            'model' => $model,
            'stages' => $stages,
            'activities' => $activities,
            'years' => $years,
        ]);
    }

    public function actionViewItems($office_id, $fund_source_id, $activity_id, $stage, $year, $obj_id)
    {
        $office = Office::findOne($office_id);
        $fundSource = FundSource::findOne($fund_source_id);
        $activity = Activity::findOne($activity_id);
        $object = Obj::findOne($obj_id);

        $quantities = ItemBreakdown::find()
            ->select([
                'ppmp_item_id',
                'sum(quantity) as total'
            ])
            ->leftJoin('ppmp_ppmp_item', 'ppmp_ppmp_item.id = ppmp_ppmp_item_breakdown.ppmp_item_id')
            ->leftJoin('ppmp_item', 'ppmp_item.id = ppmp_ppmp_item.item_id')
            ->leftJoin('ppmp_ppmp', 'ppmp_ppmp.id = ppmp_ppmp_item.ppmp_id');
        
        $items = PpmpItem::find()
            ->select([
                'ppmp_item.title',
                'ppmp_item.unit_of_measure',
                'ppmp_ppmp_item.cost as cost_per_unit',
                'quantities.total as quantity',
                'ppmp_ppmp_item.remarks'
            ])
            ->leftJoin('ppmp_item', 'ppmp_item.id = ppmp_ppmp_item.item_id')
            ->leftJoin('ppmp_ppmp', 'ppmp_ppmp.id = ppmp_ppmp_item.ppmp_id');

            if($year != ''){
                $quantities = $quantities->andWhere(['ppmp_ppmp.year' => $year]);
                $items = $items->andWhere(['ppmp_ppmp.year' => $year]);
            }

            if($stage != ''){
                $quantities = $quantities->andWhere(['ppmp_ppmp.stage' => $stage]);
                $items = $items->andWhere(['ppmp_ppmp.stage' => $stage]);
            }

            if($office_id != ''){
                $quantities = $quantities->andWhere(['ppmp_ppmp.office_id' => $office_id]);
                $items = $items->andWhere(['ppmp_ppmp.office_id' => $office_id]);
            }

            if($activity_id != ''){
                $quantities = $quantities->andWhere(['ppmp_ppmp_item.activity_id' => $activity_id]);
                $items = $items->andWhere(['ppmp_ppmp_item.activity_id' => $activity_id]);
            }

            if($obj_id != ''){
                $quantities = $quantities->andWhere(['ppmp_ppmp_item.obj_id' => $obj_id]);
                $items = $items->andWhere(['ppmp_ppmp_item.obj_id' => $obj_id]);
            }

            if($fund_source_id != ''){
                $quantities = $quantities->andWhere(['ppmp_ppmp_item.fund_source_id' => $fund_source_id]);
                $items = $items->andWhere(['ppmp_ppmp_item.fund_source_id' => $fund_source_id]);
            }

            $quantities = $quantities
            ->andWhere(['ppmp_ppmp_item.type' => 'Original'])
            ->groupBy(['ppmp_item_id'])
            ->createCommand()
            ->getRawSql();

            $items = $items->leftJoin(['quantities' => '('.$quantities.')'], 'quantities.ppmp_item_id = ppmp_ppmp_item.id')
                           ->asArray()
                           ->all();
                    
            return $this->renderAjax('items',[
                'items' => $items,
                'office' => $office,
                'fundSource' => $fundSource,
                'activity' => $activity,
                'object' => $object,
                'stage' => $stage,
                'year' => $year,
            ]);
    }

    public function actionDownload($type, $post)
    {
        $postData = json_decode($post, true);

        $objects = Obj::find()->asArray()->all();
        $data = $this->finalRecursive($this->recursive($objects, null, $postData['stage'], $postData['year'], $postData['activity_id']));

        $activity = Activity::findOne($postData['activity_id']);
        $fundSources = FundSource::find()->all();
        $offices = Office::find()->orderBy(['abbreviation' => SORT_ASC])->all();
        $filename = 'Budget Monitoring';

        if($type == 'excel')
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=".$filename.".xls");
            return $this->renderPartial('file', [
                'data' => $data,
                'activity' => $activity,
                'fundSources' => $fundSources,
                'offices' => $offices,
                'year' => $postData['year'],
                'stage' =>$postData['stage'],
                'type' => $type,
            ]);
        }else if($type == 'pdf')
        {
            $content = $this->renderPartial('file', [
                'data' => $data,
                'activity' => $activity,
                'fundSources' => $fundSources,
                'offices' => $offices,
                'year' => $postData['year'],
                'stage' => $postData['stage'],
                'type' => $type,
            ]);

            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_LEGAL, 
                'orientation' => Pdf::ORIENT_LANDSCAPE, 
                'destination' => Pdf::DEST_DOWNLOAD, 
                'filename' => $filename.'.pdf', 
                'content' => $content,  
                'marginLeft' => 11.4,
                'marginRight' => 11.4,
                'cssInline' => 'table{
                                    border-collapse: collapse;
                                }
                                thead{
                                    font-size: 12px;
                                    text-align: center;
                                }
                            
                                td{
                                    font-size: 12px;
                                    border: 1px solid black;
                                }
                            
                                th{
                                    text-align: center;
                                    border: 1px solid black;
                                }', 
                ]);
        
                $response = Yii::$app->response;
                $response->format = \yii\web\Response::FORMAT_RAW;
                $headers = Yii::$app->response->headers;
                $headers->add('Content-Type', 'application/pdf');
                return $pdf->render();
        }
    }
}
