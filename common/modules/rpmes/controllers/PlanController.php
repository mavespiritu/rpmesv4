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
        $citymunModel = new ProjectCitymun();
        $categoryModel = new ProjectCategory();

        $model->scenario = 'searchMonitoringProject';
        $regionModel->scenario = 'searchRegion';
        $provinceModel->scenario = 'searchProvince';
        $citymunModel->scenario = 'searchCitymun';
        $categoryModel->scenario = 'searchCategory';

        $years = Project::find()->select(['distinct(year) as year'])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        $agencies = Agency::find()->select(['id', 'code as title']);
        $agencies = Yii::$app->user->can('AgencyUser') ? $agencies->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $agencies;
        $agencies = $agencies->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $programs = Program::find()->select(['id', 'title'])->asArray()->all();
        $programs = ArrayHelper::map($programs, 'id', 'title');

        $sectors = Sector::find()->all();
        $sectors = ArrayHelper::map($sectors, 'id', 'title');

        $subSectors = [];

        $modes = ModeOfImplementation::find()->all();
        $modes = ArrayHelper::map($modes, 'id', 'title');

        $fundSources = FundSource::find()->select(['id', 'concat(title," (",code,")") as title'])->asArray()->all();
        $fundSources = ArrayHelper::map($fundSources, 'id', 'title');
        
        $categories = Category::find()->all();
        $categories = ArrayHelper::map($categories, 'id', 'title');

        $scopes = LocationScope::find()->all();
        $scopes = ArrayHelper::map($scopes, 'id', 'title');

        $regions = Region::find()->orderBy(['region_sort' => SORT_ASC])->all();
        $regions = ArrayHelper::map($regions, 'region_c', 'abbreviation');

        $provinces = [];
        $citymuns = [];

        $categories = Category::find()->all();
        $categories = ArrayHelper::map($categories, 'id', 'title');

        $kras = [];

        $goals = SdgGoal::find()->select(['id', 'concat("SDG #",sdg_no,": ",title) as title'])->asArray()->all();
        $goals = ArrayHelper::map($goals, 'id', 'title');

        $chapters = RdpChapter::find()->select(['id', 'concat("Chapter ",chapter_no,": ",title) as title'])->asArray()->all();
        $chapters = ArrayHelper::map($chapters, 'id', 'title');

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];
        $genders = ['M' => 'Male', 'F' => 'Female'];

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
            $citymunModel->load(Yii::$app->request->post()) &&
            $categoryModel->load(Yii::$app->request->post())
        )
        {
            $postData = Yii::$app->request->post();

            $project = $postData['Project'];
            $projectRegion = $postData['ProjectRegion'];
            $projectProvince = $postData['ProjectProvince'];
            $projectCitymun = $postData['ProjectCitymun'];
            $projectCategory = $postData['ProjectCategory'];

            $regionIDs = ProjectRegion::find();
            $provinceIDs = ProjectProvince::find();
            $citymunIDs = ProjectCitymun::find();
            $categoryIDs = ProjectCategory::find();

            $regions = Region::find()->orderBy(['region_sort' => SORT_ASC])->all();
            $regions = ArrayHelper::map($regions, 'region_c', 'abbreviation');

            $provinces = Province::find()
            ->select(['province_c as id', 'concat(tblregion.abbreviation,": ",tblprovince.province_m) as title', 'abbreviation'])
            ->leftJoin('tblregion', 'tblregion.region_c = tblprovince.region_c');
            
            $citymuns = [];

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
                $regionIDs = $regionIDs->andWhere(['year' => $project['year']]);
                $provinceIDs = $provinceIDs->andWhere(['year' => $project['year']]);
                $citymunIDs = $citymunIDs->andWhere(['year' => $project['year']]);
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

            if(!empty($projectRegion['region_id']))
            {
                $regionIDs = $regionIDs->andWhere(['region_id' => $projectRegion['region_id']]);
                $provinces = $provinces->andWhere(['tblprovince.region_c' => $projectRegion['region_id']]);
                $regionModel->region_id = $projectRegion['region_id'];
            }

            if(!empty($projectProvince['province_id']))
            {
                $provinceIDs = $provinceIDs->andWhere(['province_id' => $projectProvince['province_id']]);
                $citymuns = Citymun::find()
                ->select(['concat(tblcitymun.region_c,"-",tblcitymun.province_c,"-",tblcitymun.citymun_c) as id', 'concat(tblprovince.province_m,": ",tblcitymun.citymun_m) as title'])
                ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
                ->leftJoin('tblregion', 'tblregion.region_c = tblcitymun.region_c')
                ->andWhere(['tblcitymun.province_c' => $projectProvince['province_id']])
                ->orderBy(['tblprovince.province_m' => SORT_ASC, 'tblcitymun.citymun_m' => SORT_ASC])
                ->asArray()
                ->all();

                $citymuns = ArrayHelper::map($citymuns, 'id', 'title');

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

            if(!empty($projectCitymun['citymun_id']))
            {
                $citymunIDs = $citymunIDs->andWhere(['concat(region_id,"-",province_id,"-",citymun_id)' => $projectCitymun['citymun_id']]);
                $citymunModel->citymun_id = $projectCitymun['citymun_id'];
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

            $citymunIDs = $citymunIDs->all();
            $citymunIDs = ArrayHelper::map($citymunIDs, 'project_id', 'project_id');

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

            if(!empty($projectCitymun['citymun_id']))
            {
                $projectsPaging->andWhere(['id' => $citymunIDs]);
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
                'citymunModel' => $citymunModel,
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
                'citymuns' => $citymuns,
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
                'totals' => $totals
            ]);
        }

        return $this->render('index', [
            'model' => $model,
            'regionModel' => $regionModel,
            'provinceModel' => $provinceModel,
            'citymunModel' => $citymunModel,
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
            'citymuns' => $citymuns,
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
            'totals' => $totals
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
}
