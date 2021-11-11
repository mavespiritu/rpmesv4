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

class PpmpMonitoringController extends \yii\web\Controller
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

    public function actionIndex()
    {
        $model = new AppropriationItem();
        $model->scenario = Yii::$app->user->can('Administrator') ? 'loadPpmpMonitoringAdmin' : 'loadPpmpMonitoringUser';

        $stages = [
            'Indicative' => 'Indicative',
            'Adjusted' => 'Adjusted',
            'Final' => 'Final',
        ];

        $years = Appropriation::find()->select(['distinct(year) as year'])->asArray()->orderBy(['year' => SORT_DESC])->all();
        $years = ArrayHelper::map($years, 'year', 'year');

        $offices = Office::find()->all();
        $offices = ArrayHelper::map($offices, 'id', 'abbreviation');

        $fundSources = FundSource::find()->all();
        $fundSources = ArrayHelper::map($fundSources, 'id', 'code');

        $orders = [
            'prexc' => ['content' => 'PREXC'],
            'activity' => ['content' => 'PPA'],
            'objectTitle' => ['content' => 'Object'],
        ];

        if($model->load(Yii::$app->request->post()))
        {
            $postData = Yii::$app->request->post('AppropriationItem');
            
            $quantities = ItemBreakdown::find()
            ->select([
                'ppmp_item_id',
                'month_id',
                'quantity'
            ])
            ->createCommand()
            ->getRawSql();

            $items = PpmpItem::find()
            ->select([
                'ppmp_ppmp_item.id as id',
                'concat(
                    ppmp_cost_structure.code,
                    ppmp_organizational_outcome.code,
                    ppmp_program.code,
                    ppmp_sub_program.code,
                    ppmp_identifier.code,
                    ppmp_pap.code,
                    " - ",
                    ppmp_pap.title
                ) as prexc',
                'ppmp_activity.title as activity',
                'ppmp_sub_activity.title as subactivity',
                'IF(originalObj.obj_id IS NOT NULL, concat(parentObj.title," - ",originalObj.title), originalObj.title) as objectTitle',
                'ppmp_item.title as itemTitle',
                'ppmp_ppmp_item.cost as costPerUnit',
                '
                (
                    janQuantity.quantity +
                    febQuantity.quantity +
                    marQuantity.quantity +
                    aprQuantity.quantity +
                    mayQuantity.quantity +
                    junQuantity.quantity +
                    julQuantity.quantity +
                    augQuantity.quantity +
                    sepQuantity.quantity +
                    octQuantity.quantity +
                    novQuantity.quantity +
                    decQuantity.quantity
                ) as totalQty',
                'janQuantity.quantity as janQty',
                'febQuantity.quantity as febQty',
                'marQuantity.quantity as marQty',
                'aprQuantity.quantity as aprQty',
                'mayQuantity.quantity as mayQty',
                'junQuantity.quantity as junQty',
                'julQuantity.quantity as julQty',
                'augQuantity.quantity as augQty',
                'sepQuantity.quantity as sepQty',
                'octQuantity.quantity as octQty',
                'novQuantity.quantity as novQty',
                'decQuantity.quantity as decQty',
            ])
            ->leftJoin('ppmp_ppmp', 'ppmp_ppmp.id = ppmp_ppmp_item.ppmp_id')
            ->leftJoin('tbloffice', 'tbloffice.id = ppmp_ppmp.office_id')
            ->leftJoin('ppmp_item', 'ppmp_item.id = ppmp_ppmp_item.item_id')
            ->leftJoin('ppmp_activity', 'ppmp_activity.id = ppmp_ppmp_item.activity_id')
            ->leftJoin('ppmp_sub_activity', 'ppmp_sub_activity.id = ppmp_ppmp_item.sub_activity_id')
            ->leftJoin('ppmp_obj originalObj', 'originalObj.id = ppmp_ppmp_item.obj_id')
            ->leftJoin('ppmp_obj parentObj', 'parentObj.id = originalObj.obj_id')
            ->leftJoin('ppmp_fund_source', 'ppmp_fund_source.id = ppmp_ppmp_item.fund_source_id')
            ->leftJoin('ppmp_pap', 'ppmp_pap.id = ppmp_activity.pap_id')
            ->leftJoin('ppmp_identifier', 'ppmp_identifier.id = ppmp_pap.identifier_id')
            ->leftJoin('ppmp_sub_program', 'ppmp_sub_program.id = ppmp_pap.sub_program_id')
            ->leftJoin('ppmp_program', 'ppmp_program.id = ppmp_pap.program_id')
            ->leftJoin('ppmp_organizational_outcome', 'ppmp_organizational_outcome.id = ppmp_pap.organizational_outcome_id')
            ->leftJoin('ppmp_cost_structure', 'ppmp_cost_structure.id = ppmp_pap.cost_structure_id')
            ->leftJoin(['janQuantity' => '('.$quantities.')'], 'janQuantity.ppmp_item_id = ppmp_ppmp_item.id and janQuantity.month_id = "1"')
            ->leftJoin(['febQuantity' => '('.$quantities.')'], 'febQuantity.ppmp_item_id = ppmp_ppmp_item.id and febQuantity.month_id = "2"')
            ->leftJoin(['marQuantity' => '('.$quantities.')'], 'marQuantity.ppmp_item_id = ppmp_ppmp_item.id and marQuantity.month_id = "3"')
            ->leftJoin(['aprQuantity' => '('.$quantities.')'], 'aprQuantity.ppmp_item_id = ppmp_ppmp_item.id and aprQuantity.month_id = "4"')
            ->leftJoin(['mayQuantity' => '('.$quantities.')'], 'mayQuantity.ppmp_item_id = ppmp_ppmp_item.id and mayQuantity.month_id = "5"')
            ->leftJoin(['junQuantity' => '('.$quantities.')'], 'junQuantity.ppmp_item_id = ppmp_ppmp_item.id and junQuantity.month_id = "6"')
            ->leftJoin(['julQuantity' => '('.$quantities.')'], 'julQuantity.ppmp_item_id = ppmp_ppmp_item.id and julQuantity.month_id = "7"')
            ->leftJoin(['augQuantity' => '('.$quantities.')'], 'augQuantity.ppmp_item_id = ppmp_ppmp_item.id and augQuantity.month_id = "8"')
            ->leftJoin(['sepQuantity' => '('.$quantities.')'], 'sepQuantity.ppmp_item_id = ppmp_ppmp_item.id and sepQuantity.month_id = "9"')
            ->leftJoin(['octQuantity' => '('.$quantities.')'], 'octQuantity.ppmp_item_id = ppmp_ppmp_item.id and octQuantity.month_id = "10"')
            ->leftJoin(['novQuantity' => '('.$quantities.')'], 'novQuantity.ppmp_item_id = ppmp_ppmp_item.id and novQuantity.month_id = "11"')
            ->leftJoin(['decQuantity' => '('.$quantities.')'], 'decQuantity.ppmp_item_id = ppmp_ppmp_item.id and decQuantity.month_id = "12"');

            $items = Yii::$app->user->can('Administrator') ? $items->andWhere(['ppmp_ppmp.office_id' => $postData['office_id']]) : $items->andWhere(['ppmp_ppmp.office_id' => Yii::$app->user->identity->userinfo->OFFICE_C]);

            if(!empty($postData['stage']))
            {
                $items = $items->andWhere(['ppmp_ppmp.stage' => $postData['stage']]);
            }

            if(!empty($postData['year']))
            {
                $items = $items->andWhere(['ppmp_ppmp.year' => $postData['year']]);
            }

            if(!empty($postData['fund_source_id']))
            {
                $items = $items->andWhere(['ppmp_fund_source.id' => $postData['fund_source_id']]);
            }

            $items = $items
            ->orderBy([
                'prexc' => SORT_ASC,
                'ppmp_activity.code' => SORT_ASC,
                'ppmp_sub_activity.code' => SORT_ASC,
                'originalObj.obj_id' => SORT_ASC,
                'originalObj.id' => SORT_ASC,
                'ppmp_item.title' => SORT_ASC
            ])
            ->asArray()
            ->all();

            $data = [];

            $groups = [];
            
            $orders = !empty($postData['order']) ? explode(',',$postData['order']) : [];

            $groups = $orders;

            if(!empty($orders))
            {
                foreach($orders as $i => $order)
                {
                    if($order == 'activity')
                    {
                        array_splice($orders, $i+1, 0, 'subactivity');
                    }
                }
            }

            if(!empty($items))
            {
                foreach($items as $item)
                {
                    $data[$item[$orders[0]]][$item[$orders[1]]][$item[$orders[2]]][$item[$orders[3]]][] = $item;
                }
            }

            $months = Month::find()->all();
            $quarters = Month::find()->select(['distinct(quarter) as quarter'])->all();

            return $this->renderAjax('view',[
                'model' => $model,
                'data' => $data,
                'postData' => $postData,
                'orders' => $orders,
                'groups' => $groups,
                'months' => $months,
                'quarters' => $quarters,
            ]);
        }

        return $this->render('index',[
            'model' => $model,
            'stages' => $stages,
            'years' => $years,
            'offices' => $offices,
            'fundSources' => $fundSources,
            'orders' => $orders,
        ]);
    }

    public function actionDownload($type, $post)
    {
        $postData = json_decode($post, true);
            
        $quantities = ItemBreakdown::find()
        ->select([
            'ppmp_item_id',
            'month_id',
            'quantity'
        ])
        ->createCommand()
        ->getRawSql();

        $items = PpmpItem::find()
        ->select([
            'concat(
                ppmp_cost_structure.code,
                ppmp_organizational_outcome.code,
                ppmp_program.code,
                ppmp_sub_program.code,
                ppmp_identifier.code,
                ppmp_pap.code,
                " - ",
                ppmp_pap.title
            ) as prexc',
            'ppmp_activity.title as activity',
            'ppmp_sub_activity.title as subactivity',
            'IF(originalObj.obj_id IS NOT NULL, concat(parentObj.title," - ",originalObj.title), originalObj.title) as objectTitle',
            'ppmp_item.title as itemTitle',
            'ppmp_ppmp_item.cost as costPerUnit',
            '
            (
                janQuantity.quantity +
                febQuantity.quantity +
                marQuantity.quantity +
                aprQuantity.quantity +
                mayQuantity.quantity +
                junQuantity.quantity +
                julQuantity.quantity +
                augQuantity.quantity +
                sepQuantity.quantity +
                octQuantity.quantity +
                novQuantity.quantity +
                decQuantity.quantity
            ) as totalQty',
            'janQuantity.quantity as janQty',
            'febQuantity.quantity as febQty',
            'marQuantity.quantity as marQty',
            'aprQuantity.quantity as aprQty',
            'mayQuantity.quantity as mayQty',
            'junQuantity.quantity as junQty',
            'julQuantity.quantity as julQty',
            'augQuantity.quantity as augQty',
            'sepQuantity.quantity as sepQty',
            'octQuantity.quantity as octQty',
            'novQuantity.quantity as novQty',
            'decQuantity.quantity as decQty',
        ])
        ->leftJoin('ppmp_ppmp', 'ppmp_ppmp.id = ppmp_ppmp_item.ppmp_id')
        ->leftJoin('tbloffice', 'tbloffice.id = ppmp_ppmp.office_id')
        ->leftJoin('ppmp_item', 'ppmp_item.id = ppmp_ppmp_item.item_id')
        ->leftJoin('ppmp_activity', 'ppmp_activity.id = ppmp_ppmp_item.activity_id')
        ->leftJoin('ppmp_sub_activity', 'ppmp_sub_activity.id = ppmp_ppmp_item.sub_activity_id')
        ->leftJoin('ppmp_obj originalObj', 'originalObj.id = ppmp_ppmp_item.obj_id')
        ->leftJoin('ppmp_obj parentObj', 'parentObj.id = originalObj.obj_id')
        ->leftJoin('ppmp_fund_source', 'ppmp_fund_source.id = ppmp_ppmp_item.fund_source_id')
        ->leftJoin('ppmp_pap', 'ppmp_pap.id = ppmp_activity.pap_id')
        ->leftJoin('ppmp_identifier', 'ppmp_identifier.id = ppmp_pap.identifier_id')
        ->leftJoin('ppmp_sub_program', 'ppmp_sub_program.id = ppmp_pap.sub_program_id')
        ->leftJoin('ppmp_program', 'ppmp_program.id = ppmp_pap.program_id')
        ->leftJoin('ppmp_organizational_outcome', 'ppmp_organizational_outcome.id = ppmp_pap.organizational_outcome_id')
        ->leftJoin('ppmp_cost_structure', 'ppmp_cost_structure.id = ppmp_pap.cost_structure_id')
        ->leftJoin(['janQuantity' => '('.$quantities.')'], 'janQuantity.ppmp_item_id = ppmp_ppmp_item.id and janQuantity.month_id = "1"')
        ->leftJoin(['febQuantity' => '('.$quantities.')'], 'febQuantity.ppmp_item_id = ppmp_ppmp_item.id and febQuantity.month_id = "2"')
        ->leftJoin(['marQuantity' => '('.$quantities.')'], 'marQuantity.ppmp_item_id = ppmp_ppmp_item.id and marQuantity.month_id = "3"')
        ->leftJoin(['aprQuantity' => '('.$quantities.')'], 'aprQuantity.ppmp_item_id = ppmp_ppmp_item.id and aprQuantity.month_id = "4"')
        ->leftJoin(['mayQuantity' => '('.$quantities.')'], 'mayQuantity.ppmp_item_id = ppmp_ppmp_item.id and mayQuantity.month_id = "5"')
        ->leftJoin(['junQuantity' => '('.$quantities.')'], 'junQuantity.ppmp_item_id = ppmp_ppmp_item.id and junQuantity.month_id = "6"')
        ->leftJoin(['julQuantity' => '('.$quantities.')'], 'julQuantity.ppmp_item_id = ppmp_ppmp_item.id and julQuantity.month_id = "7"')
        ->leftJoin(['augQuantity' => '('.$quantities.')'], 'augQuantity.ppmp_item_id = ppmp_ppmp_item.id and augQuantity.month_id = "8"')
        ->leftJoin(['sepQuantity' => '('.$quantities.')'], 'sepQuantity.ppmp_item_id = ppmp_ppmp_item.id and sepQuantity.month_id = "9"')
        ->leftJoin(['octQuantity' => '('.$quantities.')'], 'octQuantity.ppmp_item_id = ppmp_ppmp_item.id and octQuantity.month_id = "10"')
        ->leftJoin(['novQuantity' => '('.$quantities.')'], 'novQuantity.ppmp_item_id = ppmp_ppmp_item.id and novQuantity.month_id = "11"')
        ->leftJoin(['decQuantity' => '('.$quantities.')'], 'decQuantity.ppmp_item_id = ppmp_ppmp_item.id and decQuantity.month_id = "12"');

        if(!empty($postData['office_id']))
        {
            $items = Yii::$app->user->can('Administrator') ? $items->andWhere(['ppmp_ppmp.office_id' => $postData['office_id']]) : $items->andWhere(['ppmp_ppmp.office_id' => Yii::$app->user->identity->userinfo->OFFICE_C]);
        }

        if(!empty($postData['stage']))
        {
            $items = $items->andWhere(['ppmp_ppmp.stage' => $postData['stage']]);
        }

        if(!empty($postData['year']))
        {
            $items = $items->andWhere(['ppmp_ppmp.year' => $postData['year']]);
        }

        if(!empty($postData['fund_source_id']))
        {
            $items = $items->andWhere(['ppmp_fund_source.id' => $postData['fund_source_id']]);
        }

        $items = $items
        ->orderBy([
            'prexc' => SORT_ASC,
            'ppmp_activity.code' => SORT_ASC,
            'ppmp_sub_activity.code' => SORT_ASC,
            'originalObj.obj_id' => SORT_ASC,
            'originalObj.id' => SORT_ASC,
            'ppmp_item.title' => SORT_ASC
        ])
        ->asArray()
        ->all();

        $data = [];

        $groups = [];
        
        $orders = !empty($postData['order']) ? explode(',',$postData['order']) : [];

        $groups = $orders;

        if(!empty($orders))
        {
            foreach($orders as $i => $order)
            {
                if($order == 'activity')
                {
                    array_splice($orders, $i+1, 0, 'subactivity');
                }
            }
        }

        if(!empty($items))
        {
            foreach($items as $item)
            {
                $data[$item[$orders[0]]][$item[$orders[1]]][$item[$orders[2]]][$item[$orders[3]]][] = $item;
            }
        }

        $months = Month::find()->all();
        $quarters = Month::find()->select(['distinct(quarter) as quarter'])->all();
            
        $filename = 'PPMP Monitoring';

        if($type == 'excel')
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=".$filename.".xls");
            return $this->renderPartial('file', [
                'data' => $data,
                'orders' => $orders,
                'groups' => $groups,
                'months' => $months,
                'quarters' => $quarters,
                'postData' => $postData,
                'type' => $type,
            ]);
        }else if($type == 'pdf')
        {
            $content = $this->renderPartial('file', [
                'data' => $data,
                'orders' => $orders,
                'groups' => $groups,
                'months' => $months,
                'quarters' => $quarters,
                'postData' => $postData,
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
