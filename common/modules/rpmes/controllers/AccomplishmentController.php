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
use common\modules\rpmes\models\SubmissionSearch;
use common\modules\rpmes\models\Model;
use common\modules\rpmes\models\MultipleModel;
use common\modules\rpmes\models\Submission;
use common\modules\rpmes\models\SubmissionLog;
use common\modules\rpmes\models\PhysicalAccomplishment;
use common\modules\rpmes\models\FinancialAccomplishment;
use common\modules\rpmes\models\PersonEmployedAccomplishment;
use common\modules\rpmes\models\BeneficiariesAccomplishment;
use common\modules\rpmes\models\GroupAccomplishment;
use common\modules\rpmes\models\ExpectedOutputAccomplishment;
use common\modules\rpmes\models\Accomplishment;
use common\modules\rpmes\models\PlanSearch;
use common\modules\rpmes\models\Settings;
use common\modules\rpmes\models\Acknowledgment;
use markavespiritu\user\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\db\Query;
use yii\helpers\Json;
use yii\data\Pagination;
use kartik\mpdf\Pdf;
use yii\web\Response;

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
                'only' => ['index', 'create', 'update', 'delete', 'view', 'accomplish-form', 'accomplish-oi'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'view', 'accomplish-form', 'accomplish-oi'],
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

        $categories = Category::find()->all();
        $categories = ArrayHelper::map($categories, 'id', 'title');

        $sectors = Sector::find()->all();
        $sectors = ArrayHelper::map($sectors, 'id', 'title');

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
            $categoryIDs = ProjectCategory::find();

            $categoryTitles = ProjectCategory::find()
                ->select(['project_id', 'GROUP_CONCAT(DISTINCT category.title ORDER BY category.title ASC SEPARATOR ", ") as title'])
                ->leftJoin('category', 'category.id = project_category.category_id')
                ->leftJoin('project', 'project.id = project_category.project_id')
                ->where(['project.draft' => 'No'])
                ->groupBy(['project_category.project_id'])
                ->createCommand()->getRawSql();

            if($model->category_id != '')
            {
                $categoryIDs = $categoryIDs->andWhere(['category_id' => $model->category_id]);
            }

            $categoryIDs = $categoryIDs->all();
            $categoryIDs = ArrayHelper::map($categoryIDs, 'project_id', 'project_id');

            if($model->scenario == 'accomplishmentUser'){

                $projectIDs = Yii::$app->user->can('AgencyUser') ? 
                        Submission::findOne(['year' => $model->year, 'agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C, 'report' => 'Monitoring Plan', 'draft' => 'No']) ?
                        Plan::find()
                        ->select(['project.id as id'])
                        ->leftJoin('project', 'project.id = plan.project_id')
                        ->where(['project.draft' => 'No', 'project.agency_id' => Yii::$app->user->identity->userinfo->AGENCY_C, 'plan.year' => $model->year])
                        ->all():
                        [] :
                        Plan::find()
                        ->select(['project.id as id'])
                        ->leftJoin('project', 'project.id = plan.project_id')
                        ->where(['project.draft' => 'No', 'project.agency_id' => $model->agency_id, 'plan.year' => $model->year])
                        ->all();

            }else{

                $projectIDs = Yii::$app->user->can('AgencyUser') ? 
                        Submission::findOne(['year' => $model->year, 'report' => 'Monitoring Plan', 'draft' => 'No']) ?
                        Plan::find()
                        ->select(['project.id as id'])
                        ->leftJoin('project', 'project.id = plan.project_id')
                        ->where(['project.draft' => 'No', 'plan.year' => $model->year]) :
                        [] :
                        Plan::find()
                        ->select(['project.id as id'])
                        ->leftJoin('project', 'project.id = plan.project_id')
                        ->where(['project.draft' => 'No', 'plan.year' => $model->year]);

                if($model->agency_id != '')
                    {
                        $projectIDs = $projectIDs->leftJoin('agency', 'agency.id = project.agency_id');
                        $projectIDs = $projectIDs->andWhere(['agency.id' => $model->agency_id]);
                    }

                if($model->sector_id != '')
                    {
                        $projectIDs = $projectIDs->leftJoin('sector', 'sector.id = project.sector_id');
                        $projectIDs = $projectIDs->andWhere(['sector.id' => $model->sector_id]);
                    }
                    
                if($model->category_id != '')
                    {
                        $projectIDs = $projectIDs->leftJoin('project_category', 'project_category.project_id = project.id');
                        $projectIDs = $projectIDs->andWhere(['project_category.category_id' => $model->category_id]);  
                    }

                $projectIDs = $projectIDs->all();
            }

            $projectIDs = !empty($projectIDs) ? ArrayHelper::map($projectIDs, 'id', 'id') : [];

            $projectsPaging = Project::find();
            $projectsPaging->andWhere(['id' => $projectIDs]);
            $countProjects = clone $projectsPaging;
            $projectsPages = new Pagination(['totalCount' => $countProjects->count()]);
            $projectsModels = $projectsPaging->offset($projectsPages->offset)
                ->limit($projectsPages->limit)
                ->orderBy(['id' => SORT_ASC])
                ->all();

                if($model->scenario == 'accomplishmentUser'){

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
                }else{

                    $projects =  Yii::$app->user->can('AgencyUser') ? 
                        Submission::findOne(['year' => $model->year, 'report' => 'Monitoring Plan', 'draft' => 'No']) ?
                        Plan::find()
                        ->leftJoin('project', 'project.id = plan.project_id')
                        ->where(['project.draft' => 'No', 'plan.year' => $model->year]) :
                        [] :
                        Plan::find()
                        ->leftJoin('project', 'project.id = plan.project_id')
                        ->where(['project.draft' => 'No', 'plan.year' => $model->year]);

                    if($model->sector_id != '')
                    {
                        $projects = $projects->leftJoin('sector', 'sector.id = project.sector_id');
                        $projects = $projects->andWhere(['sector.id' => $model->sector_id]);
                    }

                    if($model->agency_id != '')
                    {
                        $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
                        $projects = $projects->andWhere(['agency.id' => $model->agency_id]);
                    }

                    if($model->category_id != '')
                    {
                        $projects = $projects->leftJoin('project_category', 'project_category.project_id = project.id');
                        $projects = $projects->andWhere(['project_category.category_id' => $model->category_id]);  
                    }

                    $projects = $projects->all();
                }
            
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
                        ]) ;
                }
            }catch(\Exception $e){
                $transaction->rollBack();
            }
        }
        return $this->render('index',[
            'model' => $model,
            'years' => $years,
            'agencies' => $agencies,
            'categories' => $categories,
            'sectors' => $sectors,
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
    } */

    public function actionIndex()
    {
        $searchModel = new SubmissionSearch();
        $searchModel->report = 'Accomplishment';

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

    public function actionCreate()
    {

        $model = new Submission();
        $model->scenario = Yii::$app->user->can('Administrator') ? 'createAccomplishmentReportAdmin' : 'createAccomplishmentReport';

        $model->report = 'Accomplishment';
        $model->agency_id = Yii::$app->user->can('AgencyUser') ? Yii::$app->user->identity->userinfo->AGENCY_C : $model->agency_id;
        $model->draft = 'Yes';

        $agencies = Agency::find()->select(['id', 'concat(title," (",code,")") as title'])->orderBy(['title' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];

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
            'quarters' => $quarters,
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

        $model->scenario = Yii::$app->user->can('Administrator') ? 'createAccomplishmentReportAdmin' : 'createAccomplishmentReport';

        $agencies = Agency::find()->select(['id', 'concat(title," (",code,")") as title'])->orderBy(['title' => SORT_ASC])->asArray()->all();
        $agencies = ArrayHelper::map($agencies, 'id', 'title');

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];

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
            'quarters' => $quarters,
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

    public function actionView($id)
    {
        $model = Submission::findOne($id);

        if(!$model){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $planSubmission = Submission::findOne([
            'year' => $model->year,
            'agency_id' => $model->agency_id,
            'report' => 'Monitoring Plan',
            'draft' => 'No',
        ]);

        $dueDate = DueDate::findOne(['year' => $model->year, 'quarter' => $model->quarter, 'report' => 'Accomplishment']);

        $quarters = [
            'Q1', 
            'Q2', 
            'Q3', 
            'Q4'
        ];

        if(!Yii::$app->user->can('Administrator')){
            if($model->agency_id != Yii::$app->user->identity->userinfo->AGENCY_C){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        $searchModel = new PlanSearch();
        $searchModel->submission_id = $planSubmission ? $planSubmission->id : $searchModel->submission_id;

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dueDate' => $dueDate,
            'quarters' => $quarters,
        ]);
    }

    public function actionAccomplishForm($id)
    {
        $model = Submission::findOne($id);

        $planSubmission = Submission::findOne([
            'year' => $model->year,
            'agency_id' => $model->agency_id,
            'report' => 'Monitoring Plan',
            'draft' => 'No',
        ]);

        $dueDate = DueDate::findOne(['year' => $model->year, 'quarter' => $model->quarter, 'report' => 'Accomplishment']);

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];

        $nextQuarters = [
            'Q1' => [
                'Q2' => 'Q2',
                'Q3' => 'Q3',
                'Q4' => 'Q4',
            ],
            'Q2' => [
                'Q3' => 'Q3',
                'Q4' => 'Q4',
            ],
            'Q3' => [
                'Q4' => 'Q4',
            ],
            'Q4' => []
        ];

        if(!Yii::$app->user->can('Administrator')){
            if($model->agency_id != Yii::$app->user->identity->userinfo->AGENCY_C){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        if(!$planSubmission){
            throw new ForbiddenHttpException('No included projects in monitoring plan');
        }

        $accomplishments = [];

        $projectIDs = $planSubmission ? $planSubmission->plans ? ArrayHelper::map($planSubmission->plans, 'project_id', 'project_id') : [] : [];

        $projectsPaging = Plan::find();
        $projectsPaging 
            ->andWhere(['project_id' => $projectIDs])
            ->andWhere(['submission_id' => $planSubmission->id]);
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
                $financial = FinancialAccomplishment::findOne([
                    'project_id' => $plan->project_id,
                    'year' => $model->year,
                    'quarter' => $model->quarter
                ]) ? FinancialAccomplishment::findOne([
                    'project_id' => $plan->project_id,
                    'year' => $model->year,
                    'quarter' => $model->quarter
                ]) : new FinancialAccomplishment();

                $financial->project_id = $plan->project_id;
                $financial->year = $model->year;
                $financial->quarter = $model->quarter;

                $accomplishments[$plan->project_id]['financial'] = $financial;

                $physical = PhysicalAccomplishment::findOne([
                    'project_id' => $plan->project_id,
                    'year' => $model->year,
                    'quarter' => $model->quarter
                ]) ? PhysicalAccomplishment::findOne([
                    'project_id' => $plan->project_id,
                    'year' => $model->year,
                    'quarter' => $model->quarter
                ]) : new PhysicalAccomplishment();

                $physical->project_id = $plan->project_id;
                $physical->year = $model->year;
                $physical->quarter = $model->quarter;

                $accomplishments[$plan->project_id]['physical'] = $physical;

                $personEmployed = PersonEmployedAccomplishment::findOne([
                    'project_id' => $plan->project_id,
                    'year' => $model->year,
                    'quarter' => $model->quarter
                ]) ? PersonEmployedAccomplishment::findOne([
                    'project_id' => $plan->project_id,
                    'year' => $model->year,
                    'quarter' => $model->quarter
                ]) : new PersonEmployedAccomplishment();

                $personEmployed->project_id = $plan->project_id;
                $personEmployed->year = $model->year;
                $personEmployed->quarter = $model->quarter;

                $accomplishments[$plan->project_id]['personEmployed'] = $personEmployed;

                $accomplishment = Accomplishment::findOne([
                    'project_id' => $plan->project_id,
                    'year' => $model->year,
                    'quarter' => $model->quarter
                ]) ? Accomplishment::findOne([
                    'project_id' => $plan->project_id,
                    'year' => $model->year,
                    'quarter' => $model->quarter
                ]) : new Accomplishment();

                $accomplishment->project_id = $plan->project_id;
                $accomplishment->year = $model->year;
                $accomplishment->quarter = $model->quarter;

                $accomplishments[$plan->project_id]['accomplishment'] = $accomplishment;
            }
        }

        if(Yii::$app->request->post()){
            $postData = Yii::$app->request->post();
            $getData = Yii::$app->request->get();

            $financialModels = $postData['FinancialAccomplishment'];
            $physicalModels = $postData['PhysicalAccomplishment'];
            $personEmployedModels = $postData['PersonEmployedAccomplishment'];
            $accomplishmentModels = $postData['Accomplishment'];

            if(!empty($financialModels)){
                foreach($financialModels as $projectID => $financialModel){
                    $financialValue = $financialModel['financial'];

                    $financial = FinancialAccomplishment::findOne([
                        'project_id' => $projectID,
                        'year' => $model->year,
                        'quarter' => $model->quarter
                    ]) ? FinancialAccomplishment::findOne([
                        'project_id' => $projectID,
                        'year' => $model->year,
                        'quarter' => $model->quarter
                    ]) : new FinancialAccomplishment();
    
                    $financial->project_id = $projectID;
                    $financial->year = $model->year;
                    $financial->quarter = $model->quarter;
                    $financial->allocation = $this->removeMask($financialValue['allocation']);
                    $financial->releases = $this->removeMask($financialValue['releases']);
                    $financial->obligation = $this->removeMask($financialValue['obligation']);
                    $financial->expenditures = $this->removeMask($financialValue['expenditures']);
                    $financial->save(false);

                    if(!empty($nextQuarters)){
                        foreach($nextQuarters[$model->quarter] as $nextQuarter){
                            $financial = FinancialAccomplishment::findOne([
                                'project_id' => $projectID,
                                'year' => $model->year,
                                'quarter' => $nextQuarter
                            ]) ? FinancialAccomplishment::findOne([
                                'project_id' => $projectID,
                                'year' => $model->year,
                                'quarter' => $nextQuarter
                            ]) : new FinancialAccomplishment();
            
                            $financial->project_id = $projectID;
                            $financial->year = $model->year;
                            $financial->quarter = $nextQuarter;
                            $financial->allocation = $this->removeMask($financialValue['allocation']);
                            $financial->releases = $this->removeMask($financialValue['releases']);
                            $financial->obligation = $this->removeMask($financialValue['obligation']);
                            $financial->expenditures = $this->removeMask($financialValue['expenditures']);
                            $financial->save(false);
                        }
                    }
                }
            }

            if(!empty($physicalModels)){
                foreach($physicalModels as $projectID => $physicalModel){
                    $physicalValue = $physicalModel['physical'];

                    $physical = PhysicalAccomplishment::findOne([
                        'project_id' => $projectID,
                        'year' => $model->year,
                        'quarter' => $model->quarter
                    ]) ? PhysicalAccomplishment::findOne([
                        'project_id' => $projectID,
                        'year' => $model->year,
                        'quarter' => $model->quarter
                    ]) : new PhysicalAccomplishment();
    
                    $physical->project_id = $projectID;
                    $physical->year = $model->year;
                    $physical->quarter = $model->quarter;
                    $physical->value = $this->removeMask($physicalValue['value']);
                    $physical->save(false);

                    if(!empty($nextQuarters)){
                        foreach($nextQuarters[$model->quarter] as $nextQuarter){
                            $physical = PhysicalAccomplishment::findOne([
                                'project_id' => $projectID,
                                'year' => $model->year,
                                'quarter' => $nextQuarter
                            ]) ? PhysicalAccomplishment::findOne([
                                'project_id' => $projectID,
                                'year' => $model->year,
                                'quarter' => $nextQuarter
                            ]) : new PhysicalAccomplishment();
            
                            $physical->project_id = $projectID;
                            $physical->year = $model->year;
                            $physical->quarter = $nextQuarter;
                            $physical->value = $this->removeMask($physicalValue['value']);
                            $physical->save(false);
                        }
                    }
                }
            }

            if(!empty($personEmployedModels)){
                foreach($personEmployedModels as $projectID => $personEmployedModel){
                    $personEmployedValue = $personEmployedModel['personEmployed'];

                    $personEmployed = PersonEmployedAccomplishment::findOne([
                        'project_id' => $projectID,
                        'year' => $model->year,
                        'quarter' => $model->quarter
                    ]) ? PersonEmployedAccomplishment::findOne([
                        'project_id' => $projectID,
                        'year' => $model->year,
                        'quarter' => $model->quarter
                    ]) : new PersonEmployedAccomplishment();
    
                    $personEmployed->project_id = $projectID;
                    $personEmployed->year = $model->year;
                    $personEmployed->quarter = $model->quarter;
                    $personEmployed->male = $this->removeMask($personEmployedValue['male']);
                    $personEmployed->female = $this->removeMask($personEmployedValue['female']);
                    $personEmployed->save(false);

                    if(!empty($nextQuarters)){
                        foreach($nextQuarters[$model->quarter] as $nextQuarter){
                            $personEmployed = PersonEmployedAccomplishment::findOne([
                                'project_id' => $projectID,
                                'year' => $model->year,
                                'quarter' => $nextQuarter
                            ]) ? PersonEmployedAccomplishment::findOne([
                                'project_id' => $projectID,
                                'year' => $model->year,
                                'quarter' => $nextQuarter
                            ]) : new PersonEmployedAccomplishment();
            
                            $personEmployed->project_id = $projectID;
                            $personEmployed->year = $model->year;
                            $personEmployed->quarter = $nextQuarter;
                            $personEmployed->male = $this->removeMask($personEmployedValue['male']);
                            $personEmployed->female = $this->removeMask($personEmployedValue['female']);
                            $personEmployed->save(false);
                        }
                    }
                }
            }

            if(!empty($accomplishmentModels)){
                foreach($accomplishmentModels as $projectID => $accomplishmentModel){
                    $accomplishmentValue = $accomplishmentModel['accomplishment'];

                    $accomplishment = Accomplishment::findOne([
                        'project_id' => $projectID,
                        'year' => $model->year,
                        'quarter' => $model->quarter
                    ]) ? Accomplishment::findOne([
                        'project_id' => $projectID,
                        'year' => $model->year,
                        'quarter' => $model->quarter
                    ]) : new Accomplishment();
    
                    $accomplishment->project_id = $projectID;
                    $accomplishment->year = $model->year;
                    $accomplishment->quarter = $model->quarter;
                    $accomplishment->action = $accomplishmentValue['action'];
                    $accomplishment->remarks = $accomplishmentValue['remarks'];
                    $accomplishment->submitted_by = Yii::$app->user->id;
                    $accomplishment->save(false);

                    if(!empty($nextQuarters)){
                        foreach($nextQuarters[$model->quarter] as $nextQuarter){
                            $accomplishment = Accomplishment::findOne([
                                'project_id' => $projectID,
                                'year' => $model->year,
                                'quarter' => $nextQuarter
                            ]) ? Accomplishment::findOne([
                                'project_id' => $projectID,
                                'year' => $model->year,
                                'quarter' => $nextQuarter
                            ]) : new Accomplishment();
            
                            $accomplishment->project_id = $projectID;
                            $accomplishment->year = $model->year;
                            $accomplishment->quarter = $nextQuarter;
                            $accomplishment->action = $accomplishmentValue['action'];
                            $accomplishment->remarks = $accomplishmentValue['remarks'];
                            $accomplishment->submitted_by = Yii::$app->user->id;
                            $accomplishment->save(false);
                        }
                    }
                }
            }

            \Yii::$app->getSession()->setFlash('success', 'Accomplishments were saved successfully');
                    return isset($getData['page']) ? 
                        $this->redirect(['accomplish-form', 
                            'id' => $model->id, 
                            'page' => $getData['page'],
                        ]) : $this->redirect(['accomplish-form', 
                            'id' => $model->id,
                        ]);
        }
        
        return $this->render('accomplishment-form', [
            'model' => $model,
            'projectsModels' => $projectsModels,
            'projectsPages' => $projectsPages,
            'quarters' => $quarters,
            'accomplishments' => $accomplishments,
            'dueDate' => $dueDate,
        ]);
    }

    public function actionAccomplishOi($id)
    {
        $model = Submission::findOne($id);

        $planSubmission = Submission::findOne([
            'year' => $model->year,
            'agency_id' => $model->agency_id,
            'report' => 'Monitoring Plan',
            'draft' => 'No',
        ]);

        $dueDate = DueDate::findOne(['year' => $model->year, 'quarter' => $model->quarter, 'report' => 'Accomplishment']);

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];

        $nextQuarters = [
            'Q1' => [
                'Q2' => 'Q2',
                'Q3' => 'Q3',
                'Q4' => 'Q4',
            ],
            'Q2' => [
                'Q3' => 'Q3',
                'Q4' => 'Q4',
            ],
            'Q3' => [
                'Q4' => 'Q4',
            ],
            'Q4' => []
        ];

        if(!Yii::$app->user->can('Administrator')){
            if($model->agency_id != Yii::$app->user->identity->userinfo->AGENCY_C){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        if(!$planSubmission){
            throw new ForbiddenHttpException('No included projects in monitoring plan');
        }

        $outputIndicators = [];

        $projectIDs = $planSubmission ? $planSubmission->plans ? ArrayHelper::map($planSubmission->plans, 'project_id', 'project_id') : [] : [];

        $projectsPaging = Plan::find();
        $projectsPaging 
            ->andWhere(['project_id' => $projectIDs])
            ->andWhere(['submission_id' => $planSubmission->id]);
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
                $expectedOutputs = $plan->project->getProjectExpectedOutputs()->where([
                    'year' => $model->year
                ])
                ->orderBy(['id' => SORT_ASC])
                ->all();

                if($expectedOutputs){
                    foreach($expectedOutputs as $eo){
                        $expectedOutput = ExpectedOutputAccomplishment::findOne([
                            'project_id' => $plan->project_id,
                            'expected_output_id' => $eo->id,
                            'year' => $model->year,
                            'quarter' => $model->quarter
                        ]) ? ExpectedOutputAccomplishment::findOne([
                            'project_id' => $plan->project_id,
                            'expected_output_id' => $eo->id,
                            'year' => $model->year,
                            'quarter' => $model->quarter
                        ]) : new ExpectedOutputAccomplishment();
                        
                        $expectedOutput->scenario = $eo->indicator == 'number of individual beneficiaries served' ? 'individual' : 'notIndividual';
                        $expectedOutput->project_id = $plan->project_id;
                        $expectedOutput->expected_output_id = $eo->id;
                        $expectedOutput->year = $model->year;
                        $expectedOutput->quarter = $model->quarter;

                        $outputIndicators[$plan->project_id][$eo->id] = $expectedOutput;
                    }
                }
            }
        }

        if(Yii::$app->request->post()){
            $postData = Yii::$app->request->post();
            $getData = Yii::$app->request->get();

            $projectModels = $postData['ExpectedOutputAccomplishment'];

            if(!empty($projectModels)){
                foreach($projectModels as $projectID => $eoModels){
                    if(!empty($eoModels)){
                        foreach($eoModels as $eoID => $eoModel){
                            $expectedOutput = ExpectedOutputAccomplishment::findOne([
                                'project_id' => $projectID,
                                'expected_output_id' => $eoID,
                                'year' => $model->year,
                                'quarter' => $model->quarter
                            ]) ? ExpectedOutputAccomplishment::findOne([
                                'project_id' => $projectID,
                                'expected_output_id' => $eoID,
                                'year' => $model->year,
                                'quarter' => $model->quarter
                            ]) : new ExpectedOutputAccomplishment();

                            $expectedOutput->project_id = $projectID;
                            $expectedOutput->expected_output_id = $eoID;
                            $expectedOutput->year = $model->year;
                            $expectedOutput->quarter = $model->quarter;
                            $expectedOutput->value = isset($eoModel['value']) ? $this->removeMask($eoModel['value']) : 0;
                            $expectedOutput->male = isset($eoModel['male']) ? $this->removeMask($eoModel['male']) : 0;
                            $expectedOutput->female = isset($eoModel['female']) ? $this->removeMask($eoModel['female']) : 0;
                            $expectedOutput->save(false);

                            if(!empty($nextQuarters)){
                                foreach($nextQuarters[$model->quarter] as $nextQuarter){
                                    $expectedOutput = ExpectedOutputAccomplishment::findOne([
                                        'project_id' => $projectID,
                                        'expected_output_id' => $eoID,
                                        'year' => $model->year,
                                        'quarter' => $nextQuarter
                                    ]) ? ExpectedOutputAccomplishment::findOne([
                                        'project_id' => $projectID,
                                        'expected_output_id' => $eoID,
                                        'year' => $model->year,
                                        'quarter' => $nextQuarter
                                    ]) : new ExpectedOutputAccomplishment();
        
                                    $expectedOutput->project_id = $projectID;
                                    $expectedOutput->expected_output_id = $eoID;
                                    $expectedOutput->year = $model->year;
                                    $expectedOutput->quarter = $nextQuarter;
                                    $expectedOutput->value = isset($eoModel['value']) ? $this->removeMask($eoModel['value']) : 0;
                                    $expectedOutput->male = isset($eoModel['male']) ? $this->removeMask($eoModel['male']) : 0;
                                    $expectedOutput->female = isset($eoModel['female']) ? $this->removeMask($eoModel['female']) : 0;
                                    $expectedOutput->save(false);
                                }
                            }
                        }
                    }
                }
            }

            \Yii::$app->getSession()->setFlash('success', 'Accomplishment Form 2 OI/s were saved successfully');
                    return isset($getData['page']) ? 
                        $this->redirect(['accomplish-oi', 
                            'id' => $model->id, 
                            'page' => $getData['page'],
                        ]) : $this->redirect(['accomplish-oi', 
                            'id' => $model->id,
                        ]);
        }
        
        return $this->render('accomplishment-oi-form', [
            'model' => $model,
            'projectsModels' => $projectsModels,
            'projectsPages' => $projectsPages,
            'quarters' => $quarters,
            'outputIndicators' => $outputIndicators,
            'dueDate' => $dueDate,
        ]);
    }

    public function actionOutputIndicator($id, $plan_id)
    {
        $model = Submission::findOne($id);

        $plan = Plan::findOne($plan_id);

        $expectedOutputs = $plan->project->getProjectExpectedOutputs()->where(['year' => $model->year])->all();

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

    public function actionSubmit($id)
    {
        if(Yii::$app->request->post())
        {
            $model = Submission::findOne($id);

            $postData = Yii::$app->request->post();

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
                \Yii::$app->getSession()->setFlash('success', 'Accomplishment Report for '.$model->quarter.' '.$model->year.' has been submitted successfully.');
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

        $planSubmission = Submission::findOne([
            'year' => $model->year,
            'agency_id' => $model->agency_id,
            'report' => 'Monitoring Plan',
            'draft' => 'No',
        ]);

        $projectIDs = $planSubmission ? $planSubmission->plans ? ArrayHelper::map($planSubmission->plans, 'project_id', 'project_id') : [] : [];

        $projectIDs = ArrayHelper::map($planSubmission->plans, 'project_id', 'project_id');

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
        
        $fundingSourceTitles = ProjectHasFundSources::find()
                    ->select([
                        'phfs.project_id',
                        'GROUP_CONCAT(DISTINCT CONCAT(row_number, ". ", fund_source.title, " ", phfs.type) ORDER BY phfs.id ASC SEPARATOR "<br>") as title'
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
            'year' => $model->year,
            'quarter' => $model->quarter,
            'project_id' => $projectIDs
        ])
        ->createCommand()->getRawSql();

        $physicalAccomplishment = PhysicalAccomplishment::find()->where([
            'year' => $model->year,
            'quarter' => $model->quarter,
            'project_id' => $projectIDs
        ])
        ->createCommand()->getRawSql();

        $personEmployedAccomplishment = PersonEmployedAccomplishment::find()->where([
            'year' => $model->year,
            'quarter' => $model->quarter,
            'project_id' => $projectIDs
        ])
        ->createCommand()->getRawSql();

        $actualOwpa = 'IF(physicalTargets.type = "Numerical", 
                            IF('.$physicalTotal.' > 0,
                                (COALESCE(physicalAccomplishment.value, 0)/'.$physicalTotal.')*100, 
                            0), 
                        COALESCE(physicalAccomplishment.value,0))';

        $remarks = Accomplishment::find()->where([
            'year' => $model->year,
            'quarter' => $model->quarter,
            'project_id' => $projectIDs
        ])
        ->createCommand()->getRawSql();
        
        $projects = Project::find()
                    ->select([
                        'project.id',
                        'project.project_no as project_no',
                        'project.title as title',
                        'DATE_FORMAT(project.start_date, "%m-%d-%y") as startDate',
                        'DATE_FORMAT(project.completion_date, "%m-%d-%y") as endDate',
                        'fundingSourceTitles.title as fundingSourceTitle',
                        'fundingAgencyTitles.title as fundingAgencyTitle',
                        'COALESCE(project.cost, 0) as cost',
                        'maleEmployedTargets.annual as maleEmployedTotal',
                        'femaleEmployedTargets.annual as femaleEmployedTotal',
                        'outputIndicatorTitles.title as outputIndicatorTitle',
                        'COALESCE('.$financialTotal.', 0) as financialTotal',
                        'COALESCE('.$physicalTotal.', 0) as physicalTotal',
                        $targetOwpa[$model->quarter].' as targetOwpa',
                        $actualOwpa.' as actualOwpa',
                        'COALESCE('.$actualOwpa.', 0) - COALESCE('.$targetOwpa[$model->quarter].', 0) as slippage',
                        'COALESCE(financialAccomplishment.allocation, 0) as appropriations',
                        'COALESCE(financialAccomplishment.releases, 0) as allotment',
                        'COALESCE(financialAccomplishment.obligation, 0) as obligations',
                        'COALESCE(financialAccomplishment.expenditures, 0) as disbursements',
                        'COALESCE(personEmployedAccomplishment.male, 0) as maleEmployed',
                        'COALESCE(personEmployedAccomplishment.female, 0) as femaleEmployed',
                        'remarks.remarks as remarks'
                    ]);

        $projects = $projects->leftJoin(['financialTargets' => '('.$financialTargets.')'], 'financialTargets.project_id = project.id');
        $projects = $projects->leftJoin(['physicalTargets' => '('.$physicalTargets.')'], 'physicalTargets.project_id = project.id');
        $projects = $projects->leftJoin(['maleEmployedTargets' => '('.$maleEmployedTargets.')'], 'maleEmployedTargets.project_id = project.id');
        $projects = $projects->leftJoin(['femaleEmployedTargets' => '('.$femaleEmployedTargets.')'], 'femaleEmployedTargets.project_id = project.id');
        $projects = $projects->leftJoin(['fundingSourceTitles' => '('.$fundingSourceTitles.')'], 'fundingSourceTitles.project_id = project.id');
        $projects = $projects->leftJoin(['fundingAgencyTitles' => '('.$fundingAgencyTitles.')'], 'fundingAgencyTitles.project_id = project.id');
        $projects = $projects->leftJoin(['outputIndicatorTitles' => '('.$outputIndicatorTitles.')'], 'outputIndicatorTitles.project_id = project.id');
        $projects = $projects->leftJoin(['financialAccomplishment' => '('.$financialAccomplishment.')'], 'financialAccomplishment.project_id = project.id');
        $projects = $projects->leftJoin(['physicalAccomplishment' => '('.$physicalAccomplishment.')'], 'physicalAccomplishment.project_id = project.id');
        $projects = $projects->leftJoin(['personEmployedAccomplishment' => '('.$personEmployedAccomplishment.')'], 'personEmployedAccomplishment.project_id = project.id');                                                           
        $projects = $projects->leftJoin(['remarks' => '('.$remarks.')'], 'remarks.project_id = project.id');
        $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
        $projects = $projects->andWhere(['project.draft' => 'No']);
        $projects = $projects->andWhere(['project.source_id' => null]);
        $projects = $projects->andWhere(['project.id' => $projectIDs]);
        $projects = $projects 
                    ->asArray()
                    ->all();
                    
        $outputIndicatorAccomplishments = ExpectedOutputAccomplishment::find()->where([
            'year' => $model->year,
            'quarter' => $model->quarter,
            'project_id' => $projectIDs
        ])
        ->createCommand()
        ->getRawSql();

        $endOfProjectTarget = 'COALESCE(project_expected_output.baseline, 0) + ';
        foreach($months as $mo => $month){
            $endOfProjectTarget .= 'project_expected_output.'.$mo.' + ';
        }

        $endOfProjectTarget = rtrim($endOfProjectTarget, '+ ');

        $oiTargetsQuarterly = [];

        foreach($quarters as $q => $mos){
            $oiTargetsQuarterly[$q] = '';
            $oiTargetsQuarterly[$q] .= $q == 'Q1' ? 'COALESCE(project_expected_output.baseline, 0) + ' : '';
            foreach($mos as $m => $mo){
                $oiTargetsQuarterly[$q] .= 'project_expected_output.'.$m.' + ';
            }

            $oiTargetsQuarterly[$q] = rtrim($oiTargetsQuarterly[$q], '+ ');
        }

        $oiTargetsQuarterly['Q2'] = $oiTargetsQuarterly['Q1'].' + '.$oiTargetsQuarterly['Q2'];
        $oiTargetsQuarterly['Q3'] = $oiTargetsQuarterly['Q1'].' + '.$oiTargetsQuarterly['Q2'].' + '.$oiTargetsQuarterly['Q3'];
        $oiTargetsQuarterly['Q4'] = $oiTargetsQuarterly['Q1'].' + '.$oiTargetsQuarterly['Q2'].' + '.$oiTargetsQuarterly['Q3'].' + '.$oiTargetsQuarterly['Q4'];

        $outputIndicatorTargets = ProjectExpectedOutput::find()
            ->select([
                'project_expected_output.project_id',
                'project_expected_output.indicator',
                'COALESCE('.$endOfProjectTarget.', 0) as endOfProjectTarget',
                'COALESCE('.$oiTargetsQuarterly[$model->quarter].', 0) as target',
                'IF(project_expected_output.indicator = "number of individual beneficiaries served", 
                    COALESCE(outputIndicatorAccomplishments.male, 0) + 
                    COALESCE(outputIndicatorAccomplishments.female, 0), 
                    COALESCE(outputIndicatorAccomplishments.value, 0)
                ) as actual'
            ]);
            
        $outputIndicatorTargets = $outputIndicatorTargets->leftJoin(['outputIndicatorAccomplishments' => '('.$outputIndicatorAccomplishments.')'], 'outputIndicatorAccomplishments.expected_output_id = project_expected_output.id'
    );   
        
        $outputIndicatorTargets = $outputIndicatorTargets->andWhere(['project_expected_output.year' => $model->year]);
        $outputIndicatorTargets = $outputIndicatorTargets->andWhere(['project_expected_output.project_id' => $projectIDs]);
        
        $outputIndicatorTargets = $outputIndicatorTargets 
                    ->orderBy(['project_expected_output.id' => SORT_ASC])
                    ->asArray()
                    ->all();

        $ois = [];

        if(!empty($outputIndicatorTargets)){
            foreach($outputIndicatorTargets as $target){
                $ois[$target['project_id']][] = $target;
            }
        }
        
        $filename = date("YmdHis").'_'.$model->agency->code.'_'.$model->quarter.'_'.$model->year.'_'.'RPMES_Form_2';

        if($type == 'excel')
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=".$filename.".xls");
            return $this->renderPartial('_report-file', [
                'model' => $model,
                'type' => $type,
                'projects' => $projects,
                'ois' => $ois
            ]);
        }else if($type == 'pdf')
        {
            $content = $this->renderPartial('_report-file', [
                'model' => $model,
                'type' => $type,
                'projects' => $projects,
                'ois' => $ois
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
            return $this->renderAjax('_report-file', [
                'model' => $model,
                'type' => $type,
                'projects' => $projects,
                'ois' => $ois
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