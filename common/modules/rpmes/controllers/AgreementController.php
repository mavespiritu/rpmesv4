<?php

namespace common\modules\rpmes\controllers;

use Yii;
use common\models\Region;
use common\models\Province;
use common\models\Citymun;
use common\models\Barangay;
use common\modules\rpmes\models\Agreement;
use common\modules\rpmes\models\AgreementSearch;
use common\modules\rpmes\models\Project;
use common\modules\rpmes\models\Submission;
use common\modules\rpmes\models\Agency;
use common\modules\rpmes\models\Sector;
use common\modules\rpmes\models\ProjectRegion;
use common\modules\rpmes\models\ProjectTarget;
use common\modules\rpmes\models\PhysicalAccomplishment;
use common\modules\rpmes\models\FinancialAccomplishment;
use common\modules\rpmes\models\PersonEmployedAccomplishment;
use common\modules\rpmes\models\BeneficiariesAccomplishment;
use common\modules\rpmes\models\Accomplishment;
use common\modules\rpmes\models\Plan;
use common\modules\rpmes\models\ProjectProvince;
use common\modules\rpmes\models\ProjectCityMun;
use common\modules\rpmes\models\ProjectBarangay;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * AgreementController implements the CRUD actions for Agreement model.
 */
class AgreementController extends Controller
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
     * Lists all Agreement models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Submission();
        $model->year = date("Y");

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
            $financials = ProjectTarget::find()->where(['target_type' => 'Financial', 'year' => $model['year']])->createCommand()->getRawSql();
        
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

        $agreements = Agreement::find()
                    ->select([
                        'agreement.id as id',
                        'agreement.quarter',
                        'agreement.year',
                        'agreement.project_id',
                        'agreement.date_of_pss',
                        'agreement.agreement_reached',
                        'agreement.next_step',
                        'project.title as projectTitle',
                        'sector.title as sectorTitle',
                        'sub_sector.title as subSectorTitle',
                        'agency.code as agencyCode',
                        'IF(barangayTitles.title is null, IF(citymunTitles.title is null, IF(provinceTitles.title is null, IF(regionTitles.title is null, "No location", regionTitles.title), provinceTitles.title), citymunTitles.title), barangayTitles.title) as locationTitle',
                        'COALESCE('.$financialTotal.', 0) as totalCost'
                    ])
                    ->leftJoin('project','project.id= agreement.project_id')
                    ->leftJoin('sector','sector.id= project.sector_id')
                    ->leftJoin('sub_sector','sub_sector.id= project.sub_sector_id')
                    ->leftJoin('agency','agency.id= project.agency_id')
                    ->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id')
                    ->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id')
                    ->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id')
                    ->leftJoin(['barangayTitles' => '('.$barangayTitles.')'], 'barangayTitles.project_id = project.id') 
                    ;

        $agreements = $agreements->leftJoin(['financials' => '('.$financials.')'], 'financials.project_id = agreement.project_id');
        $agreements = $agreements->orderBy(['agreement.agreement_reached' => SORT_ASC])->asArray()->all();
            return $this->renderAjax('_form-table', [
                'agreements' => $agreements,
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

    /**
     * Displays a single Agreement model.
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
     * Creates a new Agreement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Agreement();
        $projects = Project::find()->where(['draft' => 'No'])->all();
        $projects = ArrayHelper::map($projects, 'id', 'title');

        if ($model->load(Yii::$app->request->post())) {
            $model->submitted_by = Yii::$app->user->id;
            $model->date_submitted = date('Y-m-d H:i:s');
            $model->save();
            \Yii::$app->getSession()->setFlash('success', 'Record Saved');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'projects' => $projects, 
        ]);
    }

    /**
     * Updates an existing Agreement model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $projects = Project::find()->where(['draft' => 'No'])->all();
        $projects = ArrayHelper::map($projects, 'id', 'title');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', 'Record Updated');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'projects' => $projects, 
        ]);
    }

    /**
     * Deletes an existing Agreement model.
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
     * Finds the Agreement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Agreement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Agreement::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}