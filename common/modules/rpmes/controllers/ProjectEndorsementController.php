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
use common\modules\rpmes\models\ProjectException;
use common\modules\rpmes\models\ProjectEndorsement;
use common\modules\rpmes\models\ProjectExceptionSearch;
use common\modules\rpmes\models\ProjectEndorsementSearch;
use common\modules\rpmes\models\Settings;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use kartik\mpdf\Pdf;
use yii\widgets\ActiveForm;
/**
 * ProjectStatusController implements the CRUD actions for Project model.
 */
class ProjectEndorsementController extends Controller
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
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'endorse', 'generate', 'print'],
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

        $searchModel = new ProjectEndorsementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ProjectEndorsement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProjectEndorsement();

        if(!Yii::$app->user->can('Administrator')){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $projectIDs = ProjectException::find()->select(['project_id'])->asArray()->all();
        $projectIDs = ArrayHelper::map($projectIDs, 'project_id', 'project_id');

        $projects = Project::find()->select(['id','CONCAT(project_no,": ",project.title) as title'])->where(['id' => $projectIDs])->asArray()->orderBy(['project_no' => SORT_ASC])->all();
        $projects = ArrayHelper::map($projects, 'id', 'title');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            \Yii::$app->getSession()->setFlash('success', 'Record Saved');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'projects' => $projects,
        ]);
    }

    public function actionView($id)
    {
        $exceptions = ProjectException::find()->where(['project_id' => $id])->all();

        return $this->renderAjax('view', [
            'exceptions' => $exceptions,
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

        if(!Yii::$app->user->can('Administrator')){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $projectIDs = ProjectException::find()->select(['project_id'])->asArray()->all();
        $projectIDs = ArrayHelper::map($projectIDs, 'project_id', 'project_id');

        $projects = Project::find()->select(['id','CONCAT(project_no,": ",project.title) as title'])->where(['id' => $projectIDs])->asArray()->orderBy(['project_no' => SORT_ASC])->all();
        $projects = ArrayHelper::map($projects, 'id', 'title');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            \Yii::$app->getSession()->setFlash('success', 'Record Saved');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'projects' => $projects,
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
        if(Yii::$app->request->post())
        {
            $this->findModel($id)->delete();
            \Yii::$app->getSession()->setFlash('success', 'Record Deleted');
            return $this->redirect(['index']);
        }
    }

    public function actionGenerate()
    {
        if(!Yii::$app->user->can('Administrator')){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = new ProjectEndorsement();
        $model->scenario = 'generate';

        $years = ProjectEndorsement::find()->select(['distinct(year) as year'])
                ->orderBy(['year' => SORT_DESC])
                ->asArray()
                ->all();

        $years = ArrayHelper::map($years, 'year', 'year');

        if(Yii::$app->request->post()){
            $postData = Yii::$app->request->post('ProjectEndorsement');

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

            $regionTitles = ProjectRegion::find()
                    ->select(['project_id', 'GROUP_CONCAT(DISTINCT tblregion.abbreviation ORDER BY tblregion.abbreviation ASC SEPARATOR ", <br>") as title'])
                    ->leftJoin('tblregion', 'tblregion.region_c = project_region.region_id')
                    ->leftJoin('project', 'project.id = project_region.project_id')
                    ->where(['project.draft' => 'No'])
                    ->groupBy(['project_region.project_id'])
                    ->createCommand()->getRawSql();

            $provinceTitles = ProjectProvince::find()
                    ->select(['project_id', 'GROUP_CONCAT(DISTINCT tblprovince.province_m ORDER BY tblprovince.province_m ASC SEPARATOR ", <br>") as title'])
                    ->leftJoin('tblprovince', 'tblprovince.province_c = project_province.province_id')
                    ->leftJoin('project', 'project.id = project_province.project_id')
                    ->where(['project.draft' => 'No'])
                    ->groupBy(['project_province.project_id'])
                    ->createCommand()->getRawSql();

            $citymunTitles = ProjectCitymun::find()
                    ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat(tblcitymun.citymun_m,", ",tblprovince.province_m) ORDER BY tblcitymun.citymun_m ASC, tblprovince.province_m ASC SEPARATOR ", <br>") as title'])
                    ->leftJoin('tblcitymun', 'tblcitymun.province_c = project_citymun.province_id and tblcitymun.citymun_c = project_citymun.citymun_id')
                    ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
                    ->leftJoin('project', 'project.id = project_citymun.project_id')
                    ->where(['project.draft' => 'No'])
                    ->groupBy(['project_citymun.project_id'])
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

            $projectException = ProjectException::find()
            ->select([
                'project_id',
                'typology.title as typologyTitle',
                'issue_status',
                'findings',
                'causes',
                'action_taken',
                'recommendations',

            ])
            ->where([
                'year' => $postData['year'],
                'quarter' => $postData['quarter'],
            ])
            ->leftJoin('typology', 'typology.id = project_exception.typology_id')
            ->createCommand()->getRawSql();
    
            $actualOwpa = 'IF(physicalTargets.type = "Numerical", 
                                IF('.$physicalTotal.' > 0,
                                    (COALESCE(physicalAccomplishment.value, 0)/'.$physicalTotal.')*100, 
                                0), 
                            COALESCE(physicalAccomplishment.value,0))';

            $records = ProjectEndorsement::find()
                        ->select([
                            'project.id',
                            'project.project_no as project_no',
                            'project.title as projectTitle',
                            'agency.code as agencyTitle',
                            'regionTitles.title as regionTitle',
                            'provinceTitles.title as provinceTitle',
                            'citymunTitles.title as citymunTitle',
                            'COALESCE('.$financialTotal.', 0) as financialTotal',
                            'COALESCE('.$physicalTotal.', 0) as physicalTotal',
                            $targetOwpa[$postData['quarter']].' as targetOwpa',
                            $actualOwpa.' as actualOwpa',
                            'COALESCE('.$actualOwpa.', 0) - COALESCE('.$targetOwpa[$postData['quarter']].', 0) as slippage',
                            'COALESCE(financialAccomplishment.releases, 0) as allotment',
                            'COALESCE(financialAccomplishment.expenditures, 0) as disbursements',
                            'projectException.findings as issue_details',
                            'projectException.issue_status as issue_status',
                            'projectException.typologyTitle as typology',
                            'projectException.causes as reasons',
                            'projectException.action_taken as action_taken',
                            'projectException.recommendations as action_to_be_taken',
                            'npmc_action',
                        ])
                        ->leftJoin('project', 'project.id = project_endorsement.project_id')
                        ->leftJoin('agency', 'agency.id = project.agency_id');
    
            $records = $records->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id');
            $records = $records->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
            $records = $records->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id');
            $records = $records->leftJoin(['financialTargets' => '('.$financialTargets.')'], 'financialTargets.project_id = project.id');
            $records = $records->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
            $records = $records->leftJoin(['financialAccomplishment' => '('.$financialAccomplishment.')'], 'financialAccomplishment.project_id = project.id');
            $records = $records->leftJoin(['physicalAccomplishment' => '('.$physicalAccomplishment.')'], 'physicalAccomplishment.project_id = project.id');
            $records = $records->leftJoin(['projectException' => '('.$projectException.')'], 'projectException.project_id = project.id');

            $records = !empty($postData['year']) ? $records->andWhere(['project_endorsement.year' => $postData['year']]) : $records;
            $records = !empty($postData['quarter']) ? $records->andWhere(['project_endorsement.quarter' => $postData['quarter']]) : $records;

            $records = $records
                ->orderBy(['project_endorsement.id' => SORT_DESC])
                ->asArray()
                ->all();

            $director = Settings::findOne(['title' => 'Agency Head']);

            $filename = date("YmdHis").'_RPMES_Form_6';

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

        $regionTitles = ProjectRegion::find()
                ->select(['project_id', 'GROUP_CONCAT(DISTINCT tblregion.abbreviation ORDER BY tblregion.abbreviation ASC SEPARATOR ", <br>") as title'])
                ->leftJoin('tblregion', 'tblregion.region_c = project_region.region_id')
                ->leftJoin('project', 'project.id = project_region.project_id')
                ->where(['project.draft' => 'No'])
                ->groupBy(['project_region.project_id'])
                ->createCommand()->getRawSql();

        $provinceTitles = ProjectProvince::find()
                ->select(['project_id', 'GROUP_CONCAT(DISTINCT tblprovince.province_m ORDER BY tblprovince.province_m ASC SEPARATOR ", <br>") as title'])
                ->leftJoin('tblprovince', 'tblprovince.province_c = project_province.province_id')
                ->leftJoin('project', 'project.id = project_province.project_id')
                ->where(['project.draft' => 'No'])
                ->groupBy(['project_province.project_id'])
                ->createCommand()->getRawSql();

        $citymunTitles = ProjectCitymun::find()
                ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat(tblcitymun.citymun_m,", ",tblprovince.province_m) ORDER BY tblcitymun.citymun_m ASC, tblprovince.province_m ASC SEPARATOR ", <br>") as title'])
                ->leftJoin('tblcitymun', 'tblcitymun.province_c = project_citymun.province_id and tblcitymun.citymun_c = project_citymun.citymun_id')
                ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
                ->leftJoin('project', 'project.id = project_citymun.project_id')
                ->where(['project.draft' => 'No'])
                ->groupBy(['project_citymun.project_id'])
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

        $projectException = ProjectException::find()
        ->select([
            'project_id',
            'typology.title as typologyTitle',
            'issue_status',
            'findings',
            'causes',
            'action_taken',
            'recommendations',

        ])
        ->where([
            'year' => $year,
            'quarter' => $quarter,
        ])
        ->leftJoin('typology', 'typology.id = project_exception.typology_id')
        ->createCommand()->getRawSql();

        $actualOwpa = 'IF(physicalTargets.type = "Numerical", 
                            IF('.$physicalTotal.' > 0,
                                (COALESCE(physicalAccomplishment.value, 0)/'.$physicalTotal.')*100, 
                            0), 
                        COALESCE(physicalAccomplishment.value,0))';

        $records = ProjectEndorsement::find()
                    ->select([
                        'project.id',
                        'project.project_no as project_no',
                        'project.title as projectTitle',
                        'agency.code as agencyTitle',
                        'regionTitles.title as regionTitle',
                        'provinceTitles.title as provinceTitle',
                        'citymunTitles.title as citymunTitle',
                        'COALESCE('.$financialTotal.', 0) as financialTotal',
                        'COALESCE('.$physicalTotal.', 0) as physicalTotal',
                        $targetOwpa[$quarter].' as targetOwpa',
                        $actualOwpa.' as actualOwpa',
                        'COALESCE('.$actualOwpa.', 0) - COALESCE('.$targetOwpa[$quarter].', 0) as slippage',
                        'COALESCE(financialAccomplishment.releases, 0) as allotment',
                        'COALESCE(financialAccomplishment.expenditures, 0) as disbursements',
                        'projectException.findings as issue_details',
                        'projectException.issue_status as issue_status',
                        'projectException.typologyTitle as typology',
                        'projectException.causes as reasons',
                        'projectException.action_taken as action_taken',
                        'projectException.recommendations as action_to_be_taken',
                        'npmc_action',
                    ])
                    ->leftJoin('project', 'project.id = project_endorsement.project_id')
                    ->leftJoin('agency', 'agency.id = project.agency_id');

        $records = $records->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id');
        $records = $records->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
        $records = $records->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id');
        $records = $records->leftJoin(['financialTargets' => '('.$financialTargets.')'], 'financialTargets.project_id = project.id');
        $records = $records->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
        $records = $records->leftJoin(['financialAccomplishment' => '('.$financialAccomplishment.')'], 'financialAccomplishment.project_id = project.id');
        $records = $records->leftJoin(['physicalAccomplishment' => '('.$physicalAccomplishment.')'], 'physicalAccomplishment.project_id = project.id');
        $records = $records->leftJoin(['projectException' => '('.$projectException.')'], 'projectException.project_id = project.id');

        $records = !empty($year) ? $records->andWhere(['project_endorsement.year' => $year]) : $records;
        $records = !empty($quarter) ? $records->andWhere(['project_endorsement.quarter' => $quarter]) : $records;

        $records = $records
            ->orderBy(['project_endorsement.id' => SORT_DESC])
            ->asArray()
            ->all();

        $director = Settings::findOne(['title' => 'Agency Head']);

        $director = Settings::findOne(['title' => 'Agency Head']);

        return $this->renderAjax('_report-file', [
            'records' => $records,
            'year' => $year,
            'quarter' => $quarter,
            'director' => $director,
            'type' => 'print',
        ]);
    }

    /**
     * Finds the ProjectProblemSolvingSession model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProjectEndorsement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProjectEndorsement::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
