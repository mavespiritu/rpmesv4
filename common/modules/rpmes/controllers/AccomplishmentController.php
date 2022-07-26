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
                        if(!($flag = $physicalAccomp->save())){
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
                        if(!($flag = $financialAccomp->save())){
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
                        if(!($flag = $personEmployedAccomp->save())){
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
                        if(!($flag = $beneficiariesAccomp->save())){
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
                        if(!($flag = $groupsAccomp->save())){
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
                        if(!($flag = $accomplishmentAccomp->save())){
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

    // public function actionDownloadFormTwo($type, $year, $agency_id, $quarter, $model)
    // {
    //     $model = json_decode($model, true);
    //     $model['year'] = $year;
    //     $model['agency_id'] = $agency_id;
    //     $model['quarter'] = $quarter;

    //     $data = [];

    //     $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];
    //     $genders = ['M' => 'Male', 'F' => 'Female'];
        
    //     $financialTargets = ProjectTarget::find()->where(['target_type' => 'Financial', 'year' => $model['year']])->createCommand()->getRawSql();
    //     $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $model['year']])->createCommand()->getRawSql();
    //     $maleEmployedTargets = ProjectTarget::find()->where(['target_type' => 'Male Employed', 'year' => $model['year']])->createCommand()->getRawSql();
    //     $femaleEmployedTargets = ProjectTarget::find()->where(['target_type' => 'Female Employed', 'year' => $model['year']])->createCommand()->getRawSql();
    //     $beneficiariesTargets = ProjectTarget::find()->where(['target_type' => 'Beneficiaries', 'year' => $model['year']])->createCommand()->getRawSql();
    //     $financialAccompsQ1 = FinancialAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q1'])->createCommand()->getRawSql();
    //     $financialAccompsQ2 = FinancialAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q2'])->createCommand()->getRawSql();
    //     $financialAccompsQ3 = FinancialAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q3'])->createCommand()->getRawSql();
    //     $financialAccompsQ4 = FinancialAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q4'])->createCommand()->getRawSql();
    //     $physicalAccompsQ1 = PhysicalAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q1'])->createCommand()->getRawSql();
    //     $physicalAccompsQ2 = PhysicalAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q2'])->createCommand()->getRawSql();
    //     $physicalAccompsQ3 = PhysicalAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q3'])->createCommand()->getRawSql();
    //     $physicalAccompsQ4 = PhysicalAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q4'])->createCommand()->getRawSql();
    //     $personEmployedAccompsQ1 = PersonEmployedAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q1'])->createCommand()->getRawSql();
    //     $personEmployedAccompsQ2 = PersonEmployedAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q2'])->createCommand()->getRawSql();
    //     $personEmployedAccompsQ3 = PersonEmployedAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q3'])->createCommand()->getRawSql();
    //     $personEmployedAccompsQ4 = PersonEmployedAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q4'])->createCommand()->getRawSql();
    //     $beneficiariesAccompsQ1 = BeneficiariesAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q1'])->createCommand()->getRawSql();
    //     $beneficiariesAccompsQ2 = BeneficiariesAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q2'])->createCommand()->getRawSql();
    //     $beneficiariesAccompsQ3 = BeneficiariesAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q3'])->createCommand()->getRawSql();
    //     $beneficiariesAccompsQ4 = BeneficiariesAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q4'])->createCommand()->getRawSql();
    //     $accomps = Accomplishment::find()->select(['project_id', 'IF(sum(COALESCE(action, 0)) > 0, 1, 0) as action'])->where(['year' => $model['year']])->groupBy(['project_id'])->createCommand()->getRawSql();

    //     $projectIDs = Plan::find()->select(['project_id'])->where(['year' => $model['year']])->asArray()->all();
    //     $projectIDs = ArrayHelper::map($projectIDs, 'project_id', 'project_id');

    //     $physicalTarget = 'IF(project.data_type = "Default",
    //                             IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalTargets.q1, 0),
    //                                 IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0),
    //                                     IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0),
    //                                     COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0)
    //                                     )
    //                                 )
    //                             )
    //                         ,   
    //                             IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalTargets.q1, 0),
    //                                 IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalTargets.q2, 0),
    //                                     IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalTargets.q3, 0),
    //                                     COALESCE(physicalTargets.q4, 0)
    //                                     )
    //                                 )
    //                             )
    //                         )';
        
    //     $financialTarget = 'IF(project.data_type <> "Cumulative",
    //                             IF("'.$model['quarter'].'" = "Q1", COALESCE(financialTargets.q1, 0),
    //                                 IF("'.$model['quarter'].'" = "Q2", COALESCE(financialTargets.q1, 0) + COALESCE(financialTargets.q2, 0),
    //                                     IF("'.$model['quarter'].'" = "Q3", COALESCE(financialTargets.q1, 0) + COALESCE(financialTargets.q2, 0) + COALESCE(financialTargets.q3, 0),
    //                                     COALESCE(financialTargets.q1, 0) + COALESCE(financialTargets.q2, 0) + COALESCE(financialTargets.q3, 0) + COALESCE(financialTargets.q4, 0)
    //                                     )
    //                                 )
    //                             )
    //                         ,   
    //                             IF("'.$model['quarter'].'" = "Q1", financialTargets.q1,
    //                                 IF("'.$model['quarter'].'" = "Q2", financialTargets.q2,
    //                                     IF("'.$model['quarter'].'" = "Q3", financialTargets.q3,
    //                                         financialTargets.q4
    //                                     )
    //                                 )
    //                             )
    //                         )'; 

    //     $physicalAccomp = 'IF(project.data_type <> "Cumulative",
    //                             IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
    //                                 IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0),
    //                                     IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0),
    //                                     COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0) + COALESCE(physicalAccompsQ4.value, 0)
    //                                     )
    //                                 )
    //                             )
    //                         ,   
    //                             IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
    //                                 IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalAccompsQ2.value, 0),
    //                                     IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalAccompsQ3.value, 0),
    //                                     COALESCE(physicalAccompsQ4.value, 0)
    //                                     )
    //                                 )
    //                             )
    //                         )';
        
    //     $releases = 'IF(project.data_type <> "Cumulative",
    //                     IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.releases, 0),
    //                         IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ1.releases, 0) + COALESCE(financialAccompsQ2.releases, 0),
    //                             IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ1.releases, 0) + COALESCE(financialAccompsQ2.releases, 0) + COALESCE(financialAccompsQ3.releases, 0),
    //                             COALESCE(financialAccompsQ1.releases, 0) + COALESCE(financialAccompsQ2.releases, 0) + COALESCE(financialAccompsQ3.releases, 0) + COALESCE(financialAccompsQ4.releases, 0)
    //                             )
    //                         )
    //                     )
    //                 ,   
    //                     IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.releases, 0),
    //                         IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ2.releases, 0),
    //                             IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ3.releases, 0),
    //                             COALESCE(financialAccompsQ4.releases, 0)
    //                             )
    //                         )
    //                     )
    //                 )';
            
    //     $obligations = 'IF(project.data_type <> "Cumulative",
    //                         IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.obligation, 0),
    //                             IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ1.obligation, 0) + COALESCE(financialAccompsQ2.obligation, 0),
    //                                 IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ1.obligation, 0) + COALESCE(financialAccompsQ2.obligation, 0) + COALESCE(financialAccompsQ3.obligation, 0),
    //                                 COALESCE(financialAccompsQ1.obligation, 0) + COALESCE(financialAccompsQ2.obligation, 0) + COALESCE(financialAccompsQ3.obligation, 0) + COALESCE(financialAccompsQ4.obligation, 0)
    //                                 )
    //                             )
    //                         )
    //                     ,   
    //                         IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.obligation, 0),
    //                             IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ2.obligation, 0),
    //                                 IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ3.obligation, 0),
    //                                 COALESCE(financialAccompsQ4.obligation, 0)
    //                                 )
    //                             )
    //                         )
    //                     )';
        
    //     $expenditures = 'IF(project.data_type <> "Cumulative",
    //                         IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.expenditures, 0),
    //                             IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ1.expenditures, 0) + COALESCE(financialAccompsQ2.expenditures, 0),
    //                                 IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ1.expenditures, 0) + COALESCE(financialAccompsQ2.expenditures, 0) + COALESCE(financialAccompsQ3.expenditures, 0),
    //                                 COALESCE(financialAccompsQ1.expenditures, 0) + COALESCE(financialAccompsQ2.expenditures, 0) + COALESCE(financialAccompsQ3.expenditures, 0) + COALESCE(financialAccompsQ4.expenditures, 0)
    //                                 )
    //                             )
    //                         )
    //                     ,   
    //                         IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.expenditures, 0),
    //                             IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ2.expenditures, 0),
    //                                 IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ3.expenditures, 0),
    //                                 COALESCE(financialAccompsQ4.expenditures, 0)
    //                                 )
    //                             )
    //                         )
    //                     )';
        
    //     $maleEmployedTarget = 'IF("'.$model['quarter'].'" = "Q1", COALESCE(maleEmployedTargets.q1, 0),
    //                             IF("'.$model['quarter'].'" = "Q2", COALESCE(maleEmployedTargets.q2, 0),
    //                                 IF("'.$model['quarter'].'" = "Q3", COALESCE(maleEmployedTargets.q3, 0),
    //                                 COALESCE(maleEmployedTargets.q4, 0)
    //                                 )
    //                             )
    //                         )';
        
    //     $femaleEmployedTarget = 'IF("'.$model['quarter'].'" = "Q1", COALESCE(femaleEmployedTargets.q1, 0),
    //                             IF("'.$model['quarter'].'" = "Q2", COALESCE(femaleEmployedTargets.q2, 0),
    //                                 IF("'.$model['quarter'].'" = "Q3", COALESCE(femaleEmployedTargets.q3, 0),
    //                                 COALESCE(femaleEmployedTargets.q4, 0)
    //                                 )
    //                             )
    //                         )';
                        
    //     $maleEmployedAccomp = 'IF("'.$model['quarter'].'" = "Q1", COALESCE(personEmployedAccompsQ1.male, 0),
    //                             IF("'.$model['quarter'].'" = "Q2", COALESCE(personEmployedAccompsQ1.male, 0) + COALESCE(personEmployedAccompsQ2.male, 0),
    //                                 IF("'.$model['quarter'].'" = "Q3", COALESCE(personEmployedAccompsQ1.male, 0) + COALESCE(personEmployedAccompsQ2.male, 0) + COALESCE(personEmployedAccompsQ3.male, 0),
    //                                 COALESCE(personEmployedAccompsQ1.male, 0) + COALESCE(personEmployedAccompsQ2.male, 0) + COALESCE(personEmployedAccompsQ3.male, 0) + COALESCE(personEmployedAccompsQ4.male, 0)
    //                                 )
    //                             )
    //                         )';

    //     $femaleEmployedAccomp = 'IF("'.$model['quarter'].'" = "Q1", COALESCE(personEmployedAccompsQ1.female, 0),
    //                                 IF("'.$model['quarter'].'" = "Q2", COALESCE(personEmployedAccompsQ1.female, 0) + COALESCE(personEmployedAccompsQ2.female, 0),
    //                                     IF("'.$model['quarter'].'" = "Q3", COALESCE(personEmployedAccompsQ1.female, 0) + COALESCE(personEmployedAccompsQ2.female, 0) + COALESCE(personEmployedAccompsQ3.female, 0),
    //                                     COALESCE(personEmployedAccompsQ1.female, 0) + COALESCE(personEmployedAccompsQ2.female, 0) + COALESCE(personEmployedAccompsQ3.female, 0) + COALESCE(personEmployedAccompsQ4.female, 0)
    //                                     )
    //                                 )
    //                             )';

    //     $beneficiaryTarget = 'IF("'.$model['quarter'].'" = "Q1", COALESCE(beneficiariesTargets.q1, 0),
    //                             IF("'.$model['quarter'].'" = "Q2", COALESCE(beneficiariesTargets.q2, 0),
    //                                 IF("'.$model['quarter'].'" = "Q3", COALESCE(beneficiariesTargets.q3, 0),
    //                                 COALESCE(beneficiariesTargets.q4, 0)
    //                                 )
    //                             )
    //                         )';

    //     $maleBeneficiaryAccomp = 'IF("'.$model['quarter'].'" = "Q1", COALESCE(beneficiariesAccompsQ1.male, 0),
    //                                 IF("'.$model['quarter'].'" = "Q2", COALESCE(beneficiariesAccompsQ1.male, 0) + COALESCE(beneficiariesAccompsQ2.male, 0),
    //                                     IF("'.$model['quarter'].'" = "Q3", COALESCE(beneficiariesAccompsQ1.male, 0) + COALESCE(beneficiariesAccompsQ2.male, 0) + COALESCE(beneficiariesAccompsQ3.male, 0),
    //                                     COALESCE(beneficiariesAccompsQ1.male, 0) + COALESCE(beneficiariesAccompsQ2.male, 0) + COALESCE(beneficiariesAccompsQ3.male, 0) + COALESCE(beneficiariesAccompsQ4.male, 0)
    //                                     )
    //                                 )
    //                             )';
            
    //     $femaleBeneficiaryAccomp = 'IF("'.$model['quarter'].'" = "Q1", COALESCE(beneficiariesAccompsQ1.female, 0),
    //                                     IF("'.$model['quarter'].'" = "Q2", COALESCE(beneficiariesAccompsQ1.female, 0) + COALESCE(beneficiariesAccompsQ2.female, 0),
    //                                         IF("'.$model['quarter'].'" = "Q3", COALESCE(beneficiariesAccompsQ1.female, 0) + COALESCE(beneficiariesAccompsQ2.female, 0) + COALESCE(beneficiariesAccompsQ3.female, 0),
    //                                         COALESCE(beneficiariesAccompsQ1.female, 0) + COALESCE(beneficiariesAccompsQ2.female, 0) + COALESCE(beneficiariesAccompsQ3.female, 0) + COALESCE(beneficiariesAccompsQ4.female, 0)
    //                                         )
    //                                     )
    //                                 )';

    //     $isCompleted = 'accomps.action';
    //     $isPercent = 'LOCATE("%", physicalTargets.indicator)';

    //     $projects = Project::find()
    //                 ->select([
    //                     'project.id',
    //                     'project.data_type as dataType',
    //                     'agency.code as agencyTitle',
    //                     'program.title as programTitle',
    //                     'category.title as categoryTitle',
    //                     'key_result_area.title as kraTitle',
    //                     'project.title as projectTitle',
    //                     'sector.title as sectorTitle',
    //                     'sub_sector.title as subSectorTitle',
    //                     'fund_source.title as fundSourceTitle',
    //                     'concat("SDG #",sdg_goal.sdg_no,": ",sdg_goal.title) as sdgGoalTitle',
    //                     'concat("Chapter ",rdp_chapter.chapter_no,": ",rdp_chapter.title) as chapterTitle',
    //                     'IF(rdp_chapter_outcome.id is not null, concat("Chapter Outcome ",rdp_chapter_outcome.level,": ",rdp_chapter_outcome.title), "No Chapter Outcome") as chapterOutcomeTitle',
    //                     'IF(rdp_sub_chapter_outcome.id is not null, concat("Sub-Chapter Outcome ",rdp_sub_chapter_outcome.level,": ",rdp_sub_chapter_outcome.title), "No Sub-Chapter Outcome") as subChapterOutcomeTitle',
    //                     'tblregion.abbreviation as regionTitle',
    //                     'IF(tblprovince.province_c is not null, tblprovince.province_m, "Region-wide") as provinceTitle',
    //                     'physicalTargets.indicator as indicator',
    //                     'SUM(accomps.action) as completed',
    //                     'SUM(IF('.$isPercent.' > 0, '.$physicalAccomp.' - '.$physicalTarget.', IF('.$physicalTarget.' > 0, (('.$physicalAccomp.'/'.$physicalTarget.') * 100) -100 , 0))) as slippage',
    //                     'SUM(
    //                         IF('.$physicalTarget.' > 0, 
    //                             IF('.$isCompleted.' = 0,
    //                                 IF(
    //                                     IF('.$isPercent.' > 0, '.$physicalAccomp.' - '.$physicalTarget.', IF('.$physicalTarget.' > 0, (('.$physicalAccomp.'/'.$physicalTarget.') * 100) - 100, 0))
    //                                 <= -15, 1 , 0)
    //                             , 0)
    //                         , 0)
    //                     ) as behindSchedule',
    //                     'SUM(
    //                         IF('.$physicalTarget.' > 0, 
    //                             IF('.$isCompleted.' = 0,
    //                                 IF(
    //                                     IF('.$isPercent.' > 0, '.$physicalAccomp.' - '.$physicalTarget.', IF('.$physicalTarget.' > 0, (('.$physicalAccomp.'/'.$physicalTarget.') * 100) - 100, 0))
    //                                 BETWEEN -15 AND 15, 1 , 0)
    //                             , 0)
    //                         , 0)
    //                     ) as onSchedule',
    //                     'SUM(
    //                         IF('.$physicalTarget.' > 0, 
    //                             IF('.$isCompleted.' = 0,
    //                                 IF(
    //                                     IF('.$isPercent.' > 0, '.$physicalAccomp.' - '.$physicalTarget.', IF('.$physicalTarget.' > 0, (('.$physicalAccomp.'/'.$physicalTarget.') * 100) - 100, 0))
    //                                 > 15, 1, 0)
    //                             , 0)
    //                         , 0)
    //                     ) as aheadOnSchedule',
    //                     'SUM(IF('.$physicalTarget.' <= 0, 1, 0)) as notYetStarted',
    //                     'SUM('.$financialTarget.') as allocationAsOfReportingPeriod',
    //                     'SUM('.$releases.') as releasesAsOfReportingPeriod',
    //                     'SUM('.$obligations.') as obligationsAsOfReportingPeriod',
    //                     'SUM('.$expenditures.') as expendituresAsOfReportingPeriod',
    //                     'SUM('.$physicalTarget.') as physicalTargetAsOfReportingPeriod',
    //                     'SUM('.$physicalAccomp.') as physicalActualAsOfReportingPeriod',
    //                     'SUM('.$maleEmployedTarget.') as malesEmployedTargetAsOfReportingPeriod',
    //                     'SUM('.$femaleEmployedTarget.') as femalesEmployedTargetAsOfReportingPeriod',
    //                     'SUM('.$maleEmployedAccomp.') as malesEmployedActualAsOfReportingPeriod',
    //                     'SUM('.$femaleEmployedAccomp.') as femalesEmployedActualAsOfReportingPeriod',
    //                     'SUM('.$beneficiaryTarget.') as beneficiariesTargetAsOfReportingPeriod',
    //                     'SUM('.$maleBeneficiaryAccomp.') as maleBeneficiariesActualAsOfReportingPeriod',
    //                     'SUM('.$femaleBeneficiaryAccomp.') as femaleBeneficiariesActualAsOfReportingPeriod',
    //                 ]);
    //     $projects = $projects->leftJoin(['financialTargets' => '('.$financialTargets.')'], 'financialTargets.project_id = project.id');
    //     $projects = $projects->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
    //     $projects = $projects->leftJoin(['maleEmployedTargets' => '('.$maleEmployedTargets.')'], 'maleEmployedTargets.project_id = project.id');
    //     $projects = $projects->leftJoin(['femaleEmployedTargets' => '('.$femaleEmployedTargets.')'], 'femaleEmployedTargets.project_id = project.id');
    //     $projects = $projects->leftJoin(['beneficiariesTargets' => '('.$beneficiariesTargets.')'], 'beneficiariesTargets.project_id = project.id');
    //     $projects = $projects->leftJoin(['financialAccompsQ1' => '('.$financialAccompsQ1.')'], 'financialAccompsQ1.project_id = project.id');
    //     $projects = $projects->leftJoin(['financialAccompsQ2' => '('.$financialAccompsQ2.')'], 'financialAccompsQ2.project_id = project.id');
    //     $projects = $projects->leftJoin(['financialAccompsQ3' => '('.$financialAccompsQ3.')'], 'financialAccompsQ3.project_id = project.id');
    //     $projects = $projects->leftJoin(['financialAccompsQ4' => '('.$financialAccompsQ4.')'], 'financialAccompsQ4.project_id = project.id');
    //     $projects = $projects->leftJoin(['physicalAccompsQ1' => '('.$physicalAccompsQ1.')'], 'physicalAccompsQ1.project_id = project.id');
    //     $projects = $projects->leftJoin(['physicalAccompsQ2' => '('.$physicalAccompsQ2.')'], 'physicalAccompsQ2.project_id = project.id');
    //     $projects = $projects->leftJoin(['physicalAccompsQ3' => '('.$physicalAccompsQ3.')'], 'physicalAccompsQ3.project_id = project.id');
    //     $projects = $projects->leftJoin(['physicalAccompsQ4' => '('.$physicalAccompsQ4.')'], 'physicalAccompsQ4.project_id = project.id');
    //     $projects = $projects->leftJoin(['personEmployedAccompsQ1' => '('.$personEmployedAccompsQ1.')'], 'personEmployedAccompsQ1.project_id = project.id');
    //     $projects = $projects->leftJoin(['personEmployedAccompsQ2' => '('.$personEmployedAccompsQ2.')'], 'personEmployedAccompsQ2.project_id = project.id');
    //     $projects = $projects->leftJoin(['personEmployedAccompsQ3' => '('.$personEmployedAccompsQ3.')'], 'personEmployedAccompsQ3.project_id = project.id');
    //     $projects = $projects->leftJoin(['personEmployedAccompsQ4' => '('.$personEmployedAccompsQ4.')'], 'personEmployedAccompsQ4.project_id = project.id');
    //     $projects = $projects->leftJoin(['beneficiariesAccompsQ1' => '('.$beneficiariesAccompsQ1.')'], 'beneficiariesAccompsQ1.project_id = project.id');
    //     $projects = $projects->leftJoin(['beneficiariesAccompsQ2' => '('.$beneficiariesAccompsQ2.')'], 'beneficiariesAccompsQ2.project_id = project.id');
    //     $projects = $projects->leftJoin(['beneficiariesAccompsQ3' => '('.$beneficiariesAccompsQ3.')'], 'beneficiariesAccompsQ3.project_id = project.id');
    //     $projects = $projects->leftJoin(['beneficiariesAccompsQ4' => '('.$beneficiariesAccompsQ4.')'], 'beneficiariesAccompsQ4.project_id = project.id');
    //     $projects = $projects->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');
    //     $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
    //     $projects = $projects->leftJoin('program', 'program.id = project.program_id');
    //     $projects = $projects->leftJoin('sector', 'sector.id = project.sector_id');
    //     $projects = $projects->leftJoin('sub_sector', 'sub_sector.id = project.sub_sector_id');
    //     $projects = $projects->leftJoin('fund_source', 'fund_source.id = project.fund_source_id');
    //     $projects = $projects->leftJoin('project_category', 'project_category.project_id = project.id and project_category.year = project.year');
    //     $projects = $projects->leftJoin('category', 'category.id = project_category.category_id');
    //     $projects = $projects->leftJoin('project_kra', 'project_kra.project_id = project.id and project_kra.year = project.year');
    //     $projects = $projects->leftJoin('key_result_area', 'key_result_area.id = project_kra.key_result_area_id');
    //     $projects = $projects->leftJoin('project_sdg_goal', 'project_sdg_goal.project_id = project.id and project_sdg_goal.year = project.year');
    //     $projects = $projects->leftJoin('sdg_goal', 'sdg_goal.id = project_sdg_goal.sdg_goal_id');
    //     $projects = $projects->leftJoin('project_rdp_chapter', 'project_rdp_chapter.project_id = project.id and project_rdp_chapter.year = project.year');
    //     $projects = $projects->leftJoin('rdp_chapter', 'rdp_chapter.id = project_rdp_chapter.rdp_chapter_id');
    //     $projects = $projects->leftJoin('project_rdp_chapter_outcome', 'project_rdp_chapter_outcome.project_id = project.id and project_rdp_chapter_outcome.year = project.year');
    //     $projects = $projects->leftJoin('rdp_chapter_outcome', 'rdp_chapter_outcome.id = project_rdp_chapter_outcome.rdp_chapter_outcome_id');
    //     $projects = $projects->leftJoin('project_rdp_sub_chapter_outcome', 'project_rdp_sub_chapter_outcome.project_id = project.id and project_rdp_sub_chapter_outcome.year = project.year');
    //     $projects = $projects->leftJoin('rdp_sub_chapter_outcome', 'rdp_sub_chapter_outcome.id = project_rdp_sub_chapter_outcome.rdp_sub_chapter_outcome_id');
    //     $projects = $projects->leftJoin('project_region', 'project_region.project_id = project.id and project_region.year = project.year');
    //     $projects = $projects->leftJoin('tblregion', 'tblregion.region_c = project_region.region_id');
    //     $projects = $projects->leftJoin('project_province', 'project_province.project_id = project.id and project_province.year = project.year');
    //     $projects = $projects->leftJoin('tblprovince', 'tblprovince.province_c = project_province.province_id');
    //     $projects = $projects->leftJoin('project_citymun', 'project_citymun.project_id = project.id and project_citymun.year = project.year');
    //     $projects = $projects->leftJoin('tblcitymun', 'tblcitymun.province_c = project_citymun.province_id and tblcitymun.citymun_c = project_citymun.citymun_id');
    //     $projects = $projects->andWhere(['project.year' => $model['year'], 'project.draft' => 'No']);
    //     $projects = $projects->andWhere(['project.id' => $projectIDs]);

    //     $projects = $projects->orderBy(['projectTitle' => SORT_ASC])->asArray()->all();

    //     $projects = $projects->asArray()->all();

    //     $filename = 'Accomplishment Report '.$year;

    //     if($type == 'excel')
    //     {
    //         header("Content-type: application/vnd.ms-excel");
    //         header("Content-Disposition: attachment; filename=".$filename.".xls");
    //         return $this->renderPartial('accomplishment/form-two', [
    //             'model' => $model,
    //             'type' => $type,
    //             'projects' => $projects,
    //             'bigCaps' => $bigCaps,
    //             'smallCaps' => $smallCaps,
    //             'numbers' => $numbers,
    //             'quarters' => $quarters,
    //             'genders' => $genders,
    //         ]);
    //     }else if($type == 'pdf')
    //     {
    //         $content = $this->renderPartial('accomplishment/form-two', [
    //             'model' => $model,
    //             'type' => $type,
    //             'projects' => $projects,
    //             'bigCaps' => $bigCaps,
    //             'smallCaps' => $smallCaps,
    //             'numbers' => $numbers,
    //             'quarters' => $quarters,
    //             'genders' => $genders,
    //         ]);

    //         $pdf = new Pdf([
    //             'mode' => Pdf::MODE_CORE,
    //             'format' => Pdf::FORMAT_LEGAL, 
    //             'orientation' => Pdf::ORIENT_LANDSCAPE, 
    //             'destination' => Pdf::DEST_DOWNLOAD, 
    //             'filename' => $filename.'.pdf', 
    //             'content' => $content,  
    //             'marginLeft' => 11.4,
    //             'marginRight' => 11.4,
    //             'cssInline' => 'table{
    //                                 font-family: "Arial";
    //                                 border-collapse: collapse;
    //                             }
    //                             thead{
    //                                 font-size: 12px;
    //                                 text-align: center;
    //                             }
                            
    //                             td{
    //                                 font-size: 10px;
    //                                 border: 1px solid black;
    //                             }
                            
    //                             th{
    //                                 text-align: center;
    //                                 border: 1px solid black;
    //                             }', 
    //             ]);
        
    //             $response = Yii::$app->response;
    //             $response->format = \yii\web\Response::FORMAT_RAW;
    //             $headers = Yii::$app->response->headers;
    //             $headers->add('Content-Type', 'application/pdf');
    //             return $pdf->render();
    //     }
    // }

    // public function actionPrintFormTwo($year,$quarter,$agency_id)
    // {
    //     $model = new Project();
    //     $model['year'] = $year;
    //     $model['quarter'] = $quarter;
    //     $model['agency_id'] = $agency_id;

    //     $data = [];

    //     $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];
    //     $genders = ['M' => 'Male', 'F' => 'Female'];
        
    //     $financialTargets = ProjectTarget::find()->where(['target_type' => 'Financial', 'year' => $model['year']])->createCommand()->getRawSql();
    //     $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $model['year']])->createCommand()->getRawSql();
    //     $maleEmployedTargets = ProjectTarget::find()->where(['target_type' => 'Male Employed', 'year' => $model['year']])->createCommand()->getRawSql();
    //     $femaleEmployedTargets = ProjectTarget::find()->where(['target_type' => 'Female Employed', 'year' => $model['year']])->createCommand()->getRawSql();
    //     $beneficiariesTargets = ProjectTarget::find()->where(['target_type' => 'Beneficiaries', 'year' => $model['year']])->createCommand()->getRawSql();
    //     $financialAccompsQ1 = FinancialAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q1'])->createCommand()->getRawSql();
    //     $financialAccompsQ2 = FinancialAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q2'])->createCommand()->getRawSql();
    //     $financialAccompsQ3 = FinancialAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q3'])->createCommand()->getRawSql();
    //     $financialAccompsQ4 = FinancialAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q4'])->createCommand()->getRawSql();
    //     $physicalAccompsQ1 = PhysicalAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q1'])->createCommand()->getRawSql();
    //     $physicalAccompsQ2 = PhysicalAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q2'])->createCommand()->getRawSql();
    //     $physicalAccompsQ3 = PhysicalAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q3'])->createCommand()->getRawSql();
    //     $physicalAccompsQ4 = PhysicalAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q4'])->createCommand()->getRawSql();
    //     $personEmployedAccompsQ1 = PersonEmployedAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q1'])->createCommand()->getRawSql();
    //     $personEmployedAccompsQ2 = PersonEmployedAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q2'])->createCommand()->getRawSql();
    //     $personEmployedAccompsQ3 = PersonEmployedAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q3'])->createCommand()->getRawSql();
    //     $personEmployedAccompsQ4 = PersonEmployedAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q4'])->createCommand()->getRawSql();
    //     $beneficiariesAccompsQ1 = BeneficiariesAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q1'])->createCommand()->getRawSql();
    //     $beneficiariesAccompsQ2 = BeneficiariesAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q2'])->createCommand()->getRawSql();
    //     $beneficiariesAccompsQ3 = BeneficiariesAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q3'])->createCommand()->getRawSql();
    //     $beneficiariesAccompsQ4 = BeneficiariesAccomplishment::find()->where(['year' => $model['year'], 'quarter' => 'Q4'])->createCommand()->getRawSql();
    //     $accomps = Accomplishment::find()->select(['project_id', 'IF(sum(COALESCE(action, 0)) > 0, 1, 0) as action'])->where(['year' => $model['year']])->groupBy(['project_id'])->createCommand()->getRawSql();

    //     $physicalTarget = 'IF(project.data_type = "Default",
    //                             IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalTargets.q1, 0),
    //                                 IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0),
    //                                     IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0),
    //                                     COALESCE(physicalTargets.q1, 0) + COALESCE(physicalTargets.q2, 0) + COALESCE(physicalTargets.q3, 0) + COALESCE(physicalTargets.q4, 0)
    //                                     )
    //                                 )
    //                             )
    //                         ,   
    //                             IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalTargets.q1, 0),
    //                                 IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalTargets.q2, 0),
    //                                     IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalTargets.q3, 0),
    //                                     COALESCE(physicalTargets.q4, 0)
    //                                     )
    //                                 )
    //                             )
    //                         )';
        
    //     $financialTarget = 'IF(project.data_type <> "Cumulative",
    //                             IF("'.$model['quarter'].'" = "Q1", COALESCE(financialTargets.q1, 0),
    //                                 IF("'.$model['quarter'].'" = "Q2", COALESCE(financialTargets.q1, 0) + COALESCE(financialTargets.q2, 0),
    //                                     IF("'.$model['quarter'].'" = "Q3", COALESCE(financialTargets.q1, 0) + COALESCE(financialTargets.q2, 0) + COALESCE(financialTargets.q3, 0),
    //                                     COALESCE(financialTargets.q1, 0) + COALESCE(financialTargets.q2, 0) + COALESCE(financialTargets.q3, 0) + COALESCE(financialTargets.q4, 0)
    //                                     )
    //                                 )
    //                             )
    //                         ,   
    //                             IF("'.$model['quarter'].'" = "Q1", financialTargets.q1,
    //                                 IF("'.$model['quarter'].'" = "Q2", financialTargets.q2,
    //                                     IF("'.$model['quarter'].'" = "Q3", financialTargets.q3,
    //                                         financialTargets.q4
    //                                     )
    //                                 )
    //                             )
    //                         )'; 

    //     $physicalAccomp = 'IF(project.data_type <> "Cumulative",
    //                             IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
    //                                 IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0),
    //                                     IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0),
    //                                     COALESCE(physicalAccompsQ1.value, 0) + COALESCE(physicalAccompsQ2.value, 0) + COALESCE(physicalAccompsQ3.value, 0) + COALESCE(physicalAccompsQ4.value, 0)
    //                                     )
    //                                 )
    //                             )
    //                         ,   
    //                             IF("'.$model['quarter'].'" = "Q1", COALESCE(physicalAccompsQ1.value, 0),
    //                                 IF("'.$model['quarter'].'" = "Q2", COALESCE(physicalAccompsQ2.value, 0),
    //                                     IF("'.$model['quarter'].'" = "Q3", COALESCE(physicalAccompsQ3.value, 0),
    //                                     COALESCE(physicalAccompsQ4.value, 0)
    //                                     )
    //                                 )
    //                             )
    //                         )';
        
    //     $releases = 'IF(project.data_type <> "Cumulative",
    //                     IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.releases, 0),
    //                         IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ1.releases, 0) + COALESCE(financialAccompsQ2.releases, 0),
    //                             IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ1.releases, 0) + COALESCE(financialAccompsQ2.releases, 0) + COALESCE(financialAccompsQ3.releases, 0),
    //                             COALESCE(financialAccompsQ1.releases, 0) + COALESCE(financialAccompsQ2.releases, 0) + COALESCE(financialAccompsQ3.releases, 0) + COALESCE(financialAccompsQ4.releases, 0)
    //                             )
    //                         )
    //                     )
    //                 ,   
    //                     IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.releases, 0),
    //                         IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ2.releases, 0),
    //                             IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ3.releases, 0),
    //                             COALESCE(financialAccompsQ4.releases, 0)
    //                             )
    //                         )
    //                     )
    //                 )';
            
    //     $obligations = 'IF(project.data_type <> "Cumulative",
    //                         IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.obligation, 0),
    //                             IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ1.obligation, 0) + COALESCE(financialAccompsQ2.obligation, 0),
    //                                 IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ1.obligation, 0) + COALESCE(financialAccompsQ2.obligation, 0) + COALESCE(financialAccompsQ3.obligation, 0),
    //                                 COALESCE(financialAccompsQ1.obligation, 0) + COALESCE(financialAccompsQ2.obligation, 0) + COALESCE(financialAccompsQ3.obligation, 0) + COALESCE(financialAccompsQ4.obligation, 0)
    //                                 )
    //                             )
    //                         )
    //                     ,   
    //                         IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.obligation, 0),
    //                             IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ2.obligation, 0),
    //                                 IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ3.obligation, 0),
    //                                 COALESCE(financialAccompsQ4.obligation, 0)
    //                                 )
    //                             )
    //                         )
    //                     )';
        
    //     $expenditures = 'IF(project.data_type <> "Cumulative",
    //                         IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.expenditures, 0),
    //                             IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ1.expenditures, 0) + COALESCE(financialAccompsQ2.expenditures, 0),
    //                                 IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ1.expenditures, 0) + COALESCE(financialAccompsQ2.expenditures, 0) + COALESCE(financialAccompsQ3.expenditures, 0),
    //                                 COALESCE(financialAccompsQ1.expenditures, 0) + COALESCE(financialAccompsQ2.expenditures, 0) + COALESCE(financialAccompsQ3.expenditures, 0) + COALESCE(financialAccompsQ4.expenditures, 0)
    //                                 )
    //                             )
    //                         )
    //                     ,   
    //                         IF("'.$model['quarter'].'" = "Q1", COALESCE(financialAccompsQ1.expenditures, 0),
    //                             IF("'.$model['quarter'].'" = "Q2", COALESCE(financialAccompsQ2.expenditures, 0),
    //                                 IF("'.$model['quarter'].'" = "Q3", COALESCE(financialAccompsQ3.expenditures, 0),
    //                                 COALESCE(financialAccompsQ4.expenditures, 0)
    //                                 )
    //                             )
    //                         )
    //                     )';
        
    //     $maleEmployedTarget = 'IF("'.$model['quarter'].'" = "Q1", COALESCE(maleEmployedTargets.q1, 0),
    //                             IF("'.$model['quarter'].'" = "Q2", COALESCE(maleEmployedTargets.q2, 0),
    //                                 IF("'.$model['quarter'].'" = "Q3", COALESCE(maleEmployedTargets.q3, 0),
    //                                 COALESCE(maleEmployedTargets.q4, 0)
    //                                 )
    //                             )
    //                         )';
        
    //     $femaleEmployedTarget = 'IF("'.$model['quarter'].'" = "Q1", COALESCE(femaleEmployedTargets.q1, 0),
    //                             IF("'.$model['quarter'].'" = "Q2", COALESCE(femaleEmployedTargets.q2, 0),
    //                                 IF("'.$model['quarter'].'" = "Q3", COALESCE(femaleEmployedTargets.q3, 0),
    //                                 COALESCE(femaleEmployedTargets.q4, 0)
    //                                 )
    //                             )
    //                         )';
                        
    //     $maleEmployedAccomp = 'IF("'.$model['quarter'].'" = "Q1", COALESCE(personEmployedAccompsQ1.male, 0),
    //                             IF("'.$model['quarter'].'" = "Q2", COALESCE(personEmployedAccompsQ1.male, 0) + COALESCE(personEmployedAccompsQ2.male, 0),
    //                                 IF("'.$model['quarter'].'" = "Q3", COALESCE(personEmployedAccompsQ1.male, 0) + COALESCE(personEmployedAccompsQ2.male, 0) + COALESCE(personEmployedAccompsQ3.male, 0),
    //                                 COALESCE(personEmployedAccompsQ1.male, 0) + COALESCE(personEmployedAccompsQ2.male, 0) + COALESCE(personEmployedAccompsQ3.male, 0) + COALESCE(personEmployedAccompsQ4.male, 0)
    //                                 )
    //                             )
    //                         )';

    //     $femaleEmployedAccomp = 'IF("'.$model['quarter'].'" = "Q1", COALESCE(personEmployedAccompsQ1.female, 0),
    //                                 IF("'.$model['quarter'].'" = "Q2", COALESCE(personEmployedAccompsQ1.female, 0) + COALESCE(personEmployedAccompsQ2.female, 0),
    //                                     IF("'.$model['quarter'].'" = "Q3", COALESCE(personEmployedAccompsQ1.female, 0) + COALESCE(personEmployedAccompsQ2.female, 0) + COALESCE(personEmployedAccompsQ3.female, 0),
    //                                     COALESCE(personEmployedAccompsQ1.female, 0) + COALESCE(personEmployedAccompsQ2.female, 0) + COALESCE(personEmployedAccompsQ3.female, 0) + COALESCE(personEmployedAccompsQ4.female, 0)
    //                                     )
    //                                 )
    //                             )';

    //     $beneficiaryTarget = 'IF("'.$model['quarter'].'" = "Q1", COALESCE(beneficiariesTargets.q1, 0),
    //                             IF("'.$model['quarter'].'" = "Q2", COALESCE(beneficiariesTargets.q2, 0),
    //                                 IF("'.$model['quarter'].'" = "Q3", COALESCE(beneficiariesTargets.q3, 0),
    //                                 COALESCE(beneficiariesTargets.q4, 0)
    //                                 )
    //                             )
    //                         )';

    //     $maleBeneficiaryAccomp = 'IF("'.$model['quarter'].'" = "Q1", COALESCE(beneficiariesAccompsQ1.male, 0),
    //                                 IF("'.$model['quarter'].'" = "Q2", COALESCE(beneficiariesAccompsQ1.male, 0) + COALESCE(beneficiariesAccompsQ2.male, 0),
    //                                     IF("'.$model['quarter'].'" = "Q3", COALESCE(beneficiariesAccompsQ1.male, 0) + COALESCE(beneficiariesAccompsQ2.male, 0) + COALESCE(beneficiariesAccompsQ3.male, 0),
    //                                     COALESCE(beneficiariesAccompsQ1.male, 0) + COALESCE(beneficiariesAccompsQ2.male, 0) + COALESCE(beneficiariesAccompsQ3.male, 0) + COALESCE(beneficiariesAccompsQ4.male, 0)
    //                                     )
    //                                 )
    //                             )';
            
    //     $femaleBeneficiaryAccomp = 'IF("'.$model['quarter'].'" = "Q1", COALESCE(beneficiariesAccompsQ1.female, 0),
    //                                     IF("'.$model['quarter'].'" = "Q2", COALESCE(beneficiariesAccompsQ1.female, 0) + COALESCE(beneficiariesAccompsQ2.female, 0),
    //                                         IF("'.$model['quarter'].'" = "Q3", COALESCE(beneficiariesAccompsQ1.female, 0) + COALESCE(beneficiariesAccompsQ2.female, 0) + COALESCE(beneficiariesAccompsQ3.female, 0),
    //                                         COALESCE(beneficiariesAccompsQ1.female, 0) + COALESCE(beneficiariesAccompsQ2.female, 0) + COALESCE(beneficiariesAccompsQ3.female, 0) + COALESCE(beneficiariesAccompsQ4.female, 0)
    //                                         )
    //                                     )
    //                                 )';

    //     $isCompleted = 'accomps.action';
    //     $isPercent = 'LOCATE("%", physicalTargets.indicator)';

    //     $projects = Project::find()
    //                 ->select([
    //                     'project.id',
    //                     'project.data_type as dataType',
    //                     'project.project_no as projectId',
    //                     'agency.code as agencyTitle',
    //                     'program.title as programTitle',
    //                     'category.title as categoryTitle',
    //                     'key_result_area.title as kraTitle',
    //                     'project.title as projectTitle',
    //                     'sector.title as sectorTitle',
    //                     'sub_sector.title as subSectorTitle',
    //                     'fund_source.title as fundSourceTitle',
    //                     'concat("SDG #",sdg_goal.sdg_no,": ",sdg_goal.title) as sdgGoalTitle',
    //                     'concat("Chapter ",rdp_chapter.chapter_no,": ",rdp_chapter.title) as chapterTitle',
    //                     'IF(rdp_chapter_outcome.id is not null, concat("Chapter Outcome ",rdp_chapter_outcome.level,": ",rdp_chapter_outcome.title), "No Chapter Outcome") as chapterOutcomeTitle',
    //                     'IF(rdp_sub_chapter_outcome.id is not null, concat("Sub-Chapter Outcome ",rdp_sub_chapter_outcome.level,": ",rdp_sub_chapter_outcome.title), "No Sub-Chapter Outcome") as subChapterOutcomeTitle',
    //                     'tblregion.abbreviation as regionTitle',
    //                     'IF(tblprovince.province_c is not null, tblprovince.province_m, "Region-wide") as provinceTitle',
    //                     'physicalTargets.indicator as indicator',
    //                     'SUM(accomps.action) as completed',
    //                     'SUM(IF('.$isPercent.' > 0, '.$physicalAccomp.' - '.$physicalTarget.', IF('.$physicalTarget.' > 0, (('.$physicalAccomp.'/'.$physicalTarget.') * 100) -100 , 0))) as slippage',
    //                     'SUM(
    //                         IF('.$physicalTarget.' > 0, 
    //                             IF('.$isCompleted.' = 0,
    //                                 IF(
    //                                     IF('.$isPercent.' > 0, '.$physicalAccomp.' - '.$physicalTarget.', IF('.$physicalTarget.' > 0, (('.$physicalAccomp.'/'.$physicalTarget.') * 100) - 100, 0))
    //                                 <= -15, 1 , 0)
    //                             , 0)
    //                         , 0)
    //                     ) as behindSchedule',
    //                     'SUM(
    //                         IF('.$physicalTarget.' > 0, 
    //                             IF('.$isCompleted.' = 0,
    //                                 IF(
    //                                     IF('.$isPercent.' > 0, '.$physicalAccomp.' - '.$physicalTarget.', IF('.$physicalTarget.' > 0, (('.$physicalAccomp.'/'.$physicalTarget.') * 100) - 100, 0))
    //                                 BETWEEN -15 AND 15, 1 , 0)
    //                             , 0)
    //                         , 0)
    //                     ) as onSchedule',
    //                     'SUM(
    //                         IF('.$physicalTarget.' > 0, 
    //                             IF('.$isCompleted.' = 0,
    //                                 IF(
    //                                     IF('.$isPercent.' > 0, '.$physicalAccomp.' - '.$physicalTarget.', IF('.$physicalTarget.' > 0, (('.$physicalAccomp.'/'.$physicalTarget.') * 100) - 100, 0))
    //                                 > 15, 1, 0)
    //                             , 0)
    //                         , 0)
    //                     ) as aheadOnSchedule',
    //                     'SUM(IF('.$physicalTarget.' <= 0, 1, 0)) as notYetStarted',
    //                     'SUM('.$financialTarget.') as allocationAsOfReportingPeriod',
    //                     'SUM('.$releases.') as releasesAsOfReportingPeriod',
    //                     'SUM('.$obligations.') as obligationsAsOfReportingPeriod',
    //                     'SUM('.$expenditures.') as expendituresAsOfReportingPeriod',
    //                     'SUM('.$physicalTarget.') as physicalTargetAsOfReportingPeriod',
    //                     'SUM('.$physicalAccomp.') as physicalActualAsOfReportingPeriod',
    //                     'SUM('.$maleEmployedTarget.') as malesEmployedTargetAsOfReportingPeriod',
    //                     'SUM('.$femaleEmployedTarget.') as femalesEmployedTargetAsOfReportingPeriod',
    //                     'SUM('.$maleEmployedAccomp.') as malesEmployedActualAsOfReportingPeriod',
    //                     'SUM('.$femaleEmployedAccomp.') as femalesEmployedActualAsOfReportingPeriod',
    //                     'SUM('.$beneficiaryTarget.') as beneficiariesTargetAsOfReportingPeriod',
    //                     'SUM('.$maleBeneficiaryAccomp.') as maleBeneficiariesActualAsOfReportingPeriod',
    //                     'SUM('.$femaleBeneficiaryAccomp.') as femaleBeneficiariesActualAsOfReportingPeriod',
    //                 ]);
    //     $projects = $projects->leftJoin(['financialTargets' => '('.$financialTargets.')'], 'financialTargets.project_id = project.id');
    //     $projects = $projects->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
    //     $projects = $projects->leftJoin(['maleEmployedTargets' => '('.$maleEmployedTargets.')'], 'maleEmployedTargets.project_id = project.id');
    //     $projects = $projects->leftJoin(['femaleEmployedTargets' => '('.$femaleEmployedTargets.')'], 'femaleEmployedTargets.project_id = project.id');
    //     $projects = $projects->leftJoin(['beneficiariesTargets' => '('.$beneficiariesTargets.')'], 'beneficiariesTargets.project_id = project.id');
    //     $projects = $projects->leftJoin(['financialAccompsQ1' => '('.$financialAccompsQ1.')'], 'financialAccompsQ1.project_id = project.id');
    //     $projects = $projects->leftJoin(['financialAccompsQ2' => '('.$financialAccompsQ2.')'], 'financialAccompsQ2.project_id = project.id');
    //     $projects = $projects->leftJoin(['financialAccompsQ3' => '('.$financialAccompsQ3.')'], 'financialAccompsQ3.project_id = project.id');
    //     $projects = $projects->leftJoin(['financialAccompsQ4' => '('.$financialAccompsQ4.')'], 'financialAccompsQ4.project_id = project.id');
    //     $projects = $projects->leftJoin(['physicalAccompsQ1' => '('.$physicalAccompsQ1.')'], 'physicalAccompsQ1.project_id = project.id');
    //     $projects = $projects->leftJoin(['physicalAccompsQ2' => '('.$physicalAccompsQ2.')'], 'physicalAccompsQ2.project_id = project.id');
    //     $projects = $projects->leftJoin(['physicalAccompsQ3' => '('.$physicalAccompsQ3.')'], 'physicalAccompsQ3.project_id = project.id');
    //     $projects = $projects->leftJoin(['physicalAccompsQ4' => '('.$physicalAccompsQ4.')'], 'physicalAccompsQ4.project_id = project.id');
    //     $projects = $projects->leftJoin(['personEmployedAccompsQ1' => '('.$personEmployedAccompsQ1.')'], 'personEmployedAccompsQ1.project_id = project.id');
    //     $projects = $projects->leftJoin(['personEmployedAccompsQ2' => '('.$personEmployedAccompsQ2.')'], 'personEmployedAccompsQ2.project_id = project.id');
    //     $projects = $projects->leftJoin(['personEmployedAccompsQ3' => '('.$personEmployedAccompsQ3.')'], 'personEmployedAccompsQ3.project_id = project.id');
    //     $projects = $projects->leftJoin(['personEmployedAccompsQ4' => '('.$personEmployedAccompsQ4.')'], 'personEmployedAccompsQ4.project_id = project.id');
    //     $projects = $projects->leftJoin(['beneficiariesAccompsQ1' => '('.$beneficiariesAccompsQ1.')'], 'beneficiariesAccompsQ1.project_id = project.id');
    //     $projects = $projects->leftJoin(['beneficiariesAccompsQ2' => '('.$beneficiariesAccompsQ2.')'], 'beneficiariesAccompsQ2.project_id = project.id');
    //     $projects = $projects->leftJoin(['beneficiariesAccompsQ3' => '('.$beneficiariesAccompsQ3.')'], 'beneficiariesAccompsQ3.project_id = project.id');
    //     $projects = $projects->leftJoin(['beneficiariesAccompsQ4' => '('.$beneficiariesAccompsQ4.')'], 'beneficiariesAccompsQ4.project_id = project.id');
    //     $projects = $projects->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');
    //     $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
    //     $projects = $projects->leftJoin('program', 'program.id = project.program_id');
    //     $projects = $projects->leftJoin('sector', 'sector.id = project.sector_id');
    //     $projects = $projects->leftJoin('sub_sector', 'sub_sector.id = project.sub_sector_id');
    //     $projects = $projects->leftJoin('fund_source', 'fund_source.id = project.fund_source_id');
    //     $projects = $projects->leftJoin('project_category', 'project_category.project_id = project.id and project_category.year = project.year');
    //     $projects = $projects->leftJoin('category', 'category.id = project_category.category_id');
    //     $projects = $projects->leftJoin('project_kra', 'project_kra.project_id = project.id and project_kra.year = project.year');
    //     $projects = $projects->leftJoin('key_result_area', 'key_result_area.id = project_kra.key_result_area_id');
    //     $projects = $projects->leftJoin('project_sdg_goal', 'project_sdg_goal.project_id = project.id and project_sdg_goal.year = project.year');
    //     $projects = $projects->leftJoin('sdg_goal', 'sdg_goal.id = project_sdg_goal.sdg_goal_id');
    //     $projects = $projects->leftJoin('project_rdp_chapter', 'project_rdp_chapter.project_id = project.id and project_rdp_chapter.year = project.year');
    //     $projects = $projects->leftJoin('rdp_chapter', 'rdp_chapter.id = project_rdp_chapter.rdp_chapter_id');
    //     $projects = $projects->leftJoin('project_rdp_chapter_outcome', 'project_rdp_chapter_outcome.project_id = project.id and project_rdp_chapter_outcome.year = project.year');
    //     $projects = $projects->leftJoin('rdp_chapter_outcome', 'rdp_chapter_outcome.id = project_rdp_chapter_outcome.rdp_chapter_outcome_id');
    //     $projects = $projects->leftJoin('project_rdp_sub_chapter_outcome', 'project_rdp_sub_chapter_outcome.project_id = project.id and project_rdp_sub_chapter_outcome.year = project.year');
    //     $projects = $projects->leftJoin('rdp_sub_chapter_outcome', 'rdp_sub_chapter_outcome.id = project_rdp_sub_chapter_outcome.rdp_sub_chapter_outcome_id');
    //     $projects = $projects->leftJoin('project_region', 'project_region.project_id = project.id and project_region.year = project.year');
    //     $projects = $projects->leftJoin('tblregion', 'tblregion.region_c = project_region.region_id');
    //     $projects = $projects->leftJoin('project_province', 'project_province.project_id = project.id and project_province.year = project.year');
    //     $projects = $projects->leftJoin('tblprovince', 'tblprovince.province_c = project_province.province_id');
    //     $projects = $projects->leftJoin('project_citymun', 'project_citymun.project_id = project.id and project_citymun.year = project.year');
    //     $projects = $projects->leftJoin('tblcitymun', 'tblcitymun.province_c = project_citymun.province_id and tblcitymun.citymun_c = project_citymun.citymun_id');
    //     $projects = $projects->andWhere(['project.year' => $model['year'], 'project.draft' => 'No']);

    //     $projects = $projects->orderBy(['projectTitle' => SORT_ASC])->asArray()->all();

        
    //     echo "<pre>"; print_r($projects); exit;

    //     return $this->renderAjax('form-two', [
    //         'type' => 'print',
    //         'projects' => $projects,
    //         'model' => $model
    //     ]);
    // }
}
