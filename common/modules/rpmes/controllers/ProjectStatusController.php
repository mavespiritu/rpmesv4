<?php

namespace common\modules\rpmes\controllers;

use Yii;
use common\models\Region;
use common\models\Province;
use common\models\Citymun;
use common\models\Barangay;
use common\modules\rpmes\models\Project;
use common\modules\rpmes\models\ProjectTarget;
use common\modules\rpmes\models\ProjectSearch;
use common\modules\rpmes\models\FinancialAccomplishment;
use common\modules\rpmes\models\PhysicalAccomplishment;
use common\modules\rpmes\models\PersonEmployedAccomplishment;
use common\modules\rpmes\models\BeneficiariesAccomplishment;
use common\modules\rpmes\models\GroupAccomplishment;
use common\modules\rpmes\models\Accomplishment;
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
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * ProjectStatusController implements the CRUD actions for Project model.
 */
class ProjectStatusController extends Controller
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
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Project models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Submission();
        $model->year = date("Y");
        $model->scenario = 'delayedProjects';

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];

        $years = Project::find()->select(['distinct(year) as year'])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        $agencies = Agency::find()->select(['id', 'code as title']);
        $agencies = Yii::$app->user->can('AgencyUser') ? $agencies->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $agencies;
        $agencies = $agencies->orderBy(['code' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $sectors = Sector::find()->all();
        $sectors = ArrayHelper::map($sectors, 'id', 'title');

        $regions = Region::find()->orderBy(['region_sort' => SORT_ASC])->all();
        $regions = ArrayHelper::map($regions, 'region_c', 'abbreviation');

        $provinces = [];

        if($model->load(Yii::$app->request->post()))
        {
            $projectIDs = Plan::find()->select(['project_id'])->where(['year' => $model->year])->asArray()->all();
            $projectIDs = ArrayHelper::map($projectIDs, 'project_id', 'project_id');

            $financialAccomps = FinancialAccomplishment::find()->where(['year' => $model->year])->createCommand()->getRawSql();

            $physicalAccomps = PhysicalAccomplishment::find()->where(['year' => $model->year])->createCommand()->getRawSql();
            $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $model->year])->createCommand()->getRawSql();
            $financials = ProjectTarget::find()->where(['target_type' => 'Financial', 'year' => $model['year']])->createCommand()->getRawSql();

            $accomps = Accomplishment::find()->select(['project_id', 'IF(sum(COALESCE(action, 0)) > 0, 1, 0) as isCompleted'])->where(['year' => $model->year])->groupBy(['project_id'])->createCommand()->getRawSql();
        
            $financialTotal = 'IF(project.data_type = "Cumulative",
                                IF(COALESCE(financials.q4, 0) <= 0,
                                    IF(COALESCE(financials.q3, 0) <= 0,
                                        IF(COALESCE(financials.q2, 0) <= 0,
                                             COALESCE(financials.q1, 0)
                                                , COALESCE(financials.q2, 0)
                                                )
                                            , COALESCE(financials.q3, 0)
                                            )
                                        , COALESCE(financials.q4, 0))
                                    ,   
                                    COALESCE(financials.q1, 0) +
                                    COALESCE(financials.q2, 0) +
                                    COALESCE(financials.q3, 0) +
                                    COALESCE(financials.q4, 0)
                                    )';
                
            $releases = 'IF(project.data_type <> "Cumulative",
                            IF("'.$model->quarter.'" = "Q1", COALESCE(financialAccompsQ1.releases, 0),
                                IF("'.$model->quarter.'" = "Q2", COALESCE(financialAccompsQ1.releases, 0) + COALESCE(financialAccompsQ2.releases, 0),
                                    IF("'.$model->quarter.'" = "Q3", COALESCE(financialAccompsQ1.releases, 0) + COALESCE(financialAccompsQ2.releases, 0) + COALESCE(financialAccompsQ3.releases, 0),
                                    COALESCE(financialAccompsQ1.releases, 0) + COALESCE(financialAccompsQ2.releases, 0) + COALESCE(financialAccompsQ3.releases, 0) + COALESCE(financialAccompsQ4.releases, 0)
                                    )
                                )
                            )
                        ,   
                            IF("'.$model->quarter.'" = "Q1", COALESCE(financialAccompsQ1.releases, 0),
                                IF("'.$model->quarter.'" = "Q2", COALESCE(financialAccompsQ2.releases, 0),
                                    IF("'.$model->quarter.'" = "Q3", COALESCE(financialAccompsQ3.releases, 0),
                                    COALESCE(financialAccompsQ4.releases, 0)
                                    )
                                )
                            )
                        )';

            $expenditures = 'IF(project.data_type <> "Cumulative",
                                IF("'.$model->quarter.'" = "Q1", COALESCE(financialAccompsQ1.expenditures, 0),
                                    IF("'.$model->quarter.'" = "Q2", COALESCE(financialAccompsQ1.expenditures, 0) + COALESCE(financialAccompsQ2.expenditures, 0),
                                        IF("'.$model->quarter.'" = "Q3", COALESCE(financialAccompsQ1.expenditures, 0) + COALESCE(financialAccompsQ2.expenditures, 0) + COALESCE(financialAccompsQ3.expenditures, 0),
                                        COALESCE(financialAccompsQ1.expenditures, 0) + COALESCE(financialAccompsQ2.expenditures, 0) + COALESCE(financialAccompsQ3.expenditures, 0) + COALESCE(financialAccompsQ4.expenditures, 0)
                                        )
                                    )
                                )
                            ,   
                                IF("'.$model->quarter.'" = "Q1", COALESCE(financialAccompsQ1.expenditures, 0),
                                    IF("'.$model->quarter.'" = "Q2", COALESCE(financialAccompsQ2.expenditures, 0),
                                        IF("'.$model->quarter.'" = "Q3", COALESCE(financialAccompsQ3.expenditures, 0),
                                        COALESCE(financialAccompsQ4.expenditures, 0)
                                        )
                                    )
                                )
                            )';

            $physicalAccompTotalPerQuarter = 'IF(project.data_type = "Default",
                                                IF("'.$model->quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                                    IF("'.$model->quarter.'" = "Q2", COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0),
                                                        IF("'.$model->quarter.'" = "Q3", COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0),
                                                        COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0) + COALESCE(physicalAccompsQ4.value, 0)
                                                        )
                                                    )
                                                )
                                            ,   
                                                IF("'.$model->quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                                    IF("'.$model->quarter.'" = "Q2", COALESCE(physicalAccompsQ2.value, 0),
                                                        IF("'.$model->quarter.'" = "Q3", COALESCE(physicalAccompsQ3.value, 0),
                                                        COALESCE(physicalAccompsQ4.value, 0)
                                                        )
                                                    )
                                                )
                                            )';

            $physicalTargetTotalPerQuarter = 'IF(project.data_type = "Default",
                                                IF("'.$model->quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                                                    IF("'.$model->quarter.'" = "Q2", COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0),
                                                        IF("'.$model->quarter.'" = "Q3", COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0),
                                                        COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0)
                                                        )
                                                    )
                                                )
                                            ,   
                                                IF("'.$model->quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                                                    IF("'.$model->quarter.'" = "Q2", COALESCE(physicalTargets.q2, 0),
                                                        IF("'.$model->quarter.'" = "Q3", COALESCE(physicalTargets.q3, 0),
                                                        COALESCE(physicalTargets.q4, 0)
                                                        )
                                                    )
                                                )
                                            )';

            $physicalTargetPerQuarter = 'IF("'.$model->quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                                            IF("'.$model->quarter.'" = "Q2", COALESCE(physicalTargets.q2, 0),
                                                IF("'.$model->quarter.'" = "Q3", COALESCE(physicalTargets.q3, 0),
                                                COALESCE(physicalTargets.q4, 0)
                                                )
                                            )
                                        )';

            $physicalAccompPerQuarter = 'IF("'.$model->quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                        IF("'.$model->quarter.'" = "Q2", COALESCE(physicalAccompsQ2.value, 0),
                                            IF("'.$model->quarter.'" = "Q3", COALESCE(physicalAccompsQ3.value, 0),
                                            COALESCE(physicalAccompsQ4.value, 0)
                                            )
                                        )
                                    )';

            $isPercent = 'LOCATE("%", physicalTargets.indicator)';
            $isCompleted = 'COALESCE(accomps.isCompleted, 0)';
            $slippage = 'IF('.$isPercent.' > 0, '.$physicalAccompPerQuarter.' - '.$physicalTargetPerQuarter.', IF('.$physicalTargetPerQuarter.' > 0, (('.$physicalAccompPerQuarter.'/'.$physicalTargetPerQuarter.') * 100) -100 , 0))';

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
                ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat(tblcitymun.citymun_m,",",tblprovince.province_m) ORDER BY tblcitymun.citymun_m ASC, tblprovince.province_m ASC SEPARATOR ", ") as title'])
                ->leftJoin('tblcitymun', 'tblcitymun.province_c = project_citymun.province_id and tblcitymun.citymun_c = project_citymun.citymun_id')
                ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
                ->leftJoin('project', 'project.id = project_citymun.project_id')
                ->where(['project.draft' => 'No'])
                ->groupBy(['project_citymun.project_id'])
                ->createCommand()->getRawSql();
            
            $barangayTitles = ProjectBarangay::find()
                ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat(tblbarangay.barangay_m,",",tblcitymun.citymun_m,",",tblprovince.province_m) ORDER BY tblbarangay.barangay_m ASC, tblcitymun.citymun_m ASC, tblprovince.province_m ASC SEPARATOR ", ") as title'])
                ->leftJoin('tblbarangay', 'tblbarangay.province_c = project_barangay.province_id and tblbarangay.citymun_c = project_barangay.citymun_id and tblbarangay.barangay_c = project_barangay.barangay_id')
                ->leftJoin('tblcitymun', 'tblcitymun.province_c = project_barangay.province_id and tblcitymun.citymun_c = project_barangay.citymun_id')
                ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
                ->leftJoin('project', 'project.id = project_barangay.project_id')
                ->where(['project.draft' => 'No'])
                ->groupBy(['project_barangay.project_id'])
                ->createCommand()->getRawSql();

            $projects = Project::find()
                        ->select([
                            'project.id',
                            'project.title as projectTitle',
                            'project.data_type as dataType',
                            'sector.title as sectorTitle',
                            'sub_sector.title as subSectorTitle',
                            'IF(barangayTitles.title is null, IF(citymunTitles.title is null, IF(provinceTitles.title is null, IF(regionTitles.title is null, "No location", regionTitles.title), provinceTitles.title), citymunTitles.title), barangayTitles.title) as locationTitle',
                            'agency.code as agencyTitle',
                            'fund_source.title as fundSourceTitle',
                            'COALESCE('.$financialTotal.', 0) as totalCost',
                            $releases.'as releases',
                            $expenditures.'as expenditures',
                            $physicalTargetPerQuarter. 'as physicalTargetTotalPerQuarter',
                            $physicalAccompPerQuarter. 'as physicalAccompTotalPerQuarter',
                            $slippage. 'as slippage',
                            'regionTitles.title as regionTitles',
                            'accomps.isCompleted as isCompleted',
                            'project_exception.recommendations as recommendations',
                            'project_exception.causes as causes',                
                        ]);
            $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
            $projects = $projects->leftJoin('program', 'program.id = project.program_id');
            $projects = $projects->leftJoin('sector', 'sector.id = project.sector_id');
            $projects = $projects->leftJoin('sub_sector', 'sub_sector.id = project.sub_sector_id');
            $projects = $projects->leftJoin('fund_source', 'fund_source.id = project.fund_source_id');
            $projects = $projects->leftJoin('project_exception', 'project_exception.project_id = project.id');
            $projects = $projects->leftJoin('project_region', 'project_region.project_id = project.id and project_region.year = project.year');
            $projects = $projects->leftJoin('tblregion', 'tblregion.region_c = project_region.region_id');
            $projects = $projects->leftJoin('project_province', 'project_province.project_id = project.id and project_province.year = project.year');
            $projects = $projects->leftJoin('tblprovince', 'tblprovince.province_c = project_province.province_id');
            $projects = $projects->leftJoin(['financialAccompsQ1' => '('.$financialAccomps.')'], 'financialAccompsQ1.project_id = project.id and financialAccompsQ1.quarter = "Q1"');
            $projects = $projects->leftJoin(['financialAccompsQ2' => '('.$financialAccomps.')'], 'financialAccompsQ2.project_id = project.id and financialAccompsQ2.quarter = "Q2"');
            $projects = $projects->leftJoin(['financialAccompsQ3' => '('.$financialAccomps.')'], 'financialAccompsQ3.project_id = project.id and financialAccompsQ3.quarter = "Q3"');
            $projects = $projects->leftJoin(['financialAccompsQ4' => '('.$financialAccomps.')'], 'financialAccompsQ4.project_id = project.id and financialAccompsQ4.quarter = "Q4"');
            $projects = $projects->leftJoin(['physicalAccompsQ1' => '('.$physicalAccomps.')'], 'physicalAccompsQ1.project_id = project.id and physicalAccompsQ1.quarter = "Q1"');
            $projects = $projects->leftJoin(['physicalAccompsQ2' => '('.$physicalAccomps.')'], 'physicalAccompsQ2.project_id = project.id and physicalAccompsQ2.quarter = "Q2"');
            $projects = $projects->leftJoin(['physicalAccompsQ3' => '('.$physicalAccomps.')'], 'physicalAccompsQ3.project_id = project.id and physicalAccompsQ3.quarter = "Q3"');
            $projects = $projects->leftJoin(['physicalAccompsQ4' => '('.$physicalAccomps.')'], 'physicalAccompsQ4.project_id = project.id and physicalAccompsQ4.quarter = "Q4"');
            $projects = $projects->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');
            $projects = $projects->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
            $projects = $projects->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id');
            $projects = $projects->leftJoin(['financials' => '('.$financials.')'], 'financials.project_id = project.id');
            $projects = $projects->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
            $projects = $projects->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id');
            $projects = $projects->leftJoin(['barangayTitles' => '('.$barangayTitles.')'], 'barangayTitles.project_id = project.id');
            $projects = $projects->andWhere(['project.year' => $model->year, 'project.draft' => 'No']);
            $projects = $projects->andWhere(['project.id' => $projectIDs]);

            if($model->agency_id != '')
            {
                $projects = $projects->andWhere(['agency.id' => $model->agency_id]);
            }

            if($model->sector_id != '')
            {
                $projects = $projects->andWhere(['sector.id' => $model->sector_id]);
            }
                
            if($model->region_id != '')
            {
                $projects = $projects->andWhere(['tblregion.region_c' => $model->region_id]);
            }

            if($model->province_id != '')
            {
                $projects = $projects->andWhere(['tblprovince.province_c' => $model->province_id]);
            }

            $projects = $projects->asArray()->all();

            //echo '<pre>'; print_r($projects); exit;

            return $this->renderAjax('_form', [
                'model' => $model,
                'projects' => $projects
            ]);

        }
        return $this->render('index', [
            'model' => $model,
            'quarters' => $quarters,
            'years' => $years,
            'agencies' => $agencies,
            'sectors' => $sectors,
            'regions' => $regions,
            'provinces' => $provinces,
        ]);
    }

    public function actionPrintFormSix($year,$quarter,$agency_id,$sector_id,$region_id,$province_id)
    {
        $model = [];
        $model['year'] = $year;
        $model['quarter'] = $quarter;
        $model['agency_id'] = $agency_id;
        $model['sector_id'] = $sector_id;
        $model['region_id'] = $region_id;
        $model['province_id'] = $province_id;

        $projectIDs = Plan::find()->select(['project_id'])->where(['year' => $model['year']])->asArray()->all();
        $projectIDs = ArrayHelper::map($projectIDs, 'project_id', 'project_id');

        $financialAccomps = FinancialAccomplishment::find()->where(['year' => $model['year']])->createCommand()->getRawSql();

        $physicalAccomps = PhysicalAccomplishment::find()->where(['year' => $model['year']])->createCommand()->getRawSql();
        $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $model['year']])->createCommand()->getRawSql();
        $financials = ProjectTarget::find()->where(['target_type' => 'Financial', 'year' => $model['year']])->createCommand()->getRawSql();

        $accomps = Accomplishment::find()->select(['project_id', 'IF(sum(COALESCE(action, 0)) > 0, 1, 0) as isCompleted'])->where(['year' => $model['year']])->groupBy(['project_id'])->createCommand()->getRawSql();
        
        $financialTotal = 'IF(project.data_type = "Cumulative",
                            IF(COALESCE(financials.q4, 0) <= 0,
                                IF(COALESCE(financials.q3, 0) <= 0,
                                    IF(COALESCE(financials.q2, 0) <= 0,
                                         COALESCE(financials.q1, 0)
                                            , COALESCE(financials.q2, 0)
                                            )
                                        , COALESCE(financials.q3, 0)
                                        )
                                    , COALESCE(financials.q4, 0))
                                ,   
                                COALESCE(financials.q1, 0) +
                                COALESCE(financials.q2, 0) +
                                COALESCE(financials.q3, 0) +
                                COALESCE(financials.q4, 0)
                                )';
                
        $releases = 'IF(project.data_type <> "Cumulative",
                        IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.releases, 0),
                            IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ1.releases, 0) + COALESCE(financialAccompsQ2.releases, 0),
                                IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ1.releases, 0) + COALESCE(financialAccompsQ2.releases, 0) + COALESCE(financialAccompsQ3.releases, 0),
                                COALESCE(financialAccompsQ1.releases, 0) + COALESCE(financialAccompsQ2.releases, 0) + COALESCE(financialAccompsQ3.releases, 0) + COALESCE(financialAccompsQ4.releases, 0)
                                )
                            )
                        )
                    ,   
                        IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.releases, 0),
                            IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ2.releases, 0),
                                IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ3.releases, 0),
                                COALESCE(financialAccompsQ4.releases, 0)
                                )
                            )
                        )
                    )';

        $expenditures = 'IF(project.data_type <> "Cumulative",
                            IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.expenditures, 0),
                                IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ1.expenditures, 0) + COALESCE(financialAccompsQ2.expenditures, 0),
                                    IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ1.expenditures, 0) + COALESCE(financialAccompsQ2.expenditures, 0) + COALESCE(financialAccompsQ3.expenditures, 0),
                                    COALESCE(financialAccompsQ1.expenditures, 0) + COALESCE(financialAccompsQ2.expenditures, 0) + COALESCE(financialAccompsQ3.expenditures, 0) + COALESCE(financialAccompsQ4.expenditures, 0)
                                    )
                                )
                            )
                        ,   
                            IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.expenditures, 0),
                                IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ2.expenditures, 0),
                                    IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ3.expenditures, 0),
                                    COALESCE(financialAccompsQ4.expenditures, 0)
                                    )
                                )
                            )
                        )';

        $physicalAccompTotalPerQuarter = 'IF(project.data_type = "Default",
                                            IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                                IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0),
                                                    IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0),
                                                    COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0) + COALESCE(physicalAccompsQ4.value, 0)
                                                    )
                                                )
                                            )
                                        ,   
                                            IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                                IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalAccompsQ2.value, 0),
                                                    IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalAccompsQ3.value, 0),
                                                    COALESCE(physicalAccompsQ4.value, 0)
                                                    )
                                                )
                                            )
                                        )';

        $physicalTargetTotalPerQuarter = 'IF(project.data_type = "Default",
                                            IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalTargets.q1, 0),
                                                IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0),
                                                    IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0),
                                                    COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0)
                                                    )
                                                )
                                            )
                                        ,   
                                            IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalTargets.q1, 0),
                                                IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalTargets.q2, 0),
                                                    IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalTargets.q3, 0),
                                                    COALESCE(physicalTargets.q4, 0)
                                                    )
                                                )
                                            )
                                        )';

        $physicalTargetPerQuarter = 'IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalTargets.q1, 0),
                                        IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalTargets.q2, 0),
                                            IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalTargets.q3, 0),
                                            COALESCE(physicalTargets.q4, 0)
                                            )
                                        )
                                    )';

        $physicalAccompPerQuarter = 'IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                    IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalAccompsQ2.value, 0),
                                        IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalAccompsQ3.value, 0),
                                        COALESCE(physicalAccompsQ4.value, 0)
                                        )
                                    )
                                )';

        $isPercent = 'LOCATE("%", physicalTargets.indicator)';
        $isCompleted = 'COALESCE(accomps.isCompleted, 0)';
        $slippage = 'IF('.$isPercent.' > 0, '.$physicalAccompPerQuarter.' - '.$physicalTargetPerQuarter.', IF('.$physicalTargetPerQuarter.' > 0, (('.$physicalAccompPerQuarter.'/'.$physicalTargetPerQuarter.') * 100) -100 , 0))';
        
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
            ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat(tblcitymun.citymun_m,",",tblprovince.province_m) ORDER BY tblcitymun.citymun_m ASC, tblprovince.province_m ASC SEPARATOR ", ") as title'])
            ->leftJoin('tblcitymun', 'tblcitymun.province_c = project_citymun.province_id and tblcitymun.citymun_c = project_citymun.citymun_id')
            ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
            ->leftJoin('project', 'project.id = project_citymun.project_id')
            ->where(['project.draft' => 'No'])
            ->groupBy(['project_citymun.project_id'])
            ->createCommand()->getRawSql();
            
        $barangayTitles = ProjectBarangay::find()
            ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat(tblbarangay.barangay_m,",",tblcitymun.citymun_m,",",tblprovince.province_m) ORDER BY tblbarangay.barangay_m ASC, tblcitymun.citymun_m ASC, tblprovince.province_m ASC SEPARATOR ", ") as title'])
            ->leftJoin('tblbarangay', 'tblbarangay.province_c = project_barangay.province_id and tblbarangay.citymun_c = project_barangay.citymun_id and tblbarangay.barangay_c = project_barangay.barangay_id')
            ->leftJoin('tblcitymun', 'tblcitymun.province_c = project_barangay.province_id and tblcitymun.citymun_c = project_barangay.citymun_id')
            ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
            ->leftJoin('project', 'project.id = project_barangay.project_id')
            ->where(['project.draft' => 'No'])
            ->groupBy(['project_barangay.project_id'])
            ->createCommand()->getRawSql();

        $projects = Project::find()
                    ->select([
                        'project.id',
                        'project.title as projectTitle',
                        'project.data_type as dataType',
                        'sector.title as sectorTitle',
                        'sub_sector.title as subSectorTitle',
                        'IF(barangayTitles.title is null, IF(citymunTitles.title is null, IF(provinceTitles.title is null, IF(regionTitles.title is null, "No location", regionTitles.title), provinceTitles.title), citymunTitles.title), barangayTitles.title) as locationTitle',
                        'agency.code as agencyTitle',
                        'fund_source.title as fundSourceTitle',
                        'COALESCE('.$financialTotal.', 0) as totalCost',
                        $releases.'as releases',
                        $expenditures.'as expenditures',
                        $physicalTargetPerQuarter. 'as physicalTargetTotalPerQuarter',
                        $physicalAccompPerQuarter. 'as physicalAccompTotalPerQuarter',
                        $slippage. 'as slippage',
                        'accomps.isCompleted as isCompleted',
                        'project_exception.recommendations as recommendations',
                        'project_exception.causes as causes',                
                    ]);
        $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
        $projects = $projects->leftJoin('program', 'program.id = project.program_id');
        $projects = $projects->leftJoin('sector', 'sector.id = project.sector_id');
        $projects = $projects->leftJoin('sub_sector', 'sub_sector.id = project.sub_sector_id');
        $projects = $projects->leftJoin('fund_source', 'fund_source.id = project.fund_source_id');
        $projects = $projects->leftJoin('project_exception', 'project_exception.project_id = project.id');
        $projects = $projects->leftJoin(['financialAccompsQ1' => '('.$financialAccomps.')'], 'financialAccompsQ1.project_id = project.id and financialAccompsQ1.quarter = "Q1"');
        $projects = $projects->leftJoin(['financialAccompsQ2' => '('.$financialAccomps.')'], 'financialAccompsQ2.project_id = project.id and financialAccompsQ2.quarter = "Q2"');
        $projects = $projects->leftJoin(['financialAccompsQ3' => '('.$financialAccomps.')'], 'financialAccompsQ3.project_id = project.id and financialAccompsQ3.quarter = "Q3"');
        $projects = $projects->leftJoin(['financialAccompsQ4' => '('.$financialAccomps.')'], 'financialAccompsQ4.project_id = project.id and financialAccompsQ4.quarter = "Q4"');
        $projects = $projects->leftJoin(['physicalAccompsQ1' => '('.$physicalAccomps.')'], 'physicalAccompsQ1.project_id = project.id and physicalAccompsQ1.quarter = "Q1"');
        $projects = $projects->leftJoin(['physicalAccompsQ2' => '('.$physicalAccomps.')'], 'physicalAccompsQ2.project_id = project.id and physicalAccompsQ2.quarter = "Q2"');
        $projects = $projects->leftJoin(['physicalAccompsQ3' => '('.$physicalAccomps.')'], 'physicalAccompsQ3.project_id = project.id and physicalAccompsQ3.quarter = "Q3"');
        $projects = $projects->leftJoin(['physicalAccompsQ4' => '('.$physicalAccomps.')'], 'physicalAccompsQ4.project_id = project.id and physicalAccompsQ4.quarter = "Q4"');
        $projects = $projects->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');
        $projects = $projects->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
        $projects = $projects->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id');
        $projects = $projects->leftJoin(['financials' => '('.$financials.')'], 'financials.project_id = project.id');
        $projects = $projects->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
        $projects = $projects->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id');
        $projects = $projects->leftJoin(['barangayTitles' => '('.$barangayTitles.')'], 'barangayTitles.project_id = project.id');
        $projects = $projects->andWhere(['project.year' => $model['year'], 'project.draft' => 'No']);
        $projects = $projects->andWhere(['project.id' => $projectIDs]);

        if($model['agency_id'] != '')
        {
            $projects = $projects->andWhere(['agency.id' => $model['agency_id']]);
        }
        if($model['sector_id'] != '')
        {
            $projects = $projects->andWhere(['sector.id' => $model['sector_id']]);
        }
        if($model['region_id'] != '')
        {
            $regionIDs = $regionIDs->andWhere(['region_id' => $model['region_id']]);
        }
        if($model['province_id'] != '')
        {
            $provinceIDs = $provinceIDs->andWhere(['province_id' => $model['province_id']]);
        }

        $projects = $projects->asArray()->all();

        //echo '<pre>'; print_r($projects); exit;

        return $this->renderAjax('form-six', [
            'model' => $model,
            'type' => 'print',
            'projects' => $projects
        ]);
    }

    public function actionDownloadFormSix($type, $year, $quarter, $agency_id, $sector_id, $region_id, $province_id, $model)
    {
        $model = json_decode($model, true); 
        $model['year'] = $year;
        $model['quarter'] = $quarter;
        $model['agency_id'] = $agency_id;
        $model['sector_id'] = $sector_id;
        $model['region_id'] = $region_id;
        $model['province_id'] = $province_id;


        $projectIDs = Plan::find()->select(['project_id'])->where(['year' => $model['year']])->asArray()->all();
        $projectIDs = ArrayHelper::map($projectIDs, 'project_id', 'project_id');

        $financialAccomps = FinancialAccomplishment::find()->where(['year' => $model['year']])->createCommand()->getRawSql();

        $physicalAccomps = PhysicalAccomplishment::find()->where(['year' => $model['year']])->createCommand()->getRawSql();
        $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $model['year']])->createCommand()->getRawSql();
        $financials = ProjectTarget::find()->where(['target_type' => 'Financial', 'year' => $model['year']])->createCommand()->getRawSql();

        $accomps = Accomplishment::find()->select(['project_id', 'IF(sum(COALESCE(action, 0)) > 0, 1, 0) as isCompleted'])->where(['year' => $model['year']])->groupBy(['project_id'])->createCommand()->getRawSql();
        
        $financialTotal = 'IF(project.data_type = "Cumulative",
                            IF(COALESCE(financials.q4, 0) <= 0,
                                IF(COALESCE(financials.q3, 0) <= 0,
                                    IF(COALESCE(financials.q2, 0) <= 0,
                                         COALESCE(financials.q1, 0)
                                            , COALESCE(financials.q2, 0)
                                            )
                                        , COALESCE(financials.q3, 0)
                                        )
                                    , COALESCE(financials.q4, 0))
                                ,   
                                COALESCE(financials.q1, 0) +
                                COALESCE(financials.q2, 0) +
                                COALESCE(financials.q3, 0) +
                                COALESCE(financials.q4, 0)
                                )';
                
        $releases = 'IF(project.data_type <> "Cumulative",
                        IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.releases, 0),
                            IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ1.releases, 0) + COALESCE(financialAccompsQ2.releases, 0),
                                IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ1.releases, 0) + COALESCE(financialAccompsQ2.releases, 0) + COALESCE(financialAccompsQ3.releases, 0),
                                COALESCE(financialAccompsQ1.releases, 0) + COALESCE(financialAccompsQ2.releases, 0) + COALESCE(financialAccompsQ3.releases, 0) + COALESCE(financialAccompsQ4.releases, 0)
                                )
                            )
                        )
                    ,   
                        IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.releases, 0),
                            IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ2.releases, 0),
                                IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ3.releases, 0),
                                COALESCE(financialAccompsQ4.releases, 0)
                                )
                            )
                        )
                    )';

        $expenditures = 'IF(project.data_type <> "Cumulative",
                            IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.expenditures, 0),
                                IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ1.expenditures, 0) + COALESCE(financialAccompsQ2.expenditures, 0),
                                    IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ1.expenditures, 0) + COALESCE(financialAccompsQ2.expenditures, 0) + COALESCE(financialAccompsQ3.expenditures, 0),
                                    COALESCE(financialAccompsQ1.expenditures, 0) + COALESCE(financialAccompsQ2.expenditures, 0) + COALESCE(financialAccompsQ3.expenditures, 0) + COALESCE(financialAccompsQ4.expenditures, 0)
                                    )
                                )
                            )
                        ,   
                            IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.expenditures, 0),
                                IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ2.expenditures, 0),
                                    IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ3.expenditures, 0),
                                    COALESCE(financialAccompsQ4.expenditures, 0)
                                    )
                                )
                            )
                        )';

        $physicalAccompTotalPerQuarter = 'IF(project.data_type = "Default",
                                            IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                                IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0),
                                                    IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0),
                                                    COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0) + COALESCE(physicalAccompsQ4.value, 0)
                                                    )
                                                )
                                            )
                                        ,   
                                            IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                                IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalAccompsQ2.value, 0),
                                                    IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalAccompsQ3.value, 0),
                                                    COALESCE(physicalAccompsQ4.value, 0)
                                                    )
                                                )
                                            )
                                        )';

        $physicalTargetTotalPerQuarter = 'IF(project.data_type = "Default",
                                            IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalTargets.q1, 0),
                                                IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0),
                                                    IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0),
                                                    COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0)
                                                    )
                                                )
                                            )
                                        ,   
                                            IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalTargets.q1, 0),
                                                IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalTargets.q2, 0),
                                                    IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalTargets.q3, 0),
                                                    COALESCE(physicalTargets.q4, 0)
                                                    )
                                                )
                                            )
                                        )';

        $physicalTargetPerQuarter = 'IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalTargets.q1, 0),
                                        IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalTargets.q2, 0),
                                            IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalTargets.q3, 0),
                                            COALESCE(physicalTargets.q4, 0)
                                            )
                                        )
                                    )';

        $physicalAccompPerQuarter = 'IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                    IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalAccompsQ2.value, 0),
                                        IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalAccompsQ3.value, 0),
                                        COALESCE(physicalAccompsQ4.value, 0)
                                        )
                                    )
                                )';

        $isPercent = 'LOCATE("%", physicalTargets.indicator)';
        $isCompleted = 'COALESCE(accomps.isCompleted, 0)';
        $slippage = 'IF('.$isPercent.' > 0, '.$physicalAccompPerQuarter.' - '.$physicalTargetPerQuarter.', IF('.$physicalTargetPerQuarter.' > 0, (('.$physicalAccompPerQuarter.'/'.$physicalTargetPerQuarter.') * 100) -100 , 0))';
        
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
            ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat(tblcitymun.citymun_m,",",tblprovince.province_m) ORDER BY tblcitymun.citymun_m ASC, tblprovince.province_m ASC SEPARATOR ", ") as title'])
            ->leftJoin('tblcitymun', 'tblcitymun.province_c = project_citymun.province_id and tblcitymun.citymun_c = project_citymun.citymun_id')
            ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
            ->leftJoin('project', 'project.id = project_citymun.project_id')
            ->where(['project.draft' => 'No'])
            ->groupBy(['project_citymun.project_id'])
            ->createCommand()->getRawSql();
            
        $barangayTitles = ProjectBarangay::find()
            ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat(tblbarangay.barangay_m,",",tblcitymun.citymun_m,",",tblprovince.province_m) ORDER BY tblbarangay.barangay_m ASC, tblcitymun.citymun_m ASC, tblprovince.province_m ASC SEPARATOR ", ") as title'])
            ->leftJoin('tblbarangay', 'tblbarangay.province_c = project_barangay.province_id and tblbarangay.citymun_c = project_barangay.citymun_id and tblbarangay.barangay_c = project_barangay.barangay_id')
            ->leftJoin('tblcitymun', 'tblcitymun.province_c = project_barangay.province_id and tblcitymun.citymun_c = project_barangay.citymun_id')
            ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
            ->leftJoin('project', 'project.id = project_barangay.project_id')
            ->where(['project.draft' => 'No'])
            ->groupBy(['project_barangay.project_id'])
            ->createCommand()->getRawSql();

        $projects = Project::find()
                    ->select([
                        'project.id',
                        'project.title as projectTitle',
                        'project.data_type as dataType',
                        'sector.title as sectorTitle',
                        'sub_sector.title as subSectorTitle',
                        'IF(barangayTitles.title is null, IF(citymunTitles.title is null, IF(provinceTitles.title is null, IF(regionTitles.title is null, "No location", regionTitles.title), provinceTitles.title), citymunTitles.title), barangayTitles.title) as locationTitle',
                        'agency.code as agencyTitle',
                        'fund_source.title as fundSourceTitle',
                        'COALESCE('.$financialTotal.', 0) as totalCost',
                        $releases.'as releases',
                        $expenditures.'as expenditures',
                        $physicalTargetPerQuarter. 'as physicalTargetTotalPerQuarter',
                        $physicalAccompPerQuarter. 'as physicalAccompTotalPerQuarter',
                        $slippage. 'as slippage',
                        'accomps.isCompleted as isCompleted',
                        'project_exception.recommendations as recommendations',
                        'project_exception.causes as causes',                
                    ]);
        $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
        $projects = $projects->leftJoin('program', 'program.id = project.program_id');
        $projects = $projects->leftJoin('sector', 'sector.id = project.sector_id');
        $projects = $projects->leftJoin('sub_sector', 'sub_sector.id = project.sub_sector_id');
        $projects = $projects->leftJoin('fund_source', 'fund_source.id = project.fund_source_id');
        $projects = $projects->leftJoin('project_exception', 'project_exception.project_id = project.id');
        $projects = $projects->leftJoin(['financialAccompsQ1' => '('.$financialAccomps.')'], 'financialAccompsQ1.project_id = project.id and financialAccompsQ1.quarter = "Q1"');
        $projects = $projects->leftJoin(['financialAccompsQ2' => '('.$financialAccomps.')'], 'financialAccompsQ2.project_id = project.id and financialAccompsQ2.quarter = "Q2"');
        $projects = $projects->leftJoin(['financialAccompsQ3' => '('.$financialAccomps.')'], 'financialAccompsQ3.project_id = project.id and financialAccompsQ3.quarter = "Q3"');
        $projects = $projects->leftJoin(['financialAccompsQ4' => '('.$financialAccomps.')'], 'financialAccompsQ4.project_id = project.id and financialAccompsQ4.quarter = "Q4"');
        $projects = $projects->leftJoin(['physicalAccompsQ1' => '('.$physicalAccomps.')'], 'physicalAccompsQ1.project_id = project.id and physicalAccompsQ1.quarter = "Q1"');
        $projects = $projects->leftJoin(['physicalAccompsQ2' => '('.$physicalAccomps.')'], 'physicalAccompsQ2.project_id = project.id and physicalAccompsQ2.quarter = "Q2"');
        $projects = $projects->leftJoin(['physicalAccompsQ3' => '('.$physicalAccomps.')'], 'physicalAccompsQ3.project_id = project.id and physicalAccompsQ3.quarter = "Q3"');
        $projects = $projects->leftJoin(['physicalAccompsQ4' => '('.$physicalAccomps.')'], 'physicalAccompsQ4.project_id = project.id and physicalAccompsQ4.quarter = "Q4"');
        $projects = $projects->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');
        $projects = $projects->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
        $projects = $projects->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id');
        $projects = $projects->leftJoin(['financials' => '('.$financials.')'], 'financials.project_id = project.id');
        $projects = $projects->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
        $projects = $projects->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id');
        $projects = $projects->leftJoin(['barangayTitles' => '('.$barangayTitles.')'], 'barangayTitles.project_id = project.id');
        $projects = $projects->andWhere(['project.year' => $model['year'], 'project.draft' => 'No']);
        $projects = $projects->andWhere(['project.id' => $projectIDs]);

        if($model['agency_id'] != '')
        {
            $projects = $projects->andWhere(['agency.id' => $model['agency_id']]);
        }
        if($model['sector_id'] != '')
        {
            $projects = $projects->andWhere(['sector.id' => $model['sector_id']]);
        }
        if($model['region_id'] != '')
        {
            $regionIDs = $regionIDs->andWhere(['region_id' => $model['region_id']]);
        }
        if($model['province_id'] != '')
        {
            $provinceIDs = $provinceIDs->andWhere(['province_id' => $model['province_id']]);
        }

        $projects = $projects->orderBy(['project.title' => SORT_ASC])->asArray()->all();

        $filename = 'RPMES Form 6: Reports on the Status of Projects Encountering Implementation Problems';

        if($type == 'excel')
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=".$filename.".xls");
            return $this->renderPartial('form-six', [
                'model' => $model,
                'type' => $type,
                'projects' => $projects,
            ]);
        }else if($type == 'pdf')
        {
            $content = $this->renderPartial('form-six', [
                'model' => $model,
                'type' => $type,
                'projects' => $projects,
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
                'cssInline' => '*{font-family: "Arial";}
                                table{
                                    font-family: "Arial";
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
                                }
                                h1,h2,h3,h4,h5,h6{
                                    text-align: center;
                                    font-weight: bolder;
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
