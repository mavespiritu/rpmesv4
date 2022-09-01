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
use common\modules\rpmes\models\PhysicalAccomplishment;
use common\modules\rpmes\models\FinancialAccomplishment;
use common\modules\rpmes\models\PersonEmployedAccomplishment;
use common\modules\rpmes\models\BeneficiariesAccomplishment;
use common\modules\rpmes\models\GroupAccomplishment;
use common\modules\rpmes\models\Accomplishment;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\helpers\Json;
use yii\data\Pagination;

class AccomplishmentController extends \yii\web\Controller
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

    function removeMask($figure)
    {
        $figure = explode(",",$figure);
        $number = implode("", $figure);

        return $number;
    }
    
    public function actionIndex()
    {
        $physical = [];
        $financial = [];
        $personEmployed = [];
        $beneficiaries = [];
        $groups = [];
        $accomplishment = [];

        $getData = [];
        $projects = [];

        $projectsModels = null;
        $projectsPages = null;
        $submissionModel = null;
        $dueDate = null;
        $agency_id = null;
        
        $model = new Project();
        $model->scenario = Yii::$app->user->can('AgencyUser') ? 'accomplishmentUser' : 'accomplishmentAdmin';
        $model->year = date("Y");
        $model->agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : null;

        $agencies = Agency::find()->select(['id', 'code as title']);
        $agencies = Yii::$app->user->can('AgencyUser') ? $agencies->andWhere(['id' => Yii::$app->user->identity->userinfo->AGENCY_C]) : $agencies;
        $agencies = $agencies->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $years = Project::find()->select(['distinct(year) as year'])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];
        $genders = ['M' => 'Male', 'F' => 'Female'];

        if($model->load(Yii::$app->request->get()))
        {   
            $agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : $model->agency_id;

            $submissionModel = Submission::findOne(['agency_id' => $model->agency_id, 'report' => 'Accomplishment', 'year' => $model->year, 'quarter' => $model->quarter]) ?
                               Submission::findOne(['agency_id' => $model->agency_id, 'report' => 'Accomplishment', 'year' => $model->year, 'quarter' => $model->quarter]) : new Submission();
            $dueDate = DueDate::findOne(['report' => 'Accomplishment', 'quarter' => $model->quarter, 'year' => $model->year]);
            if(Yii::$app->user->can('AgencyUser')){
                if(Yii::$app->user->identity->userinfo->AGENCY_C != $model->agency_id)
                {
                    throw new ForbiddenHttpException('Not allowed to access');
                }
            }

            $getData = Yii::$app->request->get('Project');

            $projectIDs = Yii::$app->user->can('AgencyUser') ? 
                        Submission::findOne(['year' => $model->year, 'agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C, 'report' => 'Monitoring Plan', 'draft' => 'No']) ?
                        Plan::find()
                        ->select(['project.id as id'])
                        ->leftJoin('project', 'project.id = plan.project_id')
                        ->where(['project.draft' => 'No', 'project.agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C, 'plan.year' => $model->year])
                        ->all() :
                        [] :
                        Plan::find()
                        ->select(['project.id as id'])
                        ->leftJoin('project', 'project.id = plan.project_id')
                        ->where(['project.draft' => 'No', 'project.agency_id' => $model->agency_id, 'plan.year' => $model->year])
                        ->all();

            $projectIDs = !empty($projectIDs) ? ArrayHelper::map($projectIDs, 'id', 'id') : [];

            $projectsPaging = Project::find();
            $projectsPaging->andWhere(['id' => $projectIDs]);
            $countProjects = clone $projectsPaging;
            $projectsPages = new Pagination(['totalCount' => $countProjects->count()]);
            $projectsModels = $projectsPaging->offset($projectsPages->offset)
                ->limit($projectsPages->limit)
                ->orderBy(['id' => SORT_ASC])
                ->all();

            $projects =  Yii::$app->user->can('AgencyUser') ? 
                        Submission::findOne(['year' => $model->year, 'agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C, 'report' => 'Monitoring Plan', 'draft' => 'No']) ?
                        Plan::find()
                        ->leftJoin('project', 'project.id = plan.project_id')
                        ->where(['project.draft' => 'No', 'project.agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C, 'plan.year' => $model->year])
                        ->all() :
                        [] :
                        Plan::find()
                        ->leftJoin('project', 'project.id = plan.project_id')
                        ->where(['project.draft' => 'No', 'project.agency_id' => $model->agency_id, 'plan.year' => $model->year])
                        ->all();
            
            if(!empty($projects))
            {
                foreach($projects as $project)
                {
                    $physicalAccomp = PhysicalAccomplishment::findOne(['project_id' => $project->project_id, 'year' => $project->year, 'quarter' => $model->quarter]) ?
                    PhysicalAccomplishment::findOne(['project_id' => $project->project_id, 'year' => $project->year, 'quarter' => $model->quarter]) : new PhysicalAccomplishment();

                    $physicalAccomp->project_id = $project->project_id;
                    $physicalAccomp->year = $project->year;
                    $physicalAccomp->quarter = $model->quarter;

                    $physical[$project->project_id] = $physicalAccomp;

                    $financialAccomp = FinancialAccomplishment::findOne(['project_id' => $project->project_id, 'year' => $project->year, 'quarter' => $model->quarter]) ?
                    FinancialAccomplishment::findOne(['project_id' => $project->project_id, 'year' => $project->year, 'quarter' => $model->quarter]) : new FinancialAccomplishment();

                    $financialAccomp->project_id = $project->project_id;
                    $financialAccomp->year = $project->year;
                    $financialAccomp->quarter = $model->quarter;

                    $financial[$project->project_id] = $financialAccomp;

                    $personEmployedAccomp = PersonEmployedAccomplishment::findOne(['project_id' => $project->project_id, 'year' => $project->year, 'quarter' => $model->quarter]) ?
                    PersonEmployedAccomplishment::findOne(['project_id' => $project->project_id, 'year' => $project->year, 'quarter' => $model->quarter]) : new PersonEmployedAccomplishment();

                    $personEmployedAccomp->project_id = $project->project_id;
                    $personEmployedAccomp->year = $project->year;
                    $personEmployedAccomp->quarter = $model->quarter;

                    $personEmployed[$project->project_id] = $personEmployedAccomp;

                    $beneficiariesAccomp = BeneficiariesAccomplishment::findOne(['project_id' => $project->project_id, 'year' => $project->year, 'quarter' => $model->quarter]) ?
                    BeneficiariesAccomplishment::findOne(['project_id' => $project->project_id, 'year' => $project->year, 'quarter' => $model->quarter]) : new BeneficiariesAccomplishment();

                    $beneficiariesAccomp->project_id = $project->project_id;
                    $beneficiariesAccomp->year = $project->year;
                    $beneficiariesAccomp->quarter = $model->quarter;

                    $beneficiaries[$project->project_id] = $beneficiariesAccomp;

                    $groupsAccomp = GroupAccomplishment::findOne(['project_id' => $project->project_id, 'year' => $project->year, 'quarter' => $model->quarter]) ?
                    GroupAccomplishment::findOne(['project_id' => $project->project_id, 'year' => $project->year, 'quarter' => $model->quarter]) : new GroupAccomplishment();

                    $groupsAccomp->project_id = $project->project_id;
                    $groupsAccomp->year = $project->year;
                    $groupsAccomp->quarter = $model->quarter;

                    $groups[$project->project_id] = $groupsAccomp;

                    $accomplishmentAccomp = Accomplishment::findOne(['project_id' => $project->project_id, 'year' => $project->year, 'quarter' => $model->quarter]) ?
                    Accomplishment::findOne(['project_id' => $project->project_id, 'year' => $project->year, 'quarter' => $model->quarter]) : new Accomplishment();

                    $accomplishmentAccomp->project_id = $project->project_id;
                    $accomplishmentAccomp->year = $project->year;
                    $accomplishmentAccomp->quarter = $model->quarter;
                    $accomplishmentAccomp->action = $project->project->isCompleted == true ? 1 : 0;

                    $accomplishment[$project->project_id] = $accomplishmentAccomp;
                }
            }
        }

        if(
            MultipleModel::loadMultiple($physical, Yii::$app->request->post()) &&
            MultipleModel::loadMultiple($financial, Yii::$app->request->post()) &&
            MultipleModel::loadMultiple($personEmployed, Yii::$app->request->post()) &&
            MultipleModel::loadMultiple($beneficiaries, Yii::$app->request->post()) &&
            MultipleModel::loadMultiple($groups, Yii::$app->request->post()) &&
            MultipleModel::loadMultiple($accomplishment, Yii::$app->request->post())
        )
        {
        
            $transaction = \Yii::$app->db->beginTransaction();
            $getData = Yii::$app->request->get('Project');

            try{
                if(!empty($physical))
                {
                    foreach($physical as $physicalAccomp)
                    {
                        $physicalAccomp->value = $this->removeMask($physicalAccomp->value);
                        if(!($flag = $physicalAccomp->save(false))){
                            $transaction->rollBack();
                            break;
                        }
                    }
                }

                if(!empty($financial))
                {
                    foreach($financial as $financialAccomp)
                    {
                        $financialAccomp->releases = $this->removeMask($financialAccomp->releases);
                        $financialAccomp->obligation = $this->removeMask($financialAccomp->obligation);
                        $financialAccomp->expenditures = $this->removeMask($financialAccomp->expenditures);
                        if(!($flag = $financialAccomp->save(false))){
                            $transaction->rollBack();
                            break;
                        }
                    }
                }

                if(!empty($personEmployed))
                {
                    foreach($personEmployed as $personEmployedAccomp)
                    {
                        $personEmployedAccomp->male = $this->removeMask($personEmployedAccomp->male);
                        $personEmployedAccomp->female = $this->removeMask($personEmployedAccomp->female);
                        if(!($flag = $personEmployedAccomp->save(false))){
                            $transaction->rollBack();
                            break;
                        }
                    }
                }

                if(!empty($beneficiaries))
                {
                    foreach($beneficiaries as $beneficiariesAccomp)
                    {
                        $beneficiariesAccomp->male = $this->removeMask($beneficiariesAccomp->male);
                        $beneficiariesAccomp->female = $this->removeMask($beneficiariesAccomp->female);
                        if(!($flag = $beneficiariesAccomp->save(false))){
                            $transaction->rollBack();
                            break;
                        }
                    }
                }

                if(!empty($groups))
                {
                    foreach($groups as $groupsAccomp)
                    {
                        $groupsAccomp->value = $this->removeMask($groupsAccomp->value);
                        if(!($flag = $groupsAccomp->save(false))){
                            $transaction->rollBack();
                            break;
                        }
                    }
                }

                if(!empty($accomplishment))
                {
                    foreach($accomplishment as $accomplishmentAccomp)
                    {
                        $accomplishmentAccomp->submitted_by = Yii::$app->user->id;
                        $accomplishmentAccomp->date_submitted = date("Y-m-d H:i:s");
                        if(!($flag = $accomplishmentAccomp->save(false))){
                            $transaction->rollBack();
                            break;
                        }
                    }
                }

                if($flag)
                {
                    $transaction->commit();

                        \Yii::$app->getSession()->setFlash('success', 'Accomplishment Saved');
                        return Yii::$app->user->can('AgencyUser') ? isset($getData['page']) ? 
                            $this->redirect(['/rpmes/accomplishment/', 
                            'Project[year]' => $getData['year'], 
                            'Project[quarter]' => $getData['quarter'], 
                            'page' => $getData['page'],
                        ]) : $this->redirect(['/rpmes/accomplishment/', 
                            'Project[year]' => $getData['year'], 
                            'Project[quarter]' => $getData['quarter'], 
                        ]) : $this->redirect(['/rpmes/accomplishment/', 
                            'Project[year]' => $getData['year'], 
                            'Project[agency_id]' => $getData['agency_id'], 
                            'Project[quarter]' => $getData['quarter'], 
                        ]);
                }
            }catch(\Exception $e){
                $transaction->rollBack();
            }
        }
        return $this->render('index',[
            'model' => $model,
            'years' => $years,
            'agencies' => $agencies,
            'quarters' => $quarters,
            'genders' => $genders,
            'physical' => $physical,
            'financial' => $financial,
            'personEmployed' => $personEmployed,
            'accomplishment' => $accomplishment,
            'beneficiaries' => $beneficiaries,
            'groups' => $groups,
            'projectsModels' => $projectsModels,
            'projectsPages' => $projectsPages,
            'getData' => $getData,
            'dueDate' => $dueDate,
            'submissionModel' => $submissionModel,
            'agency_id' => $agency_id,
            'projects' => $projects
        ]);
    }

    public function actionSubmit()
    {
        if(Yii::$app->request->post())
        {
            $postData = Yii::$app->request->post();

            $submissionModel = new Submission();
            $submissionModel->agency_id = $postData['agency_id'];
            $submissionModel->report = 'Accomplishment';
            $submissionModel->year = $postData['year'];
            $submissionModel->quarter = $postData['quarter'];
            $submissionModel->submitted_by = Yii::$app->user->id;
            $submissionModel->draft = 'No';

            if($submissionModel->save())
            {
                \Yii::$app->getSession()->setFlash('success', 'Accomplishment '.$postData['quarter'].' '.$postData['year'].' has been submitted.');
                return $this->redirect(['/rpmes/accomplishment']);
            }
        }
    }

    public function actionDownloadAccomplishment($type, $model, $year, $quarter, $agency_id)
    {
        $model = $type == 'print' ? json_decode(str_replace('\'', '"', $model), true) : json_decode($model, true);
        $model = (object) $model;
        $model->year = $year;
        $model->quarter = $quarter;
        $model->agency_id = $agency_id;

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];
        $genders = ['M' => 'Male', 'F' => 'Female'];

        $projectIDs = Plan::find()->select(['project_id'])->where(['year' => $model->year])->asArray()->all();
        $projectIDs = ArrayHelper::map($projectIDs, 'project_id', 'project_id');

        $financialTargets = ProjectTarget::find()->where(['target_type' => 'Financial', 'year' => $model->year])->createCommand()->getRawSql();
        $financialAccomps = FinancialAccomplishment::find()->where(['year' => $model->year])->createCommand()->getRawSql();
        $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $model->year])->createCommand()->getRawSql();
        $physicalAccomps = PhysicalAccomplishment::find()->where(['year' => $model->year])->createCommand()->getRawSql();

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

        $financialTargetTotalPerQuarter = 'IF(project.data_type <> "Cumulative",
                                            IF("'.$model->quarter.'" = "Q1", COALESCE(financialTargets.q1, 0),
                                                IF("'.$model->quarter.'" = "Q2", COALESCE(financialTargets.q1, 0) + COALESCE(financialTargets.q2, 0),
                                                    IF("'.$model->quarter.'" = "Q3", COALESCE(financialTargets.q1, 0) + COALESCE(financialTargets.q2, 0) + COALESCE(financialTargets.q3, 0),
                                                    COALESCE(financialTargets.q1, 0) + COALESCE(financialTargets.q2, 0) + COALESCE(financialTargets.q3, 0) + COALESCE(financialTargets.q4, 0)
                                                    )
                                                )
                                            )
                                        ,   
                                            IF(COALESCE(financialTargets.q4, 0) <= 0,
                                                IF(COALESCE(financialTargets.q3, 0) <= 0,
                                                    IF(COALESCE(financialTargets.q2, 0) <= 0,
                                                        COALESCE(financialTargets.q1, 0)
                                                    , COALESCE(financialTargets.q2, 0))
                                                , COALESCE(financialTargets.q3, 0))
                                            , COALESCE(financialTargets.q4, 0))
                                        )';

        $financialTargetPerQuarter = 'IF("'.$model->quarter.'" = "Q1", COALESCE(financialTargets.q1, 0),
                                        IF("'.$model->quarter.'" = "Q2", COALESCE(financialTargets.q2, 0),
                                            IF("'.$model->quarter.'" = "Q3", COALESCE(financialTargets.q3, 0),
                                            COALESCE(financialTargets.q4, 0)
                                            )
                                        )
                                    )';
                                    
        $releasesTotalPerQuarter = 'IF(project.data_type <> "Cumulative",
                                    IF("'.$model->quarter.'" = "Q1", COALESCE(financialAccompsQ1.releases, 0),
                                        IF("'.$model->quarter.'" = "Q2", COALESCE(financialAccompsQ1.releases, 0) + COALESCE(financialAccompsQ2.releases, 0),
                                            IF("'.$model->quarter.'" = "Q3", COALESCE(financialAccompsQ1.releases, 0) + COALESCE(financialAccompsQ2.releases, 0) + COALESCE(financialAccompsQ3.releases, 0),
                                            COALESCE(financialAccompsQ1.releases, 0) + COALESCE(financialAccompsQ2.releases, 0) + COALESCE(financialAccompsQ3.releases, 0) + COALESCE(financialAccompsQ4.releases, 0)
                                            )
                                        )
                                    )
                                ,   
                                    IF("'.$model->quarter.'" = "Q1", COALESCE(financialAccompsQ1.releases, 0),
                                        IF("'.$model->quarter.'" = "Q2", IF(COALESCE(financialAccompsQ2.releases, 0) = 0, COALESCE(financialAccompsQ1.releases, 0), COALESCE(financialAccompsQ2.releases, 0)),
                                            IF("'.$model->quarter.'" = "Q3", IF(COALESCE(financialAccompsQ3.releases, 0) = 0, IF(COALESCE(financialAccompsQ2.releases, 0) = 0, COALESCE(financialAccompsQ1.releases, 0), COALESCE(financialAccompsQ2.releases, 0)), COALESCE(financialAccompsQ3.releases, 0)),
                                            IF(COALESCE(financialAccompsQ4.releases, 0) = 0, IF(COALESCE(financialAccompsQ3.releases, 0) = 0, IF(COALESCE(financialAccompsQ2.releases, 0) = 0, COALESCE(financialAccompsQ1.releases, 0), COALESCE(financialAccompsQ2.releases, 0)), COALESCE(financialAccompsQ3.releases, 0)), COALESCE(financialAccompsQ4.releases, 0))
                                            )
                                        )
                                    )
                                )';

        $releasesPerQuarter = 'IF("'.$model->quarter.'" = "Q1", COALESCE(financialAccompsQ1.releases, 0),
                                IF("'.$model->quarter.'" = "Q2", COALESCE(financialAccompsQ2.releases, 0),
                                    IF("'.$model->quarter.'" = "Q3", COALESCE(financialAccompsQ3.releases, 0),
                                    COALESCE(financialAccompsQ4.releases, 0)
                                    )
                                )
                            )';

        $obligationsTotalPerQuarter = 'IF(project.data_type <> "Cumulative",
                                        IF("'.$model->quarter.'" = "Q1", COALESCE(financialAccompsQ1.obligation, 0),
                                            IF("'.$model->quarter.'" = "Q2", COALESCE(financialAccompsQ1.obligation, 0) + COALESCE(financialAccompsQ2.obligation, 0),
                                                IF("'.$model->quarter.'" = "Q3", COALESCE(financialAccompsQ1.obligation, 0) + COALESCE(financialAccompsQ2.obligation, 0) + COALESCE(financialAccompsQ3.obligation, 0),
                                                COALESCE(financialAccompsQ1.obligation, 0) + COALESCE(financialAccompsQ2.obligation, 0) + COALESCE(financialAccompsQ3.obligation, 0) + COALESCE(financialAccompsQ4.obligation, 0)
                                                )
                                            )
                                        )
                                    ,   
                                        IF("'.$model->quarter.'" = "Q1", COALESCE(financialAccompsQ1.obligation, 0),
                                            IF("'.$model->quarter.'" = "Q2", IF(COALESCE(financialAccompsQ2.obligation, 0) = 0, COALESCE(financialAccompsQ1.obligation, 0), COALESCE(financialAccompsQ2.obligation, 0)),
                                                IF("'.$model->quarter.'" = "Q3", IF(COALESCE(financialAccompsQ3.obligation, 0) = 0, IF(COALESCE(financialAccompsQ2.obligation, 0) = 0, COALESCE(financialAccompsQ1.obligation, 0), COALESCE(financialAccompsQ2.obligation, 0)), COALESCE(financialAccompsQ3.obligation, 0)),
                                                IF(COALESCE(financialAccompsQ4.obligation, 0) = 0, IF(COALESCE(financialAccompsQ3.obligation, 0) = 0, IF(COALESCE(financialAccompsQ2.obligation, 0) = 0, COALESCE(financialAccompsQ1.obligation, 0), COALESCE(financialAccompsQ2.obligation, 0)), COALESCE(financialAccompsQ3.obligation, 0)), COALESCE(financialAccompsQ4.obligation, 0))
                                                )
                                            )
                                        )
                                    )';

        $obligationsPerQuarter = 'IF("'.$model->quarter.'" = "Q1", COALESCE(financialAccompsQ1.obligation, 0),
                                    IF("'.$model->quarter.'" = "Q2", COALESCE(financialAccompsQ2.obligation, 0),
                                        IF("'.$model->quarter.'" = "Q3", COALESCE(financialAccompsQ3.obligation, 0),
                                        COALESCE(financialAccompsQ4.obligation, 0)
                                        )
                                    )
                                )';

        $expendituresTotalPerQuarter = 'IF(project.data_type <> "Cumulative",
                                        IF("'.$model->quarter.'" = "Q1", COALESCE(financialAccompsQ1.expenditures, 0),
                                            IF("'.$model->quarter.'" = "Q2", COALESCE(financialAccompsQ1.expenditures, 0) + COALESCE(financialAccompsQ2.expenditures, 0),
                                                IF("'.$model->quarter.'" = "Q3", COALESCE(financialAccompsQ1.expenditures, 0) + COALESCE(financialAccompsQ2.expenditures, 0) + COALESCE(financialAccompsQ3.expenditures, 0),
                                                COALESCE(financialAccompsQ1.expenditures, 0) + COALESCE(financialAccompsQ2.expenditures, 0) + COALESCE(financialAccompsQ3.expenditures, 0) + COALESCE(financialAccompsQ4.expenditures, 0)
                                                )
                                            )
                                        )
                                    ,   
                                        IF("'.$model->quarter.'" = "Q1", COALESCE(financialAccompsQ1.expenditures, 0),
                                            IF("'.$model->quarter.'" = "Q2", IF(COALESCE(financialAccompsQ2.expenditures, 0) = 0, COALESCE(financialAccompsQ1.expenditures, 0), COALESCE(financialAccompsQ2.expenditures, 0)),
                                                IF("'.$model->quarter.'" = "Q3", IF(COALESCE(financialAccompsQ3.expenditures, 0) = 0, IF(COALESCE(financialAccompsQ2.expenditures, 0) = 0, COALESCE(financialAccompsQ1.expenditures, 0), COALESCE(financialAccompsQ2.expenditures, 0)), COALESCE(financialAccompsQ3.expenditures, 0)),
                                                IF(COALESCE(financialAccompsQ4.expenditures, 0) = 0, IF(COALESCE(financialAccompsQ3.expenditures, 0) = 0, IF(COALESCE(financialAccompsQ2.expenditures, 0) = 0, COALESCE(financialAccompsQ1.expenditures, 0), COALESCE(financialAccompsQ2.expenditures, 0)), COALESCE(financialAccompsQ3.expenditures, 0)), COALESCE(financialAccompsQ4.expenditures, 0))
                                                )
                                            )
                                        )
                                    )';

        $expendituresPerQuarter = 'IF("'.$model->quarter.'" = "Q1", COALESCE(financialAccompsQ1.expenditures, 0),
                                    IF("'.$model->quarter.'" = "Q2", COALESCE(financialAccompsQ2.expenditures, 0),
                                        IF("'.$model->quarter.'" = "Q3", COALESCE(financialAccompsQ3.expenditures, 0),
                                        COALESCE(financialAccompsQ4.expenditures, 0)
                                        )
                                    )
                                )';

        $physicalTargetPerQuarter = 'IF("'.$model->quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                                        IF("'.$model->quarter.'" = "Q2", COALESCE(physicalTargets.q2, 0),
                                            IF("'.$model->quarter.'" = "Q3", COALESCE(physicalTargets.q3, 0),
                                            COALESCE(physicalTargets.q4, 0)
                                            )
                                        )
                                    )';

        $physicalTargetTotalPerQuarter = 'IF(project.data_type <> "Cumulative",
                                    IF("'.$model->quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                                        IF("'.$model->quarter.'" = "Q2", COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0),
                                            IF("'.$model->quarter.'" = "Q3", COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0),
                                            COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0)
                                            )
                                        )
                                    )
                                ,   
                                    IF("'.$model->quarter.'" = "Q1", COALESCE(physicalTargets.q1, 0),
                                        IF("'.$model->quarter.'" = "Q2", IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)),
                                            IF("'.$model->quarter.'" = "Q3", IF(COALESCE(physicalTargets.q3, 0) = 0, IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)), COALESCE(physicalTargets.q3, 0)),
                                                IF(COALESCE(physicalTargets.q4, 0) = 0, IF(COALESCE(physicalTargets.q3, 0) = 0, IF(COALESCE(physicalTargets.q2, 0) = 0, COALESCE(physicalTargets.q1, 0), COALESCE(physicalTargets.q2, 0)), COALESCE(physicalTargets.q3, 0)), COALESCE(physicalTargets.q4, 0))
                                            )
                                        )
                                    )
                                )';

        $physicalAccompPerQuarter = 'IF("'.$model->quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                        IF("'.$model->quarter.'" = "Q2", COALESCE(physicalAccompsQ2.value, 0),
                                            IF("'.$model->quarter.'" = "Q3", COALESCE(physicalAccompsQ3.value, 0),
                                                COALESCE(physicalAccompsQ4.value, 0)
                                            )
                                        )
                                    )';

        $physicalAccompTotalPerQuarter = 'IF(project.data_type <> "Cumulative",
                                            IF("'.$model->quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                                IF("'.$model->quarter.'" = "Q2", COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0),
                                                    IF("'.$model->quarter.'" = "Q3", COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0),
                                                        COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0) + COALESCE(physicalAccompsQ4.value, 0)
                                                    )
                                                )
                                            )
                                        ,   
                                            IF("'.$model->quarter.'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
                                                IF("'.$model->quarter.'" = "Q2", IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)),
                                                    IF("'.$model->quarter.'" = "Q3", IF(COALESCE(physicalAccompsQ3.value, 0) = 0, IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)), COALESCE(physicalAccompsQ3.value, 0)),
                                                        IF(COALESCE(physicalAccompsQ4.value, 0) = 0, IF(COALESCE(physicalAccompsQ3.value, 0) = 0, IF(COALESCE(physicalAccompsQ2.value, 0) = 0, COALESCE(physicalAccompsQ1.value, 0), COALESCE(physicalAccompsQ2.value, 0)), COALESCE(physicalAccompsQ3.value, 0)), COALESCE(physicalAccompsQ4.value, 0))
                                                    )
                                                )
                                            )
                                        )';

        $projects = Project::find()
                    ->select([
                        'project.id',
                        'project.data_type as dataType',
                        'project.project_no as projectNo',
                        'IF(barangayTitles.title is null, IF(citymunTitles.title is null, IF(provinceTitles.title is null, IF(regionTitles.title is null, "No location", regionTitles.title), provinceTitles.title), citymunTitles.title), barangayTitles.title) as locationTitle',
                        'project.start_date as startDate',
                        'project.completion_date as completionDate',
                        'fund_source.title as fundSourceTitle',
                        'agency.code as agencyTitle',
                        $financialTargetTotalPerQuarter.' as allocationsAsOf',
                        $financialTargetPerQuarter.'as allocationPerQtr',
                        $releasesTotalPerQuarter.'as releasesAsOf',
                        $releasesPerQuarter.'as releasesPerQtr',
                        $obligationsTotalPerQuarter.'as obligationsAsOf',
                        $obligationsPerQuarter.'as obligationsPerQtr',
                        $expendituresTotalPerQuarter.'as expendituresAsOf',
                        $expendituresPerQuarter.'as expendituresPerQtr',
                        'physicalTargets.indicator as indicator',
                        $physicalTargetTotalPerQuarter.'as physicalTargetTotalPerQtr',
                        $physicalTargetPerQuarter.'as physicalTargetPerQtr',
                        $physicalAccompTotalPerQuarter.'as physicalAccompTotalPerQuarter',
                        $physicalAccompPerQuarter.'as physicalAccompPerQuarter',
                    ]);
                    $projects = $projects->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id');
                    $projects = $projects->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
                    $projects = $projects->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id');
                    $projects = $projects->leftJoin(['barangayTitles' => '('.$barangayTitles.')'], 'barangayTitles.project_id = project.id');
                    $projects = $projects->leftJoin('fund_source', 'fund_source.id = project.fund_source_id');
                    $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
                    $projects = $projects->leftJoin(['financialTargets' => '('.$financialTargets.')'], 'financialTargets.project_id = project.id');
                    $projects = $projects->leftJoin(['financialAccompsQ1' => '('.$financialAccomps.')'], 'financialAccompsQ1.project_id = project.id and financialAccompsQ1.quarter = "Q1"');
                    $projects = $projects->leftJoin(['financialAccompsQ2' => '('.$financialAccomps.')'], 'financialAccompsQ2.project_id = project.id and financialAccompsQ2.quarter = "Q2"');
                    $projects = $projects->leftJoin(['financialAccompsQ3' => '('.$financialAccomps.')'], 'financialAccompsQ3.project_id = project.id and financialAccompsQ3.quarter = "Q3"');
                    $projects = $projects->leftJoin(['financialAccompsQ4' => '('.$financialAccomps.')'], 'financialAccompsQ4.project_id = project.id and financialAccompsQ4.quarter = "Q4"');
                    $projects = $projects->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
                    $projects = $projects->leftJoin(['physicalAccompsQ1' => '('.$physicalAccomps.')'], 'physicalAccompsQ1.project_id = project.id and physicalAccompsQ1.quarter = "Q1"');
                    $projects = $projects->leftJoin(['physicalAccompsQ2' => '('.$physicalAccomps.')'], 'physicalAccompsQ2.project_id = project.id and physicalAccompsQ2.quarter = "Q2"');
                    $projects = $projects->leftJoin(['physicalAccompsQ3' => '('.$physicalAccomps.')'], 'physicalAccompsQ3.project_id = project.id and physicalAccompsQ3.quarter = "Q3"');
                    $projects = $projects->leftJoin(['physicalAccompsQ4' => '('.$physicalAccomps.')'], 'physicalAccompsQ4.project_id = project.id and physicalAccompsQ4.quarter = "Q4"');
                    $projects = $projects->andWhere(['project.year' => $model->year, 'project.draft' => 'No']);
                    $projects = $projects->andWhere(['project.id' => $projectIDs]);
        
                    if(Yii::$app->user->can('AgencyUser'))
                    {
                        $projects = $projects->andWhere(['agency.id' => Yii::$app->user->identity->userinfo->AGENCY_C]);
                    }
        
                    if($model->agency_id != '')
                    {
                        $projects = $projects->andWhere(['agency.id' => $model->agency_id]);
                    }

        $projects = $projects->asArray()->all();

        echo '<pre>'; print_r($projects); exit;
            
        $filename = 'Accomplishment '.$year;

        if($type == 'excel')
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=".$filename.".xls");
            return $this->renderPartial('accomplishment/_report-file', [
                'type' => $type,
                'model' => $model,
                'projects' => $projects,
            ]);
        }else if($type == 'pdf')
        {
            $content = $this->renderPartial('accomplishment/_report-file', [
                'type' => $type,
                'model' => $model,
                'projects' => $projects,
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
        }else if($type == 'print'){
            return $this->renderAjax('accomplishment/_report-file', [
                'type' => $type,
                'model' => $model,
                'projects' => $projects,
            ]);
        }
    }
}