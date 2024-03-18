<?php

namespace common\modules\rpmes\controllers;

use Yii;
use common\models\Region;
use common\models\Province;
use common\models\Citymun;
use common\models\Barangay;
use common\modules\rpmes\models\Accomplishment;
use common\modules\rpmes\models\AccomplishmentSearch;
use common\modules\rpmes\models\Project;
use common\modules\rpmes\models\ProjectTarget;
use common\modules\rpmes\models\ProjectSearch;
use common\modules\rpmes\models\FinancialAccomplishment;
use common\modules\rpmes\models\PhysicalAccomplishment;
use common\modules\rpmes\models\PersonEmployedAccomplishment;
use common\modules\rpmes\models\BeneficiariesAccomplishment;
use common\modules\rpmes\models\GroupAccomplishment;
use common\modules\rpmes\models\Plan;
use common\modules\rpmes\models\ProjectCategory;
use common\modules\rpmes\models\ProjectKra;
use common\modules\rpmes\models\ProjectSdgGoal;
use common\modules\rpmes\models\Submission;
use common\modules\rpmes\models\Agency;
use common\modules\rpmes\models\Sector;
use common\modules\rpmes\models\ProjectRegion;
use common\modules\rpmes\models\ProjectProvince;
use common\modules\rpmes\models\ProjectCitymun;
use common\modules\rpmes\models\ProjectBarangay;
use common\modules\rpmes\models\ProjectException;
use common\modules\rpmes\models\ProjectHasFundSources;
use common\modules\rpmes\models\ProjectEndorsement;
use common\modules\rpmes\models\ProjectExceptionSearch;
use common\modules\rpmes\models\ProjectEndorsementSearch;
use common\modules\rpmes\models\Settings;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class ProjectSummaryController extends \yii\web\Controller
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
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'create', 'update', 'generate', 'delete', 'print'],
                        'allow' => true,
                        'roles' => ['Administrator', 'SuperAdministrator'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all ProjectException models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new AccomplishmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGenerate()
    {
        if(!Yii::$app->user->can('Administrator')){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = new Accomplishment();
        $model->scenario = 'generate';

        $years = Accomplishment::find()->select(['distinct(year) as year'])
                ->orderBy(['year' => SORT_DESC])
                ->asArray()
                ->all();

        $years = ArrayHelper::map($years, 'year', 'year');

        if(Yii::$app->request->post()){
            $postData = Yii::$app->request->post('Accomplishment');

            $months = [
                'jan' => 'January',
                'feb' => 'February',
                'mar' => 'March',
                'apr' => 'April',
                'may' => 'May',
                'jun' => 'June',
                'jul' => 'July',
                'aug' => 'August',
                'sep' => 'September',
                'oct' => 'October',
                'nov' => 'November',
                'dec' => 'December',
            ];
    
            $monthsWithoutJanuary = [
                'feb' => 'Feb',
                'mar' => 'Mar',
                'apr' => 'Apr',
                'may' => 'May',
                'jun' => 'Jun',
                'jul' => 'Jul',
                'aug' => 'Aug',
                'sep' => 'Sep',
                'oct' => 'Oct',
                'nov' => 'Nov',
                'dec' => 'Dec',
            ];
    
            $monthsWithoutDecember = [
                'jan' => 'Jan',
                'feb' => 'Feb',
                'mar' => 'Mar',
                'apr' => 'Apr',
                'may' => 'May',
                'jun' => 'Jun',
                'jul' => 'Jul',
                'aug' => 'Aug',
                'sep' => 'Sep',
                'oct' => 'Oct',
                'nov' => 'Nov',
            ];
    
            $quarters = [
                'Q1' => [
                    'jan' => 'Jan',
                    'feb' => 'Feb',
                    'mar' => 'Mar',
                ],
                'Q2' => [
                    'jan' => 'Jan',
                    'feb' => 'Feb',
                    'mar' => 'Mar',
                    'apr' => 'Apr',
                    'may' => 'May',
                    'jun' => 'Jun',
                ],
                'Q3' => [
                    'jan' => 'Jan',
                    'feb' => 'Feb',
                    'mar' => 'Mar',
                    'apr' => 'Apr',
                    'may' => 'May',
                    'jun' => 'Jun',
                    'jul' => 'Jul',
                    'aug' => 'Aug',
                    'sep' => 'Sep',
                ],
                'Q4' => [
                    'jan' => 'Jan',
                    'feb' => 'Feb',
                    'mar' => 'Mar',
                    'apr' => 'Apr',
                    'may' => 'May',
                    'jun' => 'Jun',
                    'jul' => 'Jul',
                    'aug' => 'Aug',
                    'sep' => 'Sep',
                    'oct' => 'Oct',
                    'nov' => 'Nov',
                    'dec' => 'Dec',
                ]
            ];    

            $fundingSourceTitles = ProjectHasFundSources::find()
                    ->select([
                        'phfs.project_id',
                        'GROUP_CONCAT(DISTINCT CONCAT(row_number, ". ", fund_source.title, " ", phfs.type) ORDER BY phfs.id ASC SEPARATOR "<br>") as title'
                    ])
                    ->from(['phfs' => ProjectHasFundSources::tableName()])
                    ->leftJoin('fund_source', 'fund_source.id = phfs.fund_source_id')
                    ->leftJoin('project', 'project.id = phfs.project_id')
                    ->leftJoin(
                        ['subquery' => ProjectHasFundSources::find()
                            ->select(['project_id', 'fund_source_id', 'ROW_NUMBER() OVER (PARTITION BY project_id ORDER BY fund_source_id) AS row_number'])
                        ],
                        'subquery.project_id = phfs.project_id AND subquery.fund_source_id = phfs.fund_source_id'
                    )
                    ->where(['project.draft' => 'No'])
                    ->groupBy(['phfs.project_id'])
                    ->createCommand()->getRawSql();
        
            $fundingAgencyTitles = ProjectHasFundSources::find()
                    ->select([
                        'phfs.project_id', 
                        'GROUP_CONCAT(DISTINCT CONCAT(row_number, ". ", phfs.agency) ORDER BY phfs.id ASC SEPARATOR "<br>") as title'
                        ])
                    ->from(['phfs' => ProjectHasFundSources::tableName()])
                    ->leftJoin('project', 'project.id = phfs.project_id')
                    ->leftJoin(
                        ['subquery' => ProjectHasFundSources::find()
                            ->select(['project_id', 'fund_source_id', 'ROW_NUMBER() OVER (PARTITION BY project_id ORDER BY fund_source_id) AS row_number'])
                        ],
                        'subquery.project_id = phfs.project_id AND subquery.fund_source_id = phfs.fund_source_id'
                    )
                    ->where(['project.draft' => 'No'])
                    ->groupBy(['phfs.project_id'])
                    ->createCommand()->getRawSql();

            $financialTargets = ProjectTarget::find()->where(['target_type' => 'Financial', 'year' => $postData['year']])->createCommand()->getRawSql();

            $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $postData['year']])->createCommand()->getRawSql();

            $financialTotal = 'IF(project.data_type = "Cumulative",';
            $physicalTotal = 'IF(project.data_type <> "Default",';
            foreach(array_reverse($monthsWithoutJanuary) as $mo => $month){
                $financialTotal .= 'IF(COALESCE(financialTargets.'.$mo.', 0) <= 0,';
                $physicalTotal .= 'IF(COALESCE(physicalTargets.'.$mo.', 0) <= 0,';
            }
            $financialTotal .= 'COALESCE(financialTargets.jan, 0)';
            $physicalTotal .= 'COALESCE(physicalTargets.jan, 0)';
            foreach($monthsWithoutJanuary as $mo => $month){
                $financialTotal .= ', COALESCE(financialTargets.'.$mo.', 0))';
                $physicalTotal .= ', COALESCE(physicalTargets.'.$mo.', 0))';
            }
            $financialTotal .= ',';
            $physicalTotal .= ',';
            foreach($monthsWithoutDecember as $mo => $month){
                $financialTotal .= 'COALESCE(financialTargets.'.$mo.', 0) +';
                $physicalTotal .= 'COALESCE(physicalTargets.'.$mo.', 0) +';
            }
            $financialTotal .= 'COALESCE(financialTargets.dec, 0))';
            $physicalTotal .= 'COALESCE(physicalTargets.dec, 0))';

            $targetOwpa = [];

            foreach ($quarters as $q => $mos) {
                $targetOwpa[$q] = 'IF(physicalTargets.type = "Numerical", 
                                    IF('.$physicalTotal.' > 0, ';

                $con =  'COALESCE(physicalTargets.baseline, 0) + ';

                foreach ($mos as $mo => $month) {
                    $con .= $month === end($mos) ? 'COALESCE(physicalTargets.'.$mo.', 0)' : 'COALESCE(physicalTargets.'.$mo.', 0) + ';
                }

                $targetOwpa[$q] .= '(('.$con.')/('.$physicalTotal.')*100)';
                $targetOwpa[$q] .= ',('.$con.'/('.$physicalTotal.'))*100), '.$con.')';
            }
            
            $financialAccomplishment = FinancialAccomplishment::find()->where([
                'year' => $postData['year'],
                'quarter' => $postData['quarter'],
            ])
            ->createCommand()->getRawSql();
    
            $physicalAccomplishment = PhysicalAccomplishment::find()->where([
                'year' => $postData['year'],
                'quarter' => $postData['quarter'],
            ])
            ->createCommand()->getRawSql();

            $personEmployedAccomplishment = PersonEmployedAccomplishment::find()->where([
                'year' => $postData['year'],
                'quarter' => $postData['quarter'],
            ])
            ->createCommand()->getRawSql();
    
            $actualOwpa = 'IF(physicalTargets.type = "Numerical", 
                                IF('.$physicalTotal.' > 0,
                                    (COALESCE(physicalAccomplishment.value, 0)/'.$physicalTotal.')*100, 
                                0), 
                            COALESCE(physicalAccomplishment.value,0))';

            $records = Accomplishment::find()
                        ->select([
                            'project.id',
                            'project.project_no as project_no',
                            'project.title as projectTitle',
                            'DATE_FORMAT(project.start_date, "%m-%d-%Y") as startDate',
                            'DATE_FORMAT(project.completion_date, "%m-%d-%Y") as endDate',
                            'COALESCE(project.cost, 0) as cost',
                            'agency.code as agencyTitle',
                            'sector.title as sectorTitle',
                            'fundingSourceTitles.title as fundingSourceTitle',
                            'fundingAgencyTitles.title as fundingAgencyTitle',
                            'COALESCE('.$financialTotal.', 0) as financialTotal',
                            'COALESCE('.$physicalTotal.', 0) as physicalTotal',
                            $targetOwpa[$postData['quarter']].' as targetOwpa',
                            $actualOwpa.' as actualOwpa',
                            'COALESCE('.$actualOwpa.', 0) - COALESCE('.$targetOwpa[$postData['quarter']].', 0) as slippage',
                            'COALESCE(financialAccomplishment.allocation, 0) as appropriations',
                            'COALESCE(financialAccomplishment.releases, 0) as allotment',
                            'COALESCE(financialAccomplishment.obligation, 0) as obligations',
                            'COALESCE(financialAccomplishment.expenditures, 0) as disbursements',
                            'COALESCE(personEmployedAccomplishment.male, 0) as maleEmployed',
                            'COALESCE(personEmployedAccomplishment.female, 0) as femaleEmployed',
                            'accomplishment.remarks as remarks'
                        ])
                        ->leftJoin('project', 'project.id = accomplishment.project_id')
                        ->leftJoin('agency', 'agency.id = project.agency_id')
                        ->leftJoin('sector', 'sector.id = project.sector_id');

            $records = $records->leftJoin(['fundingSourceTitles' => '('.$fundingSourceTitles.')'], 'fundingSourceTitles.project_id = project.id');
            $records = $records->leftJoin(['fundingAgencyTitles' => '('.$fundingAgencyTitles.')'], 'fundingAgencyTitles.project_id = project.id');
            $records = $records->leftJoin(['financialTargets' => '('.$financialTargets.')'], 'financialTargets.project_id = project.id');
            $records = $records->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
            $records = $records->leftJoin(['financialAccomplishment' => '('.$financialAccomplishment.')'], 'financialAccomplishment.project_id = project.id');
            $records = $records->leftJoin(['physicalAccomplishment' => '('.$physicalAccomplishment.')'], 'physicalAccomplishment.project_id = project.id');
            $records = $records->leftJoin(['personEmployedAccomplishment' => '('.$personEmployedAccomplishment.')'], 'personEmployedAccomplishment.project_id = project.id');

            $records = !empty($postData['year']) ? $records->andWhere(['accomplishment.year' => $postData['year']]) : $records;
            $records = !empty($postData['quarter']) ? $records->andWhere(['accomplishment.quarter' => $postData['quarter']]) : $records;

            $records = $records
                ->orderBy(['accomplishment.id' => SORT_DESC])
                ->asArray()
                ->all();

            $director = Settings::findOne(['title' => 'Agency Head']);

            $filename = date("YmdHis").'_RPMES_Form_5';

            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=".$filename.".xls");
            return $this->renderPartial('_report-file', [
                'records' => $records,
                'year' => $postData['year'],
                'quarter' => $postData['quarter'],
                'director' => $director,
                'type' => 'excel',
            ]);
        }

        return $this->renderAjax('generate', [
            'model' => $model,
            'years' => $years,
        ]);
    }

    public function actionPrint($year, $quarter)
    {
        if(!Yii::$app->user->can('Administrator')){
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        if($year == '' || $quarter == ''){
            echo "Please select year and quarter";
            exit;
        }

        $months = [
            'jan' => 'January',
            'feb' => 'February',
            'mar' => 'March',
            'apr' => 'April',
            'may' => 'May',
            'jun' => 'June',
            'jul' => 'July',
            'aug' => 'August',
            'sep' => 'September',
            'oct' => 'October',
            'nov' => 'November',
            'dec' => 'December',
        ];

        $monthsWithoutJanuary = [
            'feb' => 'Feb',
            'mar' => 'Mar',
            'apr' => 'Apr',
            'may' => 'May',
            'jun' => 'Jun',
            'jul' => 'Jul',
            'aug' => 'Aug',
            'sep' => 'Sep',
            'oct' => 'Oct',
            'nov' => 'Nov',
            'dec' => 'Dec',
        ];

        $monthsWithoutDecember = [
            'jan' => 'Jan',
            'feb' => 'Feb',
            'mar' => 'Mar',
            'apr' => 'Apr',
            'may' => 'May',
            'jun' => 'Jun',
            'jul' => 'Jul',
            'aug' => 'Aug',
            'sep' => 'Sep',
            'oct' => 'Oct',
            'nov' => 'Nov',
        ];

        $quarters = [
            'Q1' => [
                'jan' => 'Jan',
                'feb' => 'Feb',
                'mar' => 'Mar',
            ],
            'Q2' => [
                'jan' => 'Jan',
                'feb' => 'Feb',
                'mar' => 'Mar',
                'apr' => 'Apr',
                'may' => 'May',
                'jun' => 'Jun',
            ],
            'Q3' => [
                'jan' => 'Jan',
                'feb' => 'Feb',
                'mar' => 'Mar',
                'apr' => 'Apr',
                'may' => 'May',
                'jun' => 'Jun',
                'jul' => 'Jul',
                'aug' => 'Aug',
                'sep' => 'Sep',
            ],
            'Q4' => [
                'jan' => 'Jan',
                'feb' => 'Feb',
                'mar' => 'Mar',
                'apr' => 'Apr',
                'may' => 'May',
                'jun' => 'Jun',
                'jul' => 'Jul',
                'aug' => 'Aug',
                'sep' => 'Sep',
                'oct' => 'Oct',
                'nov' => 'Nov',
                'dec' => 'Dec',
            ]
        ];    

        $fundingSourceTitles = ProjectHasFundSources::find()
                ->select([
                    'phfs.project_id',
                    'GROUP_CONCAT(DISTINCT CONCAT(row_number, ". ", fund_source.title, " ", phfs.type) ORDER BY phfs.id ASC SEPARATOR "<br>") as title'
                ])
                ->from(['phfs' => ProjectHasFundSources::tableName()])
                ->leftJoin('fund_source', 'fund_source.id = phfs.fund_source_id')
                ->leftJoin('project', 'project.id = phfs.project_id')
                ->leftJoin(
                    ['subquery' => ProjectHasFundSources::find()
                        ->select(['project_id', 'fund_source_id', 'ROW_NUMBER() OVER (PARTITION BY project_id ORDER BY fund_source_id) AS row_number'])
                    ],
                    'subquery.project_id = phfs.project_id AND subquery.fund_source_id = phfs.fund_source_id'
                )
                ->where(['project.draft' => 'No'])
                ->groupBy(['phfs.project_id'])
                ->createCommand()->getRawSql();
    
        $fundingAgencyTitles = ProjectHasFundSources::find()
                ->select([
                    'phfs.project_id', 
                    'GROUP_CONCAT(DISTINCT CONCAT(row_number, ". ", phfs.agency) ORDER BY phfs.id ASC SEPARATOR "<br>") as title'
                    ])
                ->from(['phfs' => ProjectHasFundSources::tableName()])
                ->leftJoin('project', 'project.id = phfs.project_id')
                ->leftJoin(
                    ['subquery' => ProjectHasFundSources::find()
                        ->select(['project_id', 'fund_source_id', 'ROW_NUMBER() OVER (PARTITION BY project_id ORDER BY fund_source_id) AS row_number'])
                    ],
                    'subquery.project_id = phfs.project_id AND subquery.fund_source_id = phfs.fund_source_id'
                )
                ->where(['project.draft' => 'No'])
                ->groupBy(['phfs.project_id'])
                ->createCommand()->getRawSql();

        $financialTargets = ProjectTarget::find()->where(['target_type' => 'Financial', 'year' => $year])->createCommand()->getRawSql();

        $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $year])->createCommand()->getRawSql();

        $financialTotal = 'IF(project.data_type = "Cumulative",';
        $physicalTotal = 'IF(project.data_type <> "Default",';
        foreach(array_reverse($monthsWithoutJanuary) as $mo => $month){
            $financialTotal .= 'IF(COALESCE(financialTargets.'.$mo.', 0) <= 0,';
            $physicalTotal .= 'IF(COALESCE(physicalTargets.'.$mo.', 0) <= 0,';
        }
        $financialTotal .= 'COALESCE(financialTargets.jan, 0)';
        $physicalTotal .= 'COALESCE(physicalTargets.jan, 0)';
        foreach($monthsWithoutJanuary as $mo => $month){
            $financialTotal .= ', COALESCE(financialTargets.'.$mo.', 0))';
            $physicalTotal .= ', COALESCE(physicalTargets.'.$mo.', 0))';
        }
        $financialTotal .= ',';
        $physicalTotal .= ',';
        foreach($monthsWithoutDecember as $mo => $month){
            $financialTotal .= 'COALESCE(financialTargets.'.$mo.', 0) +';
            $physicalTotal .= 'COALESCE(physicalTargets.'.$mo.', 0) +';
        }
        $financialTotal .= 'COALESCE(financialTargets.dec, 0))';
        $physicalTotal .= 'COALESCE(physicalTargets.dec, 0))';

        $targetOwpa = [];

        foreach ($quarters as $q => $mos) {
            $targetOwpa[$q] = 'IF(physicalTargets.type = "Numerical", 
                                IF('.$physicalTotal.' > 0, ';

            $con =  'COALESCE(physicalTargets.baseline, 0) + ';

            foreach ($mos as $mo => $month) {
                $con .= $month === end($mos) ? 'COALESCE(physicalTargets.'.$mo.', 0)' : 'COALESCE(physicalTargets.'.$mo.', 0) + ';
            }

            $targetOwpa[$q] .= '(('.$con.')/('.$physicalTotal.')*100)';
            $targetOwpa[$q] .= ',('.$con.'/('.$physicalTotal.'))*100), '.$con.')';
        }
        
        $financialAccomplishment = FinancialAccomplishment::find()->where([
            'year' => $year,
            'quarter' => $quarter,
        ])
        ->createCommand()->getRawSql();

        $physicalAccomplishment = PhysicalAccomplishment::find()->where([
            'year' => $year,
            'quarter' => $quarter,
        ])
        ->createCommand()->getRawSql();

        $personEmployedAccomplishment = PersonEmployedAccomplishment::find()->where([
            'year' => $year,
            'quarter' => $quarter,
        ])
        ->createCommand()->getRawSql();

        $actualOwpa = 'IF(physicalTargets.type = "Numerical", 
                            IF('.$physicalTotal.' > 0,
                                (COALESCE(physicalAccomplishment.value, 0)/'.$physicalTotal.')*100, 
                            0), 
                        COALESCE(physicalAccomplishment.value,0))';

        $records = Accomplishment::find()
                    ->select([
                        'project.id',
                        'project.project_no as project_no',
                        'project.title as projectTitle',
                        'DATE_FORMAT(project.start_date, "%m-%d-%Y") as startDate',
                        'DATE_FORMAT(project.completion_date, "%m-%d-%Y") as endDate',
                        'COALESCE(project.cost, 0) as cost',
                        'agency.code as agencyTitle',
                        'sector.title as sectorTitle',
                        'fundingSourceTitles.title as fundingSourceTitle',
                        'fundingAgencyTitles.title as fundingAgencyTitle',
                        'COALESCE('.$financialTotal.', 0) as financialTotal',
                        'COALESCE('.$physicalTotal.', 0) as physicalTotal',
                        $targetOwpa[$quarter].' as targetOwpa',
                        $actualOwpa.' as actualOwpa',
                        'COALESCE('.$actualOwpa.', 0) - COALESCE('.$targetOwpa[$quarter].', 0) as slippage',
                        'COALESCE(financialAccomplishment.allocation, 0) as appropriations',
                        'COALESCE(financialAccomplishment.releases, 0) as allotment',
                        'COALESCE(financialAccomplishment.obligation, 0) as obligations',
                        'COALESCE(financialAccomplishment.expenditures, 0) as disbursements',
                        'COALESCE(personEmployedAccomplishment.male, 0) as maleEmployed',
                        'COALESCE(personEmployedAccomplishment.female, 0) as femaleEmployed',
                        'accomplishment.remarks as remarks'
                    ])
                    ->leftJoin('project', 'project.id = accomplishment.project_id')
                    ->leftJoin('agency', 'agency.id = project.agency_id')
                    ->leftJoin('sector', 'sector.id = project.sector_id');

        $records = $records->leftJoin(['fundingSourceTitles' => '('.$fundingSourceTitles.')'], 'fundingSourceTitles.project_id = project.id');
        $records = $records->leftJoin(['fundingAgencyTitles' => '('.$fundingAgencyTitles.')'], 'fundingAgencyTitles.project_id = project.id');
        $records = $records->leftJoin(['financialTargets' => '('.$financialTargets.')'], 'financialTargets.project_id = project.id');
        $records = $records->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
        $records = $records->leftJoin(['financialAccomplishment' => '('.$financialAccomplishment.')'], 'financialAccomplishment.project_id = project.id');
        $records = $records->leftJoin(['physicalAccomplishment' => '('.$physicalAccomplishment.')'], 'physicalAccomplishment.project_id = project.id');
        $records = $records->leftJoin(['personEmployedAccomplishment' => '('.$personEmployedAccomplishment.')'], 'personEmployedAccomplishment.project_id = project.id');

        $records = !empty($year) ? $records->andWhere(['accomplishment.year' => $year]) : $records;
        $records = !empty($quarter) ? $records->andWhere(['accomplishment.quarter' => $quarter]) : $records;

        $records = $records
            ->orderBy(['accomplishment.id' => SORT_DESC])
            ->asArray()
            ->all();

        $director = Settings::findOne(['title' => 'Agency Head']);

        return $this->renderAjax('_report-file', [
            'records' => $records,
            'year' => $year,
            'quarter' => $quarter,
            'director' => $director,
            'type' => 'print',
        ]);
    }
}
