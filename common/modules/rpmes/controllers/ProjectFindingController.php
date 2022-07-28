<?php

namespace common\modules\rpmes\controllers;

use Yii;
use common\modules\rpmes\models\ProjectFinding;
use common\modules\rpmes\models\ProjectFindingSearch;
use common\modules\rpmes\models\Project;
use common\modules\rpmes\models\ProjectTarget;
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
 * ProjectFindingController implements the CRUD actions for ProjectFinding model.
 */
class ProjectFindingController extends Controller
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
     * Lists all ProjectFinding models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectFindingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $years = ProjectFinding::find()->select(['distinct(year) as year'])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'years' => $years
        ]);
    }

    /**
     * Displays a single ProjectFinding model.
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
     * Creates a new ProjectFinding model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProjectFinding();
        $projects = Project::find()->select(['project.id','CONCAT(agency.code,'.'": ",'.'project.title) as title','agency.code']);
        $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
        $projects = $projects->andWhere(['project.draft' => 'No']);
        $projects = $projects->orderBy(['agency.code' => SORT_ASC])->asArray()->all();
        $projects = ArrayHelper::map($projects, 'id', 'title');

        //echo "<pre>"; print_r($projects); exit;

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
     * Updates an existing ProjectFinding model.
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
     * Deletes an existing ProjectFinding model.
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
     * Finds the ProjectFinding model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProjectFinding the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProjectFinding::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionPrintFormSeven($year,$quarter)
    {
        $model = [];
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
            
        $projectFindings = ProjectFinding::find()
                    ->select([
                        'project_finding.quarter',
                        'project_finding.year',
                        'project_finding.project_id',
                        'project_finding.inspection_date',
                        'project_finding.major_finding',
                        'project_finding.issues',
                        'project_finding.action',
                        'project.title as projectTitle',
                        'sector.title as sectorTitle',
                        'sub_sector.title as subSectorTitle',
                        'agency.code as agencyCode',
                        'IF(barangayTitles.title is null, IF(citymunTitles.title is null, IF(provinceTitles.title is null, IF(regionTitles.title is null, "No location", regionTitles.title), provinceTitles.title), citymunTitles.title), barangayTitles.title) as locationTitle',
                        'COALESCE('.$financialTotal.', 0) as totalCost'
                    ])
                    ->leftJoin('project','project.id= project_finding.project_id')
                    ->leftJoin('sector','sector.id= project.sector_id')
                    ->leftJoin('sub_sector','sub_sector.id= project.sub_sector_id')
                    ->leftJoin('agency','agency.id= project.agency_id')
                    ->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id')
                    ->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id')
                    ->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id')
                    ->leftJoin(['barangayTitles' => '('.$barangayTitles.')'], 'barangayTitles.project_id = project.id') 
        ;
            
        $projectFindings = $projectFindings->leftJoin(['financials' => '('.$financials.')'], 'financials.project_id = project_finding.project_id');

        if($model['year'] != '')
        {
            $projectFindings = $projectFindings->andWhere(['project_finding.year' => $model['year']]);
        }

        if($model['quarter'] != '')
        {
            $projectFindings = $projectFindings->andWhere(['project_finding.quarter' => $model['quarter']]);
        }

        $projectFindings = $projectFindings->orderBy(['project_finding.major_finding' => SORT_ASC])->asArray()->all();

        return $this->renderAjax('form-seven', [
            'model' => $model,
            'type' => 'print',
            'projectFindings' => $projectFindings
        ]);
    }

    public function actionDownloadFormSeven($type, $year, $quarter, $model)
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
            
        $projectFindings = ProjectFinding::find()
                    ->select([
                        'project_finding.quarter',
                        'project_finding.year',
                        'project_finding.project_id',
                        'project_finding.inspection_date',
                        'project_finding.major_finding',
                        'project_finding.issues',
                        'project_finding.action',
                        'project.title as projectTitle',
                        'sector.title as sectorTitle',
                        'sub_sector.title as subSectorTitle',
                        'agency.code as agencyCode',
                        'IF(barangayTitles.title is null, IF(citymunTitles.title is null, IF(provinceTitles.title is null, IF(regionTitles.title is null, "No location", regionTitles.title), provinceTitles.title), citymunTitles.title), barangayTitles.title) as locationTitle',
                        'COALESCE('.$financialTotal.', 0) as totalCost'
                    ])
                    ->leftJoin('project','project.id= project_finding.project_id')
                    ->leftJoin('sector','sector.id= project.sector_id')
                    ->leftJoin('sub_sector','sub_sector.id= project.sub_sector_id')
                    ->leftJoin('agency','agency.id= project.agency_id')
                    ->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id')
                    ->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id')
                    ->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id')
                    ->leftJoin(['barangayTitles' => '('.$barangayTitles.')'], 'barangayTitles.project_id = project.id') 
        ;
            
        $projectFindings = $projectFindings->leftJoin(['financials' => '('.$financials.')'], 'financials.project_id = project_finding.project_id');

        if($model['year'] != '')
        {
            $projectFindings = $projectFindings->andWhere(['project_finding.year' => $model['year']]);
        }

        if($model['quarter'] != '')
        {
            $projectFindings = $projectFindings->andWhere(['project_finding.quarter' => $model['quarter']]);
        }

        $projectFindings = $projectFindings->orderBy(['project_finding.major_finding' => SORT_ASC])->asArray()->all();

        $filename = 'RPMES Form 7: LIST OF PROJECT MAJOR FINDINGS';

        if($type == 'excel')
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=".$filename.".xls");
            return $this->renderPartial('form-seven', [
                'model' => $model,
                'type' => $type,
                'projectFindings' => $projectFindings,
            ]);
        }else if($type == 'pdf')
        {
            $content = $this->renderPartial('form-seven', [
                'model' => $model,
                'type' => $type,
                'projectFindings' => $projectFindings,
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
