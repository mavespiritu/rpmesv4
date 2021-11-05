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

class DashboardController extends \yii\web\Controller
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
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function filterized($params)
    {
    	$params = json_decode($params, true);

    	$data = [];

    	if(!empty($params))
        {
            foreach($params as $param)
            {
                $data[$param['name']] = $param['value'];
            }
        }

        return $data;
    }

    public function actionIndex()
    {
        $model = new AppropriationItem();
        $model->scenario = 'loadAppropriation';

        $stages = [
            'Indicative' => 'Indicative',
            'Adjusted' => 'Adjusted',
            'Final' => 'Final',
        ];

        $years = Appropriation::find()->select(['distinct(year) as year'])->asArray()->orderBy(['year' => SORT_DESC])->all();
        $years = ArrayHelper::map($years, 'year', 'year');

        return $this->render('index', [
            'model' => $model,
            'stages' => $stages,
            'years' => $years,
        ]);
    }

    public function actionPrexcSummary($params)
    {
        $filter = $this->filterized($params);

        $quantities = ItemBreakdown::find()
        ->select([
            'ppmp_item_id',
            'sum(quantity) as total'
        ])
        ->groupBy(['ppmp_item_id'])
        ->createCommand()
        ->getRawSql();

        $items = PpmpItem::find()
        ->select([
            'ppmp_cost_structure.abbreviation as csTitle',
            'ppmp_ppmp.office_id',
            'tbloffice.abbreviation as division',
            'ppmp_pap.id as pap_id',
            'ppmp_pap.short_code as short_code',
            'ppmp_pap.title as pap_title',
            'ppmp_ppmp_item.activity_id',
            'ppmp_activity.title as activity_title',
            'ppmp_ppmp_item.fund_source_id',
            'ppmp_fund_source.code as fundSource',
            'SUM(quantities.total * ppmp_ppmp_item.cost) as total'
        ])
        ->leftJoin('ppmp_ppmp', 'ppmp_ppmp.id = ppmp_ppmp_item.ppmp_id')
        ->leftJoin('tbloffice', 'tbloffice.id = ppmp_ppmp.office_id')
        ->leftJoin('ppmp_activity', 'ppmp_activity.id = ppmp_ppmp_item.activity_id')
        ->leftJoin('ppmp_pap', 'ppmp_pap.id = ppmp_activity.pap_id')
        ->leftJoin('ppmp_cost_structure', 'ppmp_cost_structure.id = ppmp_pap.cost_structure_id')
        ->leftJoin('ppmp_fund_source', 'ppmp_fund_source.id = ppmp_ppmp_item.fund_source_id')
        ->leftJoin(['quantities' => '('.$quantities.')'], 'quantities.ppmp_item_id = ppmp_ppmp_item.id');

        if($filter['AppropriationItem[year]'] != ''){
            $items = $items->andWhere(['ppmp_ppmp.year' => $filter['AppropriationItem[year]']]);
        }

        if($filter['AppropriationItem[stage]'] != ''){
            $items = $items->andWhere(['ppmp_ppmp.stage' => $filter['AppropriationItem[stage]']]);
        }

        $items = $items
        ->groupBy([
            'ppmp_activity.pap_id',
            'ppmp_ppmp.office_id',
            'ppmp_ppmp_item.activity_id',
            'ppmp_ppmp_item.fund_source_id',
        ])
        ->orderBy([
            'ppmp_pap.id' => SORT_ASC,
            'ppmp_activity.code' => SORT_ASC,
        ])
        ->asArray()
        ->all();

        $offices = Office::find()->all();
        $paps = Pap::find()->all();
        $paps = Pap::find()->all();
        $fundSources = FundSource::find()->all();

        $data = [];
        $total = [];
        $headers = [];

        if($paps)
        {
            foreach($paps as $pap)
            {
                foreach($offices as $office)
                {
                    foreach($fundSources as $fundSource)
                    {
                        $headers[$pap->short_code][$office->abbreviation][$fundSource->code] = $fundSource->code;
                    }
                }
            }
        }

        if(!empty($items))
        {
            foreach($items as $item)
            {
                $total[$item['short_code']][$item['division']][$item['fundSource']] = 0;
                $data[$item['short_code']][$item['activity_title']][$item['division']][$item['fundSource']] =  $item['total'];
            }
        }

        if(!empty($items))
        {
            foreach($items as $item)
            {
                $total[$item['short_code']][$item['division']][$item['fundSource']] += $data[$item['short_code']][$item['activity_title']][$item['division']][$item['fundSource']];
            }
        }

        return $this->renderAjax('_prexc-summary', [
           'headers' => $headers,
           'data' => $data,
           'total' => $total,
           'paps' => $paps,
           'offices' => $offices,
           'fundSources' => $fundSources
        ]);
    }

}
