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
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\helpers\Json;
use yii\data\Pagination;
use kartik\mpdf\Pdf;
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
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['AgencyUser', 'Administrator', 'SuperAdministrator'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
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

        $dueDate = DueDate::findOne(['report' => 'Monitoring Plan', 'year' => date("Y")]);
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
        if(!empty($projects))
        {
            foreach($projects as $project)
            {
                $projectIds[$project['id']] = $project;
                
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
            }
            foreach($projects as $project)
            {
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

    public function actionSubmit()
    {
        if(Yii::$app->request->post())
        {
            $postData = Yii::$app->request->post('Submission');
            
            if(Yii::$app->user->can('AgencyUser')){
                $model = Submission::findOne(['agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C, 'report' => 'Monitoring Plan', 'year' => date("Y")]) ? 
                Submission::findOne(['agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C, 'report' => 'Monitoring Plan', 'year' => date("Y")]) : 
                new Submission();
            }else{
                $model = Submission::findOne(['agency_id' => $postData['agency_id'], 'report' => 'Monitoring Plan', 'year' => date("Y")]) ? 
                Submission::findOne(['agency_id' => $postData['agency_id'], 'report' => 'Monitoring Plan', 'year' => date("Y")]) : 
                new Submission();
            }
            
            $model->agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : $postData['agency_id'];
            $model->report = 'Monitoring Plan';
            $model->year = date("Y");
            $model->submitted_by = Yii::$app->user->id;
            $model->draft = 'No';

            if($model->save(false))
            {
                \Yii::$app->getSession()->setFlash('success', 'Monitoring plan has been submitted successfully');
                return $this->redirect(['/rpmes/plan/']);
            }
        }
    }

    public function actionDownloadMonitoringPlan(
        $type, 
        $year, 
        $agency_id, 
        $category_id, 
        $fund_source_id, 
        $sector_id, 
        $sub_sector_id, 
        $region_id,
        $province_id,
        $period,
        $data_type,
        $project_no,
        $title
    )
    {
        $region_id = $type == 'print' ? json_decode(str_replace('\'', '"', $region_id), true) : json_decode($region_id, true);
        $province_id = $type == 'print' ? json_decode(str_replace('\'', '"', $province_id), true) : json_decode($province_id, true);
        $agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : $agency_id;
        
        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];
        $genders = ['M' => 'Male', 'F' => 'Female'];

        $financialTargets = ProjectTarget::find()->where(['target_type' => 'Financial'])->createCommand()->getRawSql();
        $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical'])->createCommand()->getRawSql();
        $maleEmployedTargets = ProjectTarget::find()->where(['target_type' => 'Male Employed'])->createCommand()->getRawSql();
        $femaleEmployedTargets = ProjectTarget::find()->where(['target_type' => 'Female Employed'])->createCommand()->getRawSql();
        $beneficiariesTargets = ProjectTarget::find()->where(['target_type' => 'Beneficiaries'])->createCommand()->getRawSql();
        $groupBeneficiariesTargets = ProjectTarget::find()->where(['target_type' => 'Group Beneficiaries'])->createCommand()->getRawSql();

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
        
        $financialTotal = 'IF(project.data_type = "Cumulative",
                            IF(COALESCE(financials.q4, 0) <= 0,
                                IF(COALESCE(financials.q3, 0) <= 0,
                                    IF(COALESCE(financials.q2, 0) <= 0,
                                        COALESCE(financials.q1, 0)
                                    , COALESCE(financials.q2, 0))
                                , COALESCE(financials.q3, 0))
                            , COALESCE(financials.q4, 0))
                        ,   
                        COALESCE(financials.q1, 0) +
                        COALESCE(financials.q2, 0) +
                        COALESCE(financials.q3, 0) +
                        COALESCE(financials.q4, 0)
                        )';
            
        $financialTotal = 'IF(project.data_type = "Cumulative",
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
        
        $physicalTotal = 'IF(project.data_type <> "Default",
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
        
        $projects = Project::find()
                    ->select([
                        'mode_of_implementation.title as modeOfImplementationTitle',
                        'project.title as projectTitle',
                        'sector.title as sectorTitle',
                        'sub_sector.title as subSectorTitle',
                        'fund_source.title as fundSourceTitle',
                        'IF(barangayTitles.title is null, IF(citymunTitles.title is null, IF(provinceTitles.title is null, IF(regionTitles.title is null, "No location", regionTitles.title), provinceTitles.title), citymunTitles.title), barangayTitles.title) as locationTitle',
                        'project.start_date as startDate',
                        'project.completion_date as completionDate',
                        'IF(project.data_type <> "", concat(physicalTargets.indicator, " (",project.data_type,")"), concat(physicalTargets.indicator, " (No Data Type)")) as unitOfMeasure',
                        'financialTargets.q1 as financialQ1',
                        'financialTargets.q2 as financialQ2',
                        'financialTargets.q3 as financialQ3',
                        'financialTargets.q4 as financialQ4',
                        'COALESCE('.$financialTotal.', 0) as financialTotal',
                        'physicalTargets.q1 as physicalQ1',
                        'physicalTargets.q2 as physicalQ2',
                        'physicalTargets.q3 as physicalQ3',
                        'physicalTargets.q4 as physicalQ4',
                        'COALESCE('.$physicalTotal.', 0) as physicalTotal',
                        'maleEmployedTargets.q1 as maleEmployedQ1',
                        'maleEmployedTargets.q2 as maleEmployedQ2',
                        'maleEmployedTargets.q3 as maleEmployedQ3',
                        'maleEmployedTargets.q4 as maleEmployedQ4',
                        'femaleEmployedTargets.q1 as femaleEmployedQ1',
                        'femaleEmployedTargets.q2 as femaleEmployedQ2',
                        'femaleEmployedTargets.q3 as femaleEmployedQ3',
                        'femaleEmployedTargets.q4 as femaleEmployedQ4',
                        'beneficiariesTargets.q1 as beneficiaryQ1',
                        'beneficiariesTargets.q2 as beneficiaryQ2',
                        'beneficiariesTargets.q3 as beneficiaryQ3',
                        'beneficiariesTargets.q4 as beneficiaryQ4',
                        'groupBeneficiariesTargets.q1 as groupBeneficiaryQ1',
                        'groupBeneficiariesTargets.q2 as groupBeneficiaryQ2',
                        'groupBeneficiariesTargets.q3 as groupBeneficiaryQ3',
                        'groupBeneficiariesTargets.q4 as groupBeneficiaryQ4',
                    ]);
        $projects = $projects->leftJoin(['financialTargets' => '('.$financialTargets.')'], 'financialTargets.project_id = project.id');
        $projects = $projects->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
        $projects = $projects->leftJoin(['maleEmployedTargets' => '('.$maleEmployedTargets.')'], 'maleEmployedTargets.project_id = project.id');
        $projects = $projects->leftJoin(['femaleEmployedTargets' => '('.$femaleEmployedTargets.')'], 'femaleEmployedTargets.project_id = project.id');
        $projects = $projects->leftJoin(['beneficiariesTargets' => '('.$beneficiariesTargets.')'], 'beneficiariesTargets.project_id = project.id');
        $projects = $projects->leftJoin(['groupBeneficiariesTargets' => '('.$groupBeneficiariesTargets.')'], 'groupBeneficiariesTargets.project_id = project.id');
        $projects = $projects->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id');
        $projects = $projects->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
        $projects = $projects->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id');
        $projects = $projects->leftJoin(['barangayTitles' => '('.$barangayTitles.')'], 'barangayTitles.project_id = project.id');
        $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
        $projects = $projects->leftJoin('mode_of_implementation', 'mode_of_implementation.id = project.mode_of_implementation_id');
        $projects = $projects->leftJoin('sector', 'sector.id = project.sector_id');
        $projects = $projects->leftJoin('sub_sector', 'sub_sector.id = project.sub_sector_id');
        $projects = $projects->leftJoin('fund_source', 'fund_source.id = project.fund_source_id');
        $projects = $projects->leftJoin('project_category', 'project_category.project_id = project.id');
        $projects = $projects->leftJoin('category', 'category.id = project_category.category_id');
        $projects = $projects->andWhere(['project.draft' => 'No']);

        $regionIDs = ProjectRegion::find();
        $provinceIDs = ProjectProvince::find();

        if($year != '')
        {
            $projects = $projects->andWhere(['project.year' => $year]);
        }

        if($agency_id != '')
        {
            $projects = $projects->andWhere(['project.agency_id' => $agency_id]);
        }

        if($category_id != '')
        {
            $projects = $projects->andWhere(['category.id' => $category_id]);
        }

        if($fund_source_id != '')
        {
            $projects = $projects->andWhere(['project.fund_source_id' => $fund_source_id]);
        }

        if($sector_id != '')
        {
            $projects = $projects->andWhere(['project.sector_id' => $sector_id]);
        }

        if($sub_sector_id != '')
        {
            $projects = $projects->andWhere(['project.sub_sector_id' => $sub_sector_id]);
        }

        if($region_id != '')
        {
            $regionIDs = $regionIDs->andWhere(['region_id' => $region_id]);
        }

        if($province_id != '')
        {
            $regionIDs = $regionIDs->andWhere(['province_id' => $province_id]);
        }

        if($period != '')
        {
            $projects = $projects->andWhere(['project.period' => $period]);
        }

        if($data_type != '')
        {
            $projects = $projects->andWhere(['project.data_type' => $data_type]);
        }

        if($project_no != '')
        {
            $projects = $projects->andWhere(['like', 'project.project_no', '%'.$project_no.'%', false]);
        }

        if($title != '')
        {
            $projects = $projects->andWhere(['like', 'project.title', '%'.$title.'%', false]);
        }

        $regionIDs = $regionIDs->all();
        $regionIDs = ArrayHelper::map($regionIDs, 'project_id', 'project_id');

        $provinceIDs = $provinceIDs->all();
        $provinceIDs = ArrayHelper::map($provinceIDs, 'project_id', 'project_id');

        if($region_id != '')
        {
            $projects = $projects->andWhere(['project.id' => $regionIDs]);
        }

        if($province_id != '')
        {
            $projects = $projects->andWhere(['project.id' => $provinceIDs]);
        }

        $projects = $projects 
                    ->asArray()
                    ->all();

        $filename = 'Initial Project Report';

        if($type == 'excel')
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=".$filename.".xls");
            return $this->renderPartial('_plan-file', [
                'type' => $type,
                'projects' => $projects,
                'quarters' => $quarters,
                'genders' => $genders,
            ]);
        }else if($type == 'pdf')
        {
            $content = $this->renderPartial('_plan-file', [
                'type' => $type,
                'projects' => $projects,
                'quarters' => $quarters,
                'genders' => $genders,
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
                'cssInline' => 'table{
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
                                }', 
                ]);
        
                $response = Yii::$app->response;
                $response->format = \yii\web\Response::FORMAT_RAW;
                $headers = Yii::$app->response->headers;
                $headers->add('Content-Type', 'application/pdf');
                return $pdf->render();
        }else if($type == 'print')
        {
            return $this->renderAjax('_plan-file', [
                'type' => $type,
                'projects' => $projects,
                'quarters' => $quarters,
                'genders' => $genders,
            ]);
        }
    }
}
