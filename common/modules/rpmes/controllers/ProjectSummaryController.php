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
            //'_agency_by_location' => 'Agency by Location',
            //'_agency_by_sector_by_sub_sector' => 'Agency by Sector by Sub-sector',
            //'_agency_by_sdg' => 'Agency by SDG',
            //'_agency_by_rdp' => 'Agency by RDP',
            '_agency_by_fund_source' => 'Agency by Fund Source',
            '_sector_by_agency' => 'Sector by Agency', 
            //'_sector_by_location_by_agency' => 'Sector by Location by Agency',
            //'_sector_by_sub_sector' => 'Sector by Sub-Sector',
            //'_sector_by_sdg' => 'Sector by SDG',
            //'_sector_by_rdp' => 'Sector by RDP',
            '_sector_by_fund_source' => 'Sector by Fund Source',
        ];

        if($model->load(Yii::$app->request->post())){

            $regionIDs = ProjectRegion::find();
            $provinceIDs = ProjectProvince::find();
            $fundSourceIDs = ProjectHasFundSources::find();
            $projectIDs = Plan::find();

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

            if($model->year != '')
            {
                $projectIDs = $projectIDs->andWhere(['year' => $model->year]);
            }

            $regionIDs = $regionIDs->all();
            $regionIDs = ArrayHelper::map($regionIDs, 'project_id', 'project_id');

            $provinceIDs = $provinceIDs->all();
            $provinceIDs = ArrayHelper::map($provinceIDs, 'project_id', 'project_id');

            $fundSourceIDs = $fundSourceIDs->all();
            $fundSourceIDs = ArrayHelper::map($fundSourceIDs, 'project_id', 'project_id');

            $projectIDs = $projectIDs->all();
            $projectIDs = ArrayHelper::map($projectIDs, 'project_id', 'project_id');

            $projects = Plan::find()
            ->select([
                'plan.id as id',
                'acc.year',
                'acc.quarter',
                'p.project_no as projectNo',
                'p.title as projectTitle',
                'DATE_FORMAT(p.start_date, "%m-%d-%y") as startDate',
                'DATE_FORMAT(p.completion_date, "%m-%d-%y") as endDate',
                'DATEDIFF(p.completion_date, p.start_date) AS durationDays',
                'a.code AS agencyTitle',
                's.title AS sectorTitle',
                //'ss.title AS subSectorTitle',
                //'rdp.title AS rdpChapterTitle',
                //'sdg.title AS sdgGoalTitle',
                'fs.title AS fundingSourceTitle',
                'fa.title AS fundingAgencyTitle',
                'COALESCE(p.cost, 0) AS cost',
                'COALESCE(fia.allocation, 0) AS appropriations',
                'COALESCE(fia.releases, 0) AS allotment',
                'COALESCE(fia.obligation, 0) AS obligations',
                'COALESCE(fia.expenditures, 0) AS disbursements',
                'COALESCE((fia.releases / fia.allocation) * 100, 0) AS fundingSupport',
                'COALESCE((fia.expenditures / fia.releases) * 100, 0) AS fundingUtilizationRate',
                'COALESCE(target_owpa.target, 0) AS targetOwpa',
                'COALESCE(actual_owpa.actual, 0) AS actualOwpa',
                'COALESCE(tcpa.total, 0) as perAgencyCost',
                'COALESCE(p.cost/tcpa.total, 0) as weight',
                'COALESCE(target_owpa.target*(p.cost/tcpa.total), 0) as weightedTarget',
                'COALESCE(actual_owpa.actual*(p.cost/tcpa.total), 0) as weightedAccomplishment',
                'COALESCE(actual_owpa.actual-target_owpa.target, 0) as slippage',
                'persons_employed.male as maleEmployed',
                'persons_employed.female as femaleEmployed',
                'COALESCE(persons_employed.male+persons_employed.female, 0) as totalEmployed',
                'COALESCE(ib.male+ib.female, 0) as individualBeneficiaries',
                'COALESCE(gb.value, 0) as groupBeneficiaries',
                'acc.action as isCompleted',
                'IF(acc.action = 0, IF(p_acc.value > 0, IF(COALESCE(actual_owpa.actual - target_owpa.target) < 0, 1 , 0), 0), 0) as isBehindSchedule',
                'IF(acc.action = 0, IF(p_acc.value > 0, IF(COALESCE(actual_owpa.actual - target_owpa.target) = 0, 1 , 0), 0), 0) as isOnTime',
                'IF(acc.action = 0, IF(p_acc.value > 0, IF(COALESCE(actual_owpa.actual - target_owpa.target) > 0, 1 , 0), 0), 0) as isAheadOfSchedule',
                'IF(acc.action IS NULL, 
                1, 
                IF(acc.action = 0, 
                    IF(p_acc.value = 0, 
                        IF(p_tar.total > 0, 1, 0), 
                        0), 
                    0)
                ) AS isNotYetStarted',
                'IF(acc.action = 0, 1, 0) as isOngoing',
                'p_tar.type as projectType'
            ]);

            $projects = $projects->leftJoin('project p', 'p.id = plan.project_id');
            $projects = $projects->leftJoin('accomplishment acc', 'p.id = acc.project_id AND acc.year = :year AND acc.quarter = :quarter', [
                ':year' => $model->year,
                ':quarter' => $model->quarter
            ]);
            $projects = $projects->leftJoin('agency a', 'p.agency_id = a.id');
            $projects = $projects->leftJoin('sector s', 'p.sector_id = s.id');
            $projects = $projects->leftJoin('sub_sector ss', 'p.sub_sector_id = ss.id');
            // region name
            $projects = $projects->leftJoin(['r' => "(
                SELECT project_id, GROUP_CONCAT(DISTINCT tblregion.abbreviation ORDER BY tblregion.abbreviation ASC SEPARATOR ', ') AS title
                FROM project_region
                LEFT JOIN tblregion ON tblregion.region_c = project_region.region_id
                GROUP BY project_id
            )"], 'r.project_id = p.id');
            // province name
            $projects = $projects->leftJoin(['pr' => "(
                SELECT project_id, GROUP_CONCAT(DISTINCT tblprovince.province_m ORDER BY tblprovince.province_m ASC SEPARATOR ', ') AS title
                FROM project_province
                LEFT JOIN tblprovince ON tblprovince.province_c = project_province.province_id
                GROUP BY project_id
            )"], 'pr.project_id = p.id'); 
            // fund source name
            $projects = $projects->leftJoin(['fs' => "(
                SELECT phfs.project_id, GROUP_CONCAT(DISTINCT fund_source.title ORDER BY phfs.id ASC SEPARATOR ', ') AS title
                FROM project_has_fund_sources phfs
                LEFT JOIN fund_source ON fund_source.id = phfs.fund_source_id
                GROUP BY phfs.project_id
            )"], 'fs.project_id = p.id');
            // funding agency name
            $projects = $projects->leftJoin(['fa' => "(
                SELECT phfs.project_id, GROUP_CONCAT(DISTINCT CONCAT(phfs.agency) ORDER BY phfs.id ASC SEPARATOR ', ') AS title
                FROM project_has_fund_sources phfs
                GROUP BY phfs.project_id
            )"], 'fa.project_id = p.id');
            // allocations, releases, obligations, expenditures
            $projects = $projects->leftJoin(['fia' => "(
                SELECT project_id, year, quarter, allocation, releases, obligation, expenditures
                FROM financial_accomplishment
            )"], 'fia.project_id = acc.project_id and fia.year = acc.year and fia.quarter = acc.quarter');
            // target owpa
            $projects = $projects->leftJoin(['target_owpa' => "(
                SELECT 
                    pa.project_id, 
                    pa.year, 
                    pa.quarter, 
                    CASE 
                        WHEN pt.type = 'Numerical' THEN 
                            CASE 
                                WHEN pa.quarter = 'Q1' THEN 
                                    ((COALESCE(pt.jan, 0) + COALESCE(pt.feb, 0) + COALESCE(pt.mar, 0) + COALESCE(pt.baseline, 0)) 
                                    / 
                                    (COALESCE(pt.jan, 0) + COALESCE(pt.feb, 0) + COALESCE(pt.mar, 0) + COALESCE(pt.apr, 0) + 
                                    COALESCE(pt.may, 0) + COALESCE(pt.jun, 0) + COALESCE(pt.jul, 0) + COALESCE(pt.aug, 0) + 
                                    COALESCE(pt.sep, 0) + COALESCE(pt.oct, 0) + COALESCE(pt.nov, 0) + COALESCE(`dec`, 0) + COALESCE(pt.baseline, 0))) * 100
                                WHEN pa.quarter = 'Q2' THEN 
                                    ((COALESCE(pt.jan, 0) + COALESCE(pt.feb, 0) + COALESCE(pt.mar, 0) + COALESCE(pt.apr, 0) + COALESCE(pt.may, 0) + COALESCE(pt.jun, 0) + COALESCE(pt.baseline, 0)) 
                                    / 
                                    (COALESCE(pt.jan, 0) + COALESCE(pt.feb, 0) + COALESCE(pt.mar, 0) + COALESCE(pt.apr, 0) + 
                                    COALESCE(pt.may, 0) + COALESCE(pt.jun, 0) + COALESCE(pt.jul, 0) + COALESCE(pt.aug, 0) + 
                                    COALESCE(pt.sep, 0) + COALESCE(pt.oct, 0) + COALESCE(pt.nov, 0) + COALESCE(`dec`, 0) + COALESCE(pt.baseline, 0))) * 100
                                WHEN pa.quarter = 'Q3' THEN 
                                    ((COALESCE(pt.jan, 0) + COALESCE(pt.feb, 0) + COALESCE(pt.mar, 0) + COALESCE(pt.apr, 0) + 
                                    COALESCE(pt.may, 0) + COALESCE(pt.jun, 0) + COALESCE(pt.jul, 0) + COALESCE(pt.aug, 0) + COALESCE(pt.sep, 0) + COALESCE(pt.baseline, 0)) 
                                    / 
                                    (COALESCE(pt.jan, 0) + COALESCE(pt.feb, 0) + COALESCE(pt.mar, 0) + COALESCE(pt.apr, 0) + 
                                    COALESCE(pt.may, 0) + COALESCE(pt.jun, 0) + COALESCE(pt.jul, 0) + COALESCE(pt.aug, 0) + 
                                    COALESCE(pt.sep, 0) + COALESCE(pt.oct, 0) + COALESCE(pt.nov, 0) + COALESCE(`dec`, 0) + COALESCE(pt.baseline, 0))) * 100
                                WHEN pa.quarter = 'Q4' THEN 
                                    ((COALESCE(pt.jan, 0) + COALESCE(pt.feb, 0) + COALESCE(pt.mar, 0) + COALESCE(pt.apr, 0) + 
                                    COALESCE(pt.may, 0) + COALESCE(pt.jun, 0) + COALESCE(pt.jul, 0) + COALESCE(pt.aug, 0) + 
                                    COALESCE(pt.sep, 0) + COALESCE(pt.oct, 0) + COALESCE(pt.nov, 0) + COALESCE(`dec`, 0) + COALESCE(pt.baseline, 0)) 
                                    / 
                                    (COALESCE(pt.jan, 0) + COALESCE(pt.feb, 0) + COALESCE(pt.mar, 0) + COALESCE(pt.apr, 0) + 
                                    COALESCE(pt.may, 0) + COALESCE(pt.jun, 0) + COALESCE(pt.jul, 0) + COALESCE(pt.aug, 0) + 
                                    COALESCE(pt.sep, 0) + COALESCE(pt.oct, 0) + COALESCE(pt.nov, 0) + COALESCE(`dec`, 0) + COALESCE(pt.baseline, 0))) * 100
                            END
                        WHEN pt.type = 'Percentage' THEN 
                            CASE 
                                WHEN pa.quarter = 'Q1' THEN COALESCE(pt.jan, 0) + COALESCE(pt.feb, 0) + COALESCE(pt.mar, 0) + COALESCE(pt.baseline, 0)
                                WHEN pa.quarter = 'Q2' THEN COALESCE(pt.jan, 0) + COALESCE(pt.feb, 0) + COALESCE(pt.mar, 0) + COALESCE(pt.apr, 0) + COALESCE(pt.may, 0) + COALESCE(pt.jun, 0) + COALESCE(pt.baseline, 0)
                                WHEN pa.quarter = 'Q3' THEN COALESCE(pt.jan, 0) + COALESCE(pt.feb, 0) + COALESCE(pt.mar, 0) + COALESCE(pt.apr, 0) + COALESCE(pt.may, 0) + COALESCE(pt.jun, 0) + COALESCE(pt.jul, 0) + COALESCE(pt.aug, 0) + COALESCE(pt.sep, 0) + COALESCE(pt.baseline, 0)
                                WHEN pa.quarter = 'Q4' THEN COALESCE(pt.jan, 0) + COALESCE(pt.feb, 0) + COALESCE(pt.mar, 0) + COALESCE(pt.apr, 0) + COALESCE(pt.may, 0) + COALESCE(pt.jun, 0) + COALESCE(pt.jul, 0) + COALESCE(pt.aug, 0) + COALESCE(pt.sep, 0) + COALESCE(pt.oct, 0) + COALESCE(pt.nov, 0) + COALESCE(`dec`, 0) + COALESCE(pt.baseline, 0)
                            END
                        ELSE 0
                    END AS target
                FROM 
                    physical_accomplishment pa
                LEFT JOIN 
                    project_target pt 
                    ON pa.project_id = pt.project_id AND pa.year = pt.year
                WHERE 
                    pt.target_type = 'Physical'
            )"], 'target_owpa.project_id = acc.project_id and target_owpa.year = acc.year and target_owpa.quarter = acc.quarter');
            // actual owpa
            $projects = $projects->leftJoin(['actual_owpa' => "(
                SELECT 
                pa.project_id, 
                    pa.year, 
                    pa.quarter, 
                    CASE 
                            WHEN pt.type = 'Numerical' THEN 
                                COALESCE(pa.value, 0) / 
                                        (COALESCE(pt.jan, 0) + COALESCE(pt.feb, 0) + COALESCE(pt.mar, 0) + 
                                        COALESCE(pt.apr, 0) + COALESCE(pt.may, 0) + COALESCE(pt.jun, 0) + 
                                        COALESCE(pt.jul, 0) + COALESCE(pt.aug, 0) + COALESCE(pt.sep, 0) + 
                                        COALESCE(pt.oct, 0) + COALESCE(pt.nov, 0) + COALESCE(pt.dec, 0) + COALESCE(pt.baseline, 0)) * 100
                            WHEN pt.type = 'Percentage' THEN COALESCE(pa.value, 0)
                            ELSE 0
                        END AS actual
                FROM 
                    physical_accomplishment pa
                LEFT JOIN 
                    project_target pt 
                    ON pa.project_id = pt.project_id AND pa.year = pt.year
                WHERE 
                    pt.target_type = 'Physical'
                    )"], 'actual_owpa.project_id = acc.project_id and actual_owpa.year = acc.year and actual_owpa.quarter = acc.quarter');
            // total cost per agency
            $projects = $projects->leftJoin(['tcpa' => "(
                SELECT 
                plan.year,
                agency_id,
                SUM(COALESCE(cost, 0)) as total
                from plan
                left join project on project.id = plan.project_id
                group by agency_id
            )"], 'tcpa.agency_id = a.id and tcpa.year = acc.year');
            // persons employed
            $projects = $projects->leftJoin(['persons_employed' => "(
                SELECT * from person_employed_accomplishment
            )"], 'persons_employed.year = acc.year and persons_employed.quarter = acc.quarter and persons_employed.project_id = acc.project_id');
            // individual beneficiaries
            $projects = $projects->leftJoin(['ib' => "(
                SELECT 
                eoa.project_id,
                eoa.year,
                eoa.quarter,
                eoa.male,
                eoa.female
                from project_expected_output peo
                left join expected_output_accomplishment eoa on eoa.expected_output_id = peo.id
                where LOWER(indicator) = 'number of individual beneficiaries served'
            )"], 'ib.year = acc.year and ib.quarter = acc.quarter and ib.project_id = acc.project_id');
            // group beneficiaries
            $projects = $projects->leftJoin(['gb' => "(
                SELECT 
                eoa.project_id,
                eoa.year,
                eoa.quarter,
                eoa.value
                from project_expected_output peo
                left join expected_output_accomplishment eoa on eoa.expected_output_id = peo.id
                where LOWER(indicator) = 'number of group beneficiaries served'
            )"], 'gb.year = acc.year and gb.quarter = acc.quarter and gb.project_id = acc.project_id');
            // physical accomplishment
            $projects = $projects->leftJoin(['p_acc' => "(
                SELECT * from physical_accomplishment
            )"], 'p_acc.year = acc.year and p_acc.quarter = acc.quarter and p_acc.project_id = acc.project_id');
            // physical_target
            $projects = $projects->leftJoin(['p_tar' => "(
                SELECT 
                    pt.project_id, 
                    pt.year, 
                    pt.type,
                    COALESCE(pt.jan, 0) + COALESCE(pt.feb, 0) + COALESCE(pt.mar, 0) + 
                    COALESCE(pt.apr, 0) + COALESCE(pt.may, 0) + COALESCE(pt.jun, 0) + 
                    COALESCE(pt.jul, 0) + COALESCE(pt.aug, 0) + COALESCE(pt.sep, 0) + 
                    COALESCE(pt.oct, 0) + COALESCE(pt.nov, 0) + COALESCE(pt.dec, 0) + COALESCE(pt.baseline, 0) as total
                FROM 
                    project_target pt 
                WHERE 
                    pt.target_type = 'Physical'
            )"], 'p_tar.year = acc.year and p_tar.project_id = acc.project_id');

            if($model->year != ''){
                $projects = $projects->andWhere(['plan.year' => $model->year]);
            }

            if($model->agency_id != ''){
                $projects = $projects->andWhere(['p.agency_id' => $model->agency_id]);
            }

            if($model->sector_id != ''){
                $projects = $projects->andWhere(['p.sector_id' => $model->sector_id]);
            }

            if($model->mode_of_implementation_id != ''){
                $projects = $projects->andWhere(['p.mode_of_implementation_id' => $model->mode_of_implementation_id]);
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

            if($model->period != '')
            {
                if($model->period == 'Current Year'){
                    $projects = $projects->andWhere(['<=', 'DATEDIFF(p.completion_date, p.start_date)', 365]);
                }else{
                    $projects = $projects->andWhere(['>', 'DATEDIFF(p.completion_date, p.start_date)', 365]);
                }
                
            }

            $projects = $projects 
            ->asArray()
            ->all();

            //echo "<pre>"; print_r($projects); exit;

            $initialValues = [
                'cost' => 0,
                'appropriations' => 0,
                'allotment' => 0,
                'obligations' => 0,
                'disbursements' => 0,
                'maleEmployed' => 0,
                'femaleEmployed' => 0,
                'totalEmployed' => 0,
                'individualBeneficiaries' => 0,
                'groupBeneficiaries' => 0,
                'isCompleted' => 0,
                'isBehindSchedule' => 0,
                'isOnTime' => 0,
                'isAheadOfSchedule' => 0,
                'isNotYetStarted' => 0,
                'isOngoing' => 0,
                'targetOwpa' => 0,
                'actualOwpa' => 0,
                'slippage' => 0,
                'fundingSupport' => 0,
                'fundingUtilizationRate' => 0,
            ];

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

                        // Aggregate values
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['content'][$key] += $project[$key];
                                $data[$agencyTitle]['firstLevels'][$sectorTitle]['content'][$key] += $project[$key];
                                $data[$agencyTitle]['firstLevels'][$sectorTitle]['projectLevels'][$projectId]['content'][$key] += $project[$key];
                            }
                        }       
                        
                        // Set project-specific details
                        foreach (['projectNo', 'projectTitle', 'agencyTitle', 'startDate', 'endDate', 'sectorTitle', 'fundingSourceTitle', 'fundingAgencyTitle', 'projectType'] as $field) {
                            $data[$agencyTitle]['firstLevels'][$sectorTitle]['projectLevels'][$projectId]['content'][$field] = $project[$field];
                        }
                    }

                    // first level
                    foreach ($data as $agencyTitle => &$agency) {
                        foreach ($agency['firstLevels'] as $sectorTitle => &$sector) {
                            $weightedTarget = 0;
                            $weightedAccomplishment = 0;
                            $totalCost = $sector['content']['cost'] ?? 0;
                
                            if ($totalCost > 0 && isset($sector['projectLevels'])) {
                                foreach ($sector['projectLevels'] as $project) {
                                    if (isset($project['content']['cost'], $project['content']['targetOwpa'])) {
                                        $projectCost = $project['content']['cost'];
                                        $targetOwpa = $project['content']['targetOwpa'];
                                        $weightedTarget += ($projectCost / $totalCost) * $targetOwpa;
                                    }
                
                                    if (isset($project['content']['cost'], $project['content']['actualOwpa'])) {
                                        $projectCost = $project['content']['cost'];
                                        $actualOwpa = $project['content']['actualOwpa'];
                                        $weightedAccomplishment += ($projectCost / $totalCost) * $actualOwpa;
                                    }
                                }
                            }
                
                            $sector['content']['weightedTarget'] = $weightedTarget;
                            $sector['content']['weightedAccomplishment'] = $weightedAccomplishment;
                        }
                    }

                    // second level
                    foreach ($data as $agencyTitle => &$agency) {
                        $weightedTarget = 0;
                        $weightedAccomplishment = 0;
                        $totalCost = $agency['content']['cost'] ?? 0;
                
                        if ($totalCost > 0 && isset($agency['firstLevels'])) {
                            foreach ($agency['firstLevels'] as $sector) {
                                if (isset($sector['content']['cost'], $sector['content']['weightedTarget'])) {
                                    $sectorCost = $sector['content']['cost'];
                                    $sectorTarget = $sector['content']['weightedTarget'];
                                    $weightedTarget += ($sectorCost / $totalCost) * $sectorTarget;
                                }
                
                                if (isset($sector['content']['cost'], $sector['content']['weightedAccomplishment'])) {
                                    $sectorCost = $sector['content']['cost'];
                                    $sectorAccomplishment = $sector['content']['weightedAccomplishment'];
                                    $weightedAccomplishment += ($sectorCost / $totalCost) * $sectorAccomplishment;
                                }
                            }
                        }
                
                        $agency['content']['weightedTarget'] = $weightedTarget;
                        $agency['content']['weightedAccomplishment'] = $weightedAccomplishment;
                    }

                    // grand total
                    $grandTotal = $initialValues;
                    $grandTotal['weightedTarget'] = 0;
                    $grandTotal['weightedAccomplishment'] = 0;

                    // Sum total values from each sector
                    foreach ($data as $agency) {
                        foreach ($initialValues as $key => $value) {
                            if (isset($agency['content'][$key])) {
                                $grandTotal[$key] += $agency['content'][$key];
                            }
                        }
                    }

                    $totalCost = $grandTotal['cost'] ?? 0;

                    // Compute weighted target and accomplishment across all agencies
                    if ($totalCost > 0) {
                        foreach ($data as $agency) {
                            if (isset($agency['content']['cost'], $agency['content']['weightedTarget'])) {
                                $agencyCost = $agency['content']['cost'];
                                $agencyTarget = $agency['content']['weightedTarget'];
                                $grandTotal['weightedTarget'] += ($agencyCost / $totalCost) * $agencyTarget;
                            }

                            if (isset($agency['content']['cost'], $agency['content']['weightedAccomplishment'])) {
                                $agencyCost = $agency['content']['cost'];
                                $agencyAccomplishment = $agency['content']['weightedAccomplishment'];
                                $grandTotal['weightedAccomplishment'] += ($agencyCost / $totalCost) * $agencyAccomplishment;
                            }
                        }
                    }

                    // Store it in $data or separately as needed
                    $data['__grandTotal'] = $grandTotal;
                }
            }

            if ($model->grouping == '_agency_by_fund_source') {

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

                        // Aggregate values
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$agencyTitle]['content'][$key] += $project[$key];
                                $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['content'][$key] += $project[$key];
                                $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content'][$key] += $project[$key];
                            }
                        }       
                        
                        // Set project-specific details
                        foreach (['projectNo', 'projectTitle', 'agencyTitle', 'startDate', 'endDate', 'sectorTitle', 'fundingSourceTitle', 'fundingAgencyTitle', 'projectType'] as $field) {
                            $data[$agencyTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content'][$field] = $project[$field];
                        }
                    }

                    // first level
                    foreach ($data as $agencyTitle => &$agency) {
                        foreach ($agency['firstLevels'] as $fundingSourceTitle => &$fundingSource) {
                            $weightedTarget = 0;
                            $weightedAccomplishment = 0;
                            $totalCost = $fundingSource['content']['cost'] ?? 0;
                
                            if ($totalCost > 0 && isset($fundingSource['projectLevels'])) {
                                foreach ($fundingSource['projectLevels'] as $project) {
                                    if (isset($project['content']['cost'], $project['content']['targetOwpa'])) {
                                        $projectCost = $project['content']['cost'];
                                        $targetOwpa = $project['content']['targetOwpa'];
                                        $weightedTarget += ($projectCost / $totalCost) * $targetOwpa;
                                    }
                
                                    if (isset($project['content']['cost'], $project['content']['actualOwpa'])) {
                                        $projectCost = $project['content']['cost'];
                                        $actualOwpa = $project['content']['actualOwpa'];
                                        $weightedAccomplishment += ($projectCost / $totalCost) * $actualOwpa;
                                    }
                                }
                            }
                
                            $fundingSource['content']['weightedTarget'] = $weightedTarget;
                            $fundingSource['content']['weightedAccomplishment'] = $weightedAccomplishment;
                        }
                    }

                    // second level
                    foreach ($data as $agencyTitle => &$agency) {
                        $weightedTarget = 0;
                        $weightedAccomplishment = 0;
                        $totalCost = $agency['content']['cost'] ?? 0;
                
                        if ($totalCost > 0 && isset($agency['firstLevels'])) {
                            foreach ($agency['firstLevels'] as $fundingSource) {
                                if (isset($fundingSource['content']['cost'], $fundingSource['content']['weightedTarget'])) {
                                    $fundingSourceCost = $fundingSource['content']['cost'];
                                    $fundingSourceTarget = $fundingSource['content']['weightedTarget'];
                                    $weightedTarget += ($fundingSourceCost / $totalCost) * $fundingSourceTarget;
                                }
                
                                if (isset($fundingSource['content']['cost'], $fundingSource['content']['weightedAccomplishment'])) {
                                    $fundingSourceCost = $fundingSource['content']['cost'];
                                    $fundingSourceAccomplishment = $fundingSource['content']['weightedAccomplishment'];
                                    $weightedAccomplishment += ($fundingSourceCost / $totalCost) * $fundingSourceAccomplishment;
                                }
                            }
                        }
                
                        $agency['content']['weightedTarget'] = $weightedTarget;
                        $agency['content']['weightedAccomplishment'] = $weightedAccomplishment;
                    }

                    // grand total
                    $grandTotal = $initialValues;
                    $grandTotal['weightedTarget'] = 0;
                    $grandTotal['weightedAccomplishment'] = 0;

                    // Sum total values from each agency
                    foreach ($data as $agency) {
                        foreach ($initialValues as $key => $value) {
                            if (isset($agency['content'][$key])) {
                                $grandTotal[$key] += $agency['content'][$key];
                            }
                        }
                    }

                    $totalCost = $grandTotal['cost'] ?? 0;

                    // Compute weighted target and accomplishment across all agencies
                    if ($totalCost > 0) {
                        foreach ($data as $agency) {
                            if (isset($agency['content']['cost'], $agency['content']['weightedTarget'])) {
                                $agencyCost = $agency['content']['cost'];
                                $agencyTarget = $agency['content']['weightedTarget'];
                                $grandTotal['weightedTarget'] += ($agencyCost / $totalCost) * $agencyTarget;
                            }

                            if (isset($agency['content']['cost'], $agency['content']['weightedAccomplishment'])) {
                                $agencyCost = $agency['content']['cost'];
                                $agencyAccomplishment = $agency['content']['weightedAccomplishment'];
                                $grandTotal['weightedAccomplishment'] += ($agencyCost / $totalCost) * $agencyAccomplishment;
                            }
                        }
                    }

                    // Store it in $data or separately as needed
                    $data['__grandTotal'] = $grandTotal;
                }
            }

            if ($model->grouping == '_sector_by_agency') {

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

                        // Aggregate values
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['content'][$key] += $project[$key];
                                $data[$sectorTitle]['firstLevels'][$agencyTitle]['content'][$key] += $project[$key];
                                $data[$sectorTitle]['firstLevels'][$agencyTitle]['projectLevels'][$projectId]['content'][$key] += $project[$key];
                            }
                        }       
                        
                        // Set project-specific details
                        foreach (['projectNo', 'projectTitle', 'agencyTitle', 'startDate', 'endDate', 'sectorTitle', 'fundingSourceTitle', 'fundingAgencyTitle', 'projectType'] as $field) {
                            $data[$sectorTitle]['firstLevels'][$agencyTitle]['projectLevels'][$projectId]['content'][$field] = $project[$field];
                        }
                    }

                    // first level
                    foreach ($data as $sectorTitle => &$sector) {
                        foreach ($sector['firstLevels'] as $agencyTitle => &$agency) {
                            $weightedTarget = 0;
                            $weightedAccomplishment = 0;
                            $totalCost = $agency['content']['cost'] ?? 0;
                
                            if ($totalCost > 0 && isset($agency['projectLevels'])) {
                                foreach ($agency['projectLevels'] as $project) {
                                    if (isset($project['content']['cost'], $project['content']['targetOwpa'])) {
                                        $projectCost = $project['content']['cost'];
                                        $targetOwpa = $project['content']['targetOwpa'];
                                        $weightedTarget += ($projectCost / $totalCost) * $targetOwpa;
                                    }
                
                                    if (isset($project['content']['cost'], $project['content']['actualOwpa'])) {
                                        $projectCost = $project['content']['cost'];
                                        $actualOwpa = $project['content']['actualOwpa'];
                                        $weightedAccomplishment += ($projectCost / $totalCost) * $actualOwpa;
                                    }
                                }
                            }
                
                            $agency['content']['weightedTarget'] = $weightedTarget;
                            $agency['content']['weightedAccomplishment'] = $weightedAccomplishment;
                        }
                    }

                    // second level
                    foreach ($data as $sectorTitle => &$sector) {
                        $weightedTarget = 0;
                        $weightedAccomplishment = 0;
                        $totalCost = $sector['content']['cost'] ?? 0;
                
                        if ($totalCost > 0 && isset($sector['firstLevels'])) {
                            foreach ($sector['firstLevels'] as $agency) {
                                if (isset($agency['content']['cost'], $agency['content']['weightedTarget'])) {
                                    $agencyCost = $agency['content']['cost'];
                                    $agencyTarget = $agency['content']['weightedTarget'];
                                    $weightedTarget += ($agencyCost / $totalCost) * $agencyTarget;
                                }
                
                                if (isset($agency['content']['cost'], $agency['content']['weightedAccomplishment'])) {
                                    $agencyCost = $agency['content']['cost'];
                                    $agencyAccomplishment = $agency['content']['weightedAccomplishment'];
                                    $weightedAccomplishment += ($agencyCost / $totalCost) * $agencyAccomplishment;
                                }
                            }
                        }
                
                        $sector['content']['weightedTarget'] = $weightedTarget;
                        $sector['content']['weightedAccomplishment'] = $weightedAccomplishment;
                    }

                    // grand total
                    $grandTotal = $initialValues;
                    $grandTotal['weightedTarget'] = 0;
                    $grandTotal['weightedAccomplishment'] = 0;

                    // Sum total values from each sector
                    foreach ($data as $sector) {
                        foreach ($initialValues as $key => $value) {
                            if (isset($sector['content'][$key])) {
                                $grandTotal[$key] += $sector['content'][$key];
                            }
                        }
                    }

                    $totalCost = $grandTotal['cost'] ?? 0;

                    // Compute weighted target and accomplishment across all sectors
                    if ($totalCost > 0) {
                        foreach ($data as $sector) {
                            if (isset($sector['content']['cost'], $sector['content']['weightedTarget'])) {
                                $sectorCost = $sector['content']['cost'];
                                $sectorTarget = $sector['content']['weightedTarget'];
                                $grandTotal['weightedTarget'] += ($sectorCost / $totalCost) * $sectorTarget;
                            }

                            if (isset($sector['content']['cost'], $sector['content']['weightedAccomplishment'])) {
                                $sectorCost = $sector['content']['cost'];
                                $sectorAccomplishment = $sector['content']['weightedAccomplishment'];
                                $grandTotal['weightedAccomplishment'] += ($sectorCost / $totalCost) * $sectorAccomplishment;
                            }
                        }
                    }

                    // Store it in $data or separately as needed
                    $data['__grandTotal'] = $grandTotal;
                }
            }

            if ($model->grouping == '_sector_by_fund_source') {

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

                        // Aggregate values
                        foreach ($initialValues as $key => $value) {
                            if (isset($project[$key])) {
                                $data[$sectorTitle]['content'][$key] += $project[$key];
                                $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['content'][$key] += $project[$key];
                                $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content'][$key] += $project[$key];
                            }
                        }       
                        
                        // Set project-specific details
                        foreach (['projectNo', 'projectTitle', 'agencyTitle', 'startDate', 'endDate', 'sectorTitle', 'fundingSourceTitle', 'fundingAgencyTitle', 'projectType'] as $field) {
                            $data[$sectorTitle]['firstLevels'][$fundingSourceTitle]['projectLevels'][$projectId]['content'][$field] = $project[$field];
                        }
                    }

                    // first level
                    foreach ($data as $sectorTitle => &$sector) {
                        foreach ($sector['firstLevels'] as $fundingSourceTitle => &$fundingSource) {
                            $weightedTarget = 0;
                            $weightedAccomplishment = 0;
                            $totalCost = $fundingSource['content']['cost'] ?? 0;
                
                            if ($totalCost > 0 && isset($fundingSource['projectLevels'])) {
                                foreach ($fundingSource['projectLevels'] as $project) {
                                    if (isset($project['content']['cost'], $project['content']['targetOwpa'])) {
                                        $projectCost = $project['content']['cost'];
                                        $targetOwpa = $project['content']['targetOwpa'];
                                        $weightedTarget += ($projectCost / $totalCost) * $targetOwpa;
                                    }
                
                                    if (isset($project['content']['cost'], $project['content']['actualOwpa'])) {
                                        $projectCost = $project['content']['cost'];
                                        $actualOwpa = $project['content']['actualOwpa'];
                                        $weightedAccomplishment += ($projectCost / $totalCost) * $actualOwpa;
                                    }
                                }
                            }
                
                            $fundingSource['content']['weightedTarget'] = $weightedTarget;
                            $fundingSource['content']['weightedAccomplishment'] = $weightedAccomplishment;
                        }
                    }

                    // second level
                    foreach ($data as $sectorTitle => &$sector) {
                        $weightedTarget = 0;
                        $weightedAccomplishment = 0;
                        $totalCost = $sector['content']['cost'] ?? 0;
                
                        if ($totalCost > 0 && isset($sector['firstLevels'])) {
                            foreach ($sector['firstLevels'] as $fundingSource) {
                                if (isset($fundingSource['content']['cost'], $fundingSource['content']['weightedTarget'])) {
                                    $fundingSourceCost = $fundingSource['content']['cost'];
                                    $fundingSourceTarget = $fundingSource['content']['weightedTarget'];
                                    $weightedTarget += ($fundingSourceCost / $totalCost) * $fundingSourceTarget;
                                }
                
                                if (isset($fundingSource['content']['cost'], $fundingSource['content']['weightedAccomplishment'])) {
                                    $fundingSourceCost = $fundingSource['content']['cost'];
                                    $fundingSourceAccomplishment = $fundingSource['content']['weightedAccomplishment'];
                                    $weightedAccomplishment += ($fundingSourceCost / $totalCost) * $fundingSourceAccomplishment;
                                }
                            }
                        }
                
                        $sector['content']['weightedTarget'] = $weightedTarget;
                        $sector['content']['weightedAccomplishment'] = $weightedAccomplishment;
                    }

                    // grand total
                    $grandTotal = $initialValues;
                    $grandTotal['weightedTarget'] = 0;
                    $grandTotal['weightedAccomplishment'] = 0;

                    // Sum total values from each sector
                    foreach ($data as $sector) {
                        foreach ($initialValues as $key => $value) {
                            if (isset($sector['content'][$key])) {
                                $grandTotal[$key] += $sector['content'][$key];
                            }
                        }
                    }

                    $totalCost = $grandTotal['cost'] ?? 0;

                    // Compute weighted target and accomplishment across all sectors
                    if ($totalCost > 0) {
                        foreach ($data as $sector) {
                            if (isset($sector['content']['cost'], $sector['content']['weightedTarget'])) {
                                $sectorCost = $sector['content']['cost'];
                                $sectorTarget = $sector['content']['weightedTarget'];
                                $grandTotal['weightedTarget'] += ($sectorCost / $totalCost) * $sectorTarget;
                            }

                            if (isset($sector['content']['cost'], $sector['content']['weightedAccomplishment'])) {
                                $sectorCost = $sector['content']['cost'];
                                $sectorAccomplishment = $sector['content']['weightedAccomplishment'];
                                $grandTotal['weightedAccomplishment'] += ($sectorCost / $totalCost) * $sectorAccomplishment;
                            }
                        }
                    }

                    // Store it in $data or separately as needed
                    $data['__grandTotal'] = $grandTotal;
                }
            }

            $bigCaps = range('A', 'Z');
            $smallCaps = range('a', 'z');
            $numbers = range('1', '100');
            $genders = ['M' => 'Male', 'F' => 'Female'];

            //echo "<pre>"; print_r($data); exit;

            return $this->renderAjax('_data', [
                'model' => $model,
                'data' => $data,
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
