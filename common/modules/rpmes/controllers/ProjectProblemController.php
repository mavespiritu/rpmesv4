<?php

namespace common\modules\rpmes\controllers;

use Yii;
use common\modules\rpmes\models\ProjectProblem;
use common\modules\rpmes\models\Project;
use common\modules\rpmes\models\ProjectProblemSearch;
use common\modules\rpmes\models\ProjectTarget;
use common\modules\rpmes\models\Agency;
use common\modules\rpmes\models\Model;
use common\modules\rpmes\models\ProjectRegion;
use common\modules\rpmes\models\ProjectProvince;
use common\modules\rpmes\models\ProjectCitymun;
use common\modules\rpmes\models\ProjectBarangay;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use kartik\mpdf\Pdf;

/**
 * ProjectProblemController implements the CRUD actions for ProjectProblem model.
 */
class ProjectProblemController extends Controller
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
     * Lists all ProjectProblem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectProblemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $years = ProjectProblem::find()->select(['distinct(year) as year'])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        $agencies = Agency::find()->select(['id', 'code as title']);
        $agencies = Yii::$app->user->can('AgencyUser') ? $agencies->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $agencies;
        $agencies = $agencies->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'years' => $years,
            'agencies' => $agencies
        ]);
    }

    /**
     * Displays a single ProjectProblem model.
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
     * Creates a new ProjectProblem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProjectProblem();

        $projects = Project::find()->select(['project.id','CONCAT(agency.code,'.'": ",'.'project.title) as title','agency.code']);
        $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
        $projects = $projects->andWhere(['project.draft' => 'No']);
        $projects = $projects->orderBy(['agency.code' => SORT_ASC])->asArray()->all();
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
     * Updates an existing ProjectProblem model.
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

        if ($model->load(Yii::$app->request->post())) {
            $model->submitted_by = Yii::$app->user->id;
            $model->date_submitted = date('Y-m-d H:i:s');
            $model->save();
            \Yii::$app->getSession()->setFlash('success', 'Record Updated');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'projects' => $projects,
        ]);
    }

    /**
     * Deletes an existing ProjectProblem model.
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
     * Finds the ProjectProblem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProjectProblem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProjectProblem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionPrintFormEleven($year,$quarter)
    {
        $model = [];
        $model['year'] = $year;
        $model['quarter'] = $quarter;

        //echo "<pre>"; print_r($model['year']); exit;

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

        $problems = ProjectProblem::find()
                    ->select([
                        'project_problem.quarter',
                        'project_problem.year',
                        'project_problem.project_id',
                        'project_problem.nature',
                        'project_problem.detail',
                        'project_problem.strategy',
                        'project_problem.responsible_entity',
                        'project_problem.lesson_learned',
                        'project.title as projectTitle',
                        'sector.title as sectorTitle',
                        'sub_sector.title as subSectorTitle',
                        'agency.code as agencyCode',
                        'IF(barangayTitles.title is null, IF(citymunTitles.title is null, IF(provinceTitles.title is null, IF(regionTitles.title is null, "No location", regionTitles.title), provinceTitles.title), citymunTitles.title), barangayTitles.title) as locationTitle',
                        'COALESCE('.$financialTotal.', 0) as totalCost'
                    ])
                    ->leftJoin('project','project.id= project_problem.project_id')
                    ->leftJoin('sector','sector.id= project.sector_id')
                    ->leftJoin('sub_sector','sub_sector.id= project.sub_sector_id')
                    ->leftJoin('agency','agency.id= project.agency_id')
                    ->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id')
                    ->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id')
                    ->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id')
                    ->leftJoin(['barangayTitles' => '('.$barangayTitles.')'], 'barangayTitles.project_id = project.id') 
                    ;

        $problems = $problems->leftJoin(['financials' => '('.$financials.')'], 'financials.project_id = project_problem.project_id');

        if($model['year'] != '')
        {
            $problems = $problems->andWhere(['project_problem.year' => $model['year']]);
        }
        if($model['quarter'] != '')
        {
            $problems = $problems->andWhere(['project_problem.quarter' => $model['quarter']]);
        }

        $problems = $problems->orderBy(['project_problem.nature' => SORT_ASC])->asArray()->all();

        return $this->renderAjax('form-eleven', [
            'model' => $model,
            'type' => 'print',
            'problems' => $problems
        ]);
    }
    public function actionDownloadFormEleven($type, $year, $quarter, $model)
    {
        $model = json_decode($model, true); 
        $model['year'] = $year;
        $model['quarter'] = $quarter;

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

        $problems = ProjectProblem::find()
                    ->select([
                        'project_problem.quarter',
                        'project_problem.year',
                        'project_problem.project_id',
                        'project_problem.nature',
                        'project_problem.detail',
                        'project_problem.strategy',
                        'project_problem.responsible_entity',
                        'project_problem.lesson_learned',
                        'project.title as projectTitle',
                        'sector.title as sectorTitle',
                        'sub_sector.title as subSectorTitle',
                        'agency.code as agencyCode',
                        'IF(barangayTitles.title is null, IF(citymunTitles.title is null, IF(provinceTitles.title is null, IF(regionTitles.title is null, "No location", regionTitles.title), provinceTitles.title), citymunTitles.title), barangayTitles.title) as locationTitle',
                        'COALESCE('.$financialTotal.', 0) as totalCost'
                    ])
                    ->leftJoin('project','project.id= project_problem.project_id')
                    ->leftJoin('sector','sector.id= project.sector_id')
                    ->leftJoin('sub_sector','sub_sector.id= project.sub_sector_id')
                    ->leftJoin('agency','agency.id= project.agency_id')
                    ->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id')
                    ->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id')
                    ->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id')
                    ->leftJoin(['barangayTitles' => '('.$barangayTitles.')'], 'barangayTitles.project_id = project.id') 
                    ;

        $problems = $problems->leftJoin(['financials' => '('.$financials.')'], 'financials.project_id = project_problem.project_id');

        if($model['year'] != '')
        {
            $problems = $problems->andWhere(['project_problem.year' => $model['year']]);
        }
        if($model['quarter'] != '')
        {
            $problems = $problems->andWhere(['project_problem.quarter' => $model['quarter']]);
        }

        $problems = $problems->orderBy(['project_problem.nature' => SORT_ASC])->asArray()->all();

        $filename = 'RPMES Form 11: LIST OF PROJECT PROBLEMS/ISSUES';

        if($type == 'excel')
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=".$filename.".xls");
            return $this->renderPartial('form-eleven', [
                'model' => $model,
                'type' => $type,
                'problems' => $problems,
            ]);
        }else if($type == 'pdf')
        {
            $content = $this->renderPartial('form-eleven', [
                'model' => $model,
                'type' => $type,
                'problems' => $problems,
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
