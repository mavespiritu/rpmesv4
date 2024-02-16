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
use common\modules\rpmes\models\ComponentSearch;
use common\modules\rpmes\models\DraftProjectSearch;
use common\modules\rpmes\models\Model;
use common\modules\rpmes\models\Submission;
use common\modules\rpmes\models\ProjectHasRevisedSchedules;
use common\modules\rpmes\models\ProjectHasFundSources;
use common\modules\rpmes\models\ProjectHasOutputIndicators;
use common\modules\rpmes\models\MultipleModel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\helpers\Json;
use yii\data\Pagination;
use yii\web\Response;
use yii\widgets\ActiveForm;
/**
 * ComponentController implements the CRUD actions for Project model.
 */
class ComponentController extends Controller
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
                'only' => ['create', 'update'],
                'rules' => [
                    [
                        'actions' => ['create', 'update'],
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
     * Lists all Project models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ComponentSearch();

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

    /**
     * Creates a new Project model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Project();
        $model->agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : null;
        $model->scenario = Yii::$app->user->can('AgencyUser') ? 'componentProjectCreateUser' : 'componentProjectCreateAdmin';
        $model->data_type = 'Default';

        $regionModel = new ProjectRegion();
        $provinceModel = new ProjectProvince();
        $citymunModel = new ProjectCitymun();
        $barangayModel = new ProjectBarangay();
        $revisedScheduleModels = [new ProjectHasRevisedSchedules()];
        $fundSourceModels = [new ProjectHasFundSources()];

        $agencies = Agency::find()->select(['id', 'concat(title," (",code,")") as title']);
        $agencies = Yii::$app->user->can('AgencyUser') ? $agencies->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $agencies;
        $agencies = $agencies->orderBy(['title' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $projects = Project::find()
            ->select([
                'id',
                'concat(project_no,": ",title) as title'
            ])
            ->where([
                'source_id' => null,
                'has_component' => 1
            ]);

        $projects = Yii::$app->user->can('AgencyUser') ? $projects->andWhere(['agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $projects;

        $projects = $projects
                    ->asArray()
                    ->orderBy(['id' => SORT_DESC])
                    ->all();

        $projects = ArrayHelper::map($projects, 'id', 'title');

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

        if (
            $model->load(Yii::$app->request->post()) &&
            $regionModel->load(Yii::$app->request->post()) &&
            $provinceModel->load(Yii::$app->request->post()) &&
            $citymunModel->load(Yii::$app->request->post()) &&
            $barangayModel->load(Yii::$app->request->post())) 
            {

            $revisedScheduleModels = Model::createMultiple(ProjectHasRevisedSchedules::classname());
            $fundSourceModels = Model::createMultiple(ProjectHasFundSources::classname());
            Model::loadMultiple($revisedScheduleModels, Yii::$app->request->post());
            Model::loadMultiple($fundSourceModels, Yii::$app->request->post());

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($revisedScheduleModels) && 
                     Model::validateMultiple($fundSourceModels) && 
                     //Model::validateMultiple($targets) && 
                     $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                $agency = Agency::findOne(['id' => $model->agency_id]);
                $motherProject = Project::findOne($model->source_id);
                $lastProject = Project::find()
                    ->where([
                        'source_id' => $model->source_id,
                    ])
                    ->orderBy(['id' => SORT_DESC])
                    ->one();

                $lastNumber = $lastProject ? intval(substr($lastProject->project_no, -3)): '001';
                $project_no = $lastProject ? $motherProject->project_no.'-'.str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT) : $motherProject->project_no.'-'.str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
                $model->project_no = $project_no;
                $model->cost = $this->removeMask($model->cost);
                $model->submitted_by = Yii::$app->user->id;
                $model->draft = 'No';
                try {
                    if ($flag = $model->save(false)) {

                        foreach ($revisedScheduleModels as $revisedScheduleModel) {
                            $revisedScheduleModel->project_id = $model->id;
                            if (! ($flag = $revisedScheduleModel->save())) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        foreach ($fundSourceModels as $fundSourceModel) {
                            $fundSourceModel->project_id = $model->id;
                            if (! ($flag = $fundSourceModel->save())) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        if(!empty($regionModel->region_id))
                        {
                            foreach($regionModel->region_id as $id)
                            {
                                $region = new ProjectRegion();
                                $region->project_id = $model->id;
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
                    }

                    if ($flag) {
                        $transaction->commit();
                        \Yii::$app->getSession()->setFlash('success', 'Record Saved');
                        return $this->redirect(['/rpmes/component/']);
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
            'revisedScheduleModels' => (empty($revisedScheduleModels)) ? [new ProjectHasRevisedSchedules] : $revisedScheduleModels,
            'fundSourceModels' => (empty($fundSourceModels)) ? [new ProjectHasFundSources] : $fundSourceModels,
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
        $model->data_type = 'Default';

        $model->scenario = Yii::$app->user->can('AgencyUser') ? 'componentProjectCreateUser' : 'componentProjectCreateAdmin';

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
        
        $revisedScheduleModels = $model->projectHasRevisedSchedules;
        $fundSourceModels = $model->projectHasFundSources;

        $agencies = Agency::find()->select(['id', 'concat(title," (",code,")") as title']);
        $agencies = Yii::$app->user->can('AgencyUser') ? $agencies->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $agencies;
        $agencies = $agencies->orderBy(['title' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $projects = Project::find()
            ->select([
                'id',
                'concat(project_no,": ",title) as title'
            ])
            ->where([
                'source_id' => null,
                'has_component' => 1
            ]);

        $projects = Yii::$app->user->can('AgencyUser') ? $projects->andWhere(['agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $projects;

        $projects = $projects
                    ->asArray()
                    ->orderBy(['id' => SORT_DESC])
                    ->all();

        $projects = ArrayHelper::map($projects, 'id', 'title');
        $programs = [];

        $sectors = Sector::find()->all();
        $sectorIDs = ArrayHelper::map($sectors, 'id', 'id');
        $sectors = ArrayHelper::map($sectors, 'id', 'title');

        $subSectors = SubSectorPerSector::find()
                    ->select(['sub_sector.id', 'sub_sector.title'])
                    ->leftJoin('sub_sector', 'sub_sector.id = sub_sector_per_sector.sub_sector_id')
                    ->where(['sector_id' => $model->sector_id])
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

        if (
            $model->load(Yii::$app->request->post()) &&
            $regionModel->load(Yii::$app->request->post()) &&
            $provinceModel->load(Yii::$app->request->post()) &&
            $citymunModel->load(Yii::$app->request->post()) &&
            $barangayModel->load(Yii::$app->request->post())) 
            {

            $oldRevisedScheduleIDs = ArrayHelper::map($revisedScheduleModels, 'id', 'id');
            $oldFundSourceIDs = ArrayHelper::map($fundSourceModels, 'id', 'id');
            $oldRegionIDs = array_values(ArrayHelper::map($projectRegions, 'region_id', 'region_id'));
            $oldProvinceIDs = array_values(ArrayHelper::map($projectProvinces, 'province_id', 'province_id'));
            $oldCitymunIDs = array_values(ArrayHelper::map($projectCitymuns, 'citymunId', 'citymunId'));
            $oldBarangayIDs = array_values(ArrayHelper::map($projectBarangays, 'barangayId', 'barangayId'));
            
            $revisedScheduleModels = Model::createMultiple(ProjectHasRevisedSchedules::classname(), $revisedScheduleModels);
            $fundSourceModels = Model::createMultiple(ProjectHasFundSources::classname(), $fundSourceModels);

            Model::loadMultiple($revisedScheduleModels, Yii::$app->request->post());
            Model::loadMultiple($fundSourceModels, Yii::$app->request->post());

            $deletedRevisedScheduleIDs = array_diff($oldRevisedScheduleIDs, array_filter(ArrayHelper::map($revisedScheduleModels, 'id', 'id')));
            $deletedFundSourceIDs = array_diff($oldFundSourceIDs, array_filter(ArrayHelper::map($fundSourceModels, 'id', 'id')));
            $deletedRegionIDs = $regionModel->region_id != '' ? array_diff($oldRegionIDs, array_filter($regionModel->region_id)) : array_diff($oldRegionIDs, []);
            $deletedProvinceIDs = $provinceModel->province_id != '' ? array_diff($oldProvinceIDs, array_filter($provinceModel->province_id)) : array_diff($oldProvinceIDs, []);
            $deletedCitymunIDs = $citymunModel->citymun_id != '' ? array_diff($oldCitymunIDs, array_filter($citymunModel->citymun_id)) : array_diff($oldCitymunIDs, []);
            $deletedBarangayIDs = $barangayModel->barangay_id != '' ? array_diff($oldBarangayIDs, array_filter($barangayModel->barangay_id)) : array_diff($oldBarangayIDs, []);

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($revisedScheduleModels) && 
                     Model::validateMultiple($fundSourceModels) && 
                     $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();

                $model->cost = $this->removeMask($model->cost);

                try {
                    if ($flag = $model->save(false)) {

                        if(!empty($deletedRevisedScheduleIDs))
                        {
                            ProjectHasRevisedSchedules::deleteAll(['id' => $deletedRevisedScheduleIDs]);
                        }

                        foreach ($revisedScheduleModels as $revisedScheduleModel) {
                            $revisedScheduleModel->project_id = $model->id;
                            if (! ($flag = $revisedScheduleModel->save())) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        if(!empty($deletedFundSourceIDs))
                        {
                            ProjectHasFundSources::deleteAll(['id' => $deletedFundSourceIDs]);
                        }

                        foreach ($fundSourceModels as $fundSourceModel) {
                            $fundSourceModel->project_id = $model->id;
                            if (! ($flag = $fundSourceModel->save())) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        if(!empty($deletedRegionIDs))
                        {
                            ProjectRegion::deleteAll(['project_id' => $model->id, 'region_id' => $deletedRegionIDs]);
                        }

                        if(!empty($regionModel->region_id))
                        {
                            foreach($regionModel->region_id as $id)
                            {
                                $region = ProjectRegion::findOne(['project_id' => $model->id, 'region_id' => $id]) ?
                                ProjectRegion::findOne(['project_id' => $model->id, 'region_id' => $id]) : new ProjectRegion();
                                $region->project_id = $model->id;
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
                                $province = ProjectProvince::findOne(['project_id' => $model->id, 'province_id' => $id]) ?
                                ProjectProvince::findOne(['project_id' => $model->id, 'province_id' => $id]) :  new ProjectProvince();
                                $province->project_id = $model->id;
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
                                $citymun = ProjectCitymun::findOne(['project_id' => $model->id, 'region_id' => $ids[0], 'province_id' => $ids[1], 'citymun_id' => $ids[2]]);
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
                                $citymun = ProjectCitymun::findOne(['project_id' => $model->id, 'region_id' => $ids[0], 'province_id' => $ids[1], 'citymun_id' => $ids[2]]) ?
                                ProjectCitymun::findOne(['project_id' => $model->id, 'region_id' => $ids[0], 'province_id' => $ids[1], 'citymun_id' => $ids[2]]) : new ProjectCitymun();
                                $citymun->project_id = $model->id;
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
                                $barangay = ProjectBarangay::findOne(['project_id' => $model->id, 'region_id' => $ids[0], 'province_id' => $ids[1], 'citymun_id' => $ids[2], 'barangay_id' => $ids[3]]);
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
                                $barangay = ProjectBarangay::findOne(['project_id' => $model->id, 'region_id' => $ids[0], 'province_id' => $ids[1], 'citymun_id' => $ids[2], 'barangay_id' => $ids[3]]) ?
                                ProjectBarangay::findOne(['project_id' => $model->id, 'region_id' => $ids[0], 'province_id' => $ids[1], 'citymun_id' => $ids[2], 'barangay_id' => $ids[3]]) : new ProjectBarangay();
                                $barangay->project_id = $model->id;
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
                    }

                    if ($flag) {
                        $transaction->commit();
                        \Yii::$app->getSession()->setFlash('success', 'Record Updated');
                        return $this->redirect(['/rpmes/component/']);
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
            'revisedScheduleModels' => (empty($revisedScheduleModels)) ? [new ProjectHasRevisedSchedules] : $revisedScheduleModels,
            'fundSourceModels' => (empty($fundSourceModels)) ? [new ProjectHasFundSources] : $fundSourceModels,
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
        $model->delete();
        \Yii::$app->getSession()->setFlash('success', 'Record Deleted');
        //return $project->draft == 'No' ? $this->redirect(['/rpmes/project/']) : $this->redirect(['/rpmes/project/draft']);
        return $this->redirect(['/rpmes/component/']);
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
