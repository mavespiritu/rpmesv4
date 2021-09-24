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
                'sum(quantity) as total'
            ])
            ->leftJoin('ppmp_month', 'ppmp_month.id = ppmp_ppmp_item_breakdown.month_id')
            ->leftJoin('ppmp_ppmp_item', 'ppmp_ppmp_item.id = ppmp_ppmp_item_breakdown.ppmp_item_id')
            ->leftJoin('ppmp_item', 'ppmp_item.id = ppmp_ppmp_item.item_id')
            ->groupBy(['item_id', 'month_id'])
            ->createCommand()
            ->getRawSql();

            $items = PpmpItem::find()
            ->select([
                'ppmp_item.title as title',
                'ppmp_item.unit_of_measure as unit_of_measure',
                'ppmp_item.cost_per_unit as cost',
                'jan.total as janTotal',
                'feb.total as febTotal',
                'mar.total as marTotal',
                'apr.total as aprTotal',
                'may.total as mayTotal',
                'jun.total as junTotal',
                'jul.total as julTotal',
                'aug.total as augTotal',
                'sep.total as sepTotal',
                'oct.total as octTotal',
                'nov.total as novTotal',
                'dec.total as decTotal',
            ])
            ->leftJoin('ppmp_item', 'ppmp_item.id = ppmp_ppmp_item.item_id')    
            ->leftJoin(['jan' => '('.$quantities.')'], 'jan.item_id = ppmp_item.id and jan.month_id = "1"')
            ->leftJoin(['feb' => '('.$quantities.')'], 'feb.item_id = ppmp_item.id and feb.month_id = "2"')
            ->leftJoin(['mar' => '('.$quantities.')'], 'mar.item_id = ppmp_item.id and mar.month_id = "3"')
            ->leftJoin(['apr' => '('.$quantities.')'], 'apr.item_id = ppmp_item.id and apr.month_id = "4"')
            ->leftJoin(['may' => '('.$quantities.')'], 'may.item_id = ppmp_item.id and may.month_id = "5"')
            ->leftJoin(['jun' => '('.$quantities.')'], 'jun.item_id = ppmp_item.id and jun.month_id = "6"')
            ->leftJoin(['jul' => '('.$quantities.')'], 'jul.item_id = ppmp_item.id and jul.month_id = "7"')
            ->leftJoin(['aug' => '('.$quantities.')'], 'aug.item_id = ppmp_item.id and aug.month_id = "8"')
            ->leftJoin(['sep' => '('.$quantities.')'], 'sep.item_id = ppmp_item.id and sep.month_id = "9"')
            ->leftJoin(['oct' => '('.$quantities.')'], 'oct.item_id = ppmp_item.id and oct.month_id = "10"')
            ->leftJoin(['nov' => '('.$quantities.')'], 'nov.item_id = ppmp_item.id and nov.month_id = "11"')
            ->leftJoin(['dec' => '('.$quantities.')'], 'dec.item_id = ppmp_item.id and dec.month_id = "12"')
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
                'sum(quantity) as total'
            ])
            ->leftJoin('ppmp_month', 'ppmp_month.id = ppmp_ppmp_item_breakdown.month_id')
            ->leftJoin('ppmp_ppmp_item', 'ppmp_ppmp_item.id = ppmp_ppmp_item_breakdown.ppmp_item_id')
            ->leftJoin('ppmp_item', 'ppmp_item.id = ppmp_ppmp_item.item_id')
            ->groupBy(['item_id', 'month_id'])
            ->createCommand()
            ->getRawSql();

            $items = PpmpItem::find()
            ->select([
                'ppmp_item.title as title',
                'ppmp_item.unit_of_measure as unit_of_measure',
                'ppmp_item.cost_per_unit as cost',
                'jan.total as janTotal',
                'feb.total as febTotal',
                'mar.total as marTotal',
                'apr.total as aprTotal',
                'may.total as mayTotal',
                'jun.total as junTotal',
                'jul.total as julTotal',
                'aug.total as augTotal',
                'sep.total as sepTotal',
                'oct.total as octTotal',
                'nov.total as novTotal',
                'dec.total as decTotal',
            ])
            ->leftJoin('ppmp_item', 'ppmp_item.id = ppmp_ppmp_item.item_id')    
            ->leftJoin(['jan' => '('.$quantities.')'], 'jan.item_id = ppmp_item.id and jan.month_id = "1"')
            ->leftJoin(['feb' => '('.$quantities.')'], 'feb.item_id = ppmp_item.id and feb.month_id = "2"')
            ->leftJoin(['mar' => '('.$quantities.')'], 'mar.item_id = ppmp_item.id and mar.month_id = "3"')
            ->leftJoin(['apr' => '('.$quantities.')'], 'apr.item_id = ppmp_item.id and apr.month_id = "4"')
            ->leftJoin(['may' => '('.$quantities.')'], 'may.item_id = ppmp_item.id and may.month_id = "5"')
            ->leftJoin(['jun' => '('.$quantities.')'], 'jun.item_id = ppmp_item.id and jun.month_id = "6"')
            ->leftJoin(['jul' => '('.$quantities.')'], 'jul.item_id = ppmp_item.id and jul.month_id = "7"')
            ->leftJoin(['aug' => '('.$quantities.')'], 'aug.item_id = ppmp_item.id and aug.month_id = "8"')
            ->leftJoin(['sep' => '('.$quantities.')'], 'sep.item_id = ppmp_item.id and sep.month_id = "9"')
            ->leftJoin(['oct' => '('.$quantities.')'], 'oct.item_id = ppmp_item.id and oct.month_id = "10"')
            ->leftJoin(['nov' => '('.$quantities.')'], 'nov.item_id = ppmp_item.id and nov.month_id = "11"')
            ->leftJoin(['dec' => '('.$quantities.')'], 'dec.item_id = ppmp_item.id and dec.month_id = "12"')
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
