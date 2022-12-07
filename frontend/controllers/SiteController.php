<?php
namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\helpers\Json;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use common\modules\rpmes\models\Plan;
use common\modules\rpmes\models\ProjectRegion;
use common\modules\rpmes\models\ProjectProvince;
use common\modules\rpmes\models\Project;
use common\modules\rpmes\models\Submission;
use common\modules\rpmes\models\Agency;
use common\modules\rpmes\models\Category;
use common\modules\rpmes\models\Sector;
use common\modules\rpmes\models\SubSector;
use common\modules\rpmes\models\FundSource;
use common\modules\rpmes\models\ProjectCategory;
use common\modules\rpmes\models\Accomplishment;
use common\modules\rpmes\models\ProjectTarget;
use common\modules\rpmes\models\PhysicalAccomplishment;
use common\modules\rpmes\models\FinancialAccomplishment;
use common\models\Region;
use common\models\Province;
use common\models\Citymun;
use common\modules\rpmes\models\ProjectCitymun;
use yii\helpers\ArrayHelper;

/**
 * Site controller
 */
class SiteController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Submission();

        $model->year = date("Y");
        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];

        //$status = ['C' => 'Completed', 'O' => 'On-going', 'N' => 'Not Yet Started'];

        $years = Project::find()->select(['distinct(year) as year'])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        $agencies = Agency::find()->select(['id', 'code as title']);
        $agencies = Yii::$app->user->can('AgencyUser') ? $agencies->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $agencies;
        $agencies = $agencies->orderBy(['code' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $categories = Category::find()->all();
        $categories = ArrayHelper::map($categories, 'id', 'title');

        $sectors = Sector::find()->all();
        $sectors = ArrayHelper::map($sectors, 'id', 'title');

        $subSectors = [];

        $fundSources = FundSource::find()->select(['id', 'concat(title," (",code,")") as title'])->asArray()->all();
        $fundSources = ArrayHelper::map($fundSources, 'id', 'title');

        $provinces = Province::find()->where(['region_c' => 01])->orderBy(['province_m' => SORT_ASC])->all();
        $provinces = ArrayHelper::map($provinces, 'province_c', 'province_m');

        $fundSources = FundSource::find()->select(['id', 'concat(title," (",code,")") as title'])->orderBy(['title' => SORT_ASC])->asArray()->all();
        $fundSources = ArrayHelper::map($fundSources, 'id', 'title');

        if($model->load(Yii::$app->request->post()))
        {
            $projectRaw = Plan::find()->select(['project_id'])->where(['year' => $model->year])->asArray()->all();
            $projectRaw = ArrayHelper::map($projectRaw, 'project_id', 'project_id');

            //echo '<pre>'; print_r($projectIDs); exit;

            $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $model->year])->createCommand()->getRawSql();
            $physicalAccomps = PhysicalAccomplishment::find()->where(['year' => $model->year])->createCommand()->getRawSql();
            $accomps = Accomplishment::find()->select(['project_id', 'IF(sum(COALESCE(action, 0)) > 0, 1, 0) as isCompleted'])->where(['year' => $model->year])->groupBy(['project_id'])->createCommand()->getRawSql();
            $financialAccomps = FinancialAccomplishment::find()->where(['year' => $model->year])->createCommand()->getRawSql();
            $financialTargets = ProjectTarget::find()->where(['target_type' => 'Financial', 'year' => $model->year])->createCommand()->getRawSql();

            $regionIDs = ProjectRegion::find();
            $provinceIDs = ProjectProvince::find();
            $categoryIDs = ProjectCategory::find();

            $categoryTitles = ProjectCategory::find()
                ->select(['project_id', 'GROUP_CONCAT(DISTINCT category.title ORDER BY category.title ASC SEPARATOR ", ") as title'])
                ->leftJoin('category', 'category.id = project_category.category_id')
                ->leftJoin('project', 'project.id = project_category.project_id')
                ->where(['project.draft' => 'No'])
                ->groupBy(['project_category.project_id'])
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
            
            $physicalTargetPerQuarter = 'IF("'.$model->quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                IF("'.$model->quarter.'" = "Q2", COALESCE(physicalTargets.q2, 0),
                    IF("'.$model->quarter.'" = "Q3", COALESCE(physicalTargets.q3, 0),
                    COALESCE(physicalTargets.q4, 0)
                    )
                )
            )';

            $physicalTargetTotalPerQuarter = 'IF(project.data_type = "Cumulative",
                        IF("'.$model->quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                            IF("'.$model->quarter.'" = "Q2", IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)),
                                IF("'.$model->quarter.'" = "Q3", IF(COALESCE(physicalTargets.q3, 0) = 0, IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)), COALESCE(physicalTargets.q3, 0)),
                                IF(COALESCE(physicalTargets.q4, 0) = 0, IF(COALESCE(physicalTargets.q3, 0) = 0, IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)), COALESCE(physicalTargets.q3, 0)), COALESCE(physicalTargets.q4, 0))
                                )
                            )
                        )
                    ,   
                    IF("'.$model->quarter.'" = "Q1", (COALESCE(physicalTargets.q1, 0) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                         IF("'.$model->quarter.'" = "Q2", ((COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                             IF("'.$model->quarter.'" = "Q3", ((COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                             ((COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100
                             )
                         )
                    ) 
                    )';

            $physicalTargetTotal = 'IF(project.data_type <> "Default",
                IF(COALESCE(physicalTargets.q4, 0) <= 0,
                    IF(COALESCE(physicalTargets.q3, 0) <= 0,
                        IF(COALESCE(physicalTargets.q2, 0) <= 0,
                            COALESCE(physicalTargets.q1, 0)
                        , COALESCE(physicalTargets.q2, 0))
                    , COALESCE(physicalTargets.q3, 0))
                , COALESCE(physicalTargets.q4, 0))
            ,   
                COALESCE(physicalTargets.q1, 0) +
                COALESCE(physicalTargets.q2, 0) +
                COALESCE(physicalTargets.q3, 0) +
                COALESCE(physicalTargets.q4, 0)
            )'; 

            $physicalAccompTotalPerQuarter = 'IF(project.data_type = "Cumulative",
                                    IF("'.$model->quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                        IF("'.$model->quarter.'" = "Q2", IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)),
                                            IF("'.$model->quarter.'" = "Q3", IF(COALESCE(physicalAccompsQ3.value, 0) = 0, IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)), COALESCE(physicalAccompsQ3.value, 0)),
                                            IF(COALESCE(physicalAccompsQ4.value, 0) = 0, IF(COALESCE(physicalAccompsQ3.value, 0) = 0, IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)), COALESCE(physicalAccompsQ3.value, 0)), COALESCE(physicalAccompsQ4.value, 0))
                                            )
                                        )
                                    )
                                ,   
                                IF("'.$model->quarter.'" = "Q1", (COALESCE(physicalAccompsQ1.value, 0) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                                     IF("'.$model->quarter.'" = "Q2", ((COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                                         IF("'.$model->quarter.'" = "Q3", ((COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                                         ((COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0) + COALESCE(physicalAccompsQ4.value, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100
                                         )
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

            $financialTargetTotalPerQuarter = 'IF(project.data_type <> "Cumulative",
                                        IF("'.$model->quarter.'" = "Q1", COALESCE(financialTargets.q1, 0),
                                            IF("'.$model->quarter.'" = "Q2", COALESCE(financialTargets.q1, 0) + COALESCE(financialTargets.q2, 0),
                                                IF("'.$model->quarter.'" = "Q3", COALESCE(financialTargets.q1, 0) + COALESCE(financialTargets.q2, 0) + COALESCE(financialTargets.q3, 0),
                                                COALESCE(financialTargets.q1, 0) + COALESCE(financialTargets.q2, 0) + COALESCE(financialTargets.q3, 0) + COALESCE(financialTargets.q4, 0)
                                                )
                                            )
                                        )
                                    ,   
                                        IF("'.$model->quarter.'" = "Q1", COALESCE(financialTargets.q1, 0),
                                            IF("'.$model->quarter.'" = "Q2", IF(COALESCE(financialTargets.q2, 0) = 0, COALESCE(financialTargets.q1, 0), COALESCE(financialTargets.q2, 0)),
                                                IF("'.$model->quarter.'" = "Q3", IF(COALESCE(financialTargets.q3, 0) = 0, IF(COALESCE(financialTargets.q2, 0) = 0, COALESCE(financialTargets.q1, 0), COALESCE(financialTargets.q2, 0)), COALESCE(financialTargets.q3)),
                                                    IF(COALESCE(financialTargets.q4, 0) = 0, IF(COALESCE(financialTargets.q3, 0) = 0, IF(COALESCE(financialTargets.q2, 0) = 0, COALESCE(financialTargets.q1, 0), COALESCE(financialTargets.q2, 0)), COALESCE(financialTargets.q3)), COALESCE(financialTargets.q4, 0))
                                                )
                                            )
                                        )
                                    )'; 
            
            $isPercent = 'LOCATE("%", physicalTargets.indicator)';

            $isCompleted = 'COALESCE(accomps.isCompleted, 0)';
            $slippage = 'IF('.$isPercent.' > 0, '.$physicalAccompPerQuarter.' - '.$physicalTargetPerQuarter.', IF('.$physicalTargetPerQuarter.' > 0, (('.$physicalAccompPerQuarter.'/'.$physicalTargetPerQuarter.') * 100) -100 , 0))';
            $behindSchedule = 'IF('.$isCompleted.' = 0, IF('.$physicalAccompTotalPerQuarter.' > 0, IF('.$slippage.' < 0, 1 , 0), 0), 0)';
            $onSchedule = 'IF('.$isCompleted.' = 0, IF('.$physicalAccompTotalPerQuarter.' > 0, IF('.$slippage.' = 0, 1 , 0), 0), 0)';
            $aheadOnSchedule = 'IF('.$isCompleted.' = 0, IF('.$physicalAccompTotalPerQuarter.' > 0, IF('.$slippage.' > 0, 1 , 0), 0), 0)';
            $notYetStartedWithTarget = 'IF('.$isCompleted.' = 0, IF('.$physicalAccompTotalPerQuarter.' = 0, IF('.$physicalTargetTotal.' > 0, 1, 0), 0), 0)';
            $notYetStartedWithNoTarget = 'IF('.$isCompleted.' = 0, IF('.$physicalAccompTotalPerQuarter.' = 0, IF('.$physicalTargetTotal.' <= 0, 1, 0), 0), 0)';

            $projectStatus = Project::find()
                ->select([
                    'project.id as id',
                    'project.data_type as dataType',
                    'agency.code as agencyTitle',
                    'category.title as categoryTitle',
                    'project.project_no as projectNo',
                    'project.title as projectTitle',
                    'sector.title as sectorTitle',
                    'sub_sector.title as subSectorTitle',
                    'fund_source.title as fundSourceTitle',
                    'categoryTitles.title as categoryTitle',
                    'IF(provinceTitles.title is null, IF(regionTitles.title is null, "No location", regionTitles.title), provinceTitles.title) as provinceTitle',
                    'SUM('.$financialTargetTotalPerQuarter.') as allocations',
                    'SUM('.$isCompleted.') as completed',
                    'SUM('.$slippage.') as slippage',
                    'SUM('.$behindSchedule.') as behindSchedule',
                    'SUM('.$onSchedule.') as onSchedule',
                    'SUM('.$aheadOnSchedule.') as aheadOnSchedule',
                    'SUM('.$notYetStartedWithTarget.') as notYetStartedWithTarget',
                    'SUM('.$notYetStartedWithNoTarget.') as notYetStartedWithNoTarget',
                ]);
                $projectStatus = $projectStatus->leftJoin('agency', 'agency.id = project.agency_id');
                $projectStatus = $projectStatus->leftJoin('program', 'program.id = project.program_id');
                $projectStatus = $projectStatus->leftJoin('sector', 'sector.id = project.sector_id');
                $projectStatus = $projectStatus->leftJoin('sub_sector', 'sub_sector.id = project.sub_sector_id');
                $projectStatus = $projectStatus->leftJoin('fund_source', 'fund_source.id = project.fund_source_id');
                $projectStatus = $projectStatus->leftJoin(['financialTargets' => '('.$financialTargets.')'], 'financialTargets.project_id = project.id');
                $projectStatus = $projectStatus->leftJoin(['financialAccompsQ1' => '('.$financialAccomps.')'], 'financialAccompsQ1.project_id = project.id and financialAccompsQ1.quarter = "Q1"');
                $projectStatus = $projectStatus->leftJoin(['financialAccompsQ2' => '('.$financialAccomps.')'], 'financialAccompsQ2.project_id = project.id and financialAccompsQ2.quarter = "Q2"');
                $projectStatus = $projectStatus->leftJoin(['financialAccompsQ3' => '('.$financialAccomps.')'], 'financialAccompsQ3.project_id = project.id and financialAccompsQ3.quarter = "Q3"');
                $projectStatus = $projectStatus->leftJoin(['financialAccompsQ4' => '('.$financialAccomps.')'], 'financialAccompsQ4.project_id = project.id and financialAccompsQ4.quarter = "Q4"');
                $projectStatus = $projectStatus->leftJoin(['physicalAccompsQ1' => '('.$physicalAccomps.')'], 'physicalAccompsQ1.project_id = project.id and physicalAccompsQ1.quarter = "Q1"');
                $projectStatus = $projectStatus->leftJoin(['physicalAccompsQ2' => '('.$physicalAccomps.')'], 'physicalAccompsQ2.project_id = project.id and physicalAccompsQ2.quarter = "Q2"');
                $projectStatus = $projectStatus->leftJoin(['physicalAccompsQ3' => '('.$physicalAccomps.')'], 'physicalAccompsQ3.project_id = project.id and physicalAccompsQ3.quarter = "Q3"');
                $projectStatus = $projectStatus->leftJoin(['physicalAccompsQ4' => '('.$physicalAccomps.')'], 'physicalAccompsQ4.project_id = project.id and physicalAccompsQ4.quarter = "Q4"');
                $projectStatus = $projectStatus->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
                $projectStatus = $projectStatus->leftJoin(['categoryTitles' => '('.$categoryTitles.')'], 'categoryTitles.project_id = project.id');
                $projectStatus = $projectStatus->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id');
                $projectStatus = $projectStatus->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
                $projectStatus = $projectStatus->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');
                $projectStatus = $projectStatus->andWhere(['project.year' => $model->year, 'project.draft' => 'No']);
                $projectStatus = $projectStatus->andWhere(['project.id' => $projectRaw]);

                $projectFinancial = Project::find()
                ->select([
                    'categoryTitles.title as category',
                    'SUM('.$financialTargetTotalPerQuarter.') as value',
                ]);
                $projectFinancial = $projectFinancial->leftJoin(['financialTargets' => '('.$financialTargets.')'], 'financialTargets.project_id = project.id');
                $projectFinancial = $projectFinancial->leftJoin(['financialAccompsQ1' => '('.$financialAccomps.')'], 'financialAccompsQ1.project_id = project.id and financialAccompsQ1.quarter = "Q1"');
                $projectFinancial = $projectFinancial->leftJoin(['financialAccompsQ2' => '('.$financialAccomps.')'], 'financialAccompsQ2.project_id = project.id and financialAccompsQ2.quarter = "Q2"');
                $projectFinancial = $projectFinancial->leftJoin(['financialAccompsQ3' => '('.$financialAccomps.')'], 'financialAccompsQ3.project_id = project.id and financialAccompsQ3.quarter = "Q3"');
                $projectFinancial = $projectFinancial->leftJoin(['financialAccompsQ4' => '('.$financialAccomps.')'], 'financialAccompsQ4.project_id = project.id and financialAccompsQ4.quarter = "Q4"');
                $projectFinancial = $projectFinancial->leftJoin(['categoryTitles' => '('.$categoryTitles.')'], 'categoryTitles.project_id = project.id');
                $projectFinancial = $projectFinancial->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');
                $projectFinancial = $projectFinancial->andWhere(['project.year' => $model->year, 'project.draft' => 'No']);
                $projectFinancial = $projectFinancial->andWhere(['project.id' => $projectRaw]);

                if($model->agency_id != '')
                {
                    $projectStatus = $projectStatus->andWhere(['agency.id' => $model->agency_id]);
                    $projectFinancial = $projectFinancial->andWhere(['agency.id' => $model->agency_id]);
                }
    
                if($model->sector_id != '')
                {
                    $projectStatus = $projectStatus->andWhere(['sector.id' => $model->sector_id]);
                    $projectFinancial = $projectFinancial->andWhere(['sector.id' => $model->sector_id]);
                }

                if($model->category_id != '')
                {
                    $categoryIDs = $categoryIDs->andWhere(['category_id' => $model->category_id]);
                }

                if($model->region_id != '')
                {
                    $regionIDs = $regionIDs->andWhere(['region_id' => $model->region_id]);
                }

                if($model->province_id != '')
                {
                    $provinceIDs = $provinceIDs->andWhere(['province_id' => $model->province_id]);
                }

                $regionIDs = $regionIDs->all();
                $regionIDs = ArrayHelper::map($regionIDs, 'project_id', 'project_id');

                $provinceIDs = $provinceIDs->all();
                $provinceIDs = ArrayHelper::map($provinceIDs, 'project_id', 'project_id');

                $categoryIDs = $categoryIDs->all();
                $categoryIDs = ArrayHelper::map($categoryIDs, 'project_id', 'project_id');

                if($model->region_id != '')
                {
                    $projectStatus = $projectStatus->andWhere(['project.id' => $regionIDs]);
                    $projectFinancial = $projectFinancial->andWhere(['project.id' => $regionIDs]);
                }

                if($model->province_id != '')
                {
                    $projectStatus = $projectStatus->andWhere(['project.id' => $provinceIDs]);
                    $projectFinancial = $projectFinancial->andWhere(['project.id' => $provinceIDs]);
                }

                if($model->category_id != '')
                {
                    $projectStatus = $projectStatus->andWhere(['project.id' => $categoryIDs]);
                    $projectFinancial = $projectFinancial->andWhere(['project.id' => $categoryIDs]);
                }

                if($model->fund_source_id != '')
                {
                    $projectStatus = $projectStatus->andWhere(['fund_source.id' => $model->fund_source_id]);
                    $projectFinancial = $projectFinancial->andWhere(['fund_source.id' => $model->fund_source_id]);
                }

                $projectFinancial = $projectFinancial->groupBy(['category']);

                $projectStatus = $projectStatus->groupBy(['sectorTitle']);

                $projectStatus = $projectStatus->asArray()->all();

                $projectFinancial = $projectFinancial->asArray()->all();

                $totalAllocation = 0;
                $script = "";

                if($projectFinancial)
                {
                    foreach($projectFinancial as $financial)
                    {
                        $totalAllocation = $totalAllocation + $financial['value'];
                    }
                }

                if($projectFinancial)
                {
                    foreach($projectFinancial as $financial)
                        {
                            $script .= "{ category: '".$financial['category']."', value: ".(($financial['value'] / $totalAllocation) * 1000)."}, ";
                        }
                }

                //echo $script; exit;

                return $this->renderAjax('_graphs', [
                    'model' => $model,
                    'projectStatus' => $projectStatus,
                    'projectFinancial' => $projectFinancial,
                    'script' => $script,
                    'totalAllocation' => $totalAllocation,
                ]);
        }
        

        return $this->render('index', [
            'model' => $model,
            'years' => $years,
            'quarters' => $quarters,
            'agencies' => $agencies,
            'sectors' => $sectors,
            'subSectors' => $subSectors,
            'categories' => $categories,
            'provinces' => $provinces,
            'fundSources' => $fundSources,
        ]);
    }

    public function actionProjectStatus()
    {
        $model = new Submission();

        if($model->load(Yii::$app->request->post()))
        {
            $projectRaw = Plan::find()->select(['project_id'])->where(['year' => $model->year])->asArray()->all();
            $projectRaw = ArrayHelper::map($projectRaw, 'project_id', 'project_id');

            //echo '<pre>'; print_r($projectIDs); exit;

            $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $model->year])->createCommand()->getRawSql();
            $physicalAccomps = PhysicalAccomplishment::find()->where(['year' => $model->year])->createCommand()->getRawSql();
            $accomps = Accomplishment::find()->select(['project_id', 'IF(sum(COALESCE(action, 0)) > 0, 1, 0) as isCompleted'])->where(['year' => $model->year])->groupBy(['project_id'])->createCommand()->getRawSql();

            $regionIDs = ProjectRegion::find();
            $provinceIDs = ProjectProvince::find();
            $categoryIDs = ProjectCategory::find();

            $categoryTitles = ProjectCategory::find()
                ->select(['project_id', 'GROUP_CONCAT(DISTINCT category.title ORDER BY category.title ASC SEPARATOR ", ") as title'])
                ->leftJoin('category', 'category.id = project_category.category_id')
                ->leftJoin('project', 'project.id = project_category.project_id')
                ->where(['project.draft' => 'No'])
                ->groupBy(['project_category.project_id'])
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
            
                $physicalTargetPerQuarter = 'IF("'.$model->quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                IF("'.$model->quarter.'" = "Q2", COALESCE(physicalTargets.q2, 0),
                    IF("'.$model->quarter.'" = "Q3", COALESCE(physicalTargets.q3, 0),
                    COALESCE(physicalTargets.q4, 0)
                    )
                )
            )';

            $physicalTargetTotalPerQuarter = 'IF(project.data_type = "Cumulative",
                        IF("'.$model->quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                            IF("'.$model->quarter.'" = "Q2", IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)),
                                IF("'.$model->quarter.'" = "Q3", IF(COALESCE(physicalTargets.q3, 0) = 0, IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)), COALESCE(physicalTargets.q3, 0)),
                                IF(COALESCE(physicalTargets.q4, 0) = 0, IF(COALESCE(physicalTargets.q3, 0) = 0, IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)), COALESCE(physicalTargets.q3, 0)), COALESCE(physicalTargets.q4, 0))
                                )
                            )
                        )
                    ,   
                    IF("'.$model->quarter.'" = "Q1", (COALESCE(physicalTargets.q1, 0) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                         IF("'.$model->quarter.'" = "Q2", ((COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                             IF("'.$model->quarter.'" = "Q3", ((COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                             ((COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100
                             )
                         )
                    ) 
                    )';

            $physicalTargetTotal = 'IF(project.data_type <> "Default",
                IF(COALESCE(physicalTargets.q4, 0) <= 0,
                    IF(COALESCE(physicalTargets.q3, 0) <= 0,
                        IF(COALESCE(physicalTargets.q2, 0) <= 0,
                            COALESCE(physicalTargets.q1, 0)
                        , COALESCE(physicalTargets.q2, 0))
                    , COALESCE(physicalTargets.q3, 0))
                , COALESCE(physicalTargets.q4, 0))
            ,   
                COALESCE(physicalTargets.q1, 0) +
                COALESCE(physicalTargets.q2, 0) +
                COALESCE(physicalTargets.q3, 0) +
                COALESCE(physicalTargets.q4, 0)
            )'; 

            $physicalAccompTotalPerQuarter = 'IF(project.data_type = "Cumulative",
                                    IF("'.$model->quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                        IF("'.$model->quarter.'" = "Q2", IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)),
                                            IF("'.$model->quarter.'" = "Q3", IF(COALESCE(physicalAccompsQ3.value, 0) = 0, IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)), COALESCE(physicalAccompsQ3.value, 0)),
                                            IF(COALESCE(physicalAccompsQ4.value, 0) = 0, IF(COALESCE(physicalAccompsQ3.value, 0) = 0, IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)), COALESCE(physicalAccompsQ3.value, 0)), COALESCE(physicalAccompsQ4.value, 0))
                                            )
                                        )
                                    )
                                ,   
                                IF("'.$model->quarter.'" = "Q1", (COALESCE(physicalAccompsQ1.value, 0) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                                     IF("'.$model->quarter.'" = "Q2", ((COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                                         IF("'.$model->quarter.'" = "Q3", ((COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                                         ((COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0) + COALESCE(physicalAccompsQ4.value, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100
                                         )
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
            $behindSchedule = 'IF('.$isCompleted.' = 0, IF('.$physicalAccompTotalPerQuarter.' > 0, IF('.$slippage.' < 0, 1 , 0), 0), 0)';
            $onSchedule = 'IF('.$isCompleted.' = 0, IF('.$physicalAccompTotalPerQuarter.' > 0, IF('.$slippage.' = 0, 1 , 0), 0), 0)';
            $aheadOnSchedule = 'IF('.$isCompleted.' = 0, IF('.$physicalAccompTotalPerQuarter.' > 0, IF('.$slippage.' > 0, 1 , 0), 0), 0)';
            $notYetStartedWithTarget = 'IF('.$isCompleted.' = 0, IF('.$physicalAccompTotalPerQuarter.' = 0, IF('.$physicalTargetTotal.' > 0, 1, 0), 0), 0)';
            $notYetStartedWithNoTarget = 'IF('.$isCompleted.' = 0, IF('.$physicalAccompTotalPerQuarter.' = 0, IF('.$physicalTargetTotal.' <= 0, 1, 0), 0), 0)';

            $projectStatus = Project::find()
                ->select([
                    'project.id as id',
                    'project.data_type as dataType',
                    'agency.code as agencyTitle',
                    'category.title as categoryTitle',
                    'project.project_no as projectNo',
                    'project.title as projectTitle',
                    'sector.title as sectorTitle',
                    'sub_sector.title as subSectorTitle',
                    'fund_source.title as fundSourceTitle',
                    'categoryTitles.title as categoryTitle',
                    'IF(provinceTitles.title is null, IF(regionTitles.title is null, "No location", regionTitles.title), provinceTitles.title) as provinceTitle',
                    'SUM('.$isCompleted.') as completed',
                    'SUM('.$slippage.') as slippage',
                    'SUM('.$behindSchedule.') as behindSchedule',
                    'SUM('.$onSchedule.') as onSchedule',
                    'SUM('.$aheadOnSchedule.') as aheadOnSchedule',
                    'SUM('.$notYetStartedWithTarget.') as notYetStartedWithTarget',
                    'SUM('.$notYetStartedWithNoTarget.') as notYetStartedWithNoTarget',
                ]);
                $projectStatus = $projectStatus->leftJoin('agency', 'agency.id = project.agency_id');
                $projectStatus = $projectStatus->leftJoin('program', 'program.id = project.program_id');
                $projectStatus = $projectStatus->leftJoin('sector', 'sector.id = project.sector_id');
                $projectStatus = $projectStatus->leftJoin('sub_sector', 'sub_sector.id = project.sub_sector_id');
                $projectStatus = $projectStatus->leftJoin('fund_source', 'fund_source.id = project.fund_source_id');
                $projectStatus = $projectStatus->leftJoin(['physicalAccompsQ1' => '('.$physicalAccomps.')'], 'physicalAccompsQ1.project_id = project.id and physicalAccompsQ1.quarter = "Q1"');
                $projectStatus = $projectStatus->leftJoin(['physicalAccompsQ2' => '('.$physicalAccomps.')'], 'physicalAccompsQ2.project_id = project.id and physicalAccompsQ2.quarter = "Q2"');
                $projectStatus = $projectStatus->leftJoin(['physicalAccompsQ3' => '('.$physicalAccomps.')'], 'physicalAccompsQ3.project_id = project.id and physicalAccompsQ3.quarter = "Q3"');
                $projectStatus = $projectStatus->leftJoin(['physicalAccompsQ4' => '('.$physicalAccomps.')'], 'physicalAccompsQ4.project_id = project.id and physicalAccompsQ4.quarter = "Q4"');
                $projectStatus = $projectStatus->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
                $projectStatus = $projectStatus->leftJoin(['categoryTitles' => '('.$categoryTitles.')'], 'categoryTitles.project_id = project.id');
                $projectStatus = $projectStatus->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id');
                $projectStatus = $projectStatus->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
                $projectStatus = $projectStatus->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');
                $projectStatus = $projectStatus->andWhere(['project.year' => $model->year, 'project.draft' => 'No']);
                $projectStatus = $projectStatus->andWhere(['project.id' => $projectRaw]);

                if($model->agency_id != '')
                {
                    $projectStatus = $projectStatus->andWhere(['agency.id' => $model->agency_id]);
                }
    
                if($model->sector_id != '')
                {
                    $projectStatus = $projectStatus->andWhere(['sector.id' => $model->sector_id]);
                }

                if($model->category_id != '')
                {
                    $categoryIDs = $categoryIDs->andWhere(['category_id' => $model->category_id]);
                }

                if($model->region_id != '')
                {
                    $regionIDs = $regionIDs->andWhere(['region_id' => $model->region_id]);
                }

                if($model->province_id != '')
                {
                    $provinceIDs = $provinceIDs->andWhere(['province_id' => $model->province_id]);
                }

                $regionIDs = $regionIDs->all();
                $regionIDs = ArrayHelper::map($regionIDs, 'project_id', 'project_id');

                $provinceIDs = $provinceIDs->all();
                $provinceIDs = ArrayHelper::map($provinceIDs, 'project_id', 'project_id');

                $categoryIDs = $categoryIDs->all();
                $categoryIDs = ArrayHelper::map($categoryIDs, 'project_id', 'project_id');

                if($model->region_id != '')
                {
                    $projects = $projects->andWhere(['project.id' => $regionIDs]);
                }

                if($model->province_id != '')
                {
                    $projects = $projects->andWhere(['project.id' => $provinceIDs]);
                }

                if($model->category_id != '')
                {
                    $projects = $projects->andWhere(['project.id' => $categoryIDs]);
                }

                if($model->fund_source_id != '')
                {
                    $projects = $projects->andWhere(['fund_source.id' => $model->fund_source_id]);
                }

                $projectStatus = $projectStatus->groupBy(['agencyTitle']);

                $projectStatus = $projectStatus->asArray()->all();

                //echo '<pre>'; print_r($projects); exit;

                return $this->renderAjax('_graphs', [
                    'model' => $model,
                    'projectStatus' => $projectStatus,
                ]);
        }

    }
    
    public function actionHeatMap($year, $quarter, $agency_id, $category_id, $sector_id, $sub_sector_id, $province_id, $fund_source_id)
    {
        $data = [];

        $categoryIDs = ProjectCategory::find();
        $provinceIDs = ProjectProvince::find();
        $quarterIDs = Accomplishment::find();

        $perProvince = ProjectProvince::find()
        ->select(['province_id, count(project_province.id) as total'])
        ->leftJoin('project', 'project.id = project_province.project_id');

        if($year != '')
        {
            $perProvince = $perProvince->andWhere(['project.year' => $year]);
        }

        if($quarter != '')
        {
            $quarterIDs = $quarterIDs->andWhere(['quarter' => $quarter]);
        }

        if($agency_id != '')
        {
            $perProvince = $perProvince->andWhere(['project.agency_id' => $agency_id]);
        }

        if($category_id != '')
        {
            $categoryIDs = $categoryIDs->andWhere(['category_id' => $category_id]);
        }

        if($sector_id != '')
        {
            $perProvince = $perProvince->andWhere(['project.sector_id' => $sector_id]);
        }

        if($sub_sector_id != '')
        {
            $perProvince = $perProvince->andWhere(['project.sub_sector_id' => $sub_sector_id]);
        }

        if($province_id != '')
        {
            $perProvince = $perProvince->andWhere(['project_province.province_id' => $province_id]);
        }

        if($fund_source_id != '')
        {
            $perProvince = $perProvince->andWhere(['project.fund_source_id' => $fund_source_id]);
        }

        $categoryIDs = $categoryIDs->all();
        $categoryIDs = ArrayHelper::map($categoryIDs, 'project_id', 'project_id');

        $quarterIDs = $quarterIDs->all();
        $quarterIDs = ArrayHelper::map($quarterIDs, 'project_id', 'project_id');

        if($quarter != '')
        {
            $perProvince = $perProvince->andWhere(['project.id' => $quarterIDs]);
        }

        if($category_id != '')
        {
            $perProvince = $perProvince->andWhere(['project.id' => $categoryIDs]);
        }
        
        $perProvince = $perProvince
        ->groupBy(['province_id'])
        ->createCommand()
        ->getRawSql();

        $keys = Province::find()
            ->select([
                'hc_key',
                'province_m',
                'perProvince.total as total'
            ])
            ->leftJoin(['perProvince' => '('.$perProvince.')'], 'perProvince.province_id = tblprovince.province_c')
            ->asArray()
            ->all();

        if(!empty($keys))
        {
            $i = 0;
            foreach($keys as $key)
            {
                $data[$i]['id'] = $key['hc_key'];
                $data[$i]['value'] = number_format($key['total'], 0);
                $data[$i]['scale'] = 0.5;
                $data[$i]['labelShiftY'] = 2;
                $data[$i]['zoomLevel'] = 5;
                $data[$i]['title'] = '<p>'.$key['province_m'].' : '.number_format($key['total'], 0).' projects</p>';
                $i++;
            }
        }

        $data = Json::encode($data);

        return $this->renderAjax('_map',[
            'data' => $data
        ]);
    }
}
