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
use common\modules\rpmes\models\Typology;
use common\modules\rpmes\models\Settings;
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
     * Lists all ProjectProblemSolvingSession models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectProblemSolvingSessionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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

        if(!Yii::$app->user->can('Administrator')){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $projects = Project::find()->select(['id','CONCAT(project_no,": ",project.title) as title'])->where(['draft' => 'No', 'source_id' => null])->orderBy(['title' => SORT_ASC])->asArray()->all();
        $projects = ArrayHelper::map($projects, 'id', 'title');

        $natures = Typology::find()->all();
        $natures = ArrayHelper::map($natures, 'title', 'title');

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
            'natures' => $natures,
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
        
        $projects = Project::find()->select(['id','CONCAT(project_no,": ",project.title) as title'])->where(['draft' => 'No', 'source_id' => null])->orderBy(['title' => SORT_ASC])->asArray()->all();
        $projects = ArrayHelper::map($projects, 'id', 'title');

        $natures = Typology::find()->all();
        $natures = ArrayHelper::map($natures, 'title', 'title');

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
            'natures' => $natures,
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

        $model = new ProjectProblemSolvingSession();
        $model->scenario = 'generate';

        $years = ProjectProblemSolvingSession::find()->select(['distinct(year) as year'])
                ->orderBy(['year' => SORT_DESC])
                ->asArray()
                ->all();

        $years = ArrayHelper::map($years, 'year', 'year');

        if(Yii::$app->request->post()){
            $postData = Yii::$app->request->post('ProjectProblemSolvingSession');

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

            $records = ProjectProblemSolvingSession::find()
                ->select([
                    'project.title as projectTitle',
                    'agency.code as agencyTitle',
                    'regionTitles.title as regionTitle',
                    'provinceTitles.title as provinceTitle',
                    'citymunTitles.title as citymunTitle',
                    'issue_details',
                    'issue_typology',
                    'pss_date',
                    'agencies',
                    'agreement_reached',
                ])
                ->leftJoin('project', 'project.id = project_problem_solving_session.project_id')
                ->leftJoin('agency', 'agency.id = project.agency_id');

            $records = $records->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id');
            $records = $records->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
            $records = $records->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id');

            $records = !empty($postData['year']) ? $records->andWhere(['project_problem_solving_session.year' => $postData['year']]) : $records;
            $records = !empty($postData['quarter']) ? $records->andWhere(['project_problem_solving_session.quarter' => $postData['quarter']]) : $records;

            $records = $records
                ->orderBy(['project_problem_solving_session.id' => SORT_DESC])
                ->asArray()
                ->all();

            $director = Settings::findOne(['title' => 'Agency Head']);

            $filename = date("YmdHis").'_RPMES_Form_8';

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

        $records = ProjectProblemSolvingSession::find()
                ->select([
                    'project.title as projectTitle',
                    'agency.code as agencyTitle',
                    'regionTitles.title as regionTitle',
                    'provinceTitles.title as provinceTitle',
                    'citymunTitles.title as citymunTitle',
                    'issue_details',
                    'issue_typology',
                    'pss_date',
                    'agencies',
                    'agreement_reached',
                ])
                ->leftJoin('project', 'project.id = project_problem_solving_session.project_id')
                ->leftJoin('agency', 'agency.id = project.agency_id');

        $records = $records->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id');
        $records = $records->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
        $records = $records->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id');

        $records = $year != '' ? $records->andWhere(['project_problem_solving_session.year' => $year]) : $records;
        $records = $quarter != '' ? $records->andWhere(['project_problem_solving_session.quarter' => $quarter]) : $records;

        $records = $records
                    ->orderBy(['project_problem_solving_session.id' => SORT_DESC])
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
