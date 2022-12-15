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
use common\modules\rpmes\models\EventImage;
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
use common\modules\rpmes\models\BeneficiariesAccomplishment;
use common\modules\rpmes\models\PersonEmployedAccomplishment;
use common\models\Region;
use common\models\Province;
use common\models\Citymun;
use common\modules\rpmes\models\ProjectCitymun;
use yii\helpers\Html;
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

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
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

        return $this->render('index', [
            'model' => $model,
            'years' => $years,
            'quarters' => $quarters,
        ]);
    }

    public function actionsectors()
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

            $sectors = Project::find()
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
                $sectors = $sectors->leftJoin('agency', 'agency.id = project.agency_id');
                $sectors = $sectors->leftJoin('program', 'program.id = project.program_id');
                $sectors = $sectors->leftJoin('sector', 'sector.id = project.sector_id');
                $sectors = $sectors->leftJoin('sub_sector', 'sub_sector.id = project.sub_sector_id');
                $sectors = $sectors->leftJoin('fund_source', 'fund_source.id = project.fund_source_id');
                $sectors = $sectors->leftJoin(['physicalAccompsQ1' => '('.$physicalAccomps.')'], 'physicalAccompsQ1.project_id = project.id and physicalAccompsQ1.quarter = "Q1"');
                $sectors = $sectors->leftJoin(['physicalAccompsQ2' => '('.$physicalAccomps.')'], 'physicalAccompsQ2.project_id = project.id and physicalAccompsQ2.quarter = "Q2"');
                $sectors = $sectors->leftJoin(['physicalAccompsQ3' => '('.$physicalAccomps.')'], 'physicalAccompsQ3.project_id = project.id and physicalAccompsQ3.quarter = "Q3"');
                $sectors = $sectors->leftJoin(['physicalAccompsQ4' => '('.$physicalAccomps.')'], 'physicalAccompsQ4.project_id = project.id and physicalAccompsQ4.quarter = "Q4"');
                $sectors = $sectors->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
                $sectors = $sectors->leftJoin(['categoryTitles' => '('.$categoryTitles.')'], 'categoryTitles.project_id = project.id');
                $sectors = $sectors->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id');
                $sectors = $sectors->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
                $sectors = $sectors->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');
                $sectors = $sectors->andWhere(['project.year' => $model->year, 'project.draft' => 'No']);
                $sectors = $sectors->andWhere(['project.id' => $projectRaw]);

                if($model->agency_id != '')
                {
                    $sectors = $sectors->andWhere(['agency.id' => $model->agency_id]);
                }
    
                if($model->sector_id != '')
                {
                    $sectors = $sectors->andWhere(['sector.id' => $model->sector_id]);
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

                $sectors = $sectors->groupBy(['agencyTitle']);

                $sectors = $sectors->asArray()->all();

                //echo '<pre>'; print_r($projects); exit;

                return $this->renderAjax('_graphs', [
                    'model' => $model,
                    'sectors' => $sectors,
                ]);
        }

    }
    
    public function actionHeatMap($year, $quarter)
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

    public function actionEmployment($year, $quarter)
    {
        $data = [];

        $accomplishment = Project::find()
                        ->select([
                            'project.id', 
                            'project.sector_id', 
                            'sum(COALESCE(male,0)) as maleTotal',
                            'sum(COALESCE(female, 0)) as femaleTotal'
                        ])
                        ->leftJoin('person_employed_accomplishment', 'person_employed_accomplishment.project_id = project.id');

        if($year != '')
        {
            $accomplishment = $accomplishment->andWhere(['person_employed_accomplishment.year' => $year]);
        }

        if($quarter != '')
        {
            $accomplishment = $accomplishment->andWhere(['person_employed_accomplishment.quarter' => $quarter]);
        }
        
        $accomplishment = $accomplishment
        ->groupBy(['project.id', 'project.sector_id'])
        ->createCommand()
        ->getRawSql();

        $sectors = Sector::find()
            ->select([
                'sector.title',
                'sum(COALESCE(accomplishment.maleTotal, 0)) as maleTotal',
                'sum(COALESCE(accomplishment.femaleTotal, 0)) as femaleTotal',
            ])
            ->leftJoin(['accomplishment' => '('.$accomplishment.')'], 'accomplishment.sector_id = sector.id')
            ->groupBy(['sector.id'])
            ->asArray()
            ->all();

        $sectorsTotal = Sector::find()
            ->select([
                'sum(COALESCE(accomplishment.maleTotal, 0)) as maleTotal',
                'sum(COALESCE(accomplishment.femaleTotal, 0)) as femaleTotal',
            ])
            ->leftJoin(['accomplishment' => '('.$accomplishment.')'], 'accomplishment.sector_id = sector.id')
            ->asArray()
            ->one();

        $data = [];

        if(!empty($sectors))
        {
            $i = 0;
            foreach($sectors as $sector)
            {
                $data[$i]['sector'] = $sector['title'];
                $data[$i]['male'] = $sector['maleTotal'] > 0 ? (($sector['maleTotal']/($sector['maleTotal'] + $sector['femaleTotal']))*100): 0;
                $data[$i]['maleRaw'] = $sector['maleTotal'];
                $data[$i]['female'] = $sector['femaleTotal'] > 0 ? (($sector['femaleTotal']/($sector['maleTotal'] + $sector['femaleTotal']))*100): 0;
                $data[$i]['femaleRaw'] = $sector['femaleTotal'];
                $i++;
            }
        }

        //echo "<pre>"; print_r($data); exit;

        $data = Json::encode($data);

        return $this->renderAjax('_employment',[
            'data' => $data,
            'sectorsTotal' => $sectorsTotal,
            'year' => $year,
            'quarter' => $quarter, 
        ]);
    }

    public function actionEmploymentData($year, $quarter)
    {
        $data = [];

        $accomplishment = Project::find()
                        ->select([
                            'project.id', 
                            'project.sector_id', 
                            'sum(COALESCE(male,0)) as maleTotal',
                            'sum(COALESCE(female, 0)) as femaleTotal'
                        ])
                        ->leftJoin('person_employed_accomplishment', 'person_employed_accomplishment.project_id = project.id');

        if($year != '')
        {
            $accomplishment = $accomplishment->andWhere(['person_employed_accomplishment.year' => $year]);
        }

        if($quarter != '')
        {
            $accomplishment = $accomplishment->andWhere(['person_employed_accomplishment.quarter' => $quarter]);
        }
        
        $accomplishment = $accomplishment
        ->groupBy(['project.id', 'project.sector_id'])
        ->createCommand()
        ->getRawSql();

        $sectors = Sector::find()
            ->select([
                'sector.title',
                'sum(COALESCE(accomplishment.maleTotal, 0)) as maleTotal',
                'sum(COALESCE(accomplishment.femaleTotal, 0)) as femaleTotal',
            ])
            ->leftJoin(['accomplishment' => '('.$accomplishment.')'], 'accomplishment.sector_id = sector.id')
            ->groupBy(['sector.id'])
            ->asArray()
            ->all();

        $sectorsTotal = Sector::find()
            ->select([
                'sum(COALESCE(accomplishment.maleTotal, 0)) as maleTotal',
                'sum(COALESCE(accomplishment.femaleTotal, 0)) as femaleTotal',
            ])
            ->leftJoin(['accomplishment' => '('.$accomplishment.')'], 'accomplishment.sector_id = sector.id')
            ->asArray()
            ->one();

        return $this->renderAjax('_employment-data',[
            'sectors' => $sectors,
            'sectorsTotal' => $sectorsTotal,
        ]);
    }

    public function actionDisbursementByCategory($year, $quarter)
    {
        $data = [];

        $accomplishment = Project::find()
                        ->select([
                            'project.id', 
                            'project.sector_id', 
                            'sum(COALESCE(expenditures,0)) as total',
                        ])
                        ->leftJoin('financial_accomplishment', 'financial_accomplishment.project_id = project.id');

        if($year != '')
        {
            $accomplishment = $accomplishment->andWhere(['financial_accomplishment.year' => $year]);
        }

        if($quarter != '')
        {
            $accomplishment = $accomplishment->andWhere(['financial_accomplishment.quarter' => $quarter]);
        }
        
        $accomplishment = $accomplishment
        ->groupBy(['project.id', 'project.sector_id'])
        ->createCommand()
        ->getRawSql();

        $sectors = Sector::find()
            ->select([
                'sector.title',
                'sum(COALESCE(accomplishment.total, 0)) as total',
            ])
            ->leftJoin(['accomplishment' => '('.$accomplishment.')'], 'accomplishment.sector_id = sector.id')
            ->groupBy(['sector.id'])
            ->asArray()
            ->all();

        $data = [];

        if(!empty($sectors))
        {
            $i = 0;
            foreach($sectors as $sector)
            {
                $data[$i]['sector'] = $sector['title'];
                $data[$i]['value'] = $sector['total'];
                $i++;
            }
        }

        //echo "<pre>"; print_r($data); exit;

        $data = Json::encode($data);

        return $this->renderAjax('_disbursement-by-category',[
            'data' => $data,
            'year' => $year,
            'quarter' => $quarter, 
        ]);
    }

    public function actionDisbursementByCategoryData($year, $quarter)
    {
        $accomplishment = Project::find()
                        ->select([
                            'project.id', 
                            'project.sector_id', 
                            'sum(COALESCE(expenditures,0)) as total',
                        ])
                        ->leftJoin('financial_accomplishment', 'financial_accomplishment.project_id = project.id');

        if($year != '')
        {
            $accomplishment = $accomplishment->andWhere(['financial_accomplishment.year' => $year]);
        }

        if($quarter != '')
        {
            $accomplishment = $accomplishment->andWhere(['financial_accomplishment.quarter' => $quarter]);
        }
        
        $accomplishment = $accomplishment
        ->groupBy(['project.id', 'project.sector_id'])
        ->createCommand()
        ->getRawSql();

        $sectors = Sector::find()
            ->select([
                'sector.title',
                'sum(COALESCE(accomplishment.total, 0)) as total',
            ])
            ->leftJoin(['accomplishment' => '('.$accomplishment.')'], 'accomplishment.sector_id = sector.id')
            ->groupBy(['sector.id'])
            ->asArray()
            ->all();

        return $this->renderAjax('_disbursement-by-category-data',[
            'sectors' => $sectors,
        ]);
    }

    public function actionProjectImplementation($year, $quarter)
    {
            $categoryTitles = ProjectCategory::find()
                ->select(['project_id', 'GROUP_CONCAT(DISTINCT category.code ORDER BY category.code ASC SEPARATOR ", ") as title'])
                ->leftJoin('category', 'category.id = project_category.category_id')
                ->leftJoin('project', 'project.id = project_category.project_id')
                ->where(['project.draft' => 'No'])
                ->groupBy(['project_category.project_id'])
                ->createCommand()->getRawSql();

            $physicalTargets = ProjectTarget::find()
                    ->andWhere(['target_type' => 'Physical']);

            $physicalAccomps = PhysicalAccomplishment::find();

            $accomps = Accomplishment::find()
                    ->select(['project_id', 'IF(sum(COALESCE(action, 0)) > 0, 1, 0) as isCompleted']);

            if($year != '')
            {
                $physicalTargets = $physicalTargets->andWhere(['year' => $year]);
                $physicalAccomps = $physicalAccomps->andWhere(['year' => $year]);
                $accomps = $accomps->andWhere(['year' => $year]);
            }

            $physicalTargets = $physicalTargets->createCommand()->getRawSql();
            $physicalAccomps = $physicalAccomps->createCommand()->getRawSql();
            $accomps = $accomps->groupBy(['project_id'])->createCommand()->getRawSql();

            $provinceIDs = ProjectProvince::find();
            $categoryIDs = ProjectCategory::find();
            
            $physicalTargetPerQuarter = 'IF("'.$quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                IF("'.$quarter.'" = "Q2", COALESCE(physicalTargets.q2, 0),
                    IF("'.$quarter.'" = "Q3", COALESCE(physicalTargets.q3, 0),
                    COALESCE(physicalTargets.q4, 0)
                    )
                )
            )';

            $physicalTargetTotalPerQuarter = 'IF(project.data_type = "Cumulative",
                        IF("'.$quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                            IF("'.$quarter.'" = "Q2", IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)),
                                IF("'.$quarter.'" = "Q3", IF(COALESCE(physicalTargets.q3, 0) = 0, IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)), COALESCE(physicalTargets.q3, 0)),
                                IF(COALESCE(physicalTargets.q4, 0) = 0, IF(COALESCE(physicalTargets.q3, 0) = 0, IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)), COALESCE(physicalTargets.q3, 0)), COALESCE(physicalTargets.q4, 0))
                                )
                            )
                        )
                    ,   
                    IF("'.$quarter.'" = "Q1", (COALESCE(physicalTargets.q1, 0) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                         IF("'.$quarter.'" = "Q2", ((COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                             IF("'.$quarter.'" = "Q3", ((COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
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
                                    IF("'.$quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                        IF("'.$quarter.'" = "Q2", IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)),
                                            IF("'.$quarter.'" = "Q3", IF(COALESCE(physicalAccompsQ3.value, 0) = 0, IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)), COALESCE(physicalAccompsQ3.value, 0)),
                                            IF(COALESCE(physicalAccompsQ4.value, 0) = 0, IF(COALESCE(physicalAccompsQ3.value, 0) = 0, IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)), COALESCE(physicalAccompsQ3.value, 0)), COALESCE(physicalAccompsQ4.value, 0))
                                            )
                                        )
                                    )
                                ,   
                                IF("'.$quarter.'" = "Q1", (COALESCE(physicalAccompsQ1.value, 0) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                                     IF("'.$quarter.'" = "Q2", ((COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                                         IF("'.$quarter.'" = "Q3", ((COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                                         ((COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0) + COALESCE(physicalAccompsQ4.value, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100
                                         )
                                     )
                                ) 
                                )';

            $physicalAccompPerQuarter = 'IF("'.$quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                            IF("'.$quarter.'" = "Q2", COALESCE(physicalAccompsQ2.value, 0),
                                                IF("'.$quarter.'" = "Q3", COALESCE(physicalAccompsQ3.value, 0),
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

            $sectors = Project::find()
                ->select([
                    'project.id as id',
                    'sector.title as sectorTitle',
                    'SUM('.$isCompleted.') as completed',
                    'SUM('.$slippage.') as slippage',
                    'SUM('.$behindSchedule.') as behindSchedule',
                    'SUM('.$onSchedule.') as onSchedule',
                    'SUM('.$aheadOnSchedule.') as aheadOnSchedule',
                    'SUM('.$notYetStartedWithTarget.') as notYetStartedWithTarget',
                    'SUM('.$notYetStartedWithNoTarget.') as notYetStartedWithNoTarget',
                ]);
            $sectors = $sectors->leftJoin('sector', 'sector.id = project.sector_id');
            $sectors = $sectors->leftJoin(['physicalAccompsQ1' => '('.$physicalAccomps.')'], 'physicalAccompsQ1.project_id = project.id and physicalAccompsQ1.quarter = "Q1"');
            $sectors = $sectors->leftJoin(['physicalAccompsQ2' => '('.$physicalAccomps.')'], 'physicalAccompsQ2.project_id = project.id and physicalAccompsQ2.quarter = "Q2"');
            $sectors = $sectors->leftJoin(['physicalAccompsQ3' => '('.$physicalAccomps.')'], 'physicalAccompsQ3.project_id = project.id and physicalAccompsQ3.quarter = "Q3"');
            $sectors = $sectors->leftJoin(['physicalAccompsQ4' => '('.$physicalAccomps.')'], 'physicalAccompsQ4.project_id = project.id and physicalAccompsQ4.quarter = "Q4"');
            $sectors = $sectors->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
            $sectors = $sectors->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');
            $sectors = $sectors->andWhere(['project.draft' => 'No']);
            
            $categories = Project::find()
                ->select([
                    'project.id as id',
                    'categoryTitles.title as categoryTitle',
                    'SUM('.$isCompleted.') as completed',
                    'SUM('.$slippage.') as slippage',
                    'SUM('.$behindSchedule.') as behindSchedule',
                    'SUM('.$onSchedule.') as onSchedule',
                    'SUM('.$aheadOnSchedule.') as aheadOnSchedule',
                    'SUM('.$notYetStartedWithTarget.') as notYetStartedWithTarget',
                    'SUM('.$notYetStartedWithNoTarget.') as notYetStartedWithNoTarget',
                ]);
            $categories = $categories->leftJoin(['categoryTitles' => '('.$categoryTitles.')'], 'categoryTitles.project_id = project.id');
            $categories = $categories->leftJoin(['physicalAccompsQ1' => '('.$physicalAccomps.')'], 'physicalAccompsQ1.project_id = project.id and physicalAccompsQ1.quarter = "Q1"');
            $categories = $categories->leftJoin(['physicalAccompsQ2' => '('.$physicalAccomps.')'], 'physicalAccompsQ2.project_id = project.id and physicalAccompsQ2.quarter = "Q2"');
            $categories = $categories->leftJoin(['physicalAccompsQ3' => '('.$physicalAccomps.')'], 'physicalAccompsQ3.project_id = project.id and physicalAccompsQ3.quarter = "Q3"');
            $categories = $categories->leftJoin(['physicalAccompsQ4' => '('.$physicalAccomps.')'], 'physicalAccompsQ4.project_id = project.id and physicalAccompsQ4.quarter = "Q4"');
            $categories = $categories->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
            $categories = $categories->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');
            $categories = $categories->andWhere(['project.draft' => 'No']);

            if($year != '')
            {
                $sectors = $sectors->andWhere(['project.year' => $year]);
                $categories = $categories->andWhere(['project.year' => $year]);
            }

            $sectors = $sectors->groupBy(['sectorTitle']);
            $categories = $categories->groupBy(['categoryTitle']);

            $sectors = $sectors->asArray()->all();
            $categories = $categories->asArray()->all();


            return $this->renderAjax('_project-implementation', [
                'sectors' => $sectors,
                'categories' => $categories,
                'year' => $year,
                'quarter' => $quarter, 
            ]);
    }
    public function actionProjectImplementationData($year, $quarter)
    {
            $categoryTitles = ProjectCategory::find()
                ->select(['project_id', 'GROUP_CONCAT(DISTINCT category.code ORDER BY category.code ASC SEPARATOR ", ") as title'])
                ->leftJoin('category', 'category.id = project_category.category_id')
                ->leftJoin('project', 'project.id = project_category.project_id')
                ->where(['project.draft' => 'No'])
                ->groupBy(['project_category.project_id'])
                ->createCommand()->getRawSql();

            $physicalTargets = ProjectTarget::find()
                    ->andWhere(['target_type' => 'Physical']);

            $physicalAccomps = PhysicalAccomplishment::find();

            $accomps = Accomplishment::find()
                    ->select(['project_id', 'IF(sum(COALESCE(action, 0)) > 0, 1, 0) as isCompleted']);

            if($year != '')
            {
                $physicalTargets = $physicalTargets->andWhere(['year' => $year]);
                $physicalAccomps = $physicalAccomps->andWhere(['year' => $year]);
                $accomps = $accomps->andWhere(['year' => $year]);
            }

            $physicalTargets = $physicalTargets->createCommand()->getRawSql();
            $physicalAccomps = $physicalAccomps->createCommand()->getRawSql();
            $accomps = $accomps->groupBy(['project_id'])->createCommand()->getRawSql();

            $provinceIDs = ProjectProvince::find();
            $categoryIDs = ProjectCategory::find();
            
            $physicalTargetPerQuarter = 'IF("'.$quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                IF("'.$quarter.'" = "Q2", COALESCE(physicalTargets.q2, 0),
                    IF("'.$quarter.'" = "Q3", COALESCE(physicalTargets.q3, 0),
                    COALESCE(physicalTargets.q4, 0)
                    )
                )
            )';

            $physicalTargetTotalPerQuarter = 'IF(project.data_type = "Cumulative",
                        IF("'.$quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                            IF("'.$quarter.'" = "Q2", IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)),
                                IF("'.$quarter.'" = "Q3", IF(COALESCE(physicalTargets.q3, 0) = 0, IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)), COALESCE(physicalTargets.q3, 0)),
                                IF(COALESCE(physicalTargets.q4, 0) = 0, IF(COALESCE(physicalTargets.q3, 0) = 0, IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)), COALESCE(physicalTargets.q3, 0)), COALESCE(physicalTargets.q4, 0))
                                )
                            )
                        )
                    ,   
                    IF("'.$quarter.'" = "Q1", (COALESCE(physicalTargets.q1, 0) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                         IF("'.$quarter.'" = "Q2", ((COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                             IF("'.$quarter.'" = "Q3", ((COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
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
                                    IF("'.$quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                        IF("'.$quarter.'" = "Q2", IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)),
                                            IF("'.$quarter.'" = "Q3", IF(COALESCE(physicalAccompsQ3.value, 0) = 0, IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)), COALESCE(physicalAccompsQ3.value, 0)),
                                            IF(COALESCE(physicalAccompsQ4.value, 0) = 0, IF(COALESCE(physicalAccompsQ3.value, 0) = 0, IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)), COALESCE(physicalAccompsQ3.value, 0)), COALESCE(physicalAccompsQ4.value, 0))
                                            )
                                        )
                                    )
                                ,   
                                IF("'.$quarter.'" = "Q1", (COALESCE(physicalAccompsQ1.value, 0) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                                     IF("'.$quarter.'" = "Q2", ((COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                                         IF("'.$quarter.'" = "Q3", ((COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                                         ((COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0) + COALESCE(physicalAccompsQ4.value, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100
                                         )
                                     )
                                ) 
                                )';

            $physicalAccompPerQuarter = 'IF("'.$quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                            IF("'.$quarter.'" = "Q2", COALESCE(physicalAccompsQ2.value, 0),
                                                IF("'.$quarter.'" = "Q3", COALESCE(physicalAccompsQ3.value, 0),
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

            $sectors = Project::find()
                ->select([
                    'project.id as id',
                    'sector.title as sectorTitle',
                    'SUM('.$isCompleted.') as completed',
                    'SUM('.$slippage.') as slippage',
                    'SUM('.$behindSchedule.') as behindSchedule',
                    'SUM('.$onSchedule.') as onSchedule',
                    'SUM('.$aheadOnSchedule.') as aheadOnSchedule',
                    'SUM('.$notYetStartedWithTarget.') as notYetStartedWithTarget',
                    'SUM('.$notYetStartedWithNoTarget.') as notYetStartedWithNoTarget',
                ]);
            $sectors = $sectors->leftJoin('sector', 'sector.id = project.sector_id');
            $sectors = $sectors->leftJoin(['physicalAccompsQ1' => '('.$physicalAccomps.')'], 'physicalAccompsQ1.project_id = project.id and physicalAccompsQ1.quarter = "Q1"');
            $sectors = $sectors->leftJoin(['physicalAccompsQ2' => '('.$physicalAccomps.')'], 'physicalAccompsQ2.project_id = project.id and physicalAccompsQ2.quarter = "Q2"');
            $sectors = $sectors->leftJoin(['physicalAccompsQ3' => '('.$physicalAccomps.')'], 'physicalAccompsQ3.project_id = project.id and physicalAccompsQ3.quarter = "Q3"');
            $sectors = $sectors->leftJoin(['physicalAccompsQ4' => '('.$physicalAccomps.')'], 'physicalAccompsQ4.project_id = project.id and physicalAccompsQ4.quarter = "Q4"');
            $sectors = $sectors->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
            $sectors = $sectors->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');
            $sectors = $sectors->andWhere(['project.draft' => 'No']);
            
            $categories = Project::find()
                ->select([
                    'project.id as id',
                    'categoryTitles.title as categoryTitle',
                    'SUM('.$isCompleted.') as completed',
                    'SUM('.$slippage.') as slippage',
                    'SUM('.$behindSchedule.') as behindSchedule',
                    'SUM('.$onSchedule.') as onSchedule',
                    'SUM('.$aheadOnSchedule.') as aheadOnSchedule',
                    'SUM('.$notYetStartedWithTarget.') as notYetStartedWithTarget',
                    'SUM('.$notYetStartedWithNoTarget.') as notYetStartedWithNoTarget',
                ]);
            $categories = $categories->leftJoin(['categoryTitles' => '('.$categoryTitles.')'], 'categoryTitles.project_id = project.id');
            $categories = $categories->leftJoin(['physicalAccompsQ1' => '('.$physicalAccomps.')'], 'physicalAccompsQ1.project_id = project.id and physicalAccompsQ1.quarter = "Q1"');
            $categories = $categories->leftJoin(['physicalAccompsQ2' => '('.$physicalAccomps.')'], 'physicalAccompsQ2.project_id = project.id and physicalAccompsQ2.quarter = "Q2"');
            $categories = $categories->leftJoin(['physicalAccompsQ3' => '('.$physicalAccomps.')'], 'physicalAccompsQ3.project_id = project.id and physicalAccompsQ3.quarter = "Q3"');
            $categories = $categories->leftJoin(['physicalAccompsQ4' => '('.$physicalAccomps.')'], 'physicalAccompsQ4.project_id = project.id and physicalAccompsQ4.quarter = "Q4"');
            $categories = $categories->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
            $categories = $categories->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');
            $categories = $categories->andWhere(['project.draft' => 'No']);

            if($year != '')
            {
                $sectors = $sectors->andWhere(['project.year' => $year]);
                $categories = $categories->andWhere(['project.year' => $year]);
            }

            $sectors = $sectors->groupBy(['sectorTitle']);
            $categories = $categories->groupBy(['categoryTitle']);

            $sectors = $sectors->asArray()->all();
            $categories = $categories->asArray()->all();


            return $this->renderAjax('_project-implementation-data', [
                'sectors' => $sectors,
                'categories' => $categories,
                'year' => $year,
                'quarter' => $quarter, 
            ]);
    }

    public function actionBeneficiaries($year, $quarter)
    {
        $data = [];

        $accomplishment = BeneficiariesAccomplishment::find()
                        ->select([
                            'beneficiaries_accomplishment.project_id', 
                            'sum(COALESCE(male,0)) as maleTotal',
                            'sum(COALESCE(female, 0)) as femaleTotal'
                        ])
                        ->leftJoin('project', 'project.id = beneficiaries_accomplishment.project_id');

        if($year != '')
        {
            $accomplishment = $accomplishment->andWhere(['beneficiaries_accomplishment.year' => $year]);
        }

        if($quarter != '')
        {
            $accomplishment = $accomplishment->andWhere(['beneficiaries_accomplishment.quarter' => $quarter]);
        }
        
        $accomplishment = $accomplishment
        ->createCommand()
        ->getRawSql();

        $total = Project::find()
            ->select([
                'sum(COALESCE(accomplishment.maleTotal, 0)) as maleTotal',
                'sum(COALESCE(accomplishment.femaleTotal, 0)) as femaleTotal',
            ])
            ->leftJoin(['accomplishment' => '('.$accomplishment.')'], 'accomplishment.project_id = project.id')
            ->asArray()
            ->one();

        $data['male'] = intval($total['maleTotal']) + intval($total['femaleTotal']) > 0 ? number_format(intval($total['maleTotal']) / (intval($total['maleTotal']) + intval($total['femaleTotal'])) * 100, 0) : 0;
        $data['female'] = intval($total['maleTotal']) + intval($total['femaleTotal']) > 0 ? number_format(intval($total['femaleTotal']) / (intval($total['maleTotal']) + intval($total['femaleTotal'])) * 100, 0) : 0;

        $data['maleRaw'] = number_format(intval($total['maleTotal'], 0), 0);
        $data['femaleRaw'] = number_format(intval($total['femaleTotal'], 0), 0);

        return $this->renderAjax('_beneficiaries',[
            'data' => $data,
            'year' => $year,
            'quarter' => $quarter, 
        ]);
    }

    public function actionBeneficiariesData($year, $quarter)
    {
        $data = [];

        $accomplishment = BeneficiariesAccomplishment::find()
                        ->select([
                            'beneficiaries_accomplishment.project_id', 
                            'sum(COALESCE(male,0)) as maleTotal',
                            'sum(COALESCE(female, 0)) as femaleTotal'
                        ])
                        ->leftJoin('project', 'project.id = beneficiaries_accomplishment.project_id');

        if($year != '')
        {
            $accomplishment = $accomplishment->andWhere(['beneficiaries_accomplishment.year' => $year]);
        }

        if($quarter != '')
        {
            $accomplishment = $accomplishment->andWhere(['beneficiaries_accomplishment.quarter' => $quarter]);
        }
        
        $accomplishment = $accomplishment
        ->createCommand()
        ->getRawSql();

        $total = Project::find()
            ->select([
                'sum(COALESCE(accomplishment.maleTotal, 0)) as maleTotal',
                'sum(COALESCE(accomplishment.femaleTotal, 0)) as femaleTotal',
            ])
            ->leftJoin(['accomplishment' => '('.$accomplishment.')'], 'accomplishment.project_id = project.id')
            ->asArray()
            ->one();

        return $this->renderAjax('_beneficiaries-data',[
            'total' => $total,
        ]);
    }

    public function actionPhysical($year, $quarter)
    {
            $categoryTitles = ProjectCategory::find()
                ->select(['project_id', 'GROUP_CONCAT(DISTINCT category.code ORDER BY category.code ASC SEPARATOR ", ") as title'])
                ->leftJoin('category', 'category.id = project_category.category_id')
                ->leftJoin('project', 'project.id = project_category.project_id')
                ->where(['project.draft' => 'No'])
                ->groupBy(['project_category.project_id'])
                ->createCommand()->getRawSql();

            $physicalTargets = ProjectTarget::find()->andWhere(['target_type' => 'Physical']);
            $physicalAccomps = PhysicalAccomplishment::find();
            $accomps = Accomplishment::find()->select(['project_id', 'IF(sum(COALESCE(action, 0)) > 0, 1, 0) as isCompleted']);

            if($year != '')
            {
                $physicalTargets = $physicalTargets->andWhere(['year' => $year]);
                $physicalAccomps = $physicalAccomps->andWhere(['year' => $year]);
                $accomps = $accomps->andWhere(['year' => $year]);
            }

            $physicalTargets = $physicalTargets->createCommand()->getRawSql();
            $physicalAccomps = $physicalAccomps->createCommand()->getRawSql();
            $accomps = $accomps->groupBy(['project_id'])->createCommand()->getRawSql();

            $provinceIDs = ProjectProvince::find();
            $categoryIDs = ProjectCategory::find();
            
            $physicalTargetPerQuarter = 'IF("'.$quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                IF("'.$quarter.'" = "Q2", COALESCE(physicalTargets.q2, 0),
                    IF("'.$quarter.'" = "Q3", COALESCE(physicalTargets.q3, 0),
                    COALESCE(physicalTargets.q4, 0)
                    )
                )
            )';

            $physicalTargetTotalPerQuarter = 'IF(project.data_type = "Cumulative",
                        IF("'.$quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                            IF("'.$quarter.'" = "Q2", IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)),
                                IF("'.$quarter.'" = "Q3", IF(COALESCE(physicalTargets.q3, 0) = 0, IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)), COALESCE(physicalTargets.q3, 0)),
                                IF(COALESCE(physicalTargets.q4, 0) = 0, IF(COALESCE(physicalTargets.q3, 0) = 0, IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)), COALESCE(physicalTargets.q3, 0)), COALESCE(physicalTargets.q4, 0))
                                )
                            )
                        )
                    ,   
                    IF("'.$quarter.'" = "Q1", (COALESCE(physicalTargets.q1, 0) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                         IF("'.$quarter.'" = "Q2", ((COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                             IF("'.$quarter.'" = "Q3", ((COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
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
                                    IF("'.$quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                        IF("'.$quarter.'" = "Q2", IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)),
                                            IF("'.$quarter.'" = "Q3", IF(COALESCE(physicalAccompsQ3.value, 0) = 0, IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)), COALESCE(physicalAccompsQ3.value, 0)),
                                            IF(COALESCE(physicalAccompsQ4.value, 0) = 0, IF(COALESCE(physicalAccompsQ3.value, 0) = 0, IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)), COALESCE(physicalAccompsQ3.value, 0)), COALESCE(physicalAccompsQ4.value, 0))
                                            )
                                        )
                                    )
                                ,   
                                IF("'.$quarter.'" = "Q1", (COALESCE(physicalAccompsQ1.value, 0) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                                     IF("'.$quarter.'" = "Q2", ((COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                                         IF("'.$quarter.'" = "Q3", ((COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                                         ((COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0) + COALESCE(physicalAccompsQ4.value, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100
                                         )
                                     )
                                ) 
                                )';

            $physicalAccompPerQuarter = 'IF("'.$quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                            IF("'.$quarter.'" = "Q2", COALESCE(physicalAccompsQ2.value, 0),
                                                IF("'.$quarter.'" = "Q3", COALESCE(physicalAccompsQ3.value, 0),
                                                COALESCE(physicalAccompsQ4.value, 0)
                                                )
                                            )
                                        )';

            $sectors = Project::find()
                ->select([
                    'project.id as id',
                    'sector.title as sectorTitle',
                    'SUM('.$physicalAccompTotalPerQuarter.') as physicalAccompTotalPerQuarter',
                    'SUM('.$physicalTargetTotalPerQuarter.') as physicalTargetTotalPerQuarter',
                ]);
            $sectors = $sectors->leftJoin('sector', 'sector.id = project.sector_id');
            $sectors = $sectors->leftJoin(['physicalAccompsQ1' => '('.$physicalAccomps.')'], 'physicalAccompsQ1.project_id = project.id and physicalAccompsQ1.quarter = "Q1"');
            $sectors = $sectors->leftJoin(['physicalAccompsQ2' => '('.$physicalAccomps.')'], 'physicalAccompsQ2.project_id = project.id and physicalAccompsQ2.quarter = "Q2"');
            $sectors = $sectors->leftJoin(['physicalAccompsQ3' => '('.$physicalAccomps.')'], 'physicalAccompsQ3.project_id = project.id and physicalAccompsQ3.quarter = "Q3"');
            $sectors = $sectors->leftJoin(['physicalAccompsQ4' => '('.$physicalAccomps.')'], 'physicalAccompsQ4.project_id = project.id and physicalAccompsQ4.quarter = "Q4"');
            $sectors = $sectors->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
            $sectors = $sectors->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');
            $sectors = $sectors->andWhere(['project.draft' => 'No']);

            if($year != '')
            {
                $sectors = $sectors->andWhere(['project.year' => $year]);
            }

            $sectors = $sectors->groupBy(['sectorTitle']);

            $sectors = $sectors->asArray()->all();


            return $this->renderAjax('_physical', [
                'sectors' => $sectors,
                'year' => $year,
                'quarter' => $quarter, 
            ]);
    }
    public function actionPhysicalData($year, $quarter)
    {
            $categoryTitles = ProjectCategory::find()
                ->select(['project_id', 'GROUP_CONCAT(DISTINCT category.code ORDER BY category.code ASC SEPARATOR ", ") as title'])
                ->leftJoin('category', 'category.id = project_category.category_id')
                ->leftJoin('project', 'project.id = project_category.project_id')
                ->where(['project.draft' => 'No'])
                ->groupBy(['project_category.project_id'])
                ->createCommand()->getRawSql();

            $physicalTargets = ProjectTarget::find()->andWhere(['target_type' => 'Physical']);
            $physicalAccomps = PhysicalAccomplishment::find();
            $accomps = Accomplishment::find()->select(['project_id', 'IF(sum(COALESCE(action, 0)) > 0, 1, 0) as isCompleted']);

            if($year != '')
            {
                $physicalTargets = $physicalTargets->andWhere(['year' => $year]);
                $physicalAccomps = $physicalAccomps->andWhere(['year' => $year]);
                $accomps = $accomps->andWhere(['year' => $year]);
            }

            $physicalTargets = $physicalTargets->createCommand()->getRawSql();
            $physicalAccomps = $physicalAccomps->createCommand()->getRawSql();
            $accomps = $accomps->groupBy(['project_id'])->createCommand()->getRawSql();

            $provinceIDs = ProjectProvince::find();
            $categoryIDs = ProjectCategory::find();
            
            $physicalTargetPerQuarter = 'IF("'.$quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                IF("'.$quarter.'" = "Q2", COALESCE(physicalTargets.q2, 0),
                    IF("'.$quarter.'" = "Q3", COALESCE(physicalTargets.q3, 0),
                    COALESCE(physicalTargets.q4, 0)
                    )
                )
            )';

            $physicalTargetTotalPerQuarter = 'IF(project.data_type = "Cumulative",
                        IF("'.$quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                            IF("'.$quarter.'" = "Q2", IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)),
                                IF("'.$quarter.'" = "Q3", IF(COALESCE(physicalTargets.q3, 0) = 0, IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)), COALESCE(physicalTargets.q3, 0)),
                                IF(COALESCE(physicalTargets.q4, 0) = 0, IF(COALESCE(physicalTargets.q3, 0) = 0, IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)), COALESCE(physicalTargets.q3, 0)), COALESCE(physicalTargets.q4, 0))
                                )
                            )
                        )
                    ,   
                    IF("'.$quarter.'" = "Q1", (COALESCE(physicalTargets.q1, 0) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                         IF("'.$quarter.'" = "Q2", ((COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                             IF("'.$quarter.'" = "Q3", ((COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
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
                                    IF("'.$quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                        IF("'.$quarter.'" = "Q2", IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)),
                                            IF("'.$quarter.'" = "Q3", IF(COALESCE(physicalAccompsQ3.value, 0) = 0, IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)), COALESCE(physicalAccompsQ3.value, 0)),
                                            IF(COALESCE(physicalAccompsQ4.value, 0) = 0, IF(COALESCE(physicalAccompsQ3.value, 0) = 0, IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)), COALESCE(physicalAccompsQ3.value, 0)), COALESCE(physicalAccompsQ4.value, 0))
                                            )
                                        )
                                    )
                                ,   
                                IF("'.$quarter.'" = "Q1", (COALESCE(physicalAccompsQ1.value, 0) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                                     IF("'.$quarter.'" = "Q2", ((COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                                         IF("'.$quarter.'" = "Q3", ((COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100,
                                         ((COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0) + COALESCE(physicalAccompsQ4.value, 0)) / (COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0))) * 100
                                         )
                                     )
                                ) 
                                )';

            $physicalAccompPerQuarter = 'IF("'.$quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                            IF("'.$quarter.'" = "Q2", COALESCE(physicalAccompsQ2.value, 0),
                                                IF("'.$quarter.'" = "Q3", COALESCE(physicalAccompsQ3.value, 0),
                                                COALESCE(physicalAccompsQ4.value, 0)
                                                )
                                            )
                                        )';

            $sectors = Project::find()
                ->select([
                    'project.id as id',
                    'sector.title as sectorTitle',
                    'SUM('.$physicalAccompTotalPerQuarter.') as physicalAccompTotalPerQuarter',
                    'SUM('.$physicalTargetTotalPerQuarter.') as physicalTargetTotalPerQuarter',
                ]);
            $sectors = $sectors->leftJoin('sector', 'sector.id = project.sector_id');
            $sectors = $sectors->leftJoin(['physicalAccompsQ1' => '('.$physicalAccomps.')'], 'physicalAccompsQ1.project_id = project.id and physicalAccompsQ1.quarter = "Q1"');
            $sectors = $sectors->leftJoin(['physicalAccompsQ2' => '('.$physicalAccomps.')'], 'physicalAccompsQ2.project_id = project.id and physicalAccompsQ2.quarter = "Q2"');
            $sectors = $sectors->leftJoin(['physicalAccompsQ3' => '('.$physicalAccomps.')'], 'physicalAccompsQ3.project_id = project.id and physicalAccompsQ3.quarter = "Q3"');
            $sectors = $sectors->leftJoin(['physicalAccompsQ4' => '('.$physicalAccomps.')'], 'physicalAccompsQ4.project_id = project.id and physicalAccompsQ4.quarter = "Q4"');
            $sectors = $sectors->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
            $sectors = $sectors->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');
            $sectors = $sectors->andWhere(['project.draft' => 'No']);

            if($year != '')
            {
                $sectors = $sectors->andWhere(['project.year' => $year]);
            }

            $sectors = $sectors->groupBy(['sectorTitle']);

            $sectors = $sectors->asArray()->all();


            return $this->renderAjax('_physical-data', [
                'sectors' => $sectors,
                'year' => $year,
                'quarter' => $quarter, 
            ]);
    }

    public function actionImageSlider($year, $quarter)
    {
        $all_files = glob('../../frontend/web/slider/*.*');

        $images = [];

        for ($i=0; $i<count($all_files); $i++)
        {
        $image_name = $all_files[$i];
        $supported_format = array('gif','jpg','jpeg','png');
        $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        if (in_array($ext, $supported_format))
            {
                $image_name = substr($image_name, 3, strlen($image_name) - 1);
                $images[] = Html::img($image_name);
            }
        }
        if($year == '')
        {
            $year = date("Y");
        }
        if($quarter)
        {
            $quarter = 'Q1';
        }
        

        return $this->renderAjax('_slider', [
            'images' => $images,
            'year' => $year,
            'quarter' => $quarter
        ]);
    }
}
