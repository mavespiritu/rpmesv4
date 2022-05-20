<?php

namespace common\modules\rpmes\controllers;

use Yii;
use common\models\Region;
use common\models\Province;
use common\models\Citymun;
use common\models\Barangay;
use common\modules\rpmes\models\DueDate;
use common\modules\rpmes\models\Plan;
use common\modules\rpmes\models\Project;
use common\modules\rpmes\models\ProjectTarget;
use common\modules\rpmes\models\ProjectRegion;
use common\modules\rpmes\models\ProjectProvince;
use common\modules\rpmes\models\ProjectCitymun;
use common\modules\rpmes\models\ProjectBarangay;
use common\modules\rpmes\models\ProjectCategory;
use common\modules\rpmes\models\ProjectKra;
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
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\helpers\Json;
use yii\data\Pagination;
/**
 * ProjectController implements the CRUD actions for Project model.
 */
class ProjectController extends Controller
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
                'only' => ['create', 'update', 'draft', 'carry-over'],
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'draft', 'carry-over'],
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

    public function actionSubSectorList($id)
    {
        $subSectors = SubSectorPerSector::find()
                    ->select(['sub_sector.id as id', 'sub_sector.title as title'])
                    ->leftJoin('sub_sector', 'sub_sector.id = sub_sector_per_sector.sub_sector_id')
                    ->where(['sector_id' => $id])
                    ->asArray()
                    ->all();

        $arr = [];
        $arr[] = ['id'=>'','text'=>''];
        foreach($subSectors as $subSector){
            $arr[] = ['id' => $subSector['id'] ,'text' => $subSector['title']];
        }
        \Yii::$app->response->format = 'json';
        return $arr;
    }

    public function actionKraList($id)
    {
        $kras = KeyResultArea::find()
                    ->select(['key_result_area.id', 'concat("KRA/Cluster #",kra_no,": ",key_result_area.title) as title'])
                    ->leftJoin('category', 'category.id = key_result_area.category_id')
                    ->where(['key_result_area.category_id' => $id])
                    ->orderBy(['key_result_area.kra_no' => SORT_ASC, 'key_result_area.title' => SORT_ASC])
                    ->asArray()
                    ->all();

        $arr = [];
        $arr[] = ['id'=>'','text'=>''];
        foreach($kras as $kra){
            $arr[] = ['id' => $kra['id'] ,'text' => $kra['title']];
        }
        \Yii::$app->response->format = 'json';
        return $arr;
    }

    public function actionChapterOutcomeList($id)
    {
        $ids = json_decode($id, true);
        $ids = ArrayHelper::map($ids, 'id', 'id');

        $outcomes = RdpChapterOutcome::find()
                    ->select(['id', 'concat("Chapter Outcome ",level,": ",title) as title'])
                    ->where(['in', 'rdp_chapter_id', $ids])
                    ->orderBy(['level' => SORT_ASC, 'title' => SORT_ASC])
                    ->asArray()
                    ->all();

        $arr = [];
        $arr[] = ['id'=>'','text'=>''];
        foreach($outcomes as $outcome){
            $arr[] = ['id' => $outcome['id'] ,'text' => $outcome['title']];
        }
        \Yii::$app->response->format = 'json';
        return $arr;
    }

    public function actionChapterToSubChapterOutcomeList($id)
    {
        $ids = json_decode($id, true);
        $ids = ArrayHelper::map($ids, 'id', 'id');

        $outcomes = RdpSubChapterOutcome::find()
                    ->select(['id', 'concat("Sub-Chapter Outcome ",level,": ",title) as title'])
                    ->where(['in', 'rdp_chapter_id', $ids])
                    ->andWhere(['is', 'rdp_chapter_outcome_id', null])
                    ->orderBy(['level' => SORT_ASC, 'title' => SORT_ASC])
                    ->asArray()
                    ->all();

        $arr = [];
        $arr[] = ['id'=>'','text'=>''];
        foreach($outcomes as $outcome){
            $arr[] = ['id' => $outcome['id'] ,'text' => $outcome['title']];
        }
        \Yii::$app->response->format = 'json';
        return $arr;
    }

    public function actionSubChapterOutcomeList($id)
    {
        $ids = json_decode($id, true);
        $ids = ArrayHelper::map($ids, 'id', 'id');

        $outcomes = RdpSubChapterOutcome::find()
                    ->select(['rdp_sub_chapter_outcome.id as id', 'concat("Sub-Chapter Outcome ",rdp_chapter_outcome.level,".",rdp_sub_chapter_outcome.level,": ",rdp_sub_chapter_outcome.title) as title'])
                    ->leftJoin('rdp_chapter_outcome', 'rdp_chapter_outcome.id = rdp_sub_chapter_outcome.rdp_chapter_outcome_id')
                    ->where(['in', 'rdp_sub_chapter_outcome.rdp_chapter_outcome_id', $ids])
                    ->orderBy(['rdp_chapter_outcome.level' => SORT_ASC, 'rdp_sub_chapter_outcome.level' => SORT_ASC, 'rdp_sub_chapter_outcome.title' => SORT_ASC])
                    ->asArray()
                    ->all();

        $arr = [];
        $arr[] = ['id'=>'','text'=>''];
        foreach($outcomes as $outcome){
            $arr[] = ['id' => $outcome['id'] ,'text' => $outcome['title']];
        }
        \Yii::$app->response->format = 'json';
        return $arr;
    }

    public function actionProvinceList($id)
    {
        $ids = json_decode($id, true);
        $ids = ArrayHelper::map($ids, 'id', 'id');

        $provinces = Province::find()
                    ->select(['province_c as id', 'concat(tblregion.abbreviation,": ",tblprovince.province_m) as title', 'abbreviation'])
                    ->leftJoin('tblregion', 'tblregion.region_c = tblprovince.region_c')
                    ->where(['in', 'tblprovince.region_c', $ids])
                    ->orderBy(['abbreviation' => SORT_ASC, 'province_m' => SORT_ASC])
                    ->asArray()
                    ->all();

        $arr = [];
        $arr[] = ['id'=>'','text'=>''];
        foreach($provinces as $province){
            $arr[] = ['id' => $province['id'] ,'text' => $province['title']];
        }
        \Yii::$app->response->format = 'json';
        return $arr;
    }

    public function actionCitymunList($id)
    {
        $ids = json_decode($id, true);
        $ids = ArrayHelper::map($ids, 'id', 'id');

        $citymuns = Citymun::find()
                    ->select(['concat(tblcitymun.region_c,"-",tblcitymun.province_c,"-",tblcitymun.citymun_c) as id', 'concat(tblprovince.province_m,": ",tblcitymun.citymun_m) as title'])
                    ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
                    ->leftJoin('tblregion', 'tblregion.region_c = tblcitymun.region_c')
                    ->where(['in', 'tblcitymun.province_c', $ids])
                    ->orderBy(['tblprovince.province_m' => SORT_ASC, 'tblcitymun.citymun_m' => SORT_ASC])
                    ->asArray()
                    ->all();

        $arr = [];
        $arr[] = ['id'=>'','text'=>''];
        foreach($citymuns as $citymun){
            $arr[] = ['id' => $citymun['id'] ,'text' => $citymun['title']];
        }
        \Yii::$app->response->format = 'json';
        return $arr;
    }

    public function actionBarangayList($id)
    {
        $ids = json_decode($id, true);
        $ids = ArrayHelper::map($ids, 'id', 'id');

        $nonManilaBarangays = Barangay::find()
                    ->select(['concat(tblbarangay.region_c,"-",tblbarangay.province_c,"-",tblbarangay.citymun_c,"-",tblbarangay.barangay_c) as id', 'concat(tblprovince.province_m,": ",tblcitymun.citymun_m,": ",tblbarangay.barangay_m) as title'])
                    ->leftJoin('tblcitymun', 'tblcitymun.province_c = tblbarangay.province_c and tblcitymun.citymun_c = tblbarangay.citymun_c')
                    ->leftJoin('tblprovince', 'tblprovince.province_c = tblbarangay.province_c')
                    ->leftJoin('tblregion', 'tblregion.region_c = tblbarangay.region_c')
                    ->where(['in', 'concat(tblbarangay.region_c,"-",tblbarangay.province_c,"-",tblbarangay.citymun_c)', $ids])
                    ->orderBy(['tblprovince.province_m' => SORT_ASC, 'tblcitymun.citymun_m' => SORT_ASC, 'tblbarangay.barangay_m' => SORT_ASC])
                    ->asArray()
                    ->all();
            
        $manilaBarangays = in_array('13-39-00', $ids) ?  Barangay::find()
                    ->select(['concat(tblbarangay.region_c,"-",tblbarangay.province_c,"-",tblbarangay.citymun_c,"-",tblbarangay.barangay_c) as id', 'concat(tblprovince.province_m,": ",tblcitymun.citymun_m,": ",tblbarangay.barangay_m) as title'])
                    ->leftJoin('tblcitymun', 'tblcitymun.province_c = tblbarangay.province_c and tblcitymun.citymun_c = "00"')
                    ->leftJoin('tblprovince', 'tblprovince.province_c = tblbarangay.province_c')
                    ->leftJoin('tblregion', 'tblregion.region_c = tblbarangay.region_c')
                    ->where(['tblbarangay.region_c' => '13','tblbarangay.province_c' => '39'])
                    ->orderBy(['tblprovince.province_m' => SORT_ASC, 'tblcitymun.citymun_m' => SORT_ASC, 'tblbarangay.barangay_m' => SORT_ASC])
                    ->asArray()
                    ->all() : [];

        $arr = [];
        $arr[] = ['id'=>'','text'=>''];
        foreach($nonManilaBarangays as $barangay){
            $arr[] = ['id' => $barangay['id'] ,'text' => $barangay['title']];
        }
        foreach($manilaBarangays as $barangay){
            $arr[] = ['id' => $barangay['id'] ,'text' => $barangay['title']];
        }
        \Yii::$app->response->format = 'json';
        return $arr;
    }

    public function actionProgramList($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $programs = Program::find()
                ->select(['id', 'title as text'])
                ->where(['or', 
                    ['like', 'title', $q],
                ])
                ->limit(20)
                ->asArray()
                ->all();


            $out['results'] = array_values($programs);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Program::find($id)->title];
        }
        return $out;
    }

    public function actionProjectList($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $projects = Project::find()
                ->select(['id', 'concat(project_no,": ",title) as text'])
                ->andWhere(['draft' => 'No'])
                ->andWhere(['or', 
                    ['like', 'project_no', $q],
                    ['like', 'title', $q],
                    ['like', 'concat(project_no,": ",title)', $q],
        ]);

            $projects = Yii::$app->user->can('AgencyUser') ? $projects->andWhere(['agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $projects;

            $projects = $projects       
                ->limit(20)
                ->asArray()
                ->all();


            $out['results'] = array_values($projects);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Project::find($id)->project_no.': '.Project::find($id)->title];
        }
        return $out;
    }

    /**
     * Creates a new Project model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $dueDate = DueDate::findOne(['report' => 'Monitoring Plan', 'year' => date("Y")]);

        $model = new Project();
        $model->year = date("Y");
        $model->period = 'Current Year';
        $model->agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : null;
        $model->scenario = Yii::$app->user->can('AgencyUser') ? 'projectCreateUser' : 'projectCreateAdmin';
        $regionModel = new ProjectRegion();
        $provinceModel = new ProjectProvince();
        $citymunModel = new ProjectCitymun();
        $barangayModel = new ProjectBarangay();
        $categoryModel = new ProjectCategory();
        $kraModel = new ProjectKra();
        $sdgModel = new ProjectSdgGoal();
        $rdpChapterModel = new ProjectRdpChapter();
        $rdpChapterOutcomeModel = new ProjectRdpChapterOutcome();
        $rdpSubChapterOutcomeModel = new ProjectRdpSubChapterOutcome();
        $expectedOutputModels = [new ProjectExpectedOutput()];
        $outcomeModels = [new ProjectOutcome()];

        $agencies = Agency::find()->select(['id', 'concat(title," (",code,")") as title']);
        $agencies = Yii::$app->user->can('AgencyUser') ? $agencies->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $agencies;
        $agencies = $agencies->orderBy(['title' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $projects = [];
        $programs = [];

        $sectors = Sector::find()->all();
        $sectors = ArrayHelper::map($sectors, 'id', 'title');

        $subSectors = [];

        $modes = ModeOfImplementation::find()->all();
        $modes = ArrayHelper::map($modes, 'id', 'title');

        $fundSources = FundSource::find()->select(['id', 'concat(title," (",code,")") as title'])->asArray()->all();
        $fundSources = ArrayHelper::map($fundSources, 'id', 'title');

        $scopes = LocationScope::find()->all();
        $scopes = ArrayHelper::map($scopes, 'id', 'title');

        $regions = Region::find()->orderBy(['region_sort' => SORT_ASC])->all();
        $regions = ArrayHelper::map($regions, 'region_c', 'abbreviation');

        $provinces = [];
        $citymuns = [];
        $barangays = [];

        $categories = Category::find()->all();
        $categories = ArrayHelper::map($categories, 'id', 'title');

        $kras = [];

        $goals = SdgGoal::find()->select(['id', 'concat("SDG #",sdg_no,": ",title) as title'])->asArray()->all();
        $goals = ArrayHelper::map($goals, 'id', 'title');

        $chapters = RdpChapter::find()->select(['id', 'concat("Chapter ",chapter_no,": ",title) as title'])->asArray()->all();
        $chapters = ArrayHelper::map($chapters, 'id', 'title');

        $chapterOutcomes = [];
        $subChapterOutcomes = [];

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];
        $genders = ['M' => 'Male', 'F' => 'Female'];

        $targets = [];


        $physicalTargetModel = new ProjectTarget();
        $physicalTargetModel->scenario = 'physicalTarget';
        $physicalTargetModel->target_type = 'Physical';

        $targets[0] = $physicalTargetModel;

        $financialTargetModel = new ProjectTarget();
        $financialTargetModel->target_type = 'Financial';

        $targets[1] = $financialTargetModel;

        $maleEmployedTargetModel = new ProjectTarget();
        $maleEmployedTargetModel->target_type = 'Male Employed';

        $targets[2] = $maleEmployedTargetModel;

        $femaleEmployedTargetModel = new ProjectTarget();
        $femaleEmployedTargetModel->target_type = 'Female Employed';

        $targets[3] = $femaleEmployedTargetModel;

        $beneficiaryTargetModel = new ProjectTarget();
        $beneficiaryTargetModel->scenario = 'physicalTarget';
        $beneficiaryTargetModel->target_type = 'Beneficiaries';

        $targets[4] = $beneficiaryTargetModel;

        if (
            $model->load(Yii::$app->request->post()) &&
            $regionModel->load(Yii::$app->request->post()) &&
            $provinceModel->load(Yii::$app->request->post()) &&
            $citymunModel->load(Yii::$app->request->post()) &&
            $barangayModel->load(Yii::$app->request->post()) &&
            $categoryModel->load(Yii::$app->request->post()) &&
            $kraModel->load(Yii::$app->request->post()) &&
            $sdgModel->load(Yii::$app->request->post()) &&
            $rdpChapterModel->load(Yii::$app->request->post()) &&
            $rdpChapterOutcomeModel->load(Yii::$app->request->post()) &&
            $rdpSubChapterOutcomeModel->load(Yii::$app->request->post()) &&
            MultipleModel::loadMultiple($targets, Yii::$app->request->post()) 
            ) 
            {

            $expectedOutputModels = Model::createMultiple(ProjectExpectedOutput::classname());
            $outcomeModels = Model::createMultiple(ProjectOutcome::classname());
            Model::loadMultiple($expectedOutputModels, Yii::$app->request->post());
            Model::loadMultiple($outcomeModels, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($expectedOutputModels) && Model::validateMultiple($outcomeModels) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                $agency = Agency::findOne(['id' => $model->agency_id]);
                $lastProject = Project::find()->where(['agency_id' => $model->agency_id, 'year' => $model->year])->orderBy(['id' => SORT_DESC])->one();
                $lastNumber = $lastProject ? intval(substr($lastProject->project_no, -4)): '0001';
                $project_no = $agency->code.'-'.substr($model->year, -2).'-'.str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                $model->project_no = $project_no;
                $model->submitted_by = Yii::$app->user->id;
                $model->draft = 'No';
                try {
                    if ($flag = $model->save(false)) {
                        $plan = Plan::findOne(['project_id' => $model->id, 'year' => $model->year]) ? Plan::findOne(['project_id' => $model->id, 'year' => $model->year]) : new Plan();
                        $plan->project_id = $model->id;
                        $plan->year = $model->year;
                        $plan->date_submitted = date("Y-m-d H:i:s");
                        $plan->submitted_by = Yii::$app->user->id;
                        $plan->save(false);

                        
                        foreach ($expectedOutputModels as $expectedOutputModel) {
                            $expectedOutputModel->project_id = $model->id;
                            $expectedOutputModel->year = $model->year;
                            if (! ($flag = $expectedOutputModel->save())) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        foreach ($outcomeModels as $outcomeModel) {
                            $outcomeModel->project_id = $model->id;
                            $outcomeModel->year = $model->year;
                            if (! ($flag = $outcomeModel->save())) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        if(!empty($categoryModel->category_id))
                        {
                            foreach($categoryModel->category_id as $id)
                            {
                                $category = new ProjectCategory();
                                $category->project_id = $model->id;
                                $category->year = $model->year;
                                $category->category_id = $id;
                                if (! ($flag = $category->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($kraModel->key_result_area_id))
                        {
                            foreach($kraModel->key_result_area_id as $id)
                            {
                                $kra = new ProjectKra();
                                $kra->project_id = $model->id;
                                $kra->year = $model->year;
                                $kra->key_result_area_id = $id;
                                if (! ($flag = $kra->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($sdgModel->sdg_goal_id))
                        {
                            foreach($sdgModel->sdg_goal_id as $id)
                            {
                                $sdg = new ProjectSdgGoal();
                                $sdg->project_id = $model->id;
                                $sdg->year = $model->year;
                                $sdg->sdg_goal_id = $id;
                                if (! ($flag = $sdg->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($rdpChapterModel->rdp_chapter_id))
                        {
                            foreach($rdpChapterModel->rdp_chapter_id as $id)
                            {
                                $chapter = new ProjectRdpChapter();
                                $chapter->project_id = $model->id;
                                $chapter->year = $model->year;
                                $chapter->rdp_chapter_id = $id;
                                if (! ($flag = $chapter->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($rdpChapterOutcomeModel->rdp_chapter_outcome_id))
                        {
                            foreach($rdpChapterOutcomeModel->rdp_chapter_outcome_id as $id)
                            {
                                $chapterOutcome = new ProjectRdpChapterOutcome();
                                $chapterOutcome->project_id = $model->id;
                                $chapterOutcome->year = $model->year;
                                $chapterOutcome->rdp_chapter_outcome_id = $id;
                                if (! ($flag = $chapterOutcome->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($rdpSubChapterOutcomeModel->rdp_sub_chapter_outcome_id))
                        {
                            foreach($rdpSubChapterOutcomeModel->rdp_sub_chapter_outcome_id as $id)
                            {
                                $subChapterOutcome = new ProjectRdpSubChapterOutcome();
                                $subChapterOutcome->project_id = $model->id;
                                $subChapterOutcome->year = $model->year;
                                $subChapterOutcome->rdp_sub_chapter_outcome_id = $id;
                                if (! ($flag = $subChapterOutcome->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($regionModel->region_id))
                        {
                            foreach($regionModel->region_id as $id)
                            {
                                $region = new ProjectRegion();
                                $region->project_id = $model->id;
                                $region->year = $model->year;
                                $region->region_id = $id;
                                if (! ($flag = $region->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($provinceModel->province_id))
                        {
                            foreach($provinceModel->province_id as $id)
                            {
                                $province = new ProjectProvince();
                                $province->project_id = $model->id;
                                $province->year = $model->year;
                                $province->province_id = $id;
                                if (! ($flag = $province->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($citymunModel->citymun_id))
                        {
                            foreach($citymunModel->citymun_id as $id)
                            {
                                $ids = explode("-", $id);
                                $citymun = new ProjectCitymun();
                                $citymun->project_id = $model->id;
                                $citymun->year = $model->year;
                                $citymun->region_id = $ids[0];
                                $citymun->province_id = $ids[1];
                                $citymun->citymun_id = $ids[2];
                                if (! ($flag = $citymun->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($barangayModel->barangay_id))
                        {
                            foreach($barangayModel->barangay_id as $id)
                            {
                                $ids = explode("-", $id);
                                $barangay = new ProjectBarangay();
                                $barangay->project_id = $model->id;
                                $barangay->year = $model->year;
                                $barangay->region_id = $ids[0];
                                $barangay->province_id = $ids[1];
                                $barangay->citymun_id = $ids[2];
                                $barangay->barangay_id = $ids[3];
                                if (! ($flag = $barangay->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($targets))
                        {
                            foreach($targets as $target)
                            {
                                $target->project_id = $model->id;
                                $target->year = $model->year;
                                if (! ($flag = $target->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        \Yii::$app->getSession()->setFlash('success', 'Record Saved');
                        return $this->redirect(['/rpmes/project/create']);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'regionModel' => $regionModel,
            'provinceModel' => $provinceModel,
            'citymunModel' => $citymunModel,
            'barangayModel' => $barangayModel,
            'categoryModel' => $categoryModel,
            'kraModel' => $kraModel,
            'sdgModel' => $sdgModel,
            'rdpChapterModel' => $rdpChapterModel,
            'rdpChapterOutcomeModel' => $rdpChapterOutcomeModel,
            'rdpSubChapterOutcomeModel' => $rdpSubChapterOutcomeModel,
            'targets' => $targets,
            'expectedOutputModels' => (empty($expectedOutputModels)) ? [new ProjectExpectedOutput] : $expectedOutputModels,
            'outcomeModels' => (empty($outcomeModels)) ? [new ProjectOutcome] : $outcomeModels,
            'agencies' => $agencies,
            'projects' => $projects,
            'programs' => $programs,
            'sectors' => $sectors,
            'subSectors' => $subSectors,
            'modes' => $modes,
            'fundSources' => $fundSources,
            'scopes' => $scopes,
            'regions' => $regions,
            'provinces' => $provinces,
            'citymuns' => $citymuns,
            'barangays' => $barangays,
            'categories' => $categories,
            'kras' => $kras,
            'chapters' => $chapters,
            'goals' => $goals,
            'chapterOutcomes' => $chapterOutcomes,
            'subChapterOutcomes' => $subChapterOutcomes,
            'quarters' => $quarters,
            'genders' => $genders,
            'dueDate' => $dueDate
        ]);
    }

    public function actionSaveDraft()
    {
        if(Yii::$app->request->post())
        {
            $postData = Yii::$app->request->post();
            $project = $postData['Project'];
            //echo "<pre>"; print_r($postData); exit;
            
            $model = isset($project['id']) ? $project['id'] != '' ? $this->findModel($project['id']) : new Project() : new Project();
            $model->year = date("Y");
            $model->project_no = '';
            $model->source_id = isset($project['source_id']) ? $project['source_id'] : null;
            $model->period = isset($project['period']) ? $project['period'] : '';
            $model->agency_id = isset($project['agency_id']) ? $project['agency_id'] : null;
            if(Yii::$app->user->can('AgencyUser'))
            {
                $model->agency_id = Yii::$app->user->identity->userinfo->AGENCY_C;
            }
            $model->program_id = isset($project['agency_id']) ? $project['program_id'] : null;
            $model->title = isset($project['title']) ? $project['title'] : '';
            $model->description = isset($project['description']) ? $project['description'] : '';
            $model->sector_id = isset($project['sector_id']) ? $project['sector_id'] : null;
            $model->sub_sector_id = isset($project['sub_sector_id']) ? $project['sub_sector_id'] : null;
            $model->mode_of_implementation_id = isset($project['mode_of_implementation_id']) ? $project['mode_of_implementation_id'] : null;
            $model->other_mode = isset($project['other_mode']) ? $project['other_mode'] : '';
            $model->fund_source_id = isset($project['fund_source_id']) ? $project['fund_source_id'] : null;
            $model->typhoon = isset($project['typhoon']) ? $project['typhoon'] : '';
            $model->start_date = isset($project['start_date']) ? $project['start_date'] : '';
            $model->completion_date = isset($project['completion_date']) ? $project['completion_date'] : '';
            $model->location_scope_id = isset($project['location_scope_id']) ? $project['location_scope_id'] : null;
            $model->data_type = isset($project['data_type']) ? $project['data_type'] : '';
            $model->submitted_by = Yii::$app->user->id;
            $model->draft = 'Yes';

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                if ($flag = $model->save(false)) {
                    $expectedOutputs = $postData['ProjectExpectedOutput'];
                    if(!empty($expectedOutputs))
                    {
                        foreach ($expectedOutputs as $expectedOutput) {
                            $expectedOutputModel = isset($expectedOutput['id']) ? $expectedOutput['id'] != '' ? ProjectExpectedOutput::findOne(['id' => $expectedOutput['id']]) : new ProjectExpectedOutput() : new ProjectExpectedOutput();
                            $expectedOutputModel->project_id = $model->id;
                            $expectedOutputModel->year = $model->year;
                            $expectedOutputModel->indicator = isset($expectedOutput['indicator']) ? $expectedOutput['indicator'] : '';
                            $expectedOutputModel->target = isset($expectedOutput['target']) ? $expectedOutput['target'] : '';
                            if (! ($flag = $expectedOutputModel->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    $outcomes = $postData['ProjectOutcome'];
                    if(!empty($outcomes))
                    {
                        foreach ($outcomes as $outcome) {
                            $outcomeModel = isset($outcome['id']) ? $outcome['id'] != '' ? ProjectOutcome::findOne(['id' => $outcome['id']]) : new ProjectOutcome() : new ProjectOutcome();
                            $outcomeModel->project_id = $model->id;
                            $outcomeModel->year = $model->year;
                            $outcomeModel->outcome = isset($outcome['outcome']) ? $outcome['outcome'] : '';
                            $outcomeModel->performance_indicator = isset($outcome['performance_indicator']) ? $outcome['performance_indicator'] : '';
                            $outcomeModel->target = isset($outcome['target']) ? $outcome['target'] : '';
                            $outcomeModel->timeline = isset($outcome['timeline']) ? $outcome['timeline'] : '';
                            $outcomeModel->remarks = isset($outcome['remarks']) ? $outcome['remarks'] : '';
                            if (! ($flag = $outcomeModel->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    $category = $postData['ProjectCategory'];
                    if(!empty($category['category_id']))
                    {
                        foreach($category['category_id'] as $id)
                        {
                            $categoryModel = ProjectCategory::findOne(['project_id' => $model->id, 'year' => $model->year, 'category_id' => $id]) ? 
                            ProjectCategory::findOne(['project_id' => $model->id, 'year' => $model->year, 'category_id' => $id]) : new ProjectCategory();
                            $categoryModel->project_id = $model->id;
                            $categoryModel->year = $model->year;
                            $categoryModel->category_id = $id;
                            if (! ($flag = $categoryModel->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    $kra = $postData['ProjectKra'];
                    if(!empty($kra['key_result_area_id']))
                    {
                        foreach($kra['key_result_area_id'] as $id)
                        {
                            $kraModel = ProjectKra::findOne(['project_id' => $model->id, 'year' => $model->year, 'key_result_area_id' => $id]) ? 
                            ProjectKra::findOne(['project_id' => $model->id, 'year' => $model->year, 'key_result_area_id' => $id]) : new ProjectKra();
                            $kraModel->project_id = $model->id;
                            $kraModel->year = $model->year;
                            $kraModel->key_result_area_id = $id;
                            if (! ($flag = $kraModel->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    $sdg = $postData['ProjectSdgGoal'];
                    if(!empty($sdg['sdg_goal_id']))
                    {
                        foreach($sdg['sdg_goal_id'] as $id)
                        {
                            $sdgModel = ProjectSdgGoal::findOne(['project_id' => $model->id, 'year' => $model->year, 'sdg_goal_id' => $id]) ?
                            ProjectSdgGoal::findOne(['project_id' => $model->id, 'year' => $model->year, 'sdg_goal_id' => $id]) : new ProjectSdgGoal();
                            $sdgModel->project_id = $model->id;
                            $sdgModel->year = $model->year;
                            $sdgModel->sdg_goal_id = $id;
                            if (! ($flag = $sdgModel->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    $rdpChapter = $postData['ProjectRdpChapter'];
                    if(!empty($rdpChapter['rdp_chapter_id']))
                    {
                        foreach($rdpChapter['rdp_chapter_id'] as $id)
                        {
                            $chapterModel = ProjectRdpChapter::findOne(['project_id' => $model->id, 'year' => $model->year, 'rdp_chapter_id' => $id]) ?
                            ProjectRdpChapter::findOne(['project_id' => $model->id, 'year' => $model->year, 'rdp_chapter_id' => $id]) : new ProjectRdpChapter();
                            $chapterModel->project_id = $model->id;
                            $chapterModel->year = $model->year;
                            $chapterModel->rdp_chapter_id = $id;
                            if (! ($flag = $chapterModel->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    $rdpChapterOutcome = $postData['ProjectRdpChapterOutcome'];
                    if(!empty($rdpChapterOutcome['rdp_chapter_outcome_id']))
                    {
                        foreach($rdpChapterOutcome['rdp_chapter_outcome_id'] as $id)
                        {
                            $chapterOutcomeModel = ProjectRdpChapterOutcome::findOne(['project_id' => $model->id, 'year' => $model->year, 'rdp_chapter_outcome_id' => $id]) ?
                            ProjectRdpChapterOutcome::findOne(['project_id' => $model->id, 'year' => $model->year, 'rdp_chapter_outcome_id' => $id]) : new ProjectRdpChapterOutcome();
                            $chapterOutcomeModel->project_id = $model->id;
                            $chapterOutcomeModel->year = $model->year;
                            $chapterOutcomeModel->rdp_chapter_outcome_id = $id;
                            if (! ($flag = $chapterOutcomeModel->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    $rdpSubChapterOutcome = $postData['ProjectRdpSubChapterOutcome'];
                    if(!empty($rdpSubChapterOutcome['rdp_sub_chapter_outcome_id']))
                    {
                        foreach($rdpSubChapterOutcome['rdp_sub_chapter_outcome_id'] as $id)
                        {
                            $subChapterOutcomeModel = ProjectRdpSubChapterOutcome::findOne(['project_id' => $model->id, 'year' => $model->year, 'rdp_sub_chapter_outcome_id' => $id]) ?
                            ProjectRdpSubChapterOutcome::findOne(['project_id' => $model->id, 'year' => $model->year, 'rdp_sub_chapter_outcome_id' => $id]) : new ProjectRdpSubChapterOutcome();
                            $subChapterOutcomeModel->project_id = $model->id;
                            $subChapterOutcomeModel->year = $model->year;
                            $subChapterOutcomeModel->rdp_sub_chapter_outcome_id = $id;
                            if (! ($flag = $subChapterOutcomeModel->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    $region = $postData['ProjectRegion'];
                    if(!empty($region['region_id']))
                    {
                        foreach($region['region_id'] as $id)
                        {
                            $regionModel = ProjectRegion::findOne(['project_id' => $model->id, 'year' => $model->year, 'region_id' => $id]) ?
                            ProjectRegion::findOne(['project_id' => $model->id, 'year' => $model->year, 'region_id' => $id]) : new ProjectRegion();
                            $regionModel->project_id = $model->id;
                            $regionModel->year = $model->year;
                            $regionModel->region_id = $id;
                            if (! ($flag = $regionModel->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }   

                    $province = $postData['ProjectProvince'];
                    if(!empty($province['province_id']))
                    {
                        foreach($province['province_id'] as $id)
                        {
                            $provinceModel = ProjectProvince::findOne(['project_id' => $model->id, 'year' => $model->year, 'province_id' => $id]) ?
                            ProjectProvince::findOne(['project_id' => $model->id, 'year' => $model->year, 'province_id' => $id]) :  new ProjectProvince();
                            $provinceModel->project_id = $model->id;
                            $provinceModel->year = $model->year;
                            $provinceModel->province_id = $id;
                            if (! ($flag = $provinceModel->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    $citymun = $postData['ProjectCitymun'];
                    if(!empty($citymun['citymun_id']))
                    {
                        foreach($citymun['citymun_id'] as $id)
                        {
                            $ids = explode("-", $id);
                            $citymunModel = ProjectCitymun::findOne(['project_id' => $model->id, 'year' => $model->year, 'region_id' => $ids[0], 'province_id' => $ids[1], 'citymun_id' => $ids[2]]) ?
                            ProjectCitymun::findOne(['project_id' => $model->id, 'year' => $model->year, 'region_id' => $ids[0], 'province_id' => $ids[1], 'citymun_id' => $ids[2]]) : new ProjectCitymun();
                            $citymunModel->project_id = $model->id;
                            $citymunModel->year = $model->year;
                            $citymunModel->region_id = $ids[0];
                            $citymunModel->province_id = $ids[1];
                            $citymunModel->citymun_id = $ids[2];
                            if (! ($flag = $citymunModel->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    $barangay = $postData['ProjectBarangay'];
                    if(!empty($barangay['barangay_id']))
                    {
                        foreach($barangay['barangay_id'] as $id)
                        {
                            $ids = explode("-", $id);
                            $barangayMModel = ProjectBarangay::findOne(['project_id' => $model->id, 'year' => $model->year, 'region_id' => $ids[0], 'province_id' => $ids[1], 'citymun_id' => $ids[2], 'barangay_id' => $ids[3]]) ?
                            ProjectBarangay::findOne(['project_id' => $model->id, 'year' => $model->year, 'region_id' => $ids[0], 'province_id' => $ids[1], 'citymun_id' => $ids[2], 'barangay_id' => $ids[3]]) : new ProjectBarangay();
                            $barangayMModel->project_id = $model->id;
                            $barangayMModel->year = $model->year;
                            $barangayMModel->region_id = $ids[0];
                            $barangayMModel->province_id = $ids[1];
                            $barangayMModel->citymun_id = $ids[2];
                            $barangayMModel->barangay_id = $ids[3];
                            if (! ($flag = $barangayMModel->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }

                    $targets = $postData['ProjectTarget'];
                    $targetTypes = ['Physical', 'Financial', 'Male Employed', 'Female Employed', 'Beneficiaries'];
                    if(!empty($targets))
                    {
                        foreach($targets as $i => $target)
                        {
                            $targetModel = ProjectTarget::findOne(['project_id' => $model->id, 'year' => $model->year, 'target_type' => $targetTypes[$i]]) ? 
                            ProjectTarget::findOne(['project_id' => $model->id, 'year' => $model->year, 'target_type' => $targetTypes[$i]]) : new ProjectTarget();
                            $targetModel->project_id = $model->id;
                            $targetModel->year = $model->year;
                            $targetModel->target_type = $targetTypes[$i];
                            $targetModel->indicator = isset($target['indicator']) ? $target['indicator'] : '';
                            $targetModel->q1 = isset($target['q1']) ? $this->removeMask($target['q1']) : 0;
                            $targetModel->q2 = isset($target['q2']) ? $this->removeMask($target['q2']) : 0;
                            $targetModel->q3 = isset($target['q3']) ? $this->removeMask($target['q3']) : 0;
                            $targetModel->q4 = isset($target['q4']) ? $this->removeMask($target['q4']) : 0;
                            if (! ($flag = $targetModel->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                }

                if ($flag) {
                    $transaction->commit();
                    \Yii::$app->getSession()->setFlash('success', 'Project saved as draft');
                    return $this->redirect(['/rpmes/project/create']);
                }
            } catch (Exception $e) {
                $transaction->rollBack();
            }
        }
    }

    public function actionDraft()
    {
        $dueDate = DueDate::findOne(['report' => 'Monitoring Plan', 'year' => date("Y")]);
        $projectsPaging = Project::find();
        $projectsPaging = Yii::$app->user->can('AgencyUser') ? 
            $projectsPaging
            ->andWhere(['draft' => 'Yes'])
            ->andWhere(['agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]) 
            ->andWhere(['submitted_by' => Yii::$app->user->id]) 
            : 
            $projectsPaging
            ->andWhere(['draft' => 'Yes'])
            ->andWhere(['submitted_by' => Yii::$app->user->id]) 
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
            ->all();

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];
        $genders = ['M' => 'Male', 'F' => 'Female'];

        if(Yii::$app->request->get())
        {
            $getData = Yii::$app->request->get();
            if(isset($getData['id']))
            {
                $ids = Yii::$app->request->get('id');
                $ids = explode(",", $ids);
                
                Project::deleteAll(['in', 'id', $ids]);
                \Yii::$app->getSession()->setFlash('success', 'Selected projects has been deleted successfully');
                return $this->redirect(['/rpmes/project/draft']);
            }
        }

        if(Yii::$app->request->post())
        {
            $postData = Yii::$app->request->post('Project');
            $ids = ArrayHelper::map($postData, 'id', 'id');

            
            Project::updateAll(['draft' => 'No'], 'id in ('.implode(",",$ids).')');
            if(!empty($ids))
            {
                foreach($ids as $id)
                {
                    $project = Project::findOne(['id' => $id]);
                    $plan = Plan::findOne(['project_id' => $project->id, 'year' => $project->year]) ? Plan::findOne(['project_id' => $project->id, 'year' => $project->year]) : new Plan();
                    $plan->project_id = $project->id;
                    $plan->year = $project->year;
                    $plan->date_submitted = date("Y-m-d H:i:s");
                    $plan->submitted_by = Yii::$app->user->id;
                    $plan->save(false);
                }
            }

            \Yii::$app->getSession()->setFlash('success', 'Selected projects has been submitted successfully');
            return $this->redirect(['/rpmes/project/draft']);
        }

        return $this->render('draft', [
            'dueDate' => $dueDate,
            'projectIds' => $projectIds,
            'projectsModels' => $projectsModels,
            'projectsPages' => $projectsPages,
            'quarters' => $quarters,
            'genders' => $genders,
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
        $dueDate = DueDate::findOne(['report' => 'Monitoring Plan', 'year' => date("Y")]);

        $model = $this->findModel($id);

        $regionModel = new ProjectRegion();
        $projectRegions = $model->projectRegions;
        $regionModel->region_id = array_values(ArrayHelper::map($projectRegions, 'region_id', 'region_id'));
        
        $provinceModel = new ProjectProvince();
        $projectProvinces = $model->projectProvinces;
        $provinceModel->province_id = array_values(ArrayHelper::map($projectProvinces, 'province_id', 'province_id'));
        
        $citymunModel = new ProjectCitymun();
        $projectCitymuns = $model->projectCitymuns;
        $citymunModel->citymun_id = array_values(ArrayHelper::map($projectCitymuns, 'citymunId', 'citymunId'));
        
        $barangayModel = new ProjectBarangay();
        $projectBarangays = $model->projectBarangays;
        $barangayModel->barangay_id = array_values(ArrayHelper::map($projectBarangays, 'barangayId', 'barangayId'));
        
        $categoryModel = new ProjectCategory();
        $projectCategories = $model->projectCategories;
        $categoryModel->category_id = array_values(ArrayHelper::map($projectCategories, 'category_id', 'category_id'));

        $kraModel = new ProjectKra();
        $projectKras = $model->projectKras;
        $kraModel->key_result_area_id = array_values(ArrayHelper::map($projectKras, 'key_result_area_id', 'key_result_area_id'));
        
        $sdgModel = new ProjectSdgGoal();
        $projectSdgGoals = $model->projectSdgGoals;
        $sdgModel->sdg_goal_id = array_values(ArrayHelper::map($projectSdgGoals, 'sdg_goal_id', 'sdg_goal_id'));
        
        $rdpChapterModel = new ProjectRdpChapter();
        $projectRdpChapters = $model->projectRdpChapters;
        $rdpChapterModel->rdp_chapter_id = array_values(ArrayHelper::map($projectRdpChapters, 'rdp_chapter_id', 'rdp_chapter_id'));

        $rdpChapterOutcomeModel = new ProjectRdpChapterOutcome();
        $projectRdpChapterOutcomes = $model->projectRdpChapterOutcomes;
        $rdpChapterOutcomeModel->rdp_chapter_outcome_id = array_values(ArrayHelper::map($projectRdpChapterOutcomes, 'rdp_chapter_outcome_id', 'rdp_chapter_outcome_id'));
        
        $rdpSubChapterOutcomeModel = new ProjectRdpSubChapterOutcome();
        $projectRdpSubChapterOutcomes = $model->projectRdpSubChapterOutcomes;
        $rdpSubChapterOutcomeModel->rdp_sub_chapter_outcome_id = array_values(ArrayHelper::map($projectRdpSubChapterOutcomes, 'rdp_sub_chapter_outcome_id', 'rdp_sub_chapter_outcome_id'));
        
        $expectedOutputModels = $model->projectExpectedOutputs;
        $outcomeModels = $model->projectOutcomes;

        $agencies = Agency::find()->select(['id', 'concat(title," (",code,")") as title']);
        $agencies = Yii::$app->user->can('AgencyUser') ? $agencies->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $agencies;
        $agencies = $agencies->orderBy(['title' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $projects = [];
        $programs = [];

        $sectors = Sector::find()->all();
        $sectorIDs = ArrayHelper::map($sectors, 'id', 'id');
        $sectors = ArrayHelper::map($sectors, 'id', 'title');

        $subSectors = SubSectorPerSector::find()
                    ->select(['sub_sector.id', 'sub_sector.title'])
                    ->leftJoin('sub_sector', 'sub_sector.id = sub_sector_per_sector.sub_sector_id')
                    ->where(['in', 'sector_id', $sectorIDs])
                    ->asArray()
                    ->all();

        $subSectors = ArrayHelper::map($subSectors, 'id', 'title');

        $modes = ModeOfImplementation::find()->all();
        $modes = ArrayHelper::map($modes, 'id', 'title');

        $fundSources = FundSource::find()->select(['id', 'concat(title," (",code,")") as title'])->asArray()->all();
        $fundSources = ArrayHelper::map($fundSources, 'id', 'title');

        $scopes = LocationScope::find()->all();
        $scopes = ArrayHelper::map($scopes, 'id', 'title');

        $regions = Region::find()->orderBy(['region_sort' => SORT_ASC])->all();
        $regions = ArrayHelper::map($regions, 'region_c', 'abbreviation');

        $provinces = Province::find()
                    ->leftJoin('tblregion', 'tblregion.region_c = tblprovince.region_c')
                    ->where(['in', 'tblprovince.region_c', array_values(ArrayHelper::map($projectRegions, 'region_id', 'region_id'))])
                    ->orderBy(['abbreviation' => SORT_ASC, 'province_m' => SORT_ASC])
                    ->all();
        $provinces = ArrayHelper::map($provinces, 'province_c', 'provinceTitle');

        $citymuns = Citymun::find()
                    ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
                    ->where(['in', 'tblcitymun.province_c', array_values(ArrayHelper::map($projectProvinces, 'province_id', 'province_id'))])
                    ->orderBy(['province_m' => SORT_ASC, 'citymun_m' => SORT_ASC])
                    ->all();
        
        $citymunIDs = ArrayHelper::map($citymuns, 'citymunId', 'citymunId');
        $citymuns = ArrayHelper::map($citymuns, 'citymunId', 'citymunTitle');
        
        $nonManilaBarangays = Barangay::find()
                    ->select(['concat(tblbarangay.region_c,"-",tblbarangay.province_c,"-",tblbarangay.citymun_c,"-",tblbarangay.barangay_c) as id', 'concat(tblprovince.province_m,": ",tblcitymun.citymun_m,": ",tblbarangay.barangay_m) as title'])
                    ->leftJoin('tblcitymun', 'tblcitymun.province_c = tblbarangay.province_c and tblcitymun.citymun_c = tblbarangay.citymun_c')
                    ->leftJoin('tblprovince', 'tblprovince.province_c = tblbarangay.province_c')
                    ->leftJoin('tblregion', 'tblregion.region_c = tblbarangay.region_c')
                    ->andWhere(['in', 'concat(tblbarangay.region_c,"-",tblbarangay.province_c,"-",tblbarangay.citymun_c)', array_values(ArrayHelper::map($projectCitymuns, 'citymunId', 'citymunId'))])
                    ->orderBy(['tblprovince.province_m' => SORT_ASC, 'tblcitymun.citymun_m' => SORT_ASC, 'tblbarangay.barangay_m' => SORT_ASC])
                    ->asArray()
                    ->all();
            
        $manilaBarangays = in_array('13-39-00', $citymunIDs) ?  Barangay::find()
                    ->select(['concat(tblbarangay.region_c,"-",tblbarangay.province_c,"-",tblbarangay.citymun_c,"-",tblbarangay.barangay_c) as id', 'concat(tblprovince.province_m,": ",tblcitymun.citymun_m,": ",tblbarangay.barangay_m) as title'])
                    ->leftJoin('tblcitymun', 'tblcitymun.province_c = tblbarangay.province_c and tblcitymun.citymun_c = "00"')
                    ->leftJoin('tblprovince', 'tblprovince.province_c = tblbarangay.province_c')
                    ->leftJoin('tblregion', 'tblregion.region_c = tblbarangay.region_c')
                    ->andWhere(['tblbarangay.region_c' => '13','tblbarangay.province_c' => '39'])
                    ->orderBy(['tblprovince.province_m' => SORT_ASC, 'tblcitymun.citymun_m' => SORT_ASC, 'tblbarangay.barangay_m' => SORT_ASC])
                    ->asArray()
                    ->all() : [];

        $barangays = [];

        foreach($nonManilaBarangays as $barangay){
            $barangays[$barangay['id']] = $barangay['title'];
        }
        foreach($manilaBarangays as $barangay){
            $barangays[$barangay['id']] = $barangay['title'];
        }

        $categories = Category::find()->all();
        $categoryIDs = ArrayHelper::map($categories, 'id', 'id');
        $categories = ArrayHelper::map($categories, 'id', 'title');

        $kras = KeyResultArea::find()->where(['in', 'category_id', $categoryIDs])->all();
        $kras = ArrayHelper::map($kras, 'id', 'kraTitle');

        $goals = SdgGoal::find()->select(['id', 'concat("SDG #",sdg_no,": ",title) as title'])->asArray()->all();
        $goals = ArrayHelper::map($goals, 'id', 'title');

        $chapters = RdpChapter::find()->select(['id', 'concat("Chapter ",chapter_no,": ",title) as title'])->asArray()->all();
        $chapterIDs = ArrayHelper::map($chapters, 'id', 'id');
        $chapters = ArrayHelper::map($chapters, 'id', 'title');

        $chapterOutcomes = RdpChapterOutcome::find()
                    ->select(['id', 'concat("Chapter Outcome ",level,": ",title) as title'])
                    ->where(['in', 'rdp_chapter_id', $chapterIDs])
                    ->orderBy(['level' => SORT_ASC, 'title' => SORT_ASC])
                    ->asArray()
                    ->all();
        $chapterOutcomeIDs = ArrayHelper::map($chapterOutcomes, 'id', 'id');
        $chapterOutcomes = ArrayHelper::map($chapterOutcomes, 'id', 'title');

        $subChapterOutcomes = RdpSubChapterOutcome::find()
                    ->select(['rdp_sub_chapter_outcome.id as id', 'concat("Sub-Chapter Outcome ",rdp_chapter_outcome.level,".",rdp_sub_chapter_outcome.level,": ",rdp_sub_chapter_outcome.title) as title'])
                    ->leftJoin('rdp_chapter_outcome', 'rdp_chapter_outcome.id = rdp_sub_chapter_outcome.rdp_chapter_outcome_id')
                    ->where(['in', 'rdp_sub_chapter_outcome.rdp_chapter_outcome_id', $chapterOutcomeIDs])
                    ->orderBy(['rdp_chapter_outcome.level' => SORT_ASC, 'rdp_sub_chapter_outcome.level' => SORT_ASC, 'rdp_sub_chapter_outcome.title' => SORT_ASC])
                    ->asArray()
                    ->all();
        $subChapterOutcomes = ArrayHelper::map($subChapterOutcomes, 'id', 'title');

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];
        $genders = ['M' => 'Male', 'F' => 'Female'];

        $targets = [];

        $physicalTargetModel = ProjectTarget::findOne(['project_id' => $model->id, 'year' => $model->year, 'target_type' => 'Physical']) ? ProjectTarget::findOne(['project_id' => $model->id, 'year' => $model->year, 'target_type' => 'Physical']) : new ProjectTarget();
        $physicalTargetModel->scenario = 'physicalTarget';
        $physicalTargetModel->project_id = $model->id;
        $physicalTargetModel->year = $model->year;
        $physicalTargetModel->target_type = 'Physical';

        $targets[0] = $physicalTargetModel;

        $financialTargetModel = ProjectTarget::findOne(['project_id' => $model->id, 'year' => $model->year, 'target_type' => 'Financial']) ? ProjectTarget::findOne(['project_id' => $model->id, 'year' => $model->year, 'target_type' => 'Financial']) : new ProjectTarget();
        $financialTargetModel->project_id = $model->id;
        $financialTargetModel->year = $model->year;
        $financialTargetModel->target_type = 'Financial';

        $targets[1] = $financialTargetModel;

        

        $maleEmployedTargetModel = ProjectTarget::findOne(['project_id' => $model->id, 'year' => $model->year, 'target_type' => 'Male Employed']) ? ProjectTarget::findOne(['project_id' => $model->id, 'year' => $model->year, 'target_type' => 'Male Employed']) : new ProjectTarget();
        $maleEmployedTargetModel->project_id = $model->id;
        $maleEmployedTargetModel->year = $model->year;
        $maleEmployedTargetModel->target_type = 'Male Employed';

        $targets[2] = $maleEmployedTargetModel;

        $femaleEmployedTargetModel = ProjectTarget::findOne(['project_id' => $model->id, 'year' => $model->year, 'target_type' => 'Female Employed']) ? ProjectTarget::findOne(['project_id' => $model->id, 'year' => $model->year, 'target_type' => 'Female Employed']) : new ProjectTarget();
        $femaleEmployedTargetModel->project_id = $model->id;
        $femaleEmployedTargetModel->year = $model->year;
        $femaleEmployedTargetModel->target_type = 'Female Employed';

        $targets[3] = $femaleEmployedTargetModel;

        $beneficiaryTargetModel = ProjectTarget::findOne(['project_id' => $model->id, 'year' => $model->year, 'target_type' => 'Beneficiaries']) ? ProjectTarget::findOne(['project_id' => $model->id, 'year' => $model->year, 'target_type' => 'Beneficiaries']) : new ProjectTarget();
        $beneficiaryTargetModel->project_id = $model->id;
        $beneficiaryTargetModel->year = $model->year;
        $beneficiaryTargetModel->target_type = 'Beneficiaries';

        $targets[4] = $beneficiaryTargetModel;

        if (
            $model->load(Yii::$app->request->post()) &&
            $regionModel->load(Yii::$app->request->post()) &&
            $provinceModel->load(Yii::$app->request->post()) &&
            $citymunModel->load(Yii::$app->request->post()) &&
            $barangayModel->load(Yii::$app->request->post()) &&
            $categoryModel->load(Yii::$app->request->post()) &&
            $kraModel->load(Yii::$app->request->post()) &&
            $sdgModel->load(Yii::$app->request->post()) &&
            $rdpChapterModel->load(Yii::$app->request->post()) &&
            $rdpChapterOutcomeModel->load(Yii::$app->request->post()) &&
            $rdpSubChapterOutcomeModel->load(Yii::$app->request->post()) &&
            MultipleModel::loadMultiple($targets, Yii::$app->request->post()) 
            ) 
            {

            $oldExpectedOutputIDs = ArrayHelper::map($expectedOutputModels, 'id', 'id');
            $oldOutcomeIDs = ArrayHelper::map($outcomeModels, 'id', 'id');
            $oldRegionIDs = array_values(ArrayHelper::map($projectRegions, 'region_id', 'region_id'));
            $oldProvinceIDs = array_values(ArrayHelper::map($projectProvinces, 'province_id', 'province_id'));
            $oldCitymunIDs = array_values(ArrayHelper::map($projectCitymuns, 'citymunId', 'citymunId'));
            $oldBarangayIDs = array_values(ArrayHelper::map($projectBarangays, 'barangayId', 'barangayId'));
            $oldCategoryIDs = array_values(ArrayHelper::map($projectCategories, 'category_id', 'category_id'));
            $oldKraIDs = array_values(ArrayHelper::map($projectKras, 'key_result_area_id', 'key_result_area_id'));
            $oldSdgGoalIDs = array_values(ArrayHelper::map($projectSdgGoals, 'sdg_goal_id', 'sdg_goal_id'));
            $oldRdpChapterIDs = array_values(ArrayHelper::map($projectRdpChapters, 'rdp_chapter_id', 'rdp_chapter_id'));
            $oldRdpChapterOutcomeIDs = array_values(ArrayHelper::map($projectRdpChapterOutcomes, 'rdp_chapter_outcome_id', 'rdp_chapter_outcome_id'));
            $oldRdpSubChapterOutcomeIDs = array_values(ArrayHelper::map($projectRdpSubChapterOutcomes, 'rdp_sub_chapter_outcome_id', 'rdp_sub_chapter_outcome_id'));
            
            $expectedOutputModels = Model::createMultiple(ProjectExpectedOutput::classname(), $expectedOutputModels);
            $outcomeModels = Model::createMultiple(ProjectOutcome::classname(), $outcomeModels);

            Model::loadMultiple($expectedOutputModels, Yii::$app->request->post());
            Model::loadMultiple($outcomeModels, Yii::$app->request->post());

            $deletedExpectedOutputIDs = array_diff($oldExpectedOutputIDs, array_filter(ArrayHelper::map($expectedOutputModels, 'id', 'id')));
            $deletedOutcomeIDs = array_diff($oldOutcomeIDs, array_filter(ArrayHelper::map($outcomeModels, 'id', 'id')));
            $deletedRegionIDs = $regionModel->region_id != '' ? array_diff($oldRegionIDs, array_filter($regionModel->region_id)) : array_diff($oldRegionIDs, []);
            $deletedProvinceIDs = $provinceModel->province_id != '' ? array_diff($oldProvinceIDs, array_filter($provinceModel->province_id)) : array_diff($oldProvinceIDs, []);
            $deletedCitymunIDs = $citymunModel->citymun_id != '' ? array_diff($oldCitymunIDs, array_filter($citymunModel->citymun_id)) : array_diff($oldCitymunIDs, []);
            $deletedBarangayIDs = $barangayModel->barangay_id != '' ? array_diff($oldBarangayIDs, array_filter($barangayModel->barangay_id)) : array_diff($oldBarangayIDs, []);
            $deletedCategoryIDs = $categoryModel->category_id != '' ? array_diff($oldCategoryIDs, array_filter($categoryModel->category_id)) : array_diff($oldCategoryIDs, []);
            $deletedKraIDs = $kraModel->key_result_area_id != '' ? array_diff($oldKraIDs, array_filter($kraModel->key_result_area_id)) : array_diff($oldKraIDs, []);
            $deletedSdgGoalIDs = $sdgModel->sdg_goal_id != '' ? array_diff($oldSdgGoalIDs, array_filter($sdgModel->sdg_goal_id)) : array_diff($oldSdgGoalIDs, []);
            $deletedRdpChapterIDs = $rdpChapterModel->rdp_chapter_id != '' ? array_diff($oldRdpChapterIDs, array_filter($rdpChapterModel->rdp_chapter_id)) : array_diff($oldRdpChapterIDs, []);
            $deletedRdpChapterOutcomeIDs = $rdpChapterOutcomeModel->rdp_chapter_outcome_id != '' ? array_diff($oldRdpChapterOutcomeIDs, array_filter($rdpChapterOutcomeModel->rdp_chapter_outcome_id)) : array_diff($oldRdpChapterOutcomeIDs, []);
            $deletedRdpSubChapterOutcomeIDs = $rdpSubChapterOutcomeModel->rdp_sub_chapter_outcome_id != '' ? array_diff($oldRdpSubChapterOutcomeIDs, array_filter($rdpSubChapterOutcomeModel->rdp_sub_chapter_outcome_id)) : array_diff($oldRdpSubChapterOutcomeIDs, []);

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($expectedOutputModels) && Model::validateMultiple($outcomeModels) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                $agency = Agency::findOne(['id' => $model->agency_id]);
                $lastProject = Project::find()->where(['agency_id' => $model->agency_id, 'year' => $model->year])->orderBy(['id' => SORT_DESC])->one();
                $lastNumber = $lastProject ? intval(substr($lastProject->project_no, -4)): '0001';
                $project_no = $agency->code.'-'.substr($model->year, -2).'-'.str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                $model->project_no = $project_no;
                $model->submitted_by = Yii::$app->user->id;
                $model->draft = 'No';
                try {
                    if ($flag = $model->save(false)) {
                        $plan = Plan::findOne(['project_id' => $model->id, 'year' => $model->year]) ? Plan::findOne(['project_id' => $model->id, 'year' => $model->year]) : new Plan();
                        $plan->project_id = $model->id;
                        $plan->year = $model->year;
                        $plan->date_submitted = date("Y-m-d H:i:s");
                        $plan->submitted_by = Yii::$app->user->id;
                        $plan->save(false);

                        if(!empty($deletedExpectedOutputIDs))
                        {
                            ProjectExpectedOutput::deleteAll(['id' => $deletedExpectedOutputIDs]);
                        }
        
                        foreach ($expectedOutputModels as $expectedOutputModel) {
                            $expectedOutputModel->project_id = $model->id;
                            $expectedOutputModel->year = $model->year;
                            if (! ($flag = $expectedOutputModel->save())) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        if(!empty($deletedOutcomeIDs))
                        {
                            ProjectOutcome::deleteAll(['id' => $deletedOutcomeIDs]);
                        }

                        foreach ($outcomeModels as $outcomeModel) {
                            $outcomeModel->project_id = $model->id;
                            $outcomeModel->year = $model->year;
                            if (! ($flag = $outcomeModel->save())) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        if(!empty($deletedCategoryIDs))
                        {
                            foreach($deletedCategoryIDs as $id)
                            {
                                $category = ProjectCategory::findOne(['project_id' => $model->id, 'year' => $model->year, 'category_id' => $id]);
                                if($category)
                                {
                                    if (! ($flag = $category->delete())) {
                                        $transaction->rollBack();
                                        break;
                                    }
                                }
                            }
                        }

                        if(!empty($categoryModel->category_id))
                        {
                            foreach($categoryModel->category_id as $id)
                            {
                                $category = ProjectCategory::findOne(['project_id' => $model->id, 'year' => $model->year, 'category_id' => $id]) ? 
                                ProjectCategory::findOne(['project_id' => $model->id, 'year' => $model->year, 'category_id' => $id]) : new ProjectCategory();
                                $category->project_id = $model->id;
                                $category->year = $model->year;
                                $category->category_id = $id;
                                if (! ($flag = $category->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($deletedKraIDs))
                        {
                            foreach($deletedKraIDs as $id)
                            {
                                $kra = ProjectKra::findOne(['project_id' => $model->id, 'year' => $model->year, 'key_result_area_id' => $id]);
                                if($kra)
                                {
                                    if (! ($flag = $kra->delete())) {
                                        $transaction->rollBack();
                                        break;
                                    }
                                }
                            }
                        }

                        if(!empty($kraModel->key_result_area_id))
                        {
                            foreach($kraModel->key_result_area_id as $id)
                            {
                                $kra = ProjectKra::findOne(['project_id' => $model->id, 'year' => $model->year, 'key_result_area_id' => $id]) ? 
                                ProjectKra::findOne(['project_id' => $model->id, 'year' => $model->year, 'key_result_area_id' => $id]) : new ProjectKra();
                                $kra->project_id = $model->id;
                                $kra->year = $model->year;
                                $kra->key_result_area_id = $id;
                                if (! ($flag = $kra->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($deletedSdgGoalIDs))
                        {
                            ProjectSdgGoal::deleteAll(['project_id' => $model->id, 'year' => $model->year, 'sdg_goal_id' => $deletedSdgGoalIDs]);
                        }

                        if(!empty($sdgModel->sdg_goal_id))
                        {
                            foreach($sdgModel->sdg_goal_id as $id)
                            {
                                $sdg = ProjectSdgGoal::findOne(['project_id' => $model->id, 'year' => $model->year, 'sdg_goal_id' => $id]) ?
                                ProjectSdgGoal::findOne(['project_id' => $model->id, 'year' => $model->year, 'sdg_goal_id' => $id]) : new ProjectSdgGoal();
                                $sdg->project_id = $model->id;
                                $sdg->year = $model->year;
                                $sdg->sdg_goal_id = $id;
                                if (! ($flag = $sdg->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($deletedRdpChapterIDs))
                        {
                            ProjectRdpChapter::deleteAll(['project_id' => $model->id, 'year' => $model->year, 'rdp_chapter_id' => $deletedRdpChapterIDs]);
                        }

                        if(!empty($rdpChapterModel->rdp_chapter_id))
                        {
                            foreach($rdpChapterModel->rdp_chapter_id as $id)
                            {
                                $chapter = ProjectRdpChapter::findOne(['project_id' => $model->id, 'year' => $model->year, 'rdp_chapter_id' => $id]) ?
                                ProjectRdpChapter::findOne(['project_id' => $model->id, 'year' => $model->year, 'rdp_chapter_id' => $id]) : new ProjectRdpChapter();
                                $chapter->project_id = $model->id;
                                $chapter->year = $model->year;
                                $chapter->rdp_chapter_id = $id;
                                if (! ($flag = $chapter->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($deletedRdpChapterOutcomeIDs))
                        {
                            ProjectRdpChapterOutcome::deleteAll(['project_id' => $model->id, 'year' => $model->year, 'rdp_chapter_outcome_id' => $deletedRdpChapterOutcomeIDs]);
                        }

                        if(!empty($rdpChapterOutcomeModel->rdp_chapter_outcome_id))
                        {
                            foreach($rdpChapterOutcomeModel->rdp_chapter_outcome_id as $id)
                            {
                                $chapterOutcome = ProjectRdpChapterOutcome::findOne(['project_id' => $model->id, 'year' => $model->year, 'rdp_chapter_outcome_id' => $id]) ?
                                ProjectRdpChapterOutcome::findOne(['project_id' => $model->id, 'year' => $model->year, 'rdp_chapter_outcome_id' => $id]) : new ProjectRdpChapterOutcome();
                                $chapterOutcome->project_id = $model->id;
                                $chapterOutcome->year = $model->year;
                                $chapterOutcome->rdp_chapter_outcome_id = $id;
                                if (! ($flag = $chapterOutcome->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($deletedRdpSubChapterOutcomeIDs))
                        {
                            ProjectRdpSubChapterOutcome::deleteAll(['project_id' => $model->id, 'year' => $model->year, 'rdp_sub_chapter_outcome_id' => $deletedRdpSubChapterOutcomeIDs]);
                        }

                        if(!empty($rdpSubChapterOutcomeModel->rdp_sub_chapter_outcome_id))
                        {
                            foreach($rdpSubChapterOutcomeModel->rdp_sub_chapter_outcome_id as $id)
                            {
                                $subChapterOutcome = ProjectRdpSubChapterOutcome::findOne(['project_id' => $model->id, 'year' => $model->year, 'rdp_sub_chapter_outcome_id' => $id]) ?
                                ProjectRdpSubChapterOutcome::findOne(['project_id' => $model->id, 'year' => $model->year, 'rdp_sub_chapter_outcome_id' => $id]) : new ProjectRdpSubChapterOutcome();
                                $subChapterOutcome->project_id = $model->id;
                                $subChapterOutcome->year = $model->year;
                                $subChapterOutcome->rdp_sub_chapter_outcome_id = $id;
                                if (! ($flag = $subChapterOutcome->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($deletedRegionIDs))
                        {
                            ProjectRegion::deleteAll(['project_id' => $model->id, 'year' => $model->year, 'region_id' => $deletedRegionIDs]);
                        }

                        if(!empty($regionModel->region_id))
                        {
                            foreach($regionModel->region_id as $id)
                            {
                                $region = ProjectRegion::findOne(['project_id' => $model->id, 'year' => $model->year, 'region_id' => $id]) ?
                                ProjectRegion::findOne(['project_id' => $model->id, 'year' => $model->year, 'region_id' => $id]) : new ProjectRegion();
                                $region->project_id = $model->id;
                                $region->year = $model->year;
                                $region->region_id = $id;
                                if (! ($flag = $region->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($deletedProvinceIDs))
                        {
                            ProjectProvince::deleteAll(['project_id' => $model->id, 'year' => $model->year, 'province_id' => $deletedProvinceIDs]);
                        }

                        if(!empty($provinceModel->province_id))
                        {
                            foreach($provinceModel->province_id as $id)
                            {
                                $province = ProjectProvince::findOne(['project_id' => $model->id, 'year' => $model->year, 'province_id' => $id]) ?
                                ProjectProvince::findOne(['project_id' => $model->id, 'year' => $model->year, 'province_id' => $id]) :  new ProjectProvince();
                                $province->project_id = $model->id;
                                $province->year = $model->year;
                                $province->province_id = $id;
                                if (! ($flag = $province->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($deletedCitymunIDs))
                        {
                            foreach($deletedCitymunIDs as $id)
                            {
                                $ids = explode("-", $id);
                                $citymun = ProjectCitymun::findOne(['project_id' => $model->id, 'year' => $model->year, 'region_id' => $ids[0], 'province_id' => $ids[1], 'citymun_id' => $ids[2]]);
                                if($citymun)
                                {
                                    if (! ($flag = $citymun->delete())) {
                                        $transaction->rollBack();
                                        break;
                                    }
                                }
                            }
                        }

                        if(!empty($citymunModel->citymun_id))
                        {
                            foreach($citymunModel->citymun_id as $id)
                            {
                                $ids = explode("-", $id);
                                $citymun = ProjectCitymun::findOne(['project_id' => $model->id, 'year' => $model->year, 'region_id' => $ids[0], 'province_id' => $ids[1], 'citymun_id' => $ids[2]]) ?
                                ProjectCitymun::findOne(['project_id' => $model->id, 'year' => $model->year, 'region_id' => $ids[0], 'province_id' => $ids[1], 'citymun_id' => $ids[2]]) : new ProjectCitymun();
                                $citymun->project_id = $model->id;
                                $citymun->year = $model->year;
                                $citymun->region_id = $ids[0];
                                $citymun->province_id = $ids[1];
                                $citymun->citymun_id = $ids[2];
                                if (! ($flag = $citymun->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($deletedBarangayIDs))
                        {
                            foreach($deletedBarangayIDs as $id)
                            {
                                $ids = explode("-", $id);
                                $barangay = ProjectBarangay::findOne(['project_id' => $model->id, 'year' => $model->year, 'region_id' => $ids[0], 'province_id' => $ids[1], 'citymun_id' => $ids[2], 'barangay_id' => $ids[3]]);
                                if($barangay)
                                {
                                    if (! ($flag = $barangay->delete())) {
                                        $transaction->rollBack();
                                        break;
                                    }
                                }
                            }
                        }

                        if(!empty($barangayModel->barangay_id))
                        {
                            foreach($barangayModel->barangay_id as $id)
                            {
                                $ids = explode("-", $id);
                                $barangay = ProjectBarangay::findOne(['project_id' => $model->id, 'year' => $model->year, 'region_id' => $ids[0], 'province_id' => $ids[1], 'citymun_id' => $ids[2], 'barangay_id' => $ids[3]]) ?
                                ProjectBarangay::findOne(['project_id' => $model->id, 'year' => $model->year, 'region_id' => $ids[0], 'province_id' => $ids[1], 'citymun_id' => $ids[2], 'barangay_id' => $ids[3]]) : new ProjectBarangay();
                                $barangay->project_id = $model->id;
                                $barangay->year = $model->year;
                                $barangay->region_id = $ids[0];
                                $barangay->province_id = $ids[1];
                                $barangay->citymun_id = $ids[2];
                                $barangay->barangay_id = $ids[3];
                                if (! ($flag = $barangay->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($targets))
                        {
                            foreach($targets as $target)
                            {
                                $target->project_id = $model->id;
                                $target->year = $model->year;
                                if (! ($flag = $target->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        \Yii::$app->getSession()->setFlash('success', 'Record Updated');
                        return $model->draft == 'No' ? $this->redirect(['/rpmes/plan/']) : $this->redirect(['/rpmes/project/draft']);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'regionModel' => $regionModel,
            'provinceModel' => $provinceModel,
            'citymunModel' => $citymunModel,
            'barangayModel' => $barangayModel,
            'categoryModel' => $categoryModel,
            'kraModel' => $kraModel,
            'sdgModel' => $sdgModel,
            'rdpChapterModel' => $rdpChapterModel,
            'rdpChapterOutcomeModel' => $rdpChapterOutcomeModel,
            'rdpSubChapterOutcomeModel' => $rdpSubChapterOutcomeModel,
            'targets' => $targets,
            'expectedOutputModels' => (empty($expectedOutputModels)) ? [new ProjectExpectedOutput] : $expectedOutputModels,
            'outcomeModels' => (empty($outcomeModels)) ? [new ProjectOutcome] : $outcomeModels,
            'agencies' => $agencies,
            'projects' => $projects,
            'programs' => $programs,
            'sectors' => $sectors,
            'subSectors' => $subSectors,
            'modes' => $modes,
            'fundSources' => $fundSources,
            'scopes' => $scopes,
            'regions' => $regions,
            'provinces' => $provinces,
            'citymuns' => $citymuns,
            'barangays' => $barangays,
            'categories' => $categories,
            'kras' => $kras,
            'chapters' => $chapters,
            'goals' => $goals,
            'chapterOutcomes' => $chapterOutcomes,
            'subChapterOutcomes' => $subChapterOutcomes,
            'quarters' => $quarters,
            'genders' => $genders,
            'dueDate' => $dueDate,
        ]);
    }

    public function actionCarryOver($id)
    {
        $project = $this->findModel($id);

        $dueDate = DueDate::findOne(['report' => 'Monitoring Plan', 'year' => date("Y")]);

        if($project->year == date("Y")){ throw new NotFoundHttpException('The requested page does not exist.'); }

        $model = new Project();
        $model->scenario = 'projectCarryOverUser';
        $model->year = date("Y");
        $model->period = 'Carry-Over';        
        $model->program_id = $project->program_id;
        $model->agency_id = $project->agency_id;
        $model->title = $project->title;
        $model->description = $project->description;
        $model->sector_id = $project->sector_id;
        $model->sub_sector_id = $project->sub_sector_id;
        $model->mode_of_implementation_id = $project->mode_of_implementation_id;
        $model->other_mode = $project->other_mode;
        $model->fund_source_id = $project->fund_source_id;
        $model->typhoon = $project->typhoon;
        $model->start_date = $project->start_date;
        $model->completion_date = $project->completion_date;
        $model->data_type = $project->data_type;

        $regionModel = new ProjectRegion();
        $projectRegions = $project->projectRegions;
        $regionModel->region_id = array_values(ArrayHelper::map($projectRegions, 'region_id', 'region_id'));
        
        $provinceModel = new ProjectProvince();
        $projectProvinces = $project->projectProvinces;
        $provinceModel->province_id = array_values(ArrayHelper::map($projectProvinces, 'province_id', 'province_id'));
        
        $citymunModel = new ProjectCitymun();
        $projectCitymuns = $project->projectCitymuns;
        $citymunModel->citymun_id = array_values(ArrayHelper::map($projectCitymuns, 'citymunId', 'citymunId'));
        
        $barangayModel = new ProjectBarangay();
        $projectBarangays = $project->projectBarangays;
        $barangayModel->barangay_id = array_values(ArrayHelper::map($projectBarangays, 'barangayId', 'barangayId'));
        
        $categoryModel = new ProjectCategory();
        $projectCategories = $project->projectCategories;
        $categoryModel->category_id = array_values(ArrayHelper::map($projectCategories, 'category_id', 'category_id'));

        $kraModel = new ProjectKra();
        $projectKras = $project->projectKras;
        $kraModel->key_result_area_id = array_values(ArrayHelper::map($projectKras, 'key_result_area_id', 'key_result_area_id'));
        
        $sdgModel = new ProjectSdgGoal();
        $projectSdgGoals = $project->projectSdgGoals;
        $sdgModel->sdg_goal_id = array_values(ArrayHelper::map($projectSdgGoals, 'sdg_goal_id', 'sdg_goal_id'));
        
        $rdpChapterModel = new ProjectRdpChapter();
        $projectRdpChapters = $project->projectRdpChapters;
        $rdpChapterModel->rdp_chapter_id = array_values(ArrayHelper::map($projectRdpChapters, 'rdp_chapter_id', 'rdp_chapter_id'));

        $rdpChapterOutcomeModel = new ProjectRdpChapterOutcome();
        $projectRdpChapterOutcomes = $project->projectRdpChapterOutcomes;
        $rdpChapterOutcomeModel->rdp_chapter_outcome_id = array_values(ArrayHelper::map($projectRdpChapterOutcomes, 'rdp_chapter_outcome_id', 'rdp_chapter_outcome_id'));
        
        $rdpSubChapterOutcomeModel = new ProjectRdpSubChapterOutcome();
        $projectRdpSubChapterOutcomes = $project->projectRdpSubChapterOutcomes;
        $rdpSubChapterOutcomeModel->rdp_sub_chapter_outcome_id = array_values(ArrayHelper::map($projectRdpSubChapterOutcomes, 'rdp_sub_chapter_outcome_id', 'rdp_sub_chapter_outcome_id'));
        
        $expectedOutputModels = $project->projectExpectedOutputs;
        $outcomeModels = $project->projectOutcomes;

        $agencies = Agency::find()->select(['id', 'concat(title," (",code,")") as title']);
        $agencies = Yii::$app->user->can('AgencyUser') ? $agencies->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $agencies;
        $agencies = $agencies->orderBy(['title' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $projects = [];
        $programs = [];

        $sectors = Sector::find()->all();
        $sectorIDs = ArrayHelper::map($sectors, 'id', 'id');
        $sectors = ArrayHelper::map($sectors, 'id', 'title');

        $subSectors = SubSectorPerSector::find()
                    ->select(['sub_sector.id', 'sub_sector.title'])
                    ->leftJoin('sub_sector', 'sub_sector.id = sub_sector_per_sector.sub_sector_id')
                    ->where(['in', 'sector_id', $sectorIDs])
                    ->asArray()
                    ->all();

        $subSectors = ArrayHelper::map($subSectors, 'id', 'title');

        $modes = ModeOfImplementation::find()->all();
        $modes = ArrayHelper::map($modes, 'id', 'title');

        $fundSources = FundSource::find()->select(['id', 'concat(title," (",code,")") as title'])->asArray()->all();
        $fundSources = ArrayHelper::map($fundSources, 'id', 'title');

        $scopes = LocationScope::find()->all();
        $scopes = ArrayHelper::map($scopes, 'id', 'title');

        $regions = Region::find()->orderBy(['region_sort' => SORT_ASC])->all();
        $regions = ArrayHelper::map($regions, 'region_c', 'abbreviation');

        $provinces = Province::find()
                    ->leftJoin('tblregion', 'tblregion.region_c = tblprovince.region_c')
                    ->where(['in', 'tblprovince.region_c', array_values(ArrayHelper::map($projectRegions, 'region_id', 'region_id'))])
                    ->orderBy(['abbreviation' => SORT_ASC, 'province_m' => SORT_ASC])
                    ->all();
        $provinces = ArrayHelper::map($provinces, 'province_c', 'provinceTitle');

        $citymuns = Citymun::find()
                    ->leftJoin('tblprovince', 'tblprovince.province_c = tblcitymun.province_c')
                    ->where(['in', 'tblcitymun.province_c', array_values(ArrayHelper::map($projectProvinces, 'province_id', 'province_id'))])
                    ->orderBy(['province_m' => SORT_ASC, 'citymun_m' => SORT_ASC])
                    ->all();
        
        $citymunIDs = ArrayHelper::map($citymuns, 'citymunId', 'citymunId');
        $citymuns = ArrayHelper::map($citymuns, 'citymunId', 'citymunTitle');
        
        $nonManilaBarangays = Barangay::find()
                    ->select(['concat(tblbarangay.region_c,"-",tblbarangay.province_c,"-",tblbarangay.citymun_c,"-",tblbarangay.barangay_c) as id', 'concat(tblprovince.province_m,": ",tblcitymun.citymun_m,": ",tblbarangay.barangay_m) as title'])
                    ->leftJoin('tblcitymun', 'tblcitymun.province_c = tblbarangay.province_c and tblcitymun.citymun_c = tblbarangay.citymun_c')
                    ->leftJoin('tblprovince', 'tblprovince.province_c = tblbarangay.province_c')
                    ->leftJoin('tblregion', 'tblregion.region_c = tblbarangay.region_c')
                    ->andWhere(['in', 'concat(tblbarangay.region_c,"-",tblbarangay.province_c,"-",tblbarangay.citymun_c)', array_values(ArrayHelper::map($projectCitymuns, 'citymunId', 'citymunId'))])
                    ->orderBy(['tblprovince.province_m' => SORT_ASC, 'tblcitymun.citymun_m' => SORT_ASC, 'tblbarangay.barangay_m' => SORT_ASC])
                    ->asArray()
                    ->all();
            
        $manilaBarangays = in_array('13-39-00', $citymunIDs) ?  Barangay::find()
                    ->select(['concat(tblbarangay.region_c,"-",tblbarangay.province_c,"-",tblbarangay.citymun_c,"-",tblbarangay.barangay_c) as id', 'concat(tblprovince.province_m,": ",tblcitymun.citymun_m,": ",tblbarangay.barangay_m) as title'])
                    ->leftJoin('tblcitymun', 'tblcitymun.province_c = tblbarangay.province_c and tblcitymun.citymun_c = "00"')
                    ->leftJoin('tblprovince', 'tblprovince.province_c = tblbarangay.province_c')
                    ->leftJoin('tblregion', 'tblregion.region_c = tblbarangay.region_c')
                    ->andWhere(['tblbarangay.region_c' => '13','tblbarangay.province_c' => '39'])
                    ->orderBy(['tblprovince.province_m' => SORT_ASC, 'tblcitymun.citymun_m' => SORT_ASC, 'tblbarangay.barangay_m' => SORT_ASC])
                    ->asArray()
                    ->all() : [];

        $barangays = [];

        foreach($nonManilaBarangays as $barangay){
            $barangays[$barangay['id']] = $barangay['title'];
        }
        foreach($manilaBarangays as $barangay){
            $barangays[$barangay['id']] = $barangay['title'];
        }

        $categories = Category::find()->all();
        $categoryIDs = ArrayHelper::map($categories, 'id', 'id');
        $categories = ArrayHelper::map($categories, 'id', 'title');

        $kras = KeyResultArea::find()->where(['in', 'category_id', $categoryIDs])->all();
        $kras = ArrayHelper::map($kras, 'id', 'kraTitle');

        $goals = SdgGoal::find()->select(['id', 'concat("SDG #",sdg_no,": ",title) as title'])->asArray()->all();
        $goals = ArrayHelper::map($goals, 'id', 'title');

        $chapters = RdpChapter::find()->select(['id', 'concat("Chapter ",chapter_no,": ",title) as title'])->asArray()->all();
        $chapterIDs = ArrayHelper::map($chapters, 'id', 'id');
        $chapters = ArrayHelper::map($chapters, 'id', 'title');

        $chapterOutcomes = RdpChapterOutcome::find()
                    ->select(['id', 'concat("Chapter Outcome ",level,": ",title) as title'])
                    ->where(['in', 'rdp_chapter_id', $chapterIDs])
                    ->orderBy(['level' => SORT_ASC, 'title' => SORT_ASC])
                    ->asArray()
                    ->all();
        $chapterOutcomeIDs = ArrayHelper::map($chapterOutcomes, 'id', 'id');
        $chapterOutcomes = ArrayHelper::map($chapterOutcomes, 'id', 'title');

        $subChapterOutcomes = RdpSubChapterOutcome::find()
                    ->select(['rdp_sub_chapter_outcome.id as id', 'concat("Sub-Chapter Outcome ",rdp_chapter_outcome.level,".",rdp_sub_chapter_outcome.level,": ",rdp_sub_chapter_outcome.title) as title'])
                    ->leftJoin('rdp_chapter_outcome', 'rdp_chapter_outcome.id = rdp_sub_chapter_outcome.rdp_chapter_outcome_id')
                    ->where(['in', 'rdp_sub_chapter_outcome.rdp_chapter_outcome_id', $chapterOutcomeIDs])
                    ->orderBy(['rdp_chapter_outcome.level' => SORT_ASC, 'rdp_sub_chapter_outcome.level' => SORT_ASC, 'rdp_sub_chapter_outcome.title' => SORT_ASC])
                    ->asArray()
                    ->all();
        $subChapterOutcomes = ArrayHelper::map($subChapterOutcomes, 'id', 'title');

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];
        $genders = ['M' => 'Male', 'F' => 'Female'];

        $targets = [];

        $projectPhysicalTargetModel = ProjectTarget::findOne(['project_id' => $project->id, 'year' => $project->year, 'target_type' => 'Physical']);
        $physicalTargetModel = new ProjectTarget();
        $physicalTargetModel->scenario = 'physicalTarget';
        $physicalTargetModel->indicator = $projectPhysicalTargetModel ? $projectPhysicalTargetModel->indicator : '';
        $physicalTargetModel->year = $model->year;
        $physicalTargetModel->q1 = $projectPhysicalTargetModel ? $projectPhysicalTargetModel->q1 : 0;
        $physicalTargetModel->q2 = $projectPhysicalTargetModel ? $projectPhysicalTargetModel->q2 : 0;
        $physicalTargetModel->q3 = $projectPhysicalTargetModel ? $projectPhysicalTargetModel->q3 : 0;
        $physicalTargetModel->q4 = $projectPhysicalTargetModel ? $projectPhysicalTargetModel->q4 : 0;
        $physicalTargetModel->target_type = 'Physical';

        $targets[0] = $physicalTargetModel;

        $projectFinancialTargetModel = ProjectTarget::findOne(['project_id' => $project->id, 'year' => $project->year, 'target_type' => 'Financial']);
        $financialTargetModel = new ProjectTarget();
        $financialTargetModel->year = $model->year;
        $financialTargetModel->q1 = $projectFinancialTargetModel ? $projectFinancialTargetModel->q1 : 0;
        $financialTargetModel->q2 = $projectFinancialTargetModel ? $projectFinancialTargetModel->q2 : 0;
        $financialTargetModel->q3 = $projectFinancialTargetModel ? $projectFinancialTargetModel->q3 : 0;
        $financialTargetModel->q4 = $projectFinancialTargetModel ? $projectFinancialTargetModel->q4 : 0;
        $financialTargetModel->target_type = 'Financial';

        $targets[1] = $financialTargetModel;

        $projectMaleEmployedTargetModel = ProjectTarget::findOne(['project_id' => $project->id, 'year' => $project->year, 'target_type' => 'Male Employed']);
        $maleEmployedTargetModel = new ProjectTarget();
        $maleEmployedTargetModel->year = $model->year;
        $maleEmployedTargetModel->q1 = $projectMaleEmployedTargetModel ? $projectMaleEmployedTargetModel->q1 : 0;
        $maleEmployedTargetModel->q2 = $projectMaleEmployedTargetModel ? $projectMaleEmployedTargetModel->q2 : 0;
        $maleEmployedTargetModel->q3 = $projectMaleEmployedTargetModel ? $projectMaleEmployedTargetModel->q3 : 0;
        $maleEmployedTargetModel->q4 = $projectMaleEmployedTargetModel ? $projectMaleEmployedTargetModel->q4 : 0;
        $maleEmployedTargetModel->target_type = 'Male Employed';

        $targets[2] = $maleEmployedTargetModel;

        $projectFemaleEmployedTargetModel = ProjectTarget::findOne(['project_id' => $project->id, 'year' => $project->year, 'target_type' => 'Female Employed']);
        $femaleEmployedTargetModel = new ProjectTarget();
        $femaleEmployedTargetModel->year = $model->year;
        $femaleEmployedTargetModel->q1 = $projectFemaleEmployedTargetModel ? $projectFemaleEmployedTargetModel->q1 : 0;
        $femaleEmployedTargetModel->q2 = $projectFemaleEmployedTargetModel ? $projectFemaleEmployedTargetModel->q2 : 0;
        $femaleEmployedTargetModel->q3 = $projectFemaleEmployedTargetModel ? $projectFemaleEmployedTargetModel->q3 : 0;
        $femaleEmployedTargetModel->q4 = $projectFemaleEmployedTargetModel ? $projectFemaleEmployedTargetModel->q4 : 0;
        $femaleEmployedTargetModel->target_type = 'Female Employed';

        $targets[3] = $femaleEmployedTargetModel;

        $projectBeneficiaryTargetModel = ProjectTarget::findOne(['project_id' => $project->id, 'year' => $project->year, 'target_type' => 'Beneficiaries']);
        $beneficiaryTargetModel = new ProjectTarget();
        $beneficiaryTargetModel->year = $model->year;
        $beneficiaryTargetModel->q1 = $projectBeneficiaryTargetModel ? $projectBeneficiaryTargetModel->q1 : 0;
        $beneficiaryTargetModel->q2 = $projectBeneficiaryTargetModel ? $projectBeneficiaryTargetModel->q2 : 0;
        $beneficiaryTargetModel->q3 = $projectBeneficiaryTargetModel ? $projectBeneficiaryTargetModel->q3 : 0;
        $beneficiaryTargetModel->q4 = $projectBeneficiaryTargetModel ? $projectBeneficiaryTargetModel->q4 : 0;
        $beneficiaryTargetModel->target_type = 'Beneficiaries';

        $targets[4] = $beneficiaryTargetModel;

        if (
            $model->load(Yii::$app->request->post()) &&
            $regionModel->load(Yii::$app->request->post()) &&
            $provinceModel->load(Yii::$app->request->post()) &&
            $citymunModel->load(Yii::$app->request->post()) &&
            $barangayModel->load(Yii::$app->request->post()) &&
            $categoryModel->load(Yii::$app->request->post()) &&
            $kraModel->load(Yii::$app->request->post()) &&
            $sdgModel->load(Yii::$app->request->post()) &&
            $rdpChapterModel->load(Yii::$app->request->post()) &&
            $rdpChapterOutcomeModel->load(Yii::$app->request->post()) &&
            $rdpSubChapterOutcomeModel->load(Yii::$app->request->post()) &&
            MultipleModel::loadMultiple($targets, Yii::$app->request->post()) 
            ) 
            {

            $expectedOutputModels = Model::createMultiple(ProjectExpectedOutput::classname());
            $outcomeModels = Model::createMultiple(ProjectOutcome::classname());
            Model::loadMultiple($expectedOutputModels, Yii::$app->request->post());
            Model::loadMultiple($outcomeModels, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($expectedOutputModels) && Model::validateMultiple($outcomeModels) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                $agency = Agency::findOne(['id' => $model->agency_id]);
                $lastProject = Project::find()->where(['agency_id' => $model->agency_id, 'year' => $model->year])->orderBy(['id' => SORT_DESC])->one();
                $lastNumber = $lastProject ? intval(substr($lastProject->project_no, -4)): '0001';
                $project_no = $agency->code.'-'.substr($model->year, -2).'-'.str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                $model->project_no = $project_no;
                $model->submitted_by = Yii::$app->user->id;
                $model->draft = 'No';
                try {
                    if ($flag = $model->save(false)) {
                        $plan = Plan::findOne(['project_id' => $model->id, 'year' => $model->year]) ? Plan::findOne(['project_id' => $model->id, 'year' => $model->year]) : new Plan();
                        $plan->project_id = $model->id;
                        $plan->year = $model->year;
                        $plan->date_submitted = date("Y-m-d H:i:s");
                        $plan->submitted_by = Yii::$app->user->id;
                        $plan->save(false);
                        
                        foreach ($expectedOutputModels as $expectedOutputModel) {
                            $expectedOutputModel->project_id = $model->id;
                            $expectedOutputModel->year = $model->year;
                            if (! ($flag = $expectedOutputModel->save())) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        foreach ($outcomeModels as $outcomeModel) {
                            $outcomeModel->project_id = $model->id;
                            $outcomeModel->year = $model->year;
                            if (! ($flag = $outcomeModel->save())) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        if(!empty($categoryModel->category_id))
                        {
                            foreach($categoryModel->category_id as $id)
                            {
                                $category = new ProjectCategory();
                                $category->project_id = $model->id;
                                $category->year = $model->year;
                                $category->category_id = $id;
                                if (! ($flag = $category->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($kraModel->key_result_area_id))
                        {
                            foreach($kraModel->key_result_area_id as $id)
                            {
                                $kra = new ProjectKra();
                                $kra->project_id = $model->id;
                                $kra->year = $model->year;
                                $kra->key_result_area_id = $id;
                                if (! ($flag = $kra->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($sdgModel->sdg_goal_id))
                        {
                            foreach($sdgModel->sdg_goal_id as $id)
                            {
                                $sdg = new ProjectSdgGoal();
                                $sdg->project_id = $model->id;
                                $sdg->year = $model->year;
                                $sdg->sdg_goal_id = $id;
                                if (! ($flag = $sdg->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($rdpChapterModel->rdp_chapter_id))
                        {
                            foreach($rdpChapterModel->rdp_chapter_id as $id)
                            {
                                $chapter = new ProjectRdpChapter();
                                $chapter->project_id = $model->id;
                                $chapter->year = $model->year;
                                $chapter->rdp_chapter_id = $id;
                                if (! ($flag = $chapter->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($rdpChapterOutcomeModel->rdp_chapter_outcome_id))
                        {
                            foreach($rdpChapterOutcomeModel->rdp_chapter_outcome_id as $id)
                            {
                                $chapterOutcome = new ProjectRdpChapterOutcome();
                                $chapterOutcome->project_id = $model->id;
                                $chapterOutcome->year = $model->year;
                                $chapterOutcome->rdp_chapter_outcome_id = $id;
                                if (! ($flag = $chapterOutcome->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($rdpSubChapterOutcomeModel->rdp_sub_chapter_outcome_id))
                        {
                            foreach($rdpSubChapterOutcomeModel->rdp_sub_chapter_outcome_id as $id)
                            {
                                $subChapterOutcome = new ProjectRdpSubChapterOutcome();
                                $subChapterOutcome->project_id = $model->id;
                                $subChapterOutcome->year = $model->year;
                                $subChapterOutcome->rdp_sub_chapter_outcome_id = $id;
                                if (! ($flag = $subChapterOutcome->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($regionModel->region_id))
                        {
                            foreach($regionModel->region_id as $id)
                            {
                                $region = new ProjectRegion();
                                $region->project_id = $model->id;
                                $region->year = $model->year;
                                $region->region_id = $id;
                                if (! ($flag = $region->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($provinceModel->province_id))
                        {
                            foreach($provinceModel->province_id as $id)
                            {
                                $province = new ProjectProvince();
                                $province->project_id = $model->id;
                                $province->year = $model->year;
                                $province->province_id = $id;
                                if (! ($flag = $province->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($citymunModel->citymun_id))
                        {
                            foreach($citymunModel->citymun_id as $id)
                            {
                                $ids = explode("-", $id);
                                $citymun = new ProjectCitymun();
                                $citymun->project_id = $model->id;
                                $citymun->year = $model->year;
                                $citymun->region_id = $ids[0];
                                $citymun->province_id = $ids[1];
                                $citymun->citymun_id = $ids[2];
                                if (! ($flag = $citymun->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($barangayModel->barangay_id))
                        {
                            foreach($barangayModel->barangay_id as $id)
                            {
                                $ids = explode("-", $id);
                                $barangay = new ProjectBarangay();
                                $barangay->project_id = $model->id;
                                $barangay->year = $model->year;
                                $barangay->region_id = $ids[0];
                                $barangay->province_id = $ids[1];
                                $barangay->citymun_id = $ids[2];
                                $barangay->barangay_id = $ids[3];
                                if (! ($flag = $barangay->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if(!empty($targets))
                        {
                            foreach($targets as $target)
                            {
                                $target->project_id = $model->id;
                                $target->year = $model->year;
                                if (! ($flag = $target->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        \Yii::$app->getSession()->setFlash('success', 'Project has been carried over');
                        return $this->redirect(['/rpmes/project/create']);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('carry-over', [
            'model' => $model,
            'project' => $project,
            'regionModel' => $regionModel,
            'provinceModel' => $provinceModel,
            'citymunModel' => $citymunModel,
            'barangayModel' => $barangayModel,
            'categoryModel' => $categoryModel,
            'kraModel' => $kraModel,
            'sdgModel' => $sdgModel,
            'rdpChapterModel' => $rdpChapterModel,
            'rdpChapterOutcomeModel' => $rdpChapterOutcomeModel,
            'rdpSubChapterOutcomeModel' => $rdpSubChapterOutcomeModel,
            'targets' => $targets,
            'expectedOutputModels' => (empty($expectedOutputModels)) ? [new ProjectExpectedOutput] : $expectedOutputModels,
            'outcomeModels' => (empty($outcomeModels)) ? [new ProjectOutcome] : $outcomeModels,
            'agencies' => $agencies,
            'projects' => $projects,
            'programs' => $programs,
            'sectors' => $sectors,
            'subSectors' => $subSectors,
            'modes' => $modes,
            'fundSources' => $fundSources,
            'scopes' => $scopes,
            'regions' => $regions,
            'provinces' => $provinces,
            'citymuns' => $citymuns,
            'barangays' => $barangays,
            'categories' => $categories,
            'kras' => $kras,
            'chapters' => $chapters,
            'goals' => $goals,
            'chapterOutcomes' => $chapterOutcomes,
            'subChapterOutcomes' => $subChapterOutcomes,
            'quarters' => $quarters,
            'genders' => $genders,
            'dueDate' => $dueDate,
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
        $model = $this->findModel($id);
        $project = $model;
        $model->delete();
        \Yii::$app->getSession()->setFlash('success', 'Record Deleted');
        return $project->draft == 'No' ? $this->redirect(['/rpmes/plan/']) : $this->redirect(['/rpmes/project/draft']);
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
