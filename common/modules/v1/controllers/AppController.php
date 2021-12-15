<?php

namespace common\modules\v1\controllers;

use Yii;
use common\modules\v1\models\Ppmp;
use common\modules\v1\models\Item;
use common\modules\v1\models\PpmpItem;
use common\modules\v1\models\Settings;
use common\modules\v1\models\ItemBreakdown;
use markavespiritu\user\models\Office;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\db\Query;
use yii\helpers\Url;
use kartik\mpdf\Pdf;

class AppController extends \yii\web\Controller
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
        $model = new Ppmp();
        $model->scenario = Yii::$app->user->can('Administrator') ? 'isAdminApp' : 'isUserApp';

        $offices = Office::find()->all();
        $offices = ArrayHelper::map($offices, 'id', 'abbreviation');

        $years = Ppmp::find()->select(['distinct(year) as year'])->orderBy(['year' => SORT_DESC])->asArray()->all();
        $years = ArrayHelper::map($years, 'year', 'year');

        $stages = [
            'Indicative' => 'Indicative',
            'Adjusted' => 'Adjusted',
            'Final' => 'Final',
        ];

        if($model->load(Yii::$app->request->post()))
        {
            $postData = Yii::$app->request->post('Ppmp');
            $postData['office_id'] = Yii::$app->user->can('Administrator') ? $postData['office_id'] : Yii::$app->user->identity->userinfo->OFFICE_C;

            $quantities = ItemBreakdown::find()
            ->select([
                'month_id',
                'item_id',
                'ppmp_item_id',
                'sum(quantity) as total'
            ])
            ->leftJoin('ppmp_ppmp_item', 'ppmp_ppmp_item.id = ppmp_ppmp_item_breakdown.ppmp_item_id')
            ->leftJoin('ppmp_ppmp', 'ppmp_ppmp.id = ppmp_ppmp_item.ppmp_id')
            ->leftJoin('ppmp_item', 'ppmp_item.id = ppmp_ppmp_item.item_id');

            if(!empty($postData['office_id'])){
                $quantities = $quantities->andWhere(['ppmp_ppmp.office_id' => $postData['office_id']]);
            }

            if(!empty($postData['stage'])){
                $quantities = $quantities->andWhere(['ppmp_ppmp.stage' => $postData['stage']]);
            }

            if(!empty($postData['year'])){
                $quantities = $quantities->andWhere(['ppmp_ppmp.year' => $postData['year']]);
            }

            if(!empty($postData['cse'])){
                $quantities = $quantities->andWhere(['ppmp_item.cse' => $postData['cse']]);
            }

            $quantities = $quantities->groupBy(['month_id', 'ppmp_item_id'])
                    ->createCommand()
                    ->getRawSql();

            $items = PpmpItem::find()
            ->select([
                'ppmp_item.title as title',
                'ppmp_item.unit_of_measure as unit_of_measure',
                'ppmp_ppmp_item.cost as cost',
                'SUM(jan.total) as janTotal',
                'SUM(feb.total) as febTotal',
                'SUM(mar.total) as marTotal',
                'SUM(jan.total + feb.total + mar.total) as q1total',
                'SUM((jan.total + feb.total + mar.total) * cost) as q1amount',
                'SUM(apr.total) as aprTotal',
                'SUM(may.total) as mayTotal',
                'SUM(jun.total) as junTotal',
                'SUM(apr.total + may.total + jun.total) as q2total',
                'SUM((apr.total + may.total + jun.total) * cost) as q2amount',
                'SUM(jul.total) as julTotal',
                'SUM(aug.total) as augTotal',
                'SUM(sep.total) as sepTotal',
                'SUM(jul.total + aug.total + sep.total) as q3total',
                'SUM((jul.total + aug.total + sep.total) * cost) as q3amount',
                'SUM(oct.total) as octTotal',
                'SUM(nov.total) as novTotal',
                'SUM(dec.total) as decTotal',
                'SUM(oct.total + nov.total + dec.total) as q4total',
                'SUM((oct.total + nov.total + dec.total) * cost) as q4amount',
            ])
            ->leftJoin('ppmp_item', 'ppmp_item.id = ppmp_ppmp_item.item_id')    
            ->leftJoin(['jan' => '('.$quantities.')'], 'jan.ppmp_item_id = ppmp_ppmp_item.id and jan.month_id = "1"')
            ->leftJoin(['feb' => '('.$quantities.')'], 'feb.ppmp_item_id = ppmp_ppmp_item.id and feb.month_id = "2"')
            ->leftJoin(['mar' => '('.$quantities.')'], 'mar.ppmp_item_id = ppmp_ppmp_item.id and mar.month_id = "3"')
            ->leftJoin(['apr' => '('.$quantities.')'], 'apr.ppmp_item_id = ppmp_ppmp_item.id and apr.month_id = "4"')
            ->leftJoin(['may' => '('.$quantities.')'], 'may.ppmp_item_id = ppmp_ppmp_item.id and may.month_id = "5"')
            ->leftJoin(['jun' => '('.$quantities.')'], 'jun.ppmp_item_id = ppmp_ppmp_item.id and jun.month_id = "6"')
            ->leftJoin(['jul' => '('.$quantities.')'], 'jul.ppmp_item_id = ppmp_ppmp_item.id and jul.month_id = "7"')
            ->leftJoin(['aug' => '('.$quantities.')'], 'aug.ppmp_item_id = ppmp_ppmp_item.id and aug.month_id = "8"')
            ->leftJoin(['sep' => '('.$quantities.')'], 'sep.ppmp_item_id = ppmp_ppmp_item.id and sep.month_id = "9"')
            ->leftJoin(['oct' => '('.$quantities.')'], 'oct.ppmp_item_id = ppmp_ppmp_item.id and oct.month_id = "10"')
            ->leftJoin(['nov' => '('.$quantities.')'], 'nov.ppmp_item_id = ppmp_ppmp_item.id and nov.month_id = "11"')
            ->leftJoin(['dec' => '('.$quantities.')'], 'dec.ppmp_item_id = ppmp_ppmp_item.id and dec.month_id = "12"')
            ->leftJoin('ppmp_ppmp', 'ppmp_ppmp.id = ppmp_ppmp_item.ppmp_id');

            if(!empty($postData['office_id'])){
                $items = $items->andWhere(['ppmp_ppmp.office_id' => $postData['office_id']]);
            }

            if(!empty($postData['stage'])){
                $items = $items->andWhere(['ppmp_ppmp.stage' => $postData['stage']]);
            }

            if(!empty($postData['year'])){
                $items = $items->andWhere(['ppmp_ppmp.year' => $postData['year']]);
            }

            if(!empty($postData['cse'])){
                $items = $items->andWhere(['ppmp_item.cse' => $postData['cse']]);
            }

            $items = $items
                ->andWhere(['ppmp_ppmp_item.type' => 'Original'])
                ->orderBy(['ppmp_item.title' => SORT_ASC])
                ->groupBy(['ppmp_item.id'])
                ->asArray()
                ->all();

            $filename = '';
            $office = !empty($postData['office_id']) ? Office::findOne($postData['office_id']) : '';
            $filename .= 'APP ';
            $filename .= !empty($postData['cse']) ? $postData['cse'] == 'Yes' ? 'CSE ' : 'NON-CSE ' : 'CSE/NON-CSE ';
            $filename .= !empty($postData['office_id']) ? '('.$office->abbreviation : '(ALL DIVISIONS';
            $filename .= !empty($postData['stage']) ? '-'.$postData['stage'] : '';
            $filename .= !empty($postData['year']) ? '-'.$postData['year'] : '';
            $filename .= ')';

            $entity = Settings::findOne(['title' => 'Entity Name']);
            $region = Settings::findOne(['title' => 'Region']);
            $address = Settings::findOne(['title' => 'Address']);
            $agencyCode = Settings::findOne(['title' => 'Agency Code']);
            $organizationType = Settings::findOne(['title' => 'Organization Type']);
            $contactPerson = Settings::findOne(['title' => 'Contact Person']);
            $contactPersonPosition = Settings::findOne(['title' => 'Contact Person Position']);
            $email = Settings::findOne(['title' => 'Email']);
            $telephone = Settings::findOne(['title' => 'Telephone No.']);
            $mobile = Settings::findOne(['title' => 'Mobile No.']);
            $accountant = Settings::findOne(['title' => 'Accountant']);
            $accountantPosition = Settings::findOne(['title' => 'Accountant Position']);
            $regionalDirector = Settings::findOne(['title' => 'Regional Director']);

            return $this->renderAjax('view', [
                'postData' => $postData,
                'items' => $items,
                'filename' => $filename,
                'entity' => $entity,
                'region' => $region,
                'address' => $address,
                'agencyCode' => $agencyCode,
                'organizationType' => $organizationType,
                'contactPerson' => $contactPerson,
                'contactPersonPosition' => $contactPersonPosition,
                'email' => $email,
                'telephone' => $telephone,
                'mobile' => $mobile,
                'accountant' => $accountant,
                'accountantPosition' => $accountantPosition,
                'regionalDirector' => $regionalDirector,
            ]);

        }

        return $this->render('index',[
            'model' => $model,
            'offices' => $offices,
            'years' => $years,
            'stages' => $stages,
        ]);
    }

    public function actionDownload($type, $post)
    {
        $postData = json_decode($post, true);

        $quantities = ItemBreakdown::find()
            ->select([
                'month_id',
                'item_id',
                'ppmp_item_id',
                'sum(quantity) as total'
            ])
            ->leftJoin('ppmp_ppmp_item', 'ppmp_ppmp_item.id = ppmp_ppmp_item_breakdown.ppmp_item_id')
            ->leftJoin('ppmp_ppmp', 'ppmp_ppmp.id = ppmp_ppmp_item.ppmp_id')
            ->leftJoin('ppmp_item', 'ppmp_item.id = ppmp_ppmp_item.item_id');

        if(!empty($postData['office_id'])){
            $quantities = $quantities->andWhere(['ppmp_ppmp.office_id' => $postData['office_id']]);
        }

        if(!empty($postData['stage'])){
            $quantities = $quantities->andWhere(['ppmp_ppmp.stage' => $postData['stage']]);
        }

        if(!empty($postData['year'])){
            $quantities = $quantities->andWhere(['ppmp_ppmp.year' => $postData['year']]);
        }

        if(!empty($postData['cse'])){
            $quantities = $quantities->andWhere(['ppmp_item.cse' => $postData['cse']]);
        }

        $quantities = $quantities->groupBy(['month_id', 'ppmp_item_id'])
                ->createCommand()
                ->getRawSql();

        $items = PpmpItem::find()
        ->select([
            'ppmp_item.title as title',
            'ppmp_item.unit_of_measure as unit_of_measure',
            'ppmp_ppmp_item.cost as cost',
            'SUM(jan.total) as janTotal',
            'SUM(feb.total) as febTotal',
            'SUM(mar.total) as marTotal',
            'SUM(jan.total + feb.total + mar.total) as q1total',
            'SUM((jan.total + feb.total + mar.total) * cost) as q1amount',
            'SUM(apr.total) as aprTotal',
            'SUM(may.total) as mayTotal',
            'SUM(jun.total) as junTotal',
            'SUM(apr.total + may.total + jun.total) as q2total',
            'SUM((apr.total + may.total + jun.total) * cost) as q2amount',
            'SUM(jul.total) as julTotal',
            'SUM(aug.total) as augTotal',
            'SUM(sep.total) as sepTotal',
            'SUM(jul.total + aug.total + sep.total) as q3total',
            'SUM((jul.total + aug.total + sep.total) * cost) as q3amount',
            'SUM(oct.total) as octTotal',
            'SUM(nov.total) as novTotal',
            'SUM(dec.total) as decTotal',
            'SUM(oct.total + nov.total + dec.total) as q4total',
            'SUM((oct.total + nov.total + dec.total) * cost) as q4amount',
        ])
        ->leftJoin('ppmp_item', 'ppmp_item.id = ppmp_ppmp_item.item_id')    
        ->leftJoin(['jan' => '('.$quantities.')'], 'jan.ppmp_item_id = ppmp_ppmp_item.id and jan.month_id = "1"')
        ->leftJoin(['feb' => '('.$quantities.')'], 'feb.ppmp_item_id = ppmp_ppmp_item.id and feb.month_id = "2"')
        ->leftJoin(['mar' => '('.$quantities.')'], 'mar.ppmp_item_id = ppmp_ppmp_item.id and mar.month_id = "3"')
        ->leftJoin(['apr' => '('.$quantities.')'], 'apr.ppmp_item_id = ppmp_ppmp_item.id and apr.month_id = "4"')
        ->leftJoin(['may' => '('.$quantities.')'], 'may.ppmp_item_id = ppmp_ppmp_item.id and may.month_id = "5"')
        ->leftJoin(['jun' => '('.$quantities.')'], 'jun.ppmp_item_id = ppmp_ppmp_item.id and jun.month_id = "6"')
        ->leftJoin(['jul' => '('.$quantities.')'], 'jul.ppmp_item_id = ppmp_ppmp_item.id and jul.month_id = "7"')
        ->leftJoin(['aug' => '('.$quantities.')'], 'aug.ppmp_item_id = ppmp_ppmp_item.id and aug.month_id = "8"')
        ->leftJoin(['sep' => '('.$quantities.')'], 'sep.ppmp_item_id = ppmp_ppmp_item.id and sep.month_id = "9"')
        ->leftJoin(['oct' => '('.$quantities.')'], 'oct.ppmp_item_id = ppmp_ppmp_item.id and oct.month_id = "10"')
        ->leftJoin(['nov' => '('.$quantities.')'], 'nov.ppmp_item_id = ppmp_ppmp_item.id and nov.month_id = "11"')
        ->leftJoin(['dec' => '('.$quantities.')'], 'dec.ppmp_item_id = ppmp_ppmp_item.id and dec.month_id = "12"')
        ->leftJoin('ppmp_ppmp', 'ppmp_ppmp.id = ppmp_ppmp_item.ppmp_id');

        if(!empty($postData['office_id'])){
            $items = $items->andWhere(['ppmp_ppmp.office_id' => $postData['office_id']]);
        }

        if(!empty($postData['stage'])){
            $items = $items->andWhere(['ppmp_ppmp.stage' => $postData['stage']]);
        }

        if(!empty($postData['year'])){
            $items = $items->andWhere(['ppmp_ppmp.year' => $postData['year']]);
        }

        if(!empty($postData['cse'])){
            $items = $items->andWhere(['ppmp_item.cse' => $postData['cse']]);
        }

        $items = $items
            ->andWhere(['ppmp_ppmp_item.type' => 'Original'])
            ->orderBy(['ppmp_item.title' => SORT_ASC])
            ->groupBy(['ppmp_item.id'])
            ->asArray()
            ->all();
        
        $filename = '';
        $office = !empty($postData['office_id']) ? Office::findOne($postData['office_id']) : '';
        $filename .= 'APP ';
        $filename .= !empty($postData['cse']) ? $postData['cse'] == 'Yes' ? 'CSE ' : 'NON-CSE ' : 'CSE/NON-CSE ';
        $filename .= !empty($postData['office_id']) ? '('.$office->abbreviation : '(ALL DIVISIONS';
        $filename .= !empty($postData['stage']) ? '-'.$postData['stage'] : '';
        $filename .= !empty($postData['year']) ? '-'.$postData['year'] : '';
        $filename .= ')';

        $entity = Settings::findOne(['title' => 'Entity Name']);
        $region = Settings::findOne(['title' => 'Region']);
        $address = Settings::findOne(['title' => 'Address']);
        $agencyCode = Settings::findOne(['title' => 'Agency Code']);
        $organizationType = Settings::findOne(['title' => 'Organization Type']);
        $contactPerson = Settings::findOne(['title' => 'Contact Person']);
        $contactPersonPosition = Settings::findOne(['title' => 'Contact Person Position']);
        $email = Settings::findOne(['title' => 'Email']);
        $telephone = Settings::findOne(['title' => 'Telephone No.']);
        $mobile = Settings::findOne(['title' => 'Mobile No.']);
        $accountant = Settings::findOne(['title' => 'Accountant']);
        $accountantPosition = Settings::findOne(['title' => 'Accountant Position']);
        $regionalDirector = Settings::findOne(['title' => 'Regional Director']);
        
        if($type == 'excel')
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=".$filename.".xls");
            return $this->renderPartial('file', [
                'items' => $items,
                'type' => $type,
                'entity' => $entity,
                'region' => $region,
                'address' => $address,
                'agencyCode' => $agencyCode,
                'organizationType' => $organizationType,
                'contactPerson' => $contactPerson,
                'contactPersonPosition' => $contactPersonPosition,
                'email' => $email,
                'telephone' => $telephone,
                'mobile' => $mobile,
                'accountant' => $accountant,
                'accountantPosition' => $accountantPosition,
                'regionalDirector' => $regionalDirector,
            ]);
        }else if($type == 'pdf')
        {
            $content = $this->renderPartial('file', [
                'items' => $items,
                'type' => $type,
                'entity' => $entity,
                'region' => $region,
                'address' => $address,
                'agencyCode' => $agencyCode,
                'organizationType' => $organizationType,
                'contactPerson' => $contactPerson,
                'contactPersonPosition' => $contactPersonPosition,
                'email' => $email,
                'telephone' => $telephone,
                'mobile' => $mobile,
                'accountant' => $accountant,
                'accountantPosition' => $accountantPosition,
                'regionalDirector' => $regionalDirector,
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
                'cssInline' => 'p, table{
                                    font-family: "Tahoma";
                                    border-collapse: collapse;
                                }
                                thead{
                                    font-size: 12px;
                                    text-align: center;
                                }
                            
                                td{
                                    font-size: 10px;
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
