<?php

namespace common\modules\rpmes\controllers;

use Yii;
use common\modules\rpmes\models\ProjectProblemSolvingSession;
use common\modules\rpmes\models\ProjectProblemSolvingSessionSearch;
use common\modules\rpmes\models\Project;
use common\modules\rpmes\models\Agency;
use common\modules\rpmes\models\Sector;
use common\modules\rpmes\models\ProjectRegion;
use common\modules\rpmes\models\ProjectProvince;
use common\modules\rpmes\models\ProjectTarget;
use common\modules\rpmes\models\ProjectException;
use common\modules\rpmes\models\ProjectCitymun;
use common\modules\rpmes\models\ProjectBarangay;
use common\modules\rpmes\models\PhysicalAccomplishment;
use common\modules\rpmes\models\Accomplishment;
use common\models\Region;
use common\models\Province;
use common\models\Citymun;
use common\models\Barangay;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * ProjectProblemSolvingSessionController implements the CRUD actions for ProjectProblemSolvingSession model.
 */
class ProjectProblemSolvingSessionController extends Controller
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
                        'roles' => ['AgencyUser', 'Administrator', 'SuperAdministrator'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all ProjectProblemSolvingSession models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new ProjectProblemSolvingSession();

        $model->year = date("Y");
        $model->scenario = 'projectProblemSolvingSession';

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
            $projectIDs = ProjectProblemSolvingSession::find()->select(['project_id'])->where(['year' => $model->year, 'quarter' => $model->quarter])->asArray()->all();
            $projectIDs = ArrayHelper::map($projectIDs, 'project_id', 'project_id');

            $physicalAccomps = PhysicalAccomplishment::find()->where(['year' => $model->year])->createCommand()->getRawSql();
            $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $model->year])->createCommand()->getRawSql();
            $financials = ProjectTarget::find()->where(['target_type' => 'Financial', 'year' => $model->year])->createCommand()->getRawSql();
            $causes = ProjectException::find()->select(['project_id', 'causes'])->where(['year' => $model->year, 'quarter' => $model->quarter])->groupBy(['project_id'])->createCommand()->getRawSql();
        
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
                
            $physicalAccompTotalPerQuarter = 'IF("'.$model->quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                                IF("'.$model->quarter.'" = "Q2", COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0),
                                                    IF("'.$model->quarter.'" = "Q3", COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0),
                                                        COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0) + COALESCE(physicalAccompsQ4.value, 0)
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
            $slippage = 'IF('.$isPercent.' > 0, '.$physicalAccompTotalPerQuarter.' - '.$physicalTargetTotalPerQuarter.', IF('.$physicalTargetTotalPerQuarter.' > 0, (('.$physicalAccompTotalPerQuarter.'/'.$physicalTargetTotalPerQuarter.') * 100) -100 , 0))';

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
                            'project.id as id',
                            'project.title as projectTitle',
                            'project.data_type as dataType',
                            'sector.title as sectorTitle',
                            'sub_sector.title as subSectorTitle',
                            'IF(barangayTitles.title is null, IF(citymunTitles.title is null, IF(provinceTitles.title is null, IF(regionTitles.title is null, "No location", regionTitles.title), provinceTitles.title), citymunTitles.title), barangayTitles.title) as locationTitle',
                            'agency.code as agencyTitle',
                            'fund_source.title as fundSourceTitle',
                            'COALESCE('.$financialTotal.', 0) as totalCost',
                            $physicalTargetTotalPerQuarter. 'as physicalTargetTotalPerQuarter',
                            $physicalAccompTotalPerQuarter. 'as physicalAccompTotalPerQuarter',
                            $slippage. 'as slippage',
                            'causes.causes as cause',
                            'project_problem_solving_session.id as pssId',
                            'project_problem_solving_session.pss_date as pssDate',
                            'project_problem_solving_session.agreement_reached as agreementReached',
                            'project_problem_solving_session.next_step as nextStep',
                            'regionTitles.title as regionTitles',
                            'IF(project_exception.recommendations is null, "No Recommendation/s", project_exception.recommendations) as recommendations',
                            'IF(project_exception.causes is null, "No Issue/s", project_exception.causes) as causes',                
                        ]);
            $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
            $projects = $projects->leftJoin('project_problem_solving_session', 'project_problem_solving_session.project_id = project.id');
            $projects = $projects->leftJoin('program', 'program.id = project.program_id');
            $projects = $projects->leftJoin('sector', 'sector.id = project.sector_id');
            $projects = $projects->leftJoin('sub_sector', 'sub_sector.id = project.sub_sector_id');
            $projects = $projects->leftJoin('fund_source', 'fund_source.id = project.fund_source_id');
            $projects = $projects->leftJoin('project_exception', 'project_exception.project_id = project.id');
            $projects = $projects->leftJoin('project_region', 'project_region.project_id = project.id and project_region.year = project.year');
            $projects = $projects->leftJoin('tblregion', 'tblregion.region_c = project_region.region_id');
            $projects = $projects->leftJoin('project_province', 'project_province.project_id = project.id and project_province.year = project.year');
            $projects = $projects->leftJoin('tblprovince', 'tblprovince.province_c = project_province.province_id');
            $projects = $projects->leftJoin(['causes' => '('.$causes.')'], 'causes.project_id = project.id');;
            $projects = $projects->leftJoin(['physicalAccompsQ1' => '('.$physicalAccomps.')'], 'physicalAccompsQ1.project_id = project.id and physicalAccompsQ1.quarter = "Q1"');
            $projects = $projects->leftJoin(['physicalAccompsQ2' => '('.$physicalAccomps.')'], 'physicalAccompsQ2.project_id = project.id and physicalAccompsQ2.quarter = "Q2"');
            $projects = $projects->leftJoin(['physicalAccompsQ3' => '('.$physicalAccomps.')'], 'physicalAccompsQ3.project_id = project.id and physicalAccompsQ3.quarter = "Q3"');
            $projects = $projects->leftJoin(['physicalAccompsQ4' => '('.$physicalAccomps.')'], 'physicalAccompsQ4.project_id = project.id and physicalAccompsQ4.quarter = "Q4"');
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

            return $this->renderAjax('_report', [
                'model' => $model,
                'projects' => $projects
            ]);
        }
        return $this->render('index', [
            'model' => $model,
            'quarters' => $quarters,
            'agencies' => $agencies,
            'sectors' => $sectors,
            'regions' => $regions,
            'provinces' => $provinces,
            'years' => $years
        ]);
    }

    /**
     * Displays a single ProjectProblemSolvingSession model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ProjectProblemSolvingSession model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProjectProblemSolvingSession();

        $projects = Project::find()->select(['project.id','CONCAT(agency.code,'.'": ",'.'project.title) as title','agency.code']);
        $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
        $projects = $projects->andWhere(['project.draft' => 'No']);
        $projects = $projects->orderBy(['agency.code' => SORT_ASC])->asArray()->all();
        $projects = ArrayHelper::map($projects, 'id', 'title');

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];

        $years = Project::find()->select(['distinct(year) as year'])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');

        $agencies = Agency::find()->select(['id', 'code as title']);
        $agencies = Yii::$app->user->can('AgencyUser') ? $agencies->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $agencies;
        $agencies = $agencies->orderBy(['code' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $sectors = Sector::find()->all();
        $sectors = ArrayHelper::map($sectors, 'id', 'title');

        $regions = Region::find()->orderBy(['region_sort' => SORT_ASC])->all();
        $regions = ArrayHelper::map($regions, 'region_c', 'abbreviation');

        $provinces = [];

        if ($model->load(Yii::$app->request->post())) {

            $physicalAccomps = PhysicalAccomplishment::find()->where(['year' => $model->year])->createCommand()->getRawSql();
            $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $model->year])->createCommand()->getRawSql();

            $physicalAccompTotalPerQuarter = 'IF("'.$model->quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                                IF("'.$model->quarter.'" = "Q2", COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0),
                                                    IF("'.$model->quarter.'" = "Q3", COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0),
                                                        COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0) + COALESCE(physicalAccompsQ4.value, 0)
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

            $isPercent = 'LOCATE("%", physicalTargets.indicator)';
            $slippage = 'IF('.$isPercent.' > 0, '.$physicalAccompTotalPerQuarter.' - '.$physicalTargetTotalPerQuarter.', IF('.$physicalTargetTotalPerQuarter.' > 0, (('.$physicalAccompTotalPerQuarter.'/'.$physicalTargetTotalPerQuarter.') * 100) -100 , 0))';

            $projects = Project::find()
            ->select([
                'project.id as id',
                'project.title as projectTitle',
                'project.data_type as dataType',
                $physicalTargetTotalPerQuarter. 'as physicalTargetTotalPerQuarter',
                $physicalAccompTotalPerQuarter. 'as physicalAccompTotalPerQuarter',
                $slippage. 'as slippage',            
            ]);
            $projects = $projects->leftJoin(['physicalAccompsQ1' => '('.$physicalAccomps.')'], 'physicalAccompsQ1.project_id = project.id and physicalAccompsQ1.quarter = "Q1"');
            $projects = $projects->leftJoin(['physicalAccompsQ2' => '('.$physicalAccomps.')'], 'physicalAccompsQ2.project_id = project.id and physicalAccompsQ2.quarter = "Q2"');
            $projects = $projects->leftJoin(['physicalAccompsQ3' => '('.$physicalAccomps.')'], 'physicalAccompsQ3.project_id = project.id and physicalAccompsQ3.quarter = "Q3"');
            $projects = $projects->leftJoin(['physicalAccompsQ4' => '('.$physicalAccomps.')'], 'physicalAccompsQ4.project_id = project.id and physicalAccompsQ4.quarter = "Q4"');
            $projects = $projects->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
            $projects = $projects->andWhere(['project.id' => $model->project_id]);

            $projects = $projects->asArray()->all();

            //echo '<pre>'; print_r($projects); exit;

            $i=0;
            foreach($projects as $project){

                $model->slippage = number_format($project['slippage'], 2);
                    $i++;
                if ($i = 1){ $i=0; break; }
            }

            $model->submitted_by = Yii::$app->user->id;
            $model->date_submitted = date('Y-m-d H:i:s');
            $model->save();
            \Yii::$app->getSession()->setFlash('success', 'Record Saved');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'projects' => $projects,
            'quarters' => $quarters,
            'agencies' => $agencies,
            'sectors' => $sectors,
            'regions' => $regions,
            'provinces' => $provinces,
            'years' => $years
        ]);
    }

    /**
     * Updates an existing ProjectProblemSolvingSession model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $projects = Project::find()->select(['project.id','CONCAT(agency.code,'.'": ",'.'project.title) as title','agency.code']);
        $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
        $projects = $projects->andWhere(['project.draft' => 'No']);
        $projects = $projects->orderBy(['agency.code' => SORT_ASC])->asArray()->all();
        $projects = ArrayHelper::map($projects, 'id', 'title');

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];

        $years = Project::find()->select(['distinct(year) as year'])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');

        $agencies = Agency::find()->select(['id', 'code as title']);
        $agencies = Yii::$app->user->can('AgencyUser') ? $agencies->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $agencies;
        $agencies = $agencies->orderBy(['code' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $sectors = Sector::find()->all();
        $sectors = ArrayHelper::map($sectors, 'id', 'title');

        $regions = Region::find()->orderBy(['region_sort' => SORT_ASC])->all();
        $regions = ArrayHelper::map($regions, 'region_c', 'abbreviation');

        $provinces = [];

        if ($model->load(Yii::$app->request->post())) {
            \Yii::$app->getSession()->setFlash('success', 'Record Updated');
            $model->save();
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'projects' => $projects,
            'quarters' => $quarters,
            'agencies' => $agencies,
            'sectors' => $sectors,
            'regions' => $regions,
            'provinces' => $provinces,
            'years' => $years
        ]);
    }

    /**
     * Deletes an existing ProjectProblemSolvingSession model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \Yii::$app->getSession()->setFlash('success', 'Record Deleted');
        return $this->redirect(['index']);
    }

    /**
     * Finds the ProjectProblemSolvingSession model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProjectProblemSolvingSession the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProjectProblemSolvingSession::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
