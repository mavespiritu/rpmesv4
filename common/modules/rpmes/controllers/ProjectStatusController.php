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

        if($model->load(Yii::$app->request->get()))
        {
            $financialTargets = ProjectTarget::find()->where(['target_type' => 'Financial', 'year' => $model->year])->createCommand()->getRawSql();
            $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $model->year])->createCommand()->getRawSql();
            $financialAccomps = FinancialAccomplishment::find()->where(['year' => $model->year])->createCommand()->getRawSql();
            $physicalAccomps = PhysicalAccomplishment::find()->where(['year' => $model->year])->createCommand()->getRawSql();
            $accomps = Accomplishment::find()->select(['project_id', 'IF(sum(COALESCE(action, 0)) > 0, 1, 0) as isCompleted'])->where(['year' => $model->year])->groupBy(['project_id'])->createCommand()->getRawSql();
            
            $projectIDs = Plan::find()->select(['project_id'])->where(['year' => $model->year])->asArray()->all();
            $projectIDs = ArrayHelper::map($projectIDs, 'project_id', 'project_id');

            $regionIDs = ProjectRegion::find();
            $provinceIDs = ProjectProvince::find();
            $categoryIDs = ProjectCategory::find();

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
            
            $isPercent = 'LOCATE("%", physicalTargets.indicator)';
            
            $physicalTargetPerQuarter = 'IF("'.$model->quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                                                IF("'.$model->quarter.'" = "Q2", COALESCE(physicalTargets.q2, 0),
                                                    IF("'.$model->quarter.'" = "Q3", COALESCE(physicalTargets.q3, 0),
                                                    COALESCE(physicalTargets.q4, 0)
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
            
            $financialTargetPerQuarter = 'IF("'.$model->quarter.'" = "Q1", COALESCE(financialTargets.q1, 0),
                                            IF("'.$model->quarter.'" = "Q2", COALESCE(financialTargets.q2, 0),
                                                IF("'.$model->quarter.'" = "Q3", COALESCE(financialTargets.q3, 0),
                                                COALESCE(financialTargets.q4, 0)
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
                                                IF("'.$model->quarter.'" = "Q1", financialTargets.q1,
                                                    IF("'.$model->quarter.'" = "Q2", financialTargets.q2,
                                                        IF("'.$model->quarter.'" = "Q3", financialTargets.q3,
                                                            financialTargets.q4
                                                        )
                                                    )
                                                )
                                            )'; 

            $financialTargetTotal = 'IF(project.data_type = "Cumulative",
                                        IF(COALESCE(financialTargets.q4, 0) <= 0,
                                            IF(COALESCE(financialTargets.q3, 0) <= 0,
                                                IF(COALESCE(financialTargets.q2, 0) <= 0,
                                                    COALESCE(financialTargets.q1, 0)
                                                , COALESCE(financialTargets.q2, 0))
                                            , COALESCE(financialTargets.q3, 0))
                                        , COALESCE(financialTargets.q4, 0))
                                    ,   
                                        COALESCE(financialTargets.q1, 0) +
                                        COALESCE(financialTargets.q2, 0) +
                                        COALESCE(financialTargets.q3, 0) +
                                        COALESCE(financialTargets.q4, 0)
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

            $physicalAccompPerQuarter = 'IF("'.$model->quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                            IF("'.$model->quarter.'" = "Q2", COALESCE(physicalAccompsQ2.value, 0),
                                                IF("'.$model->quarter.'" = "Q3", COALESCE(physicalAccompsQ3.value, 0),
                                                COALESCE(physicalAccompsQ4.value, 0)
                                                )
                                            )
                                        )';

            $isCompleted = 'COALESCE(accomps.isCompleted, 0)';
            $slippage = 'IF('.$isPercent.' > 0, '.$physicalAccompPerQuarter.' - '.$physicalTargetPerQuarter.', IF('.$physicalTargetPerQuarter.' > 0, (('.$physicalAccompPerQuarter.'/'.$physicalTargetPerQuarter.') * 100) -100 , 0))';
            $behindSchedule = 'IF('.$isCompleted.' = 0, IF('.$physicalAccompPerQuarter.' > 0, IF('.$slippage.' < 0, 1 , 0), 0), 0)';
            $onSchedule = 'IF('.$isCompleted.' = 0, IF('.$physicalAccompPerQuarter.' > 0, IF('.$slippage.' = 0, 1 , 0), 0), 0)';
            $aheadOnSchedule = 'IF('.$isCompleted.' = 0, IF('.$physicalAccompPerQuarter.' > 0, IF('.$slippage.' > 0, 1 , 0), 0), 0)';

            $regionIDs = $regionIDs->all();
            $regionIDs = ArrayHelper::map($regionIDs, 'project_id', 'project_id');

            $provinceIDs = $provinceIDs->all();
            $provinceIDs = ArrayHelper::map($provinceIDs, 'project_id', 'project_id');

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
                            'IF(provinceTitles.title is null, IF(regionTitles.title is null, "No location", regionTitles.title), provinceTitles.title) as provinceTitle',
                            'physicalTargets.indicator as indicator',
                            'SUM('.$financialTargetPerQuarter.') as allocations',
                            'SUM('.$releases.') as releases',
                            'SUM('.$obligations.') as obligations',
                            'SUM('.$expenditures.') as expenditures',
                            'SUM('.$physicalTargetTotalPerQuarter.') as projectPhysicalTarget',
                            'SUM('.$physicalAccompTotalPerQuarter.') as projectPhysicalAccomp',
                            'SUM('.$isCompleted.') as completed',
                            'SUM('.$slippage.') as slippage',
                            'SUM('.$behindSchedule.') as behindSchedule',
                            'SUM('.$onSchedule.') as onSchedule',
                            'SUM('.$aheadOnSchedule.') as aheadOnSchedule',
                        ]);
            $projects = $projects->leftJoin(['financialTargets' => '('.$financialTargets.')'], 'financialTargets.project_id = project.id');
            $projects = $projects->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
            $projects = $projects->leftJoin(['financialAccompsQ1' => '('.$financialAccomps.')'], 'financialAccompsQ1.project_id = project.id and financialAccompsQ1.quarter = "Q1"');
            $projects = $projects->leftJoin(['financialAccompsQ2' => '('.$financialAccomps.')'], 'financialAccompsQ2.project_id = project.id and financialAccompsQ2.quarter = "Q2"');
            $projects = $projects->leftJoin(['financialAccompsQ3' => '('.$financialAccomps.')'], 'financialAccompsQ3.project_id = project.id and financialAccompsQ3.quarter = "Q3"');
            $projects = $projects->leftJoin(['financialAccompsQ4' => '('.$financialAccomps.')'], 'financialAccompsQ4.project_id = project.id and financialAccompsQ4.quarter = "Q4"');
            $projects = $projects->leftJoin(['physicalAccompsQ1' => '('.$physicalAccomps.')'], 'physicalAccompsQ1.project_id = project.id and physicalAccompsQ1.quarter = "Q1"');
            $projects = $projects->leftJoin(['physicalAccompsQ2' => '('.$physicalAccomps.')'], 'physicalAccompsQ2.project_id = project.id and physicalAccompsQ2.quarter = "Q2"');
            $projects = $projects->leftJoin(['physicalAccompsQ3' => '('.$physicalAccomps.')'], 'physicalAccompsQ3.project_id = project.id and physicalAccompsQ3.quarter = "Q3"');
            $projects = $projects->leftJoin(['physicalAccompsQ4' => '('.$physicalAccomps.')'], 'physicalAccompsQ4.project_id = project.id and physicalAccompsQ4.quarter = "Q4"');
            $projects = $projects->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');
            $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
            $projects = $projects->leftJoin('program', 'program.id = project.program_id');
            $projects = $projects->leftJoin('sector', 'sector.id = project.sector_id');
            $projects = $projects->leftJoin('sub_sector', 'sub_sector.id = project.sub_sector_id');
            $projects = $projects->leftJoin('fund_source', 'fund_source.id = project.fund_source_id');
            $projects = $projects->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id');
            $projects = $projects->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
            $projects = $projects->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id');
            $projects = $projects->leftJoin(['barangayTitles' => '('.$barangayTitles.')'], 'barangayTitles.project_id = project.id');
            $projects = $projects->andWhere(['project.year' => $model->year, 'project.draft' => 'No']);
            $projects = $projects->andWhere(['project.id' => $projectIDs]);

            $projects = $projects->asArray()->all();

            echo '<pre>'; print_r($projects); exit;

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

    /**
     * Displays a single Project model.
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
     * Creates a new Project model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Project();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', 'Record Saved');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Project model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', 'Record Updated');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Project model.
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
     * Finds the Project model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Project the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Project::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
