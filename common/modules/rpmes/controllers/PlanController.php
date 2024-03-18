<?php

namespace common\modules\rpmes\controllers;

use Yii;
use common\models\Region;
use common\models\Province;
use common\models\Citymun;
use common\models\Barangay;
use common\modules\rpmes\models\DueDate;
use common\modules\rpmes\models\Project;
use common\modules\rpmes\models\Plan;
use common\modules\rpmes\models\PlanSearch;
use common\modules\rpmes\models\Settings;
use common\modules\rpmes\models\ProjectTarget;
use common\modules\rpmes\models\ProjectRegion;
use common\modules\rpmes\models\ProjectProvince;
use common\modules\rpmes\models\ProjectCitymun;
use common\modules\rpmes\models\ProjectBarangay;
use common\modules\rpmes\models\ProjectCategory;
use common\modules\rpmes\models\ProjectSdgGoal;
use common\modules\rpmes\models\ProjectRdpChapter;
use common\modules\rpmes\models\ProjectRdpChapterOutcome;
use common\modules\rpmes\models\ProjectRdpSubChapterOutcome;
use common\modules\rpmes\models\ProjectExpectedOutput;
use common\modules\rpmes\models\ProjectOutcome;
use common\modules\rpmes\models\ProjectHasFundSources;
use common\modules\rpmes\models\Agency;
use common\modules\rpmes\models\Program;
use common\modules\rpmes\models\Sector;
use common\modules\rpmes\models\SubSector;
use common\modules\rpmes\models\SubSectorPerSector;
use common\modules\rpmes\models\Category;
use common\modules\rpmes\models\KeyResultArea;
use common\modules\rpmes\models\ModeOfImplementation;
use common\modules\rpmes\models\FundSource;
use common\modules\rpmes\models\LocationScope;
use common\modules\rpmes\models\SdgGoal;
use common\modules\rpmes\models\RdpChapter;
use common\modules\rpmes\models\RdpChapterOutcome;
use common\modules\rpmes\models\RdpSubChapterOutcome;
use common\modules\rpmes\models\ProjectSearch;
use common\modules\rpmes\models\Model;
use common\modules\rpmes\models\MultipleModel;
use common\modules\rpmes\models\Submission;
use common\modules\rpmes\models\SubmissionLog;
use common\modules\rpmes\models\SubmissionSearch;
use common\modules\rpmes\models\Acknowledgment;
use markavespiritu\user\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\db\Query;
use yii\helpers\Json;
use yii\data\Pagination;
use kartik\mpdf\Pdf;
use yii\web\Response;

class PlanController extends \yii\web\Controller
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
                'only' => ['index', 'create', 'update', 'delete', 'view'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'view'],
                        'allow' => true,
                        'roles' => ['AgencyUser', 'Administrator', 'SuperAdministrator'],
                    ],
                ],
            ],
        ];
    }

    function removeMask($figure)
    {
        $figure = explode(",",$figure);
        $number = implode("", $figure);

        return $number;
    }

    /* public function actionIndex()
    {
        $model = new Project();
        $model->year = date("Y");
        $totals = [];
        $submissionModel = Yii::$app->user->can('AgencyUser') ? Submission::findOne(['agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C, 'year' => date("Y"), 'report' => 'Monitoring Plan', 'draft' => 'No']) ? Submission::findOne(['agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C, 'year' => date("Y"), 'report' => 'Monitoring Plan', 'draft' => 'No']) : new Submission() : new Submission();
        $submissionModel->scenario = Yii::$app->user->can('Administrator') || Yii::$app->user->can('SuperAdministrator') ? 'monitoringPlanAdmin' : '';
        $submissionModel->agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : null;

        $projectCount = Yii::$app->user->can('AgencyUser') ? 
        Plan::find()
        ->leftJoin('project', 'project.id = plan.project_id')
        ->where(['project.agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C, 'plan.year' => date("Y")])
        ->count() : Plan::find()
        ->where(['plan.year' => date("Y")])
        ->count();

        $regionModel = new ProjectRegion();
        $provinceModel = new Projectprovince();
        $categoryModel = new ProjectCategory();

        $model->scenario = 'searchMonitoringProject';
        $regionModel->scenario = 'searchRegion';
        $provinceModel->scenario = 'searchProvince';
        $categoryModel->scenario = 'searchCategory';

        $years = Project::find()->select(['distinct(year) as year'])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        $agencies = Agency::find()->select(['id', 'code as title']);
        $agencies = Yii::$app->user->can('AgencyUser') ? $agencies->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $agencies;
        $agencies = $agencies->orderBy(['code' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $programs = Program::find()->select(['id', 'title'])->asArray()->all();
        $programs = ArrayHelper::map($programs, 'id', 'title');

        $sectors = Sector::find()->all();
        $sectors = ArrayHelper::map($sectors, 'id', 'title');

        $subSectors = [];

        $modes = ModeOfImplementation::find()->all();
        $modes = ArrayHelper::map($modes, 'id', 'title');

        $fundSources = FundSource::find()->select(['id', 'concat(title," (",code,")") as title'])->orderBy(['title' => SORT_ASC])->asArray()->all();
        $fundSources = ArrayHelper::map($fundSources, 'id', 'title');
        
        $categories = Category::find()->orderBy(['title' => SORT_ASC])->all();
        $categories = ArrayHelper::map($categories, 'id', 'title');

        $scopes = LocationScope::find()->all();
        $scopes = ArrayHelper::map($scopes, 'id', 'title');

        $regions = Region::find()->orderBy(['region_sort' => SORT_ASC])->all();
        $regions = ArrayHelper::map($regions, 'region_c', 'abbreviation');

        $provinces = [];

        $categories = Category::find()->all();
        $categories = ArrayHelper::map($categories, 'id', 'title');

        $kras = [];

        $goals = SdgGoal::find()->select(['id', 'concat("SDG #",sdg_no,": ",title) as title'])->asArray()->all();
        $goals = ArrayHelper::map($goals, 'id', 'title');

        $chapters = RdpChapter::find()->select(['id', 'concat("Chapter ",chapter_no,": ",title) as title'])->asArray()->all();
        $chapters = ArrayHelper::map($chapters, 'id', 'title');

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];
        $genders = ['M' => 'Male', 'F' => 'Female'];

        $periods = ['Current Year' => 'Current Year', 'Carry-Over' => 'Carry-Over'];
        $dataTypes = [
            'Default' => 'Default',
            'Cumulative' => 'Cumulative',
            'Maintained' => 'Maintained',
        ];

        $dueDate = DueDate::find()->where(['report' => 'Monitoring Plan']);
        $projectsPaging = Project::find();
        $projectsPaging = Yii::$app->user->can('AgencyUser') ? 
            $projectsPaging
            ->andWhere(['draft' => 'No', 'year' => date("Y")])
            ->andWhere(['agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C, 'year' => date("Y")]) 
            : 
            $projectsPaging
            ->andWhere(['draft' => 'No'])
            ;

        $countProjects = clone $projectsPaging;
        $projects = clone $projectsPaging;
        $projects = $projects->all();
        $projectIds = [];

        $totals['financials']['Q1'] = 0;
        $totals['financials']['Q2'] = 0;
        $totals['financials']['Q3'] = 0;
        $totals['financials']['Q4'] = 0;
        $totals['physicals']['Q1'] = 0;
        $totals['physicals']['Q2'] = 0;
        $totals['physicals']['Q3'] = 0;
        $totals['physicals']['Q4'] = 0;
        $totals['maleEmployed']['Q1'] = 0;
        $totals['maleEmployed']['Q2'] = 0;
        $totals['maleEmployed']['Q3'] = 0;
        $totals['maleEmployed']['Q4'] = 0;
        $totals['femaleEmployed']['Q1'] = 0;
        $totals['femaleEmployed']['Q2'] = 0;
        $totals['femaleEmployed']['Q3'] = 0;
        $totals['femaleEmployed']['Q4'] = 0;
        $totals['beneficiaries']['Q1'] = 0;
        $totals['beneficiaries']['Q2'] = 0;
        $totals['beneficiaries']['Q3'] = 0;
        $totals['beneficiaries']['Q4'] = 0;
        $totals['groupBeneficiaries']['Q1'] = 0;
        $totals['groupBeneficiaries']['Q2'] = 0;
        $totals['groupBeneficiaries']['Q3'] = 0;
        $totals['groupBeneficiaries']['Q4'] = 0;

        $projectsPages = new Pagination(['totalCount' => $countProjects->count()]);
        
        $projectsModels = $projectsPaging->offset($projectsPages->offset)
            ->limit($projectsPages->limit)
            ->orderBy(['id' => SORT_DESC])
            ->all();

        if($projectsModels)
        {
            foreach($projectsModels as $project)
            {
                $projectIds[$project->id] = $project;
            }
        }
        
        if(Yii::$app->request->get())
        {
            $ids = Yii::$app->request->get('id');

            if(isset($ids))
            {
                $ids = explode(",", $ids);
                if(!empty($ids)){ 
                    
                    Project::deleteAll(['in', 'id', $ids]);
                    \Yii::$app->getSession()->setFlash('success', 'Selected projects has been deleted successfully'); 
                    return $this->redirect(['/rpmes/plan']);
                }      
            } 
        }

        if(
            $model->load(Yii::$app->request->post()) &&
            $regionModel->load(Yii::$app->request->post()) &&
            $provinceModel->load(Yii::$app->request->post()) &&
            $categoryModel->load(Yii::$app->request->post())
        )
        {
            $postData = Yii::$app->request->post();

            $project = $postData['Project'];
            $projectRegion = $postData['ProjectRegion'];
            $projectProvince = $postData['ProjectProvince'];
            $projectCategory = $postData['ProjectCategory'];

            $regionIDs = ProjectRegion::find();
            $provinceIDs = ProjectProvince::find();
            $categoryIDs = ProjectCategory::find();

            $regions = Region::find()->orderBy(['region_sort' => SORT_ASC])->all();
            $regions = ArrayHelper::map($regions, 'region_c', 'abbreviation');

            $provinces = Province::find()
            ->select(['province_c as id', 'concat(tblregion.abbreviation,": ",tblprovince.province_m) as title', 'abbreviation'])
            ->leftJoin('tblregion', 'tblregion.region_c = tblprovince.region_c');

            $projectsPaging = Project::find();
            $projectsPaging = Yii::$app->user->can('AgencyUser') ? 
                $projectsPaging
                ->andWhere(['draft' => 'No'])
                ->andWhere(['agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]) 
                : 
                $projectsPaging
                ->andWhere(['draft' => 'No'])
                ;

            if(!empty($project['year']))
            {
                $projectsPaging = $projectsPaging->andWhere(['year' => $project['year']]);
                $provinceIDs = $provinceIDs->andWhere(['year' => $project['year']]);
                $categoryIDs = $categoryIDs->andWhere(['year' => $project['year']]);
                $model->year = $project['year'];
                $dueDate = $dueDate->andWhere(['year' => $project['year']]);
            }else{
                $dueDate = $dueDate->andWhere(['year' => date("Y")]);
            }

            if(!empty($project['agency_id']))
            {
                $projectsPaging = $projectsPaging->andWhere(['agency_id' => $project['agency_id']]);
                $model->agency_id = $project['agency_id'];
            }

            if(!empty($project['sector_id']))
            {
                $projectsPaging = $projectsPaging->andWhere(['sector_id' => $project['sector_id']]);
                $model->sector_id = $project['sector_id'];

                $subSectors = SubSectorPerSector::find()
                    ->select(['sub_sector.id as id', 'sub_sector.title as title'])
                    ->leftJoin('sub_sector', 'sub_sector.id = sub_sector_per_sector.sub_sector_id')
                    ->where(['sector_id' => $project['sector_id']])
                    ->asArray()
                    ->all();
                $subSectors = ArrayHelper::map($subSectors, 'id', 'title');
            }

            if(!empty($project['sub_sector_id']))
            {
                $projectsPaging = $projectsPaging->andWhere(['sub_sector_id' => $project['sub_sector_id']]);
                $model->sub_sector_id = $project['sub_sector_id'];
            }

            if(!empty($project['fund_source_id']))
            {
                $projectsPaging = $projectsPaging->andWhere(['fund_source_id' => $project['fund_source_id']]);
                $model->fund_source_id = $project['fund_source_id'];
            }

            if(!empty($project['period']))
            {
                $projectsPaging = $projectsPaging->andWhere(['period' => $project['period']]);
                $model->period = $project['period'];
            }

            if(!empty($project['data_type']))
            {
                $projectsPaging = $projectsPaging->andWhere(['data_type' => $project['data_type']]);
                $model->data_type = $project['data_type'];
            }

            if(!empty($projectRegion['region_id']))
            {
                $regionIDs = $regionIDs->andWhere(['region_id' => $projectRegion['region_id']]);
                $provinces = $provinces->andWhere(['tblprovince.region_c' => $projectRegion['region_id']]);
                $regionModel->region_id = $projectRegion['region_id'];
            }

            if(!empty($projectProvince['province_id']))
            {
                $provinceIDs = $provinceIDs->andWhere(['province_id' => $projectProvince['province_id']]);
                $provinceModel->province_id = $projectProvince['province_id'];
            }

            if(!empty($project['title']))
            {
                $projectsPaging = $projectsPaging->andWhere(['like', 'project.title', '%'.$project['title'].'%', false]);
                $model->title = $project['title'];
            }

            if(!empty($project['project_no']))
            {
                $projectsPaging = $projectsPaging->andWhere(['like', 'project.project_no', '%'.$project['project_no'].'%', false]);
                $model->project_no = $project['project_no'];
            }

            if(!empty($projectCategory['category_id']))
            {
                $categoryIDs = $categoryIDs->andWhere(['category_id' => $projectCategory['category_id']]);
                $categoryModel->category_id = $projectCategory['category_id'];
            }

            $regionIDs = $regionIDs->all();
            $regionIDs = ArrayHelper::map($regionIDs, 'project_id', 'project_id');

            $provinceIDs = $provinceIDs->all();
            $provinceIDs = ArrayHelper::map($provinceIDs, 'project_id', 'project_id');

            $provinces = $provinces
                        ->orderBy(['abbreviation' => SORT_ASC, 'province_m' => SORT_ASC])
                        ->asArray()
                        ->all();

            $provinces = ArrayHelper::map($provinces, 'id', 'title');

            $categoryIDs = $categoryIDs->all();
            $categoryIDs = ArrayHelper::map($categoryIDs, 'project_id', 'project_id');

            if(!empty($projectRegion['region_id']))
            {
                $projectsPaging->andWhere(['id' => $regionIDs]);
            }

            if(!empty($projectProvince['province_id']))
            {
                $projectsPaging->andWhere(['id' => $provinceIDs]);
            }

            if(!empty($projectCategory['category_id']))
            {
                $projectsPaging->andWhere(['id' => $categoryIDs]);
            }

            $countProjects = clone $projectsPaging;
            $projects = clone $projectsPaging;
            $projects = $projects->all();
            $projectIds = [];
            if(!empty($projects))
            {
                foreach($projects as $project)
                {
                    $projectIds[$project['id']] = $project;

                    $totals['financials']['Q1'] += $project->financialTarget ? floatval($project->financialTarget->q1) : 0;
                    $totals['financials']['Q2'] += $project->financialTarget ? floatval($project->financialTarget->q2) : 0;
                    $totals['financials']['Q3'] += $project->financialTarget ? floatval($project->financialTarget->q3) : 0;
                    $totals['financials']['Q4'] += $project->financialTarget ? floatval($project->financialTarget->q4) : 0;
                    $totals['physicals']['Q1'] += $project->physicalTarget ? intval($project->physicalTarget->q1) : 0;
                    $totals['physicals']['Q2'] += $project->physicalTarget ? intval($project->physicalTarget->q2) : 0;
                    $totals['physicals']['Q3'] += $project->physicalTarget ? intval($project->physicalTarget->q3) : 0;
                    $totals['physicals']['Q4'] += $project->physicalTarget ? intval($project->physicalTarget->q4) : 0;
                    $totals['maleEmployed']['Q1'] += $project->maleEmployedTarget ? intval($project->maleEmployedTarget->q1) : 0;
                    $totals['maleEmployed']['Q2'] += $project->maleEmployedTarget ? intval($project->maleEmployedTarget->q2) : 0;
                    $totals['maleEmployed']['Q3'] += $project->maleEmployedTarget ? intval($project->maleEmployedTarget->q3) : 0;
                    $totals['maleEmployed']['Q4'] += $project->maleEmployedTarget ? intval($project->maleEmployedTarget->q4) : 0;
                    $totals['femaleEmployed']['Q1'] += $project->femaleEmployedTarget ? intval($project->femaleEmployedTarget->q1) : 0;
                    $totals['femaleEmployed']['Q2'] += $project->femaleEmployedTarget ? intval($project->femaleEmployedTarget->q2) : 0;
                    $totals['femaleEmployed']['Q3'] += $project->femaleEmployedTarget ? intval($project->femaleEmployedTarget->q3) : 0;
                    $totals['femaleEmployed']['Q4'] += $project->femaleEmployedTarget ? intval($project->femaleEmployedTarget->q4) : 0;
                    $totals['beneficiaries']['Q1'] += $project->beneficiaryTarget ? intval($project->beneficiaryTarget->q1) : 0;
                    $totals['beneficiaries']['Q2'] += $project->beneficiaryTarget ? intval($project->beneficiaryTarget->q2) : 0;
                    $totals['beneficiaries']['Q3'] += $project->beneficiaryTarget ? intval($project->beneficiaryTarget->q3) : 0;
                    $totals['beneficiaries']['Q4'] += $project->beneficiaryTarget ? intval($project->beneficiaryTarget->q4) : 0;
                    $totals['groupBeneficiaries']['Q1'] += $project->groupTarget ? intval($project->groupTarget->q1) : 0;
                    $totals['groupBeneficiaries']['Q2'] += $project->groupTarget ? intval($project->groupTarget->q2) : 0;
                    $totals['groupBeneficiaries']['Q3'] += $project->groupTarget ? intval($project->groupTarget->q3) : 0;
                    $totals['groupBeneficiaries']['Q4'] += $project->groupTarget ? intval($project->groupTarget->q4) : 0;
                }
            }

            $projectsPages = new Pagination(['totalCount' => $countProjects->count()]);
            $projectsModels = $projectsPaging->offset($projectsPages->offset)
                ->limit($projectsPages->limit)
                ->orderBy(['id' => SORT_DESC])
                ->all();

            $dueDate = $dueDate->one();

            return $this->render('index', [
                'model' => $model,
                'regionModel' => $regionModel,
                'provinceModel' => $provinceModel,
                'categoryModel' => $categoryModel,
                'years' => $years,
                'quarters' => $quarters,
                'genders' => $genders,
                'agencies' => $agencies,
                'programs' => $programs,
                'sectors' => $sectors,
                'subSectors' => $subSectors,
                'modes' => $modes,
                'fundSources' => $fundSources,
                'scopes' => $scopes,
                'regions' => $regions,
                'provinces' => $provinces,
                'categories' => $categories,
                'kras' => $kras,
                'goals' => $goals,
                'chapters' => $chapters,
                'dueDate' => $dueDate,
                'projectIds' => $projectIds,
                'projectsModels' => $projectsModels,
                'projectsPages' => $projectsPages,
                'submissionModel' => $submissionModel,
                'projectCount' => $projectCount,
                'totals' => $totals,
                'periods' => $periods,
                'dataTypes' => $dataTypes,
            ]);
        }

        $dueDate = $dueDate->one();

        return $this->render('index', [
            'model' => $model,
            'regionModel' => $regionModel,
            'provinceModel' => $provinceModel,
            'categoryModel' => $categoryModel,
            'years' => $years,
            'quarters' => $quarters,
            'genders' => $genders,
            'agencies' => $agencies,
            'programs' => $programs,
            'sectors' => $sectors,
            'subSectors' => $subSectors,
            'modes' => $modes,
            'fundSources' => $fundSources,
            'scopes' => $scopes,
            'regions' => $regions,
            'provinces' => $provinces,
            'categories' => $categories,
            'kras' => $kras,
            'goals' => $goals,
            'chapters' => $chapters,
            'dueDate' => $dueDate,
            'projectIds' => $projectIds,
            'projectsModels' => $projectsModels,
            'projectsPages' => $projectsPages,
            'submissionModel' => $submissionModel,
            'projectCount' => $projectCount,
            'totals' => $totals,
            'periods' => $periods,
            'dataTypes' => $dataTypes,
        ]);
    } */

    public function actionIndex()
    {
        $searchModel = new SubmissionSearch();
        $searchModel->report = 'Monitoring Plan';

        if(Yii::$app->user->can('AgencyUser'))
        {
            $searchModel->agency_id = Yii::$app->user->identity->userinfo->AGENCY_C;
        }

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = Submission::findOne($id);

        if(!$model){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $dueDate = DueDate::findOne(['year' => $model->year, 'report' => 'Monitoring Plan']);

        if(!Yii::$app->user->can('Administrator')){
            if($model->agency_id != Yii::$app->user->identity->userinfo->AGENCY_C){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        $searchModel = new PlanSearch();
        $searchModel->submission_id = $model->id;

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $projects = [];

        if($model->plans){
            foreach($model->plans as $plan){
                $projects[$plan->id] = $plan;
            }
        }

        if(MultipleModel::loadMultiple($projects, Yii::$app->request->post()))
        {
            $selectedProjects = [];
            $postData = Yii::$app->request->post('Plan');
            if(!empty($postData)){
                foreach($postData as $project){
                    if($project['id'] != 0){
                        $plan = Plan::findOne($project['id']);
                        if($plan){
                            ProjectExpectedOutput::deleteAll([
                                'project_id' => $plan->project_id,
                                'year' => $plan->year
                            ]);
    
                            ProjectTarget::deleteAll([
                                'project_id' => $plan->project_id,
                                'year' => $plan->year
                            ]);

                            $plan->delete();
                        }
                    }
                }
            }


            \Yii::$app->getSession()->setFlash('success', 'Selected projects were removed in monitoring plan successfully');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'projects' => $projects,
            'dueDate' => $dueDate,
        ]);
    }

    public function actionTarget($id)
    {
        $model = Submission::findOne($id);

        $dueDate = DueDate::findOne(['year' => $model->year, 'report' => 'Monitoring Plan']);

        if(!Yii::$app->user->can('Administrator')){
            if($model->agency_id != Yii::$app->user->identity->userinfo->AGENCY_C){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        $months = [
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
        ];

        $targets = [];
        $oiTargets = [];
        $indicators = [];

        $metrics = [
            'Percentage' => '%',
            'Numerical' => '123'
        ];

        $projectIDs = $model->plans ? ArrayHelper::map($model->plans, 'project_id', 'project_id') : [];

        $projectsPaging = Plan::find();
        $projectsPaging
            ->andWhere(['project_id' => $projectIDs])
            ->andWhere(['submission_id' => $model->id]);
        $countProjects = clone $projectsPaging;
        $projectsPages = new Pagination([
            'totalCount' => $countProjects->count(),
            'pageSize' => 5
        ]);
        $projectsModels = $projectsPaging->offset($projectsPages->offset)
            ->limit($projectsPages->limit)
            ->orderBy(['project_id' => SORT_ASC])
            ->all();

        if($projectsModels){
            foreach($projectsModels as $plan){
                $physical = ProjectTarget::findOne([
                                'project_id' => $plan->project_id,
                                'year' => $plan->year,
                                'target_type' => 'Physical',
                            ]) ? ProjectTarget::findOne([
                                'project_id' => $plan->project_id,
                                'year' => $plan->year,
                                'target_type' => 'Physical',
                            ]) : new ProjectTarget();
                
                $physical->scenario = 'physicalTarget';
                $physical->project_id = $plan->project_id;
                $physical->year = $plan->year;
                $physical->target_type = 'Physical';

                $targets[$plan->project_id]['physical'] = $physical;

                $financial = ProjectTarget::findOne([
                    'project_id' => $plan->project_id,
                    'year' => $plan->year,
                    'target_type' => 'Financial',
                ]) ? ProjectTarget::findOne([
                    'project_id' => $plan->project_id,
                    'year' => $plan->year,
                    'target_type' => 'Financial',
                ]) : new ProjectTarget();
    
                $financial->scenario = 'financialTarget';
                $financial->project_id = $plan->project_id;
                $financial->year = $plan->year;
                $financial->target_type = 'Financial';

                $targets[$plan->project_id]['financial'] = $financial;

                $maleEmployed = ProjectTarget::findOne([
                    'project_id' => $plan->project_id,
                    'year' => $plan->year,
                    'target_type' => 'Male Employed',
                ]) ? ProjectTarget::findOne([
                    'project_id' => $plan->project_id,
                    'year' => $plan->year,
                    'target_type' => 'Male Employed',
                ]) : new ProjectTarget();
    
                $maleEmployed->scenario = 'employmentTarget';
                $maleEmployed->project_id = $plan->project_id;
                $maleEmployed->year = $plan->year;
                $maleEmployed->target_type = 'Male Employed';

                $targets[$plan->project_id]['maleEmployed'] = $maleEmployed;

                $femaleEmployed = ProjectTarget::findOne([
                    'project_id' => $plan->project_id,
                    'year' => $plan->year,
                    'target_type' => 'Female Employed',
                ]) ? ProjectTarget::findOne([
                    'project_id' => $plan->project_id,
                    'year' => $plan->year,
                    'target_type' => 'Female Employed',
                ]) : new ProjectTarget();
                    
                $femaleEmployed->scenario = 'employmentTarget';
                $femaleEmployed->project_id = $plan->project_id;
                $femaleEmployed->year = $plan->year;
                $femaleEmployed->target_type = 'Female Employed';

                $targets[$plan->project_id]['femaleEmployed'] = $femaleEmployed;

                $ois = $plan->project->getProjectExpectedOutputs()->where(['year' => $model->year])->all();

                if($ois){
                    foreach($ois as $oi){
                        $oiTargets[$plan->project->id][$oi->indicator] = $oi; 
                    }
                }
            }
        }

        if(Yii::$app->request->post()){
            $postData = Yii::$app->request->post();
            $getData = Yii::$app->request->get();

            $targetModels = isset($postData['ProjectTarget']) ? $postData['ProjectTarget'] : [];
            $oiTargetModels = isset($postData['ProjectExpectedOutput']) ? $postData['ProjectExpectedOutput'] : [];

            if(!empty($targetModels)){
                foreach($targetModels as $projectID => $targetModel){

                    $physicalValue = $targetModel['physical'];

                    $physicalModel = ProjectTarget::findOne([
                        'project_id' => $projectID,
                        'year' => $model->year,
                        'target_type' => 'Physical',
                    ]) ? ProjectTarget::findOne([
                        'project_id' => $projectID,
                        'year' => $model->year,
                        'target_type' => 'Physical',
                    ]) : new ProjectTarget();
                    
                    $physicalModel->project_id = $projectID;
                    $physicalModel->year = $model->year;
                    $physicalModel->target_type = 'Physical';
                    $physicalModel->indicator = !empty($physicalValue['updatedIndicator']) ? $physicalValue['updatedIndicator'] : $physicalValue['indicator'];
                    $physicalModel->type = !empty($physicalValue['updatedType']) ? $physicalValue['updatedType'] : $physicalValue['type'];
                    $physicalModel->baseline = $this->removeMask($physicalValue['baseline']);

                    foreach($months as $mo => $month){
                        $physicalModel->$mo = $this->removeMask($physicalValue[$mo]);
                    }

                    $physicalModel->save(false);

                    $financialValue = $targetModel['financial'];

                    $financialModel = ProjectTarget::findOne([
                        'project_id' => $projectID,
                        'year' => $model->year,
                        'target_type' => 'Financial',
                    ]) ? ProjectTarget::findOne([
                        'project_id' => $projectID,
                        'year' => $model->year,
                        'target_type' => 'Financial',
                    ]) : new ProjectTarget();
                    
                    $financialModel->project_id = $projectID;
                    $financialModel->year = $model->year;
                    $financialModel->target_type = 'Financial';
                    $financialModel->allocation = $this->removeMask($financialValue['allocation']);
                    $financialModel->releases = $this->removeMask($financialValue['releases']);

                    foreach($months as $mo => $month){
                        $financialModel->$mo = $this->removeMask($financialValue[$mo]);
                    }

                    $financialModel->save(false);

                    $maleEmployedValue = $targetModel['maleEmployed'];

                    $maleEmployedModel = ProjectTarget::findOne([
                        'project_id' => $projectID,
                        'year' => $model->year,
                        'target_type' => 'Male Employed',
                    ]) ? ProjectTarget::findOne([
                        'project_id' => $projectID,
                        'year' => $model->year,
                        'target_type' => 'Male Employed',
                    ]) : new ProjectTarget();
                    
                    $maleEmployedModel->project_id = $projectID;
                    $maleEmployedModel->year = $model->year;
                    $maleEmployedModel->target_type = 'Male Employed';
                    $maleEmployedModel->annual = $this->removeMask($maleEmployedValue['annual']);

                    $maleEmployedModel->save(false);

                    $femaleEmployedValue = $targetModel['femaleEmployed'];

                    $femaleEmployedModel = ProjectTarget::findOne([
                        'project_id' => $projectID,
                        'year' => $model->year,
                        'target_type' => 'Female Employed',
                    ]) ? ProjectTarget::findOne([
                        'project_id' => $projectID,
                        'year' => $model->year,
                        'target_type' => 'Female Employed',
                    ]) : new ProjectTarget();
                    
                    $femaleEmployedModel->project_id = $projectID;
                    $femaleEmployedModel->year = $model->year;
                    $femaleEmployedModel->target_type = 'Female Employed';
                    $femaleEmployedModel->annual = $this->removeMask($femaleEmployedValue['annual']);

                    $femaleEmployedModel->save(false);
                    
                    if(!empty($oiTargetModels)){
                        if(!empty($oiTargetModels[$projectID])){
                            foreach($oiTargetModels[$projectID] as $oiID => $oiTarget){
        
                                $oiModel = ProjectExpectedOutput::findOne([
                                    'project_id' => $projectID,
                                    'year' => $model->year,
                                    'indicator' => $oiID,
                                ]) ? ProjectExpectedOutput::findOne([
                                    'project_id' => $projectID,
                                    'year' => $model->year,
                                    'indicator' => $oiID,
                                ]) : new ProjectExpectedOutput();
        
                                $oiModel->project_id = $projectID;
                                $oiModel->year = $model->year;
                                $oiModel->indicator = $oiID;
                                $oiModel->target = $oiTarget['target'];
                                $oiModel->type = 'Numerical';
                                $oiModel->baseline = $this->removeMask($oiTarget['baseline']);

                                foreach($months as $mo => $month){
                                    $oiModel->$mo = $this->removeMask($oiTarget[$mo]);
                                }

                                $oiModel->save(false);
                            }
                        }
                    }
                }
            }

            \Yii::$app->getSession()->setFlash('success', 'Targets were saved successfully');
                    return isset($getData['page']) ? 
                        $this->redirect(['target', 
                            'id' => $model->id, 
                            'page' => $getData['page'],
                        ]) : $this->redirect(['target', 
                            'id' => $model->id,
                        ]);
        }

        return $this->render('target', [
            'model' => $model,
            'projectsModels' => $projectsModels,
            'projectsPages' => $projectsPages,
            'months' => $months,
            'targets' => $targets,
            'oiTargets' => $oiTargets,
            'metrics' => $metrics,
            'dueDate' => $dueDate,
        ]);
    }

    public function actionCreate()
    {

        $model = new Submission();
        $model->scenario = Yii::$app->user->can('Administrator') ? 'createMonitoringPlanAdmin' : 'createMonitoringPlan';

        $model->report = 'Monitoring Plan';
        $model->agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : $model->agency_id;
        $model->draft = 'Yes';

        $agencies = Agency::find()->select(['id', 'concat(title," (",code,")") as title'])->orderBy(['title' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            \Yii::$app->getSession()->setFlash('success', 'Record created');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->renderAjax('create', [
            'model' => $model,
            'agencies' => $agencies,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = Submission::findOne($id);

        if(!Yii::$app->user->can('Administrator')){
            if($model->agency_id != Yii::$app->user->identity->userinfo->AGENCY_C){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        $model->scenario = Yii::$app->user->can('Administrator') ? 'createMonitoringPlanAdmin' : 'createMonitoringPlan';

        $agencies = Agency::find()->select(['id', 'concat(title," (",code,")") as title'])->orderBy(['title' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            \Yii::$app->getSession()->setFlash('success', 'Record updated');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->renderAjax('update', [
            'model' => $model,
            'agencies' => $agencies,
        ]);
    }

    public function actionDelete($id)
    {
        $model = Submission::findOne($id);

        if(!Yii::$app->user->can('Administrator')){
            if($model->agency_id != Yii::$app->user->identity->userinfo->AGENCY_C){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        if(Yii::$app->request->post())
        {
            $model->delete();

            \Yii::$app->getSession()->setFlash('success', 'Record deleted');

            return $this->redirect(['index']);
        }
    }

    public function actionInclude($id)
    {

        $model = Submission::findOne($id);

        $dueDate = DueDate::findOne(['year' => $model->year, 'report' => 'Monitoring Plan']);

        if($dueDate){
            if(strtotime(date("Y-m-d")) >= strtotime($dueDate->due_date)){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if(!Yii::$app->user->can('Administrator')){
            if($model->agency_id != Yii::$app->user->identity->userinfo->AGENCY_C){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        $existingProjects = $model->plans;
        $existingProjects = ArrayHelper::map($existingProjects, 'project_id', 'project_id');

        $projects = [];

        $availableProjects = Project::find()
                    ->andWhere(['agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C])
                    ->andWhere(['source_id' => null])
                    ->andWhere(['draft' => 'No'])
                    ->andWhere(['not in', 'id', $existingProjects]);                    
                    /* ->orderBy(['id' => SORT_DESC])
                    ->all(); */
        $countProjects = clone $availableProjects;
        $projectsPages = new Pagination([
            'totalCount' => $countProjects->count(),
            'pageSize' => 20
        ]);
        $projectsModels = $availableProjects->offset($projectsPages->offset)
        ->limit($projectsPages->limit)
        ->orderBy(['id' => SORT_DESC])
        ->all();


        if($projectsModels){
            foreach($projectsModels as $project){
                $projects[$project->id] = $project;
            }
        }

        if(MultipleModel::loadMultiple($projects, Yii::$app->request->post()))
        {
            $selectedProjects = [];
            $postData = Yii::$app->request->post('Project');
            if(!empty($postData)){
                foreach($postData as $project){
                    if($project['id'] != 0){
                        $project = Project::findOne($project['id']);

                        $includedProject = new Plan();
                        $includedProject->submission_id = $model->id;
                        $includedProject->project_id = $project['id'];
                        $includedProject->year = $model->year;
                        $includedProject->submitted_by = Yii::$app->user->id;
                        if($includedProject->save(false)){
                            if($project->projectHasOutputIndicators){
                                foreach($project->projectHasOutputIndicators as $oi){
                                    $eoModel = new ProjectExpectedOutput();
                                    $eoModel->project_id = $project->id;
                                    $eoModel->year = $model->year;
                                    $eoModel->indicator = $oi->indicator;
                                    $eoModel->target = $oi->target;
                                    $eoModel->save(false);
                                }
                            }   

                            if($project->projectHasOutcomeIndicators){
                                foreach($project->projectHasOutcomeIndicators as $oi){
                                    $outcomeModel = new ProjectOutcome();
                                    $outcomeModel->project_id = $project->id;
                                    $outcomeModel->year = $model->year;
                                    $outcomeModel->outcome = $oi->indicator;
                                    $outcomeModel->save(false);
                                }
                            }   
                        }                     
                    }
                }
            }

            \Yii::$app->getSession()->setFlash('success', 'Selected projects were included in monitoring plan successfully');
            return $this->redirect(['view', 'id' => $model->id]);

        }
        return $this->render('include', [
            'model' => $model,
            'projects' => $projects,
            'projectsModels' => $projectsModels,
            'projectsPages' => $projectsPages,
            'dueDate' => $dueDate,
        ]);

    }

    public function actionOutputIndicator($id, $plan_id, $year)
    {
        $model = Submission::findOne($id);

        $plan = Plan::findOne($plan_id);

        $expectedOutputs = $plan->project->getProjectExpectedOutputs()->where(['year' => $year])->all();

        $months = [
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
        ];

        return $this->renderAjax('output-indicator', [
            'model' => $model,
            'plan' => $plan,
            'months' => $months,
            'expectedOutputs' => $expectedOutputs
        ]);
    }

    public function actionCreateOutputIndicator($plan_id, $submission_id)
    {
        $plan = Plan::findOne($plan_id);

        $submission = Submission::findOne($submission_id);

        $model = new ProjectExpectedOutput();
        $model->project_id = $plan->project_id;
        $model->year = $plan->year;

        if($model->load(Yii::$app->request->post()) && $model->save()){
            $getData = Yii::$app->request->get();

            \Yii::$app->getSession()->setFlash('success', 'Output indicator has been added successfully');
            return isset($getData['page']) ? 
                $this->redirect(['view', 
                    'id' => $submission->id, 
                    'page' => $getData['page'],
                ]) : $this->redirect(['view', 
                    'id' => $submission->id,
                ]);
        }

        return $this->renderAjax('_output-indicator-form', [
            'model' => $model,
            'plan' => $plan,
            'submission' => $submission,
        ]);
    }

    public function actionUpdateOutputIndicator($id, $plan_id, $submission_id)
    {
        $model = ProjectExpectedOutput::findOne($id);

        $plan = Plan::findOne($plan_id);

        $submission = Submission::findOne($submission_id);

        if($model->load(Yii::$app->request->post()) && $model->save()){
            $getData = Yii::$app->request->get();

            \Yii::$app->getSession()->setFlash('success', 'Output indicator has been updated successfully');
            return isset($getData['page']) ? 
                $this->redirect(['view', 
                    'id' => $submission->id, 
                    'page' => $getData['page'],
                ]) : $this->redirect(['view', 
                    'id' => $submission->id,
                ]);
        }

        return $this->renderAjax('_output-indicator-form', [
            'model' => $model,
            'plan' => $plan,
            'submission' => $submission,
        ]);
    }

    public function actionDeleteOutputIndicator($id, $plan_id, $submission_id)
    {
        $model = ProjectExpectedOutput::findOne($id);

        $plan = Plan::findOne($plan_id);

        $submission = Submission::findOne($submission_id);

        if(Yii::$app->request->post())
        {
            $getData = Yii::$app->request->get();

            if($model->delete())
            {
                \Yii::$app->getSession()->setFlash('success', 'Output indicator has been deleted successfully');
                    return isset($getData['page']) ? 
                        $this->redirect(['view', 
                            'id' => $submission->id, 
                            'page' => $getData['page'],
                        ]) : $this->redirect(['view', 
                            'id' => $submission->id,
                        ]);
                    }
        }

        
    }

    public function actionMonitoringPlan($filters)
    {
        $dueDate = DueDate::findOne(['report' => 'Monitoring Plan', 'year' => date("Y")]);
        $projectsPaging = Project::find();
        $projectsPaging = Yii::$app->user->can('AgencyUser') ? 
            $projectsPaging
            ->andWhere(['draft' => 'No', 'year' => $year])
            ->andWhere(['agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]) 
            : 
            $projectsPaging
            ->andWhere(['draft' => 'No', 'year' => $year])
            ;

        $countProjects = clone $projectsPaging;
        $projects = clone $projectsPaging;
        $projects = $projects->all();
        $projectIds = [];
        if(!empty($projects))
        {
            foreach($projects as $project)
            {
                $projectIds[$project['id']] = $project;
            }
        }

        $projectsPages = new Pagination(['totalCount' => $countProjects->count()]);
        $projectsModels = $projectsPaging->offset($projectsPages->offset)
            ->limit($projectsPages->limit)
            ->orderBy(['id' => SORT_DESC])
            ->all();
    }

    public function actionCount($agency_id)
    {
        $count = Plan::find()->select(['distinct(project.id) as id'])->leftJoin('project', 'project.id = plan.project_id')
        ->andWhere(['project.agency_id' => $agency_id])
        ->count();

        return $count;
    }

    public function actionSubmissionInfo($agency_id)
    {
        $submission = Submission::findOne(['agency_id' => $agency_id, 'year' => date("Y"), 'report' => 'Monitoring Plan', 'draft' => 'No']);

        return $submission ? '<i class="fa fa-exclamation-circle"></i> Monitoring plan has been submitted last '.date("F j, Y H:i:s", strtotime($submission->date_submitted)).' by '.$submission->submitter : '<i class="fa fa-exclamation-circle"></i> Monitoring plan not yet submitted';
    }

    public function actionSubmit($id)
    {
        $model = Submission::findOne($id);

        if(Yii::$app->request->post())
        {
            $model->submitted_by = Yii::$app->user->id;
            $model->date_submitted = date("Y-m-d H:i:s");
            $model->draft = 'No';
            $model->save(false);

            $logModel = new SubmissionLog();
            $logModel->submission_id = $model->id;
            $logModel->user_id = Yii::$app->user->id;
            $logModel->status = 'Submitted';

            if($logModel->save(false))
            {
                \Yii::$app->getSession()->setFlash('success', 'Monitoring plan has been submitted successfully');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
    }

    public function actionDownload(
        $id,
        $type,
    )
    {
        $model = Submission::findOne($id);

        $projectIDs = ArrayHelper::map($model->plans, 'project_id', 'project_id');

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

        $targets = [];

        $financials = ProjectTarget::find()->where(['target_type' => 'Financial', 'year' => $model->year, 'project_id' => $projectIDs])->asArray()->all();

        if(!empty($financials)){
            foreach($financials as $target){
                $targets['financial'][$target['project_id']] = $target;
            }
        }

        $financialTargets = ProjectTarget::find()->where(['target_type' => 'Financial', 'year' => $model->year, 'project_id' => $projectIDs])->createCommand()->getRawSql();
        
        
        $physicals = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $model->year])->asArray()->all();

        if(!empty($physicals)){
            foreach($physicals as $target){
                $targets['physical'][$target['project_id']] = $target;
            }
        }

        $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $model->year, 'project_id' => $projectIDs])->createCommand()->getRawSql();

        $malesEmployed = ProjectTarget::find()->where(['target_type' => 'Male Employed', 'year' => $model->year, 'project_id' => $projectIDs])->asArray()->all();

        if(!empty($malesEmployed)){
            foreach($malesEmployed as $target){
                $targets['maleEmployed'][$target['project_id']] = $target;
            }
        }

        $maleEmployedTargets = ProjectTarget::find()->where(['target_type' => 'Male Employed', 'year' => $model->year, 'project_id' => $projectIDs])->createCommand()->getRawSql();


        $femalesEmployed = ProjectTarget::find()->where(['target_type' => 'Female Employed', 'year' => $model->year, 'project_id' => $projectIDs])->asArray()->all();

        if(!empty($femalesEmployed)){
            foreach($femalesEmployed as $target){
                $targets['maleEmployed'][$target['project_id']] = $target;
            }
        }

        $femaleEmployedTargets = ProjectTarget::find()->where(['target_type' => 'Female Employed', 'year' => $model->year, 'project_id' => $projectIDs])->createCommand()->getRawSql();

        $outputIndicatorTargets = ProjectExpectedOutput::find()->where(['year' => $model->year, 'project_id' => $projectIDs])->orderBy(['id' => SORT_ASC])->asArray()->all();

        if (!empty($outputIndicatorTargets)) {
            foreach ($outputIndicatorTargets as $i => $target) {
                $targets['outputIndicators'][$target['project_id']][] = $target; // Change $i to [] to automatically append the next index
        
                $currentIndex = count($targets['outputIndicators'][$target['project_id']]) - 1; // Get the current index
        
                $targets['outputIndicators'][$target['project_id']][$currentIndex]['total'] = 0;
                $targets['outputIndicators'][$target['project_id']][$currentIndex]['rawTotal'] = 0;
        
                if ($target['type'] == 'Percentage') {
                    foreach ($months as $mo => $month) {
                        $targets['outputIndicators'][$target['project_id']][$currentIndex]['total'] += floatval($target[$mo]);
                    }
                } else {
                    foreach ($months as $mo => $month) {
                        $targets['outputIndicators'][$target['project_id']][$currentIndex]['rawTotal'] += floatval($target[$mo]);
                    }
                    $targets['outputIndicators'][$target['project_id']][$currentIndex]['total'] = $targets['outputIndicators'][$target['project_id']][$currentIndex]['rawTotal'] > 0 ? 100 : 0;
                }
            }

            // Reindex the array numerically
            foreach ($targets['outputIndicators'] as $outputIndicators) {
                $outputIndicators = array_values($outputIndicators);
            }
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
        
        $barangayTitles = ProjectBarangay::find()
                    ->select(['project_id', 'GROUP_CONCAT(DISTINCT concat(tblbarangay.barangay_m,", ",tblcitymun.citymun_m,", ",tblprovince.province_m) ORDER BY tblbarangay.barangay_m ASC, tblcitymun.citymun_m ASC, tblprovince.province_m ASC SEPARATOR ", <br>") as title'])
                    ->leftJoin('tblbarangay', 'tblbarangay.province_c = project_barangay.province_id and tblbarangay.citymun_c = project_barangay.citymun_id and tblbarangay.barangay_c = project_barangay.barangay_id')
                    ->leftJoin('tblcitymun', 'tblcitymun.province_c = project_barangay.province_id and tblcitymun.citymun_c = project_barangay.citymun_id')
                    ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
                    ->leftJoin('project', 'project.id = project_barangay.project_id')
                    ->where(['project.draft' => 'No'])
                    ->groupBy(['project_barangay.project_id'])
                    ->createCommand()->getRawSql();

        $componentTitles = Project::find()
                    ->select([
                        'pro.source_id as project_id', 
                        'GROUP_CONCAT(DISTINCT CONCAT(row_number, ". ", pro.title) ORDER BY pro.id ASC SEPARATOR "<br>") as title'
                    ])
                    ->from(['pro' => Project::tableName()])
                    ->leftJoin('project mp', 'mp.id = pro.source_id')
                    ->leftJoin(
                        ['subquery' => Project::find()
                            ->select(['id', 'source_id', 'ROW_NUMBER() OVER (PARTITION BY source_id ORDER BY id) AS row_number'])
                        ],
                        'subquery.source_id = pro.source_id and subquery.id = pro.id'
                    )
                    ->andWhere(['mp.draft' => 'No'])
                    ->groupBy(['pro.source_id'])
                    ->createCommand()->getRawSql();
            
        $fundingSourceTitles = ProjectHasFundSources::find()
                    ->select([
                        'phfs.project_id',
                        'GROUP_CONCAT(DISTINCT CONCAT(row_number, ". ", fund_source.title) ORDER BY phfs.id ASC SEPARATOR "<br>") as title'
                    ])
                    ->from(['phfs' => ProjectHasFundSources::tableName()])
                    ->leftJoin('fund_source', 'fund_source.id = phfs.fund_source_id')
                    ->leftJoin('project', 'project.id = phfs.project_id')
                    ->leftJoin(
                        ['subquery' => ProjectHasFundSources::find()
                            ->select(['project_id', 'fund_source_id', 'ROW_NUMBER() OVER (PARTITION BY project_id ORDER BY fund_source_id) AS row_number'])
                        ],
                        'subquery.project_id = phfs.project_id AND subquery.fund_source_id = phfs.fund_source_id'
                    )
                    ->where(['project.draft' => 'No'])
                    ->groupBy(['phfs.project_id'])
                    ->createCommand()->getRawSql();
        
        $fundingAgencyTitles = ProjectHasFundSources::find()
                    ->select([
                        'phfs.project_id', 
                        'GROUP_CONCAT(DISTINCT CONCAT(row_number, ". ", phfs.agency) ORDER BY phfs.id ASC SEPARATOR "<br>") as title'
                        ])
                    ->from(['phfs' => ProjectHasFundSources::tableName()])
                    ->leftJoin('project', 'project.id = phfs.project_id')
                    ->leftJoin(
                        ['subquery' => ProjectHasFundSources::find()
                            ->select(['project_id', 'fund_source_id', 'ROW_NUMBER() OVER (PARTITION BY project_id ORDER BY fund_source_id) AS row_number'])
                        ],
                        'subquery.project_id = phfs.project_id AND subquery.fund_source_id = phfs.fund_source_id'
                    )
                    ->where(['project.draft' => 'No'])
                    ->groupBy(['phfs.project_id'])
                    ->createCommand()->getRawSql();

        $outputIndicatorTitles = ProjectExpectedOutput::find()
                    ->select([
                        'peo.project_id', 
                        'GROUP_CONCAT(DISTINCT CONCAT(row_number, ". ", peo.indicator) ORDER BY peo.id ASC SEPARATOR "<br>") as title'
                        ])
                    ->from(['peo' => ProjectExpectedOutput::tableName()])
                    ->leftJoin('project', 'project.id = peo.project_id')
                    ->leftJoin(
                        ['subquery' => ProjectExpectedOutput::find()
                            ->select(['id', 'project_id', 'ROW_NUMBER() OVER (PARTITION BY project_id ORDER BY id) AS row_number'])
                        ],
                        'subquery.project_id = peo.project_id AND subquery.id = peo.id'
                    )
                    ->where(['project.draft' => 'No'])
                    ->groupBy(['peo.project_id'])
                    ->createCommand()->getRawSql();
        
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
        
        $projects = Project::find()
                    ->select([
                        'project.id',
                        'project.project_no as project_no',
                        'project.title as title',
                        'componentTitles.title as componentTitle',
                        'fundingSourceTitles.title as fundingSourceTitle',
                        'fundingAgencyTitles.title as fundingAgencyTitle',
                        'mode_of_implementation.title as modeOfImplementationTitle',
                        'COALESCE(project.cost, 0) as cost',
                        'sector.title as sectorTitle',
                        'provinceTitles.title as provinceTitle',
                        'citymunTitles.title as citymunTitle',
                        'barangayTitles.title as barangayTitle',
                        'DATE_FORMAT(project.start_date, "%m-%d-%y") as startDate',
                        'DATE_FORMAT(project.completion_date, "%m-%d-%y") as endDate',
                        'project.remarks as remarks',
                        'maleEmployedTargets.annual as maleEmployedTotal',
                        'femaleEmployedTargets.annual as femaleEmployedTotal',
                        'outputIndicatorTitles.title as outputIndicatorTitle',
                        'COALESCE('.$financialTotal.', 0) as financialTotal',
                        'COALESCE('.$physicalTotal.', 0) as physicalTotal',
                        'physicalTargets.type as metrics'
                    ]);

        $projects = $projects->leftJoin(['financialTargets' => '('.$financialTargets.')'], 'financialTargets.project_id = project.id');
        $projects = $projects->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
        $projects = $projects->leftJoin(['maleEmployedTargets' => '('.$maleEmployedTargets.')'], 'maleEmployedTargets.project_id = project.id');
        $projects = $projects->leftJoin(['femaleEmployedTargets' => '('.$femaleEmployedTargets.')'], 'femaleEmployedTargets.project_id = project.id');
        $projects = $projects->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id');
        $projects = $projects->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
        $projects = $projects->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id');
        $projects = $projects->leftJoin(['barangayTitles' => '('.$barangayTitles.')'], 'barangayTitles.project_id = project.id');
        $projects = $projects->leftJoin(['componentTitles' => '('.$componentTitles.')'], 'componentTitles.project_id = project.id');
        $projects = $projects->leftJoin(['fundingSourceTitles' => '('.$fundingSourceTitles.')'], 'fundingSourceTitles.project_id = project.id');
        $projects = $projects->leftJoin(['fundingAgencyTitles' => '('.$fundingAgencyTitles.')'], 'fundingAgencyTitles.project_id = project.id');
        $projects = $projects->leftJoin(['outputIndicatorTitles' => '('.$outputIndicatorTitles.')'], 'outputIndicatorTitles.project_id = project.id');
        $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
        $projects = $projects->leftJoin('mode_of_implementation', 'mode_of_implementation.id = project.mode_of_implementation_id');
        $projects = $projects->leftJoin('sector', 'sector.id = project.sector_id');
        $projects = $projects->leftJoin('sub_sector', 'sub_sector.id = project.sub_sector_id');
        $projects = $projects->leftJoin('fund_source', 'fund_source.id = project.fund_source_id');
        $projects = $projects->andWhere(['project.draft' => 'No']);
        $projects = $projects->andWhere(['project.source_id' => null]);
        $projects = $projects->andWhere(['project.id' => $projectIDs]);
        $projects = $projects 
                    ->asArray()
                    ->all();

        $maxOutputIndicator = ProjectExpectedOutput::find()
                ->select(['count(id) as total'])
                ->andWhere(['project_id' => $projectIDs])
                ->andWhere(['year' => $model->year])
                ->groupBy(['project_id'])
                ->orderBy(['count(id)' => SORT_DESC])
                ->asArray()
                ->one();
        
        $filename = date("YmdHis").'_'.$model->agency->code.'_'.$model->year.'_'.'RPMES_Form_1';

        if($type == 'excel')
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=".$filename.".xls");
            return $this->renderPartial('_plan-file', [
                'model' => $model,
                'type' => $type,
                'projects' => $projects,
                'months' => $months,
                'maxOutputIndicator' => $maxOutputIndicator,
                'targets' => $targets,
            ]);
        }else if($type == 'pdf')
        {
            $content = $this->renderPartial('_plan-file', [
                'model' => $model,
                'type' => $type,
                'projects' => $projects,
                'months' => $months,
                'maxOutputIndicator' => $maxOutputIndicator,
                'targets' => $targets,
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
                'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
                'cssInline' => 'table{font-family: "Arial";border-collapse: collapse;}thead{font-size: 14px;text-align: center;vertical-align: middle;background-color: #002060;color: white;}thead tr{background-color: #002060;color: white;}td{font-size: 14px;border: 1px solid black;vertical-align: middle;}th{text-align: center;border: 1px solid black;vertical-align: middle;}h1,h2,h3,h4,h5,h6{text-align: center;font-weight: bolder;}', 
                ]);
        
                $response = Yii::$app->response;
                $response->format = \yii\web\Response::FORMAT_RAW;
                $headers = Yii::$app->response->headers;
                $headers->add('Content-Type', 'application/pdf');
                return $pdf->render();
        }else if($type == 'print')
        {
            return $this->renderAjax('_plan-file', [
                'model' => $model,
                'type' => $type,
                'projects' => $projects,
                'months' => $months,
                'maxOutputIndicator' => $maxOutputIndicator,
                'targets' => $targets,
            ]);
        }
    }

    public function actionAcknowledge($id)
    {
        $officeTitle = Settings::findOne(['Agency Title Long']);
        $officeAddress = Settings::findOne(['Agency Address']);
        $officeHead = Settings::findOne(['Agency Head']);
        $officeTitleShort = Settings::findOne(['Agency Title Short']);
        $submission = Submission::findOne($id);
        $agency = Agency::findOne(['id' => $submission->agency_id]);
        $model = Acknowledgment::findOne(['submission_id' => $submission->id]) ? Acknowledgment::findOne(['submission_id' => $submission->id]) : new Acknowledgment();

        $lastAcknowledgment = Acknowledgment::find()->orderBy(['id' => SORT_DESC])->one();
        $lastNumber = $lastAcknowledgment ? intval($lastAcknowledgment->id) + 1 : '1';
        $model->submission_id = $submission->id;
        $model->control_no = $model->isNewRecord ? 'NEDARO1-QOP-03-'.date("Y").'001'.$lastNumber : $model->control_no;
        $model->recipient_name = $agency->head;
        $model->recipient_designation = $agency->head_designation;
        $model->recipient_office = $agency->title;
        $model->recipient_address = $agency->address;

        if($model->load(Yii::$app->request->post()))
        {
            $model->acknowledged_by = Yii::$app->user->id;
            if($model->save()){
                $logModel = new SubmissionLog();
                $logModel->submission_id = $submission->id;
                $logModel->user_id = Yii::$app->user->id;
                $logModel->status = 'Acknowledged';

                if($logModel->save())
                {
                    \Yii::$app->getSession()->setFlash('success', 'This report has been acknowledged successfully');
                    return $this->redirect(['view', 'id' => $submission->id]);
                }
            }

            
        }

        return $this->renderAjax('_acknowledgment-form', [
            'model' => $model,
            'submission' => $submission,
            'agency' => $agency,
            'officeTitle' => $officeTitle,
            'officeAddress' => $officeAddress,
            'officeHead' => $officeHead,
            'officeTitleShort' => $officeTitleShort,
        ]);
    }

    public function actionRevert($id)
    {
        if(!Yii::$app->user->can('Administrator')){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $submission = Submission::findOne($id);

        $model = new SubmissionLog();
        $model->scenario = 'forFurtherValidation';
        $model->submission_id = $submission->id;
        $model->user_id = Yii::$app->user->id;
        $model->status = 'For further validation';

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            \Yii::$app->getSession()->setFlash('success', 'This report has been sent successfully for further validation');
            return $this->redirect(['view', 'id' => $submission->id]);

            
        }

        return $this->renderAjax('_revert-form', [
            'model' => $model,
        ]);
    }
}
