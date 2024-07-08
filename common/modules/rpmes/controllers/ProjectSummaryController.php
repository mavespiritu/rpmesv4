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
use common\modules\rpmes\models\ProjectRdpChapter;
use common\modules\rpmes\models\ProjectHasFundSources;
use common\modules\rpmes\models\ProjectEndorsement;
use common\modules\rpmes\models\ProjectExceptionSearch;
use common\modules\rpmes\models\ProjectEndorsementSearch;
use common\modules\rpmes\models\Settings;
use common\modules\rpmes\models\ModeOfImplementation;
use common\modules\rpmes\models\FundSource;
use common\modules\rpmes\models\ExpectedOutputAccomplishment;
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

        $years = Submission::find()->select(['distinct(year) as year'])->orderBy(['year' => SORT_DESC])->asArray()->all();

        $years = !empty($years) ? [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year') : [date("Y") => date("Y")];
        array_unique($years);

        $agencies = Agency::find()->orderBy(['code' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'code');

        $sectors = Sector::find()->orderBy(['title' => SORT_ASC])->asArray()->all();
        $sectors = ArrayHelper::map($sectors, 'id', 'title');

        $modes = ModeOfImplementation::find()->orderBy(['title' => SORT_ASC])->asArray()->all();
        $modes = ArrayHelper::map($modes, 'id', 'title');
        
        $regions = Region::find()->orderBy(['region_sort' => SORT_ASC])->all();
        $regions = ArrayHelper::map($regions, 'region_c', 'abbreviation');

        $provinces = [];
        $citymuns = [];

        $model = new Project();
        $model->scenario = 'searchSummary';

        $fundSources = FundSource::find()->select(['id', 'concat(title," (",code,")") as title'])->asArray()->all();
        $fundSources = ArrayHelper::map($fundSources, 'id', 'title');

        $sorts = [
            '_agency_by_sector' => 'Agency by Sector',
            '_agency_by_location' => 'Agency by Location',
            '_agency_by_sector_by_sub_sector' => 'Agency by Sector by Sub-sector',
            '_agency_by_sdg' => 'Agency by SDG',
            '_agency_by_rdp' => 'Agency by RDP',
            '_agency_by_fund_source' => 'Agency by Fund Source',
            '_sector_by_agency' => 'Sector by Agency', 
            '_sector_by_location_by_agency' => 'Sector by Location by Agency',
            '_sector_by_sub_sector' => 'Sector by Sub-Sector',
            '_sector_by_sdg' => 'Sector by SDG',
            '_sector_by_rdp' => 'Sector by RDP',
            '_sector_by_fund_source' => 'Sector by Fund Source',
        ];

        if($model->load(Yii::$app->request->post())){
            $financialTargets = ProjectTarget::find()->where(['target_type' => 'Financial', 'year' => $model->year])->createCommand()->getRawSql();
            $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $model->year])->createCommand()->getRawSql();
            $maleEmployedTargets = ProjectTarget::find()->where(['target_type' => 'Male Employed', 'year' => $model->year])->createCommand()->getRawSql();
            $femaleEmployedTargets = ProjectTarget::find()->where(['target_type' => 'Female Employed', 'year' => $model->year])->createCommand()->getRawSql();

            $projectIDs = Plan::find()->select(['project_id'])->where(['year' => $model->year])->asArray()->all();
            $projectIDs = ArrayHelper::map($projectIDs, 'project_id', 'project_id');

            $regionIDs = ProjectRegion::find();
            $provinceIDs = ProjectProvince::find();
            $fundSourceIDs = ProjectHasFundSources::find();

            if($model->region_id != '')
            {
                $regionIDs = $regionIDs->andWhere(['region_id' => $model->region_id]);
            }

            if($model->province_id != '')
            {
                $provinceIDs = $provinceIDs->andWhere(['province_id' => $model->province_id]);
            }

            if($model->fund_source_id != '')
            {
                $fundSourceIDs = $fundSourceIDs->andWhere(['fund_source_id' => $model->fund_source_id]);
            }

            $regionIDs = $regionIDs->all();
            $regionIDs = ArrayHelper::map($regionIDs, 'project_id', 'project_id');

            $provinceIDs = $provinceIDs->all();
            $provinceIDs = ArrayHelper::map($provinceIDs, 'project_id', 'project_id');

            $fundSourceIDs = $fundSourceIDs->all();
            $fundSourceIDs = ArrayHelper::map($fundSourceIDs, 'project_id', 'project_id');

            $sdgGoalTitles = ProjectSdgGoal::find()
                ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat("SDG #",sdg_goal.sdg_no,": ",sdg_goal.title) ORDER BY sdg_goal.sdg_no ASC SEPARATOR ", ") as title'])
                ->leftJoin('sdg_goal', 'sdg_goal.id = project_sdg_goal.sdg_goal_id')
                ->leftJoin('project', 'project.id = project_sdg_goal.project_id')
                ->where(['project.draft' => 'No'])
                ->groupBy(['project_sdg_goal.project_id'])
                ->createCommand()->getRawSql();

            $rdpChapterTitles = ProjectRdpChapter::find()
                ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat("Chapter ",rdp_chapter.chapter_no,": ", rdp_chapter.title) ORDER BY rdp_chapter.chapter_no ASC, rdp_chapter.title ASC SEPARATOR ", ") as title'])
                ->leftJoin('rdp_chapter', 'rdp_chapter.id = project_rdp_chapter.rdp_chapter_id')
                ->leftJoin('project', 'project.id = project_rdp_chapter.project_id')
                ->where(['project.draft' => 'No'])
                ->groupBy(['project_rdp_chapter.project_id'])
                ->createCommand()->getRawSql();

            $fundingSourceTitles = ProjectHasFundSources::find()
                    ->select([
                        'phfs.project_id',
                        'GROUP_CONCAT(DISTINCT fund_source.title ORDER BY phfs.id ASC SEPARATOR ", ") as title'
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
                        'GROUP_CONCAT(DISTINCT CONCAT(phfs.agency) ORDER BY phfs.id ASC SEPARATOR ", ") as title'
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

            $regionTitles = ProjectRegion::find()
                ->select(['project_id', 'GROUP_CONCAT(DISTINCT tblregion.abbreviation ORDER BY tblregion.abbreviation ASC SEPARATOR ", ") as title'])
                ->leftJoin('tblregion', 'tblregion.region_c = project_region.region_id')
                ->leftJoin('project', 'project.id = project_region.project_id')
                ->where(['project.draft' => 'No'])
                ->groupBy(['project_region.project_id'])
                ->createCommand()->getRawSql();

            $provinceTitles = ProjectProvince::find()
                ->select(['project_id', 'GROUP_CONCAT(DISTINCT tblprovince.province_m ORDER BY tblprovince.province_m ASC SEPARATOR ", ") as title'])
                ->leftJoin('tblprovince', 'tblprovince.province_c = project_province.province_id')
                ->leftJoin('project', 'project.id = project_province.project_id')
                ->where(['project.draft' => 'No'])
                ->groupBy(['project_province.project_id'])
                ->createCommand()->getRawSql();

            $citymunTitles = ProjectCitymun::find()
                ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat(tblcitymun.citymun_m,", ",tblprovince.province_m) ORDER BY tblcitymun.citymun_m ASC, tblprovince.province_m ASC SEPARATOR "; ") as title'])
                ->leftJoin('tblcitymun', 'tblcitymun.province_c = project_citymun.province_id and tblcitymun.citymun_c = project_citymun.citymun_id')
                ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
                ->leftJoin('project', 'project.id = project_citymun.project_id')
                ->where(['project.draft' => 'No'])
                ->groupBy(['project_citymun.project_id'])
                ->createCommand()->getRawSql();

            $barangayTitles = ProjectBarangay::find()
                ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat(tblbarangay.barangay_m,", ",tblcitymun.citymun_m,", ",tblprovince.province_m) ORDER BY tblbarangay.barangay_m ASC, tblcitymun.citymun_m ASC, tblprovince.province_m ASC SEPARATOR "; ") as title'])
                ->leftJoin('tblbarangay', 'tblbarangay.province_c = project_barangay.province_id and tblbarangay.citymun_c = project_barangay.citymun_id and tblbarangay.barangay_c = project_barangay.barangay_id')
                ->leftJoin('tblcitymun', 'tblcitymun.province_c = project_barangay.province_id and tblcitymun.citymun_c = project_barangay.citymun_id')
                ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
                ->leftJoin('project', 'project.id = project_barangay.project_id')
                ->where(['project.draft' => 'No'])
                ->groupBy(['project_barangay.project_id'])
                ->createCommand()->getRawSql();

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

            $financialTargetTotal = 'IF(project.data_type = "Cumulative",';
            $physicalTargetTotal = 'IF(project.data_type <> "Default",';
            foreach(array_reverse($monthsWithoutJanuary) as $mo => $month){
                $financialTargetTotal .= 'IF(COALESCE(financialTargets.'.$mo.', 0) <= 0,';
                $physicalTargetTotal .= 'IF(COALESCE(physicalTargets.'.$mo.', 0) <= 0,';
            }
            $financialTargetTotal .= 'COALESCE(financialTargets.jan, 0)';
            $physicalTargetTotal .= 'COALESCE(physicalTargets.jan, 0)';
            foreach($monthsWithoutJanuary as $mo => $month){
                $financialTargetTotal .= ', COALESCE(financialTargets.'.$mo.', 0))';
                $physicalTargetTotal .= ', COALESCE(physicalTargets.'.$mo.', 0))';
            }
            $financialTargetTotal .= ',';
            $physicalTargetTotal .= ',';
            foreach($monthsWithoutDecember as $mo => $month){
                $financialTargetTotal .= 'COALESCE(financialTargets.'.$mo.', 0) +';
                $physicalTargetTotal .= 'COALESCE(physicalTargets.'.$mo.', 0) +';
            }
            $financialTargetTotal .= 'COALESCE(financialTargets.dec, 0))';
            $physicalTargetTotal .= 'COALESCE(physicalTargets.dec, 0) + COALESCE(physicalTargets.baseline, 0))';

            $targetOwpa = [];

            foreach ($quarters as $q => $mos) {
                $targetOwpa[$q] = 'IF(physicalTargets.type = "Numerical", 
                                    IF('.$physicalTargetTotal.' > 0, ';

                $con =  'COALESCE(physicalTargets.baseline, 0) + ';

                foreach ($mos as $mo => $month) {
                    $con .= $month === end($mos) ? 'COALESCE(physicalTargets.'.$mo.', 0)' : 'COALESCE(physicalTargets.'.$mo.', 0) + ';
                }

                $targetOwpa[$q] .= '(('.$con.')/('.$physicalTargetTotal.')*100)';
                $targetOwpa[$q] .= ',('.$con.'/('.$physicalTargetTotal.'))*100), '.$con.')';
            } 

            $financialAccomplishment = FinancialAccomplishment::find()->where([
                'year' => $model->year,
                'quarter' => $model->quarter,
                'project_id' => $projectIDs
            ])
            ->createCommand()->getRawSql();

            $physicalAccomplishment = PhysicalAccomplishment::find()->where([
                'year' => $model->year,
                'quarter' => $model->quarter,
                'project_id' => $projectIDs
            ])
            ->createCommand()->getRawSql();

            $personEmployedAccomplishment = PersonEmployedAccomplishment::find()->where([
                'year' => $model->year,
                'quarter' => $model->quarter,
                'project_id' => $projectIDs
            ])
            ->createCommand()->getRawSql();

            $individualBeneAccomplishment = ExpectedOutputAccomplishment::find()
            ->select(['expected_output_accomplishment.project_id', 'sum(COALESCE(male, 0) + COALESCE(female, 0)) as total'])
            ->leftJoin('project_expected_output', 'project_expected_output.id = expected_output_accomplishment.expected_output_id')
            ->where([
                'indicator' => 'number of individual beneficiaries served',
                'expected_output_accomplishment.year' => $model->year,
                'expected_output_accomplishment.quarter' => $model->quarter,
                'expected_output_accomplishment.project_id' => $projectIDs,
            ])
            ->groupBy(['expected_output_accomplishment.project_id'])
            ->createCommand()->getRawSql();

            $groupBeneAccomplishment = ExpectedOutputAccomplishment::find()
            ->select(['expected_output_accomplishment.project_id', 'COALESCE(value, 0) as total'])
            ->leftJoin('project_expected_output', 'project_expected_output.id = expected_output_accomplishment.expected_output_id')
            ->where([
                'indicator' => 'number of group beneficiaries served',
                'expected_output_accomplishment.year' => $model->year,
                'expected_output_accomplishment.quarter' => $model->quarter,
                'expected_output_accomplishment.project_id' => $projectIDs,
            ])
            ->groupBy(['expected_output_accomplishment.project_id'])
            ->createCommand()->getRawSql();

            $includedQuarters = [
                'Q1' => ['Q1'],
                'Q2' => ['Q1', 'Q2'],
                'Q3' => ['Q1', 'Q2', 'Q3'],
                'Q4' => ['Q1', 'Q2', 'Q3', 'Q4']
            ];

            $accomplishments = Accomplishment::find()
                                ->select(['project_id', 'IF(sum(COALESCE(action, 0)) > 0, 1, 0) as isCompleted'])
                                ->where([
                                    'year' => $model->year,
                                    'quarter' => ['Q1', 'Q2']
                                ])
                                ->groupBy(['project_id'])
                                ->createCommand()
                                ->getRawSql();

            $actualOwpa = 'IF(physicalTargets.type = "Numerical", 
                            IF('.$physicalTargetTotal.' > 0,
                                (COALESCE(physicalAccomplishment.value, 0)/'.$physicalTargetTotal.')*100, 
                            0), 
                        COALESCE(physicalAccomplishment.value,0))';

            $slippage = 'COALESCE('.$actualOwpa.', 0) - COALESCE('.$targetOwpa[$model->quarter].', 0)';

            $isCompleted = 'COALESCE(accomplishments.isCompleted, 0)';
            $isBehindSchedule = 'IF('.$isCompleted.' = 0, IF(physicalAccomplishment.value > 0, IF('.$slippage.' < 0, 1 , 0), 0), 0)';
            $isOnTime = 'IF('.$isCompleted.' = 0, IF(physicalAccomplishment.value > 0, IF('.$slippage.' = 0, 1 , 0), 0), 0)';
            $isAheadOfSchedule = 'IF('.$isCompleted.' = 0, IF(physicalAccomplishment.value > 0, IF('.$slippage.' > 0, 1 , 0), 0), 0)';
            $isNotYetStartedWithTarget = 'IF('.$isCompleted.' = 0, IF(physicalAccomplishment.value = 0, IF('.$physicalTargetTotal.' > 0, 1, 0), 0), 0)';
            $isNotYetStartedWithNoTarget = 'IF('.$isCompleted.' = 0, IF(physicalAccomplishment.value = 0, IF('.$physicalTargetTotal.' <= 0, 1, 0), 0), 0)';

            $totalProjectCostPerAgency = Project::find()
                    ->select([
                        'project.agency_id as agency_id',
                        'SUM(COALESCE(project.cost, 0)) as cost',
                    ]);

            $totalProjectCostPerAgency = $totalProjectCostPerAgency->andWhere(['project.draft' => 'No']);
            $totalProjectCostPerAgency = $totalProjectCostPerAgency->andWhere(['project.source_id' => null]);
            $totalProjectCostPerAgency = $totalProjectCostPerAgency->andWhere(['project.id' => $projectIDs]);

            $totalProjectCostPerAgency = $totalProjectCostPerAgency->groupBy(['project.agency_id'])
                                    ->createCommand()
                                    ->getRawSql();

            $projects = Project::find()
                    ->select([
                        'project.id',
                        'project.project_no as projectNo',
                        'project.title as projectTitle',
                        'DATE_FORMAT(project.start_date, "%m-%d-%y") as startDate',
                        'DATE_FORMAT(project.completion_date, "%m-%d-%y") as endDate',
                        'agency.code as agencyTitle',
                        'sector.title as sectorTitle',
                        'sub_sector.title as subSectorTitle',
                        'IF(barangayTitles.title is null, IF(citymunTitles.title is null, IF(provinceTitles.title is null, IF(regionTitles.title is null, "No location", regionTitles.title), provinceTitles.title), citymunTitles.title), barangayTitles.title) as locationTitle',
                        'rdpChapterTitles.title as rdpChapterTitle',
                        'sdgGoalTitles.title as sdgGoalTitle',
                        'fundingSourceTitles.title as fundingSourceTitle',
                        'fundingAgencyTitles.title as fundingAgencyTitle',
                        'COALESCE(maleEmployedTargets.annual, 0) as malesEmployedTarget',
                        'COALESCE(femaleEmployedTargets.annual, 0) as femalesEmployedTarget',
                        'COALESCE('.$financialTargetTotal.', 0) as financialTargetTotal',
                        'COALESCE('.$physicalTargetTotal.', 0) as physicalTargetTotal',
                        'COALESCE(project.cost, 0) as cost',
                        'COALESCE(financialAccomplishment.allocation, 0) as appropriations',
                        'COALESCE(financialAccomplishment.releases, 0) as allotment',
                        'COALESCE(financialAccomplishment.obligation, 0) as obligations',
                        'COALESCE(financialAccomplishment.expenditures, 0) as disbursements',
                        'COALESCE(IF(financialAccomplishment.allocation > 0, (financialAccomplishment.releases/financialAccomplishment.allocation)*100, 0), 0) as fundingSupport',
                        'COALESCE(IF(financialAccomplishment.releases > 0, (financialAccomplishment.expenditures/financialAccomplishment.releases)*100, 0), 0) as fundingUtilizationRate',
                        'COALESCE('.$targetOwpa[$model->quarter].', 0) as targetOwpa',
                        'COALESCE('.$actualOwpa.', 0) as actualOwpa',
                        'COALESCE(IF(totalProjectCostPerAgency.cost > 0, project.cost/totalProjectCostPerAgency.cost, 0), 0) as physicalWeights',
                        'COALESCE('.$targetOwpa[$model->quarter].' * IF(totalProjectCostPerAgency.cost > 0, project.cost/totalProjectCostPerAgency.cost, 0), 0) as physicalWeightedTarget',
                        'COALESCE('.$actualOwpa.' * IF(totalProjectCostPerAgency.cost > 0, project.cost/totalProjectCostPerAgency.cost, 0), 0) as physicalWeightedAccomplishment',
                        $slippage.' as slippage',
                        'COALESCE(personEmployedAccomplishment.male, 0) as malesEmployedActual',
                        'COALESCE(personEmployedAccomplishment.female, 0) as femalesEmployedActual',
                        'COALESCE(individualBeneAccomplishment.total, 0) as individualBeneficiaries',
                        'COALESCE(groupBeneAccomplishment.total, 0) as groupBeneficiaries',
                        $isCompleted.' as isCompleted',
                        $isBehindSchedule.' as isBehindSchedule',
                        $isOnTime.' as isOnTime',
                        $isAheadOfSchedule.' as isAheadOfSchedule',
                        $isNotYetStartedWithTarget.' as isNotYetStarted',
                    ]);

            $projects = $projects->leftJoin(['financialTargets' => '('.$financialTargets.')'], 'financialTargets.project_id = project.id');
            $projects = $projects->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
            $projects = $projects->leftJoin(['maleEmployedTargets' => '('.$maleEmployedTargets.')'], 'maleEmployedTargets.project_id = project.id');
            $projects = $projects->leftJoin(['femaleEmployedTargets' => '('.$femaleEmployedTargets.')'], 'femaleEmployedTargets.project_id = project.id');
            $projects = $projects->leftJoin(['fundingSourceTitles' => '('.$fundingSourceTitles.')'], 'fundingSourceTitles.project_id = project.id');
            $projects = $projects->leftJoin(['fundingAgencyTitles' => '('.$fundingAgencyTitles.')'], 'fundingAgencyTitles.project_id = project.id');
            $projects = $projects->leftJoin(['rdpChapterTitles' => '('.$rdpChapterTitles.')'], 'rdpChapterTitles.project_id = project.id');
            $projects = $projects->leftJoin(['sdgGoalTitles' => '('.$sdgGoalTitles.')'], 'sdgGoalTitles.project_id = project.id');
            $projects = $projects->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id');
            $projects = $projects->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
            $projects = $projects->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id');
            $projects = $projects->leftJoin(['barangayTitles' => '('.$barangayTitles.')'], 'barangayTitles.project_id = project.id');
            $projects = $projects->leftJoin(['financialAccomplishment' => '('.$financialAccomplishment.')'], 'financialAccomplishment.project_id = project.id');
            $projects = $projects->leftJoin(['physicalAccomplishment' => '('.$physicalAccomplishment.')'], 'physicalAccomplishment.project_id = project.id');
            $projects = $projects->leftJoin(['personEmployedAccomplishment' => '('.$personEmployedAccomplishment.')'], 'personEmployedAccomplishment.project_id = project.id');                                                           
            $projects = $projects->leftJoin(['individualBeneAccomplishment' => '('.$individualBeneAccomplishment.')'], 'individualBeneAccomplishment.project_id = project.id');                                                           
            $projects = $projects->leftJoin(['groupBeneAccomplishment' => '('.$groupBeneAccomplishment.')'], 'groupBeneAccomplishment.project_id = project.id');                                                           
            $projects = $projects->leftJoin(['totalProjectCostPerAgency' => '('.$totalProjectCostPerAgency.')'], 'totalProjectCostPerAgency.agency_id = project.agency_id');                                                           
            $projects = $projects->leftJoin(['accomplishments' => '('.$accomplishments.')'], 'accomplishments.project_id = project.id');                                                           
            $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
            $projects = $projects->leftJoin('sector', 'sector.id = project.sector_id');
            $projects = $projects->leftJoin('sub_sector', 'sub_sector.id = project.sub_sector_id');
            $projects = $projects->andWhere(['project.draft' => 'No']);
            $projects = $projects->andWhere(['project.source_id' => null]);
            $projects = $projects->andWhere(['project.id' => $projectIDs]);

            if($model->agency_id != ''){
                $projects = $projects->andWhere(['project.agency_id' => $model->agency_id]);
            }

            if($model->sector_id != ''){
                $projects = $projects->andWhere(['project.sector_id' => $model->sector_id]);
            }

            if($model->mode_of_implementation_id != ''){
                $projects = $projects->andWhere(['project.mode_of_implementation_id' => $model->mode_of_implementation_id]);
            }

            if($model->region_id != '')
            {
                $projects = $projects->andWhere(['project.id' => $regionIDs]);
            }

            if($model->province_id != '')
            {
                $projects = $projects->andWhere(['project.id' => $provinceIDs]);
            }

            if($model->fund_source_id != '')
            {
                $projects = $projects->andWhere(['project.id' => $fundSourceIDs]);
            }

           /*  if($model->grouping == '_agency_by_sector'){ $projects = $projects->groupBy(['agencyTitle', 'sectorTitle', 'id']); }
            if($model->grouping == '_agency_by_location'){ $projects = $projects->groupBy(['agencyTitle', 'locationTitle', 'id']); }
            if($model->grouping == '_agency_by_sector_by_sub_sector'){ $projects = $projects->groupBy(['agencyTitle', 'sectorTitle', 'subSectorTitle', 'id']); }
            if($model->grouping == '_agency_by_sdg'){ $projects = $projects->groupBy(['agencyTitle', 'sdgGoalTitle', 'id']); }
            if($model->grouping == '_agency_by_rdp'){ $projects = $projects->groupBy(['agencyTitle', 'rdpChapterTitle', 'id']); }
            if($model->grouping == '_agency_by_fund_source'){ $projects = $projects->groupBy(['agencyTitle', 'fundingSourceTitle', 'id']); }
            if($model->grouping == '_sector_by_agency'){ $projects = $projects->groupBy(['sectorTitle', 'agencyTitle', 'id']); }
            if($model->grouping == '_sector_by_location_by_agency'){ $projects = $projects->groupBy(['sectorTitle', 'locationTitle', 'agencyTitle', 'id']); }
            if($model->grouping == '_sector_by_sub_sector'){ $projects = $projects->groupBy(['sectorTitle', 'subSectorTitle', 'id']); }
            if($model->grouping == '_sector_by_sdg'){ $projects = $projects->groupBy(['sectorTitle', 'sdgGoalTitle', 'id']); }
            if($model->grouping == '_sector_by_rdp'){ $projects = $projects->groupBy(['sectorTitle', 'rdpChapterTitle', 'id']); }
            if($model->grouping == '_sector_by_fund_source'){ $projects = $projects->groupBy(['sectorTitle', 'fundingSourceTitle', 'id']); } */

            $projects = $projects 
                        ->asArray()
                        ->all();

            $initialValues = [
                'malesEmployedTarget' => 0,
                'femalesEmployedTarget' => 0,
                'financialTargetTotal' => 0,
                'physicalTargetTotal' => 0,
                'cost' => 0,
                'appropriations' => 0,
                'allotment' => 0,
                'obligations' => 0,
                'disbursements' => 0,
                'fundingSupport' => 0,
                'fundingUtilizationRate' => 0,
                'targetOwpa' => 0,
                'actualOwpa' => 0,
                'physicalWeights' => 0,
                'physicalWeightedTarget' => 0,
                'physicalWeightedAccomplishment' => 0,
                'slippage' => 0,
                'malesEmployedTarget' => 0,
                'femalesEmployedTarget' => 0,
                'malesEmployedActual' => 0,
                'femalesEmployedActual' => 0,
                'isCompleted' => 0,
                'isBehindSchedule' => 0,
                'isOnTime' => 0,
                'isAheadOfSchedule' => 0,
                'isNotYetStarted' => 0,
                'individualBeneficiaries' => 0,
                'groupBeneficiaries' => 0,
            ];

            $totals = $initialValues;

            if (!empty($projects)) {
                $keys = array_keys($totals);
                foreach ($projects as $project) {
                    foreach ($keys as $key) {
                        $totals[$key] += $project[$key];
                    }
                }
            }

            $data = [];

            if ($model->grouping == '_agency_by_sector') {
                if (!empty($projects)) {
                    // Initialize arrays
                    foreach ($projects as $project) {
                        $agencyTitle = $project['agencyTitle'];
                        $sectorTitle = $project['sectorTitle'];
                        $projectId = $project['id'];
            
                        // Initialize data array if not set
                        if (!isset($data[$agencyTitle]['content'])) {
                            $data[$agencyTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$agencyTitle]['firstLevels'][$sectorTitle]['content'])) {
                            $data[$agencyTitle]['firstLevels'][$sectorTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$agencyTitle]['firstLevels'][$sectorTitle]['projectLevels'][$projectId]['content'])) {
                            $data[$agencyTitle]['firstLevels'][$sectorTitle]['projectLevels'][$projectId]['content'] = $initialValues;
                        }
                    }
            
                    // Aggregate data
                    foreach ($projects as $project) {
                        $agencyTitle = $project['agencyTitle'];
                        $sectorTitle = $project['sectorTitle'];
                        $projectId = $project['id'];
            
                        // Aggregate top level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate first level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['firstLevels'][$sectorTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate second level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['firstLevels'][$sectorTitle]['projectLevels'][$projectId]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Set project specific details

                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['projectLevels'][$projectId]['content']['projectNo'] = $project['projectNo'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['projectLevels'][$projectId]['content']['projectTitle'] = $project['projectTitle'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['projectLevels'][$projectId]['content']['startDate'] = $project['startDate'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['projectLevels'][$projectId]['content']['endDate'] = $project['endDate'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['projectLevels'][$projectId]['content']['agencyTitle'] = $project['agencyTitle'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['projectLevels'][$projectId]['content']['sectorTitle'] = $project['sectorTitle'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['projectLevels'][$projectId]['content']['subSectorTitle'] = $project['subSectorTitle'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['projectLevels'][$projectId]['content']['locationTitle'] = $project['locationTitle'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['projectLevels'][$projectId]['content']['rdpChapterTitle'] = $project['rdpChapterTitle'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['projectLevels'][$projectId]['content']['sdgGoalTitle'] = $project['sdgGoalTitle'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['projectLevels'][$projectId]['content']['fundingSourceTitle'] = $project['fundingSourceTitle'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['projectLevels'][$projectId]['content']['fundingAgencyTitle'] = $project['fundingAgencyTitle'];
                    }
                }
            }else if ($model->grouping == '_agency_by_location') {
                if (!empty($projects)) {
                    // Initialize arrays
                    foreach ($projects as $project) {
                        $agencyTitle = $project['agencyTitle'];
                        $locationTitle = $project['locationTitle'];
                        $projectId = $project['id'];
            
                        // Initialize data array if not set
                        if (!isset($data[$agencyTitle]['content'])) {
                            $data[$agencyTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$agencyTitle]['firstLevels'][$locationTitle]['content'])) {
                            $data[$agencyTitle]['firstLevels'][$locationTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$agencyTitle]['firstLevels'][$locationTitle]['projectLevels'][$projectId]['content'])) {
                            $data[$agencyTitle]['firstLevels'][$locationTitle]['projectLevels'][$projectId]['content'] = $initialValues;
                        }
                    }
            
                    // Aggregate data
                    foreach ($projects as $project) {
                        $agencyTitle = $project['agencyTitle'];
                        $locationTitle = $project['locationTitle'];
                        $projectId = $project['id'];
            
                        // Aggregate top level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate first level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['firstLevels'][$locationTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate second level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['firstLevels'][$locationTitle]['projectLevels'][$projectId]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Set project specific details

                        $data[$agencyTitle]['firstLevels'][$locationTitle]['projectLevels'][$projectId]['content']['projectNo'] = $project['projectNo'];
                        $data[$agencyTitle]['firstLevels'][$locationTitle]['projectLevels'][$projectId]['content']['projectTitle'] = $project['projectTitle'];
                        $data[$agencyTitle]['firstLevels'][$locationTitle]['projectLevels'][$projectId]['content']['startDate'] = $project['startDate'];
                        $data[$agencyTitle]['firstLevels'][$locationTitle]['projectLevels'][$projectId]['content']['endDate'] = $project['endDate'];
                        $data[$agencyTitle]['firstLevels'][$locationTitle]['projectLevels'][$projectId]['content']['agencyTitle'] = $project['agencyTitle'];
                        $data[$agencyTitle]['firstLevels'][$locationTitle]['projectLevels'][$projectId]['content']['sectorTitle'] = $project['sectorTitle'];
                        $data[$agencyTitle]['firstLevels'][$locationTitle]['projectLevels'][$projectId]['content']['subSectorTitle'] = $project['subSectorTitle'];
                        $data[$agencyTitle]['firstLevels'][$locationTitle]['projectLevels'][$projectId]['content']['locationTitle'] = $project['locationTitle'];
                        $data[$agencyTitle]['firstLevels'][$locationTitle]['projectLevels'][$projectId]['content']['rdpChapterTitle'] = $project['rdpChapterTitle'];
                        $data[$agencyTitle]['firstLevels'][$locationTitle]['projectLevels'][$projectId]['content']['sdgGoalTitle'] = $project['sdgGoalTitle'];
                        $data[$agencyTitle]['firstLevels'][$locationTitle]['projectLevels'][$projectId]['content']['fundingSourceTitle'] = $project['fundingSourceTitle'];
                        $data[$agencyTitle]['firstLevels'][$locationTitle]['projectLevels'][$projectId]['content']['fundingAgencyTitle'] = $project['fundingAgencyTitle'];
                    }
                }
            }else if ($model->grouping == '_agency_by_sector_by_sub_sector') {
                if (!empty($projects)) {
                    // Initialize arrays
                    foreach ($projects as $project) {
                        $agencyTitle = $project['agencyTitle'];
                        $sectorTitle = $project['sectorTitle'];
                        $subSectorTitle = $project['subSectorTitle'];
                        $projectId = $project['id'];
            
                        // Initialize data array if not set
                        if (!isset($data[$agencyTitle]['content'])) {
                            $data[$agencyTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$agencyTitle]['firstLevels'][$sectorTitle]['content'])) {
                            $data[$agencyTitle]['firstLevels'][$sectorTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$agencyTitle]['firstLevels'][$sectorTitle]['secondLevels'][$subSectorTitle]['content'])) {
                            $data[$agencyTitle]['firstLevels'][$sectorTitle]['secondLevels'][$subSectorTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$agencyTitle]['firstLevels'][$sectorTitle]['secondLevels'][$subSectorTitle]['projectLevels'][$projectId]['content'])) {
                            $data[$agencyTitle]['firstLevels'][$sectorTitle]['secondLevels'][$subSectorTitle]['projectLevels'][$projectId]['content'] = $initialValues;
                        }
                    }
            
                    // Aggregate data
                    foreach ($projects as $project) {
                        $agencyTitle = $project['agencyTitle'];
                        $sectorTitle = $project['sectorTitle'];
                        $subSectorTitle = $project['subSectorTitle'];
                        $projectId = $project['id'];
            
                        // Aggregate top level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate first level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['firstLevels'][$sectorTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate second level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['firstLevels'][$sectorTitle]['secondLevels'][$subSectorTitle]['content'][$key] += $project[$key];
                            }
                        }


                        // Aggregate third level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['firstLevels'][$sectorTitle]['secondLevels'][$subSectorTitle]['projectLevels'][$projectId]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Set project specific details

                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['secondLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['projectNo'] = $project['projectNo'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['secondLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['projectTitle'] = $project['projectTitle'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['secondLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['startDate'] = $project['startDate'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['secondLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['endDate'] = $project['endDate'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['secondLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['agencyTitle'] = $project['agencyTitle'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['secondLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['sectorTitle'] = $project['sectorTitle'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['secondLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['subSectorTitle'] = $project['subSectorTitle'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['secondLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['locationTitle'] = $project['locationTitle'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['secondLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['rdpChapterTitle'] = $project['rdpChapterTitle'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['secondLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['sdgGoalTitle'] = $project['sdgGoalTitle'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['secondLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['fundingSourceTitle'] = $project['fundingSourceTitle'];
                        $data[$agencyTitle]['firstLevels'][$sectorTitle]['secondLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['fundingAgencyTitle'] = $project['fundingAgencyTitle'];
                    }
                }
            }else if ($model->grouping == '_agency_by_sdg') {
                if (!empty($projects)) {
                    // Initialize arrays
                    foreach ($projects as $project) {
                        $agencyTitle = $project['agencyTitle'];
                        $sdgGoalTitle = $project['sdgGoalTitle'];
                        $projectId = $project['id'];
            
                        // Initialize data array if not set
                        if (!isset($data[$agencyTitle]['content'])) {
                            $data[$agencyTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$agencyTitle]['firstLevels'][$sdgGoalTitle]['content'])) {
                            $data[$agencyTitle]['firstLevels'][$sdgGoalTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$agencyTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content'])) {
                            $data[$agencyTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content'] = $initialValues;
                        }
                    }
            
                    // Aggregate data
                    foreach ($projects as $project) {
                        $agencyTitle = $project['agencyTitle'];
                        $sdgGoalTitle = $project['sdgGoalTitle'];
                        $projectId = $project['id'];
            
                        // Aggregate top level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate first level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['firstLevels'][$sdgGoalTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate second level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Set project specific details

                        $data[$agencyTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['projectNo'] = $project['projectNo'];
                        $data[$agencyTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['projectTitle'] = $project['projectTitle'];
                        $data[$agencyTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['startDate'] = $project['startDate'];
                        $data[$agencyTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['endDate'] = $project['endDate'];
                        $data[$agencyTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['agencyTitle'] = $project['agencyTitle'];
                        $data[$agencyTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['sectorTitle'] = $project['sectorTitle'];
                        $data[$agencyTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['subSectorTitle'] = $project['subSectorTitle'];
                        $data[$agencyTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['locationTitle'] = $project['locationTitle'];
                        $data[$agencyTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['rdpChapterTitle'] = $project['rdpChapterTitle'];
                        $data[$agencyTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['sdgGoalTitle'] = $project['sdgGoalTitle'];
                        $data[$agencyTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['fundingSourceTitle'] = $project['fundingSourceTitle'];
                        $data[$agencyTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['fundingAgencyTitle'] = $project['fundingAgencyTitle'];
                    }
                }
            }else if ($model->grouping == '_agency_by_rdp') {
                if (!empty($projects)) {
                    // Initialize arrays
                    foreach ($projects as $project) {
                        $agencyTitle = $project['agencyTitle'];
                        $rdpChapterTitle = $project['rdpChapterTitle'];
                        $projectId = $project['id'];
            
                        // Initialize data array if not set
                        if (!isset($data[$agencyTitle]['content'])) {
                            $data[$agencyTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$agencyTitle]['firstLevels'][$rdpChapterTitle]['content'])) {
                            $data[$agencyTitle]['firstLevels'][$rdpChapterTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$agencyTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content'])) {
                            $data[$agencyTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content'] = $initialValues;
                        }
                    }
            
                    // Aggregate data
                    foreach ($projects as $project) {
                        $agencyTitle = $project['agencyTitle'];
                        $rdpChapterTitle = $project['rdpChapterTitle'];
                        $projectId = $project['id'];
            
                        // Aggregate top level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate first level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['firstLevels'][$rdpChapterTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate second level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Set project specific details

                        $data[$agencyTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['projectNo'] = $project['projectNo'];
                        $data[$agencyTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['projectTitle'] = $project['projectTitle'];
                        $data[$agencyTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['startDate'] = $project['startDate'];
                        $data[$agencyTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['endDate'] = $project['endDate'];
                        $data[$agencyTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['agencyTitle'] = $project['agencyTitle'];
                        $data[$agencyTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['sectorTitle'] = $project['sectorTitle'];
                        $data[$agencyTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['subSectorTitle'] = $project['subSectorTitle'];
                        $data[$agencyTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['locationTitle'] = $project['locationTitle'];
                        $data[$agencyTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['rdpChapterTitle'] = $project['rdpChapterTitle'];
                        $data[$agencyTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['sdgGoalTitle'] = $project['sdgGoalTitle'];
                        $data[$agencyTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['fundingSourceTitle'] = $project['fundingSourceTitle'];
                        $data[$agencyTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['fundingAgencyTitle'] = $project['fundingAgencyTitle'];
                    }
                }
            }else if ($model->grouping == '_agency_by_fund_source') {
                if (!empty($projects)) {
                    // Initialize arrays
                    foreach ($projects as $project) {
                        $agencyTitle = $project['agencyTitle'];
                        $fundingSourceTitle = $project['fundingSourceTitle'];
                        $projectId = $project['id'];
            
                        // Initialize data array if not set
                        if (!isset($data[$agencyTitle]['content'])) {
                            $data[$agencyTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['content'])) {
                            $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content'])) {
                            $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content'] = $initialValues;
                        }
                    }
            
                    // Aggregate data
                    foreach ($projects as $project) {
                        $agencyTitle = $project['agencyTitle'];
                        $fundingSourceTitle = $project['fundingSourceTitle'];
                        $projectId = $project['id'];
            
                        // Aggregate top level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate first level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate second level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Set project specific details

                        $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['projectNo'] = $project['projectNo'];
                        $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['projectTitle'] = $project['projectTitle'];
                        $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['startDate'] = $project['startDate'];
                        $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['endDate'] = $project['endDate'];
                        $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['agencyTitle'] = $project['agencyTitle'];
                        $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['sectorTitle'] = $project['sectorTitle'];
                        $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['subSectorTitle'] = $project['subSectorTitle'];
                        $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['locationTitle'] = $project['locationTitle'];
                        $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['rdpChapterTitle'] = $project['rdpChapterTitle'];
                        $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['sdgGoalTitle'] = $project['sdgGoalTitle'];
                        $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['fundingSourceTitle'] = $project['fundingSourceTitle'];
                        $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['fundingAgencyTitle'] = $project['fundingAgencyTitle'];
                    }
                }
            }else if ($model->grouping == '_sector_by_agency') {
                if (!empty($projects)) {
                    // Initialize arrays
                    foreach ($projects as $project) {
                        $sectorTitle = $project['sectorTitle'];
                        $agencyTitle = $project['agencyTitle'];
                        $projectId = $project['id'];
            
                        // Initialize data array if not set
                        if (!isset($data[$sectorTitle]['content'])) {
                            $data[$sectorTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$sectorTitle]['firstLevels'][$agencyTitle]['content'])) {
                            $data[$sectorTitle]['firstLevels'][$agencyTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$sectorTitle]['firstLevels'][$agencyTitle]['projectLevels'][$projectId]['content'])) {
                            $data[$sectorTitle]['firstLevels'][$agencyTitle]['projectLevels'][$projectId]['content'] = $initialValues;
                        }
                    }
            
                    // Aggregate data
                    foreach ($projects as $project) {
                        $sectorTitle = $project['sectorTitle'];
                        $agencyTitle = $project['agencyTitle'];
                        $projectId = $project['id'];
            
                        // Aggregate top level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate first level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['firstLevels'][$agencyTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate second level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['firstLevels'][$agencyTitle]['projectLevels'][$projectId]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Set project specific details

                        $data[$sectorTitle]['firstLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['projectNo'] = $project['projectNo'];
                        $data[$sectorTitle]['firstLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['projectTitle'] = $project['projectTitle'];
                        $data[$sectorTitle]['firstLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['startDate'] = $project['startDate'];
                        $data[$sectorTitle]['firstLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['endDate'] = $project['endDate'];
                        $data[$sectorTitle]['firstLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['agencyTitle'] = $project['agencyTitle'];
                        $data[$sectorTitle]['firstLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['sectorTitle'] = $project['sectorTitle'];
                        $data[$sectorTitle]['firstLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['subSectorTitle'] = $project['subSectorTitle'];
                        $data[$sectorTitle]['firstLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['locationTitle'] = $project['locationTitle'];
                        $data[$sectorTitle]['firstLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['rdpChapterTitle'] = $project['rdpChapterTitle'];
                        $data[$sectorTitle]['firstLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['sdgGoalTitle'] = $project['sdgGoalTitle'];
                        $data[$sectorTitle]['firstLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['fundingSourceTitle'] = $project['fundingSourceTitle'];
                        $data[$sectorTitle]['firstLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['fundingAgencyTitle'] = $project['fundingAgencyTitle'];
                    }
                }
            }else if ($model->grouping == '_sector_by_location_by_agency') {
                if (!empty($projects)) {
                    // Initialize arrays
                    foreach ($projects as $project) {
                        $sectorTitle = $project['sectorTitle'];
                        $locationTitle = $project['locationTitle'];
                        $agencyTitle = $project['agencyTitle'];
                        $projectId = $project['id'];
            
                        // Initialize data array if not set
                        if (!isset($data[$sectorTitle]['content'])) {
                            $data[$sectorTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$sectorTitle]['firstLevels'][$locationTitle]['content'])) {
                            $data[$sectorTitle]['firstLevels'][$locationTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$sectorTitle]['firstLevels'][$locationTitle]['secondLevels'][$agencyTitle]['content'])) {
                            $data[$sectorTitle]['firstLevels'][$locationTitle]['secondLevels'][$agencyTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$sectorTitle]['firstLevels'][$locationTitle]['secondLevels'][$agencyTitle]['projectLevels'][$projectId]['content'])) {
                            $data[$sectorTitle]['firstLevels'][$locationTitle]['secondLevels'][$agencyTitle]['projectLevels'][$projectId]['content'] = $initialValues;
                        }
                    }
            
                    // Aggregate data
                    foreach ($projects as $project) {
                        $sectorTitle = $project['sectorTitle'];
                        $locationTitle = $project['locationTitle'];
                        $agencyTitle = $project['agencyTitle'];
                        $projectId = $project['id'];
            
                        // Aggregate top level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate first level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['firstLevels'][$locationTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate second level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['firstLevels'][$locationTitle]['secondLevels'][$agencyTitle]['content'][$key] += $project[$key];
                            }
                        }


                        // Aggregate third level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['firstLevels'][$locationTitle]['secondLevels'][$agencyTitle]['projectLevels'][$projectId]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Set project specific details

                        $data[$sectorTitle]['firstLevels'][$locationTitle]['secondLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['projectNo'] = $project['projectNo'];
                        $data[$sectorTitle]['firstLevels'][$locationTitle]['secondLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['projectTitle'] = $project['projectTitle'];
                        $data[$sectorTitle]['firstLevels'][$locationTitle]['secondLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['startDate'] = $project['startDate'];
                        $data[$sectorTitle]['firstLevels'][$locationTitle]['secondLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['endDate'] = $project['endDate'];
                        $data[$sectorTitle]['firstLevels'][$locationTitle]['secondLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['agencyTitle'] = $project['agencyTitle'];
                        $data[$sectorTitle]['firstLevels'][$locationTitle]['secondLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['sectorTitle'] = $project['sectorTitle'];
                        $data[$sectorTitle]['firstLevels'][$locationTitle]['secondLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['subSectorTitle'] = $project['subSectorTitle'];
                        $data[$sectorTitle]['firstLevels'][$locationTitle]['secondLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['locationTitle'] = $project['locationTitle'];
                        $data[$sectorTitle]['firstLevels'][$locationTitle]['secondLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['rdpChapterTitle'] = $project['rdpChapterTitle'];
                        $data[$sectorTitle]['firstLevels'][$locationTitle]['secondLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['sdgGoalTitle'] = $project['sdgGoalTitle'];
                        $data[$sectorTitle]['firstLevels'][$locationTitle]['secondLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['fundingSourceTitle'] = $project['fundingSourceTitle'];
                        $data[$sectorTitle]['firstLevels'][$locationTitle]['secondLevels'][$agencyTitle]['projectLevels'][$projectId]['content']['fundingAgencyTitle'] = $project['fundingAgencyTitle'];
                    }
                }
            }else if ($model->grouping == '_sector_by_sub_sector') {
                if (!empty($projects)) {
                    // Initialize arrays
                    foreach ($projects as $project) {
                        $sectorTitle = $project['sectorTitle'];
                        $subSectorTitle = $project['subSectorTitle'];
                        $projectId = $project['id'];
            
                        // Initialize data array if not set
                        if (!isset($data[$sectorTitle]['content'])) {
                            $data[$sectorTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$sectorTitle]['firstLevels'][$subSectorTitle]['content'])) {
                            $data[$sectorTitle]['firstLevels'][$subSectorTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$sectorTitle]['firstLevels'][$subSectorTitle]['projectLevels'][$projectId]['content'])) {
                            $data[$sectorTitle]['firstLevels'][$subSectorTitle]['projectLevels'][$projectId]['content'] = $initialValues;
                        }
                    }
            
                    // Aggregate data
                    foreach ($projects as $project) {
                        $sectorTitle = $project['sectorTitle'];
                        $subSectorTitle = $project['subSectorTitle'];
                        $projectId = $project['id'];
            
                        // Aggregate top level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate first level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['firstLevels'][$subSectorTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate second level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['firstLevels'][$subSectorTitle]['projectLevels'][$projectId]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Set project specific details

                        $data[$sectorTitle]['firstLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['projectNo'] = $project['projectNo'];
                        $data[$sectorTitle]['firstLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['projectTitle'] = $project['projectTitle'];
                        $data[$sectorTitle]['firstLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['startDate'] = $project['startDate'];
                        $data[$sectorTitle]['firstLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['endDate'] = $project['endDate'];
                        $data[$sectorTitle]['firstLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['agencyTitle'] = $project['agencyTitle'];
                        $data[$sectorTitle]['firstLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['sectorTitle'] = $project['sectorTitle'];
                        $data[$sectorTitle]['firstLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['subSectorTitle'] = $project['subSectorTitle'];
                        $data[$sectorTitle]['firstLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['locationTitle'] = $project['locationTitle'];
                        $data[$sectorTitle]['firstLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['rdpChapterTitle'] = $project['rdpChapterTitle'];
                        $data[$sectorTitle]['firstLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['sdgGoalTitle'] = $project['sdgGoalTitle'];
                        $data[$sectorTitle]['firstLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['fundingSourceTitle'] = $project['fundingSourceTitle'];
                        $data[$sectorTitle]['firstLevels'][$subSectorTitle]['projectLevels'][$projectId]['content']['fundingAgencyTitle'] = $project['fundingAgencyTitle'];
                    }
                }
            }else if ($model->grouping == '_sector_by_sdg') {
                if (!empty($projects)) {
                    // Initialize arrays
                    foreach ($projects as $project) {
                        $sectorTitle = $project['sectorTitle'];
                        $sdgGoalTitle = $project['sdgGoalTitle'];
                        $projectId = $project['id'];
            
                        // Initialize data array if not set
                        if (!isset($data[$sectorTitle]['content'])) {
                            $data[$sectorTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$sectorTitle]['firstLevels'][$sdgGoalTitle]['content'])) {
                            $data[$sectorTitle]['firstLevels'][$sdgGoalTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$sectorTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content'])) {
                            $data[$sectorTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content'] = $initialValues;
                        }
                    }
            
                    // Aggregate data
                    foreach ($projects as $project) {
                        $sectorTitle = $project['sectorTitle'];
                        $sdgGoalTitle = $project['sdgGoalTitle'];
                        $projectId = $project['id'];
            
                        // Aggregate top level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate first level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['firstLevels'][$sdgGoalTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate second level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Set project specific details

                        $data[$sectorTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['projectNo'] = $project['projectNo'];
                        $data[$sectorTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['projectTitle'] = $project['projectTitle'];
                        $data[$sectorTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['startDate'] = $project['startDate'];
                        $data[$sectorTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['endDate'] = $project['endDate'];
                        $data[$sectorTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['agencyTitle'] = $project['agencyTitle'];
                        $data[$sectorTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['sectorTitle'] = $project['sectorTitle'];
                        $data[$sectorTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['subSectorTitle'] = $project['subSectorTitle'];
                        $data[$sectorTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['locationTitle'] = $project['locationTitle'];
                        $data[$sectorTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['rdpChapterTitle'] = $project['rdpChapterTitle'];
                        $data[$sectorTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['sdgGoalTitle'] = $project['sdgGoalTitle'];
                        $data[$sectorTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['fundingSourceTitle'] = $project['fundingSourceTitle'];
                        $data[$sectorTitle]['firstLevels'][$sdgGoalTitle]['projectLevels'][$projectId]['content']['fundingAgencyTitle'] = $project['fundingAgencyTitle'];
                    }
                }
            }else if ($model->grouping == '_sector_by_rdp') {
                if (!empty($projects)) {
                    // Initialize arrays
                    foreach ($projects as $project) {
                        $sectorTitle = $project['sectorTitle'];
                        $rdpChapterTitle = $project['rdpChapterTitle'];
                        $projectId = $project['id'];
            
                        // Initialize data array if not set
                        if (!isset($data[$sectorTitle]['content'])) {
                            $data[$sectorTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$sectorTitle]['firstLevels'][$rdpChapterTitle]['content'])) {
                            $data[$sectorTitle]['firstLevels'][$rdpChapterTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$sectorTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content'])) {
                            $data[$sectorTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content'] = $initialValues;
                        }
                    }
            
                    // Aggregate data
                    foreach ($projects as $project) {
                        $sectorTitle = $project['sectorTitle'];
                        $rdpChapterTitle = $project['rdpChapterTitle'];
                        $projectId = $project['id'];
            
                        // Aggregate top level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate first level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['firstLevels'][$rdpChapterTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate second level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Set project specific details

                        $data[$sectorTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['projectNo'] = $project['projectNo'];
                        $data[$sectorTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['projectTitle'] = $project['projectTitle'];
                        $data[$sectorTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['startDate'] = $project['startDate'];
                        $data[$sectorTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['endDate'] = $project['endDate'];
                        $data[$sectorTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['agencyTitle'] = $project['agencyTitle'];
                        $data[$sectorTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['sectorTitle'] = $project['sectorTitle'];
                        $data[$sectorTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['subSectorTitle'] = $project['subSectorTitle'];
                        $data[$sectorTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['locationTitle'] = $project['locationTitle'];
                        $data[$sectorTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['rdpChapterTitle'] = $project['rdpChapterTitle'];
                        $data[$sectorTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['sdgGoalTitle'] = $project['sdgGoalTitle'];
                        $data[$sectorTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['fundingSourceTitle'] = $project['fundingSourceTitle'];
                        $data[$sectorTitle]['firstLevels'][$rdpChapterTitle]['projectLevels'][$projectId]['content']['fundingAgencyTitle'] = $project['fundingAgencyTitle'];
                    }
                }
            }else if ($model->grouping == '_sector_by_fund_source') {
                if (!empty($projects)) {
                    // Initialize arrays
                    foreach ($projects as $project) {
                        $sectorTitle = $project['sectorTitle'];
                        $fundingSourceTitle = $project['fundingSourceTitle'];
                        $projectId = $project['id'];
            
                        // Initialize data array if not set
                        if (!isset($data[$sectorTitle]['content'])) {
                            $data[$sectorTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['content'])) {
                            $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['content'] = $initialValues;
                        }
                        if (!isset($data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content'])) {
                            $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content'] = $initialValues;
                        }
                    }
            
                    // Aggregate data
                    foreach ($projects as $project) {
                        $sectorTitle = $project['sectorTitle'];
                        $fundingSourceTitle = $project['fundingSourceTitle'];
                        $projectId = $project['id'];
            
                        // Aggregate top level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate first level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Aggregate second level
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content'][$key] += $project[$key];
                            }
                        }
            
                        // Set project specific details

                        $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['projectNo'] = $project['projectNo'];
                        $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['projectTitle'] = $project['projectTitle'];
                        $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['startDate'] = $project['startDate'];
                        $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['endDate'] = $project['endDate'];
                        $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['agencyTitle'] = $project['agencyTitle'];
                        $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['sectorTitle'] = $project['sectorTitle'];
                        $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['subSectorTitle'] = $project['subSectorTitle'];
                        $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['locationTitle'] = $project['locationTitle'];
                        $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['rdpChapterTitle'] = $project['rdpChapterTitle'];
                        $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['sdgGoalTitle'] = $project['sdgGoalTitle'];
                        $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['fundingSourceTitle'] = $project['fundingSourceTitle'];
                        $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content']['fundingAgencyTitle'] = $project['fundingAgencyTitle'];
                    }
                }
            }

            $bigCaps = range('A', 'Z');
            $smallCaps = range('a', 'z');
            $numbers = range('1', '100');
            $genders = ['M' => 'Male', 'F' => 'Female'];

            return $this->renderAjax('_data', [
                'model' => $model,
                'data' => $data,
                'totals' => $totals,
                'bigCaps' => $bigCaps,
                'smallCaps' => $smallCaps,
                'numbers' => $numbers,
                'genders' => $genders,
            ]);
        }
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            'years' => $years,
            'agencies' => $agencies,
            'sectors' => $sectors,
            'modes' => $modes,
            'regions' => $regions,
            'provinces' => $provinces,
            'citymuns' => $citymuns,
            'fundSources' => $fundSources,
            'sorts' => $sorts,
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
                        'GROUP_CONCAT(DISTINCT CONCAT(row_number, ". ", fund_source.title) ORDER BY phfs.id ASC SEPARATOR "<br>") as title'
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
                    'GROUP_CONCAT(DISTINCT CONCAT(row_number, ". ", fund_source.title) ORDER BY phfs.id ASC SEPARATOR "<br>") as title'
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
