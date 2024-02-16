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
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index','downloadAccomplishment'],
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
        if(!Yii::$app->user->can('AgencyUser')){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

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

        $model->delete();

        \Yii::$app->getSession()->setFlash('success', 'Record deleted');

        return $this->redirect(['index']);
    }

    public function actionView($id)
    {
        $model = Submission::findOne($id);

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
                    $accomplishment->remarks = $accomplishmentValue['remarks'];
                    $accomplishment->submitted_by = Yii::$app->user->id;
                    $accomplishment->save(false);
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
                    \Yii::$app->getSession()->setFlash('success', 'Accomplishment Report for '.$model->quarter.' '.$model->year.' has been submitted successfully.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }
    }

    public function actionDownloadAccomplishment($type, $model, $year, $quarter, $agency_id, $category_id, $sector_id)
    {
        $model = $type == 'print' ? json_decode(str_replace('\'', '"', $model), true) : json_decode($model, true);
        $model = (object) $model;
        $model->year = $year;
        $model->quarter = $quarter;
        $model->agency_id = $agency_id;
        $model->category_id = $category_id;
        $model->sector_id = $sector_id;

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];
        $genders = ['M' => 'Male', 'F' => 'Female'];

        $projectIDs = Plan::find()->select(['project_id'])->where(['year' => $model->year])->asArray()->all();
        $projectIDs = ArrayHelper::map($projectIDs, 'project_id', 'project_id');

        $financialTargets = ProjectTarget::find()->where(['target_type' => 'Financial', 'year' => $model->year])->createCommand()->getRawSql();
        $financialAccomps = FinancialAccomplishment::find()->where(['year' => $model->year])->createCommand()->getRawSql();
        $physicalTargets = ProjectTarget::find()->where(['target_type' => 'Physical', 'year' => $model->year])->createCommand()->getRawSql();
        $physicalAccomps = PhysicalAccomplishment::find()->where(['year' => $model->year])->createCommand()->getRawSql();
        $maleEmployedTargets = ProjectTarget::find()->where(['target_type' => 'Male Employed', 'year' => $model->year])->createCommand()->getRawSql();
        $femaleEmployedTargets = ProjectTarget::find()->where(['target_type' => 'Female Employed', 'year' => $model->year])->createCommand()->getRawSql();
        $personEmployedAccomps = PersonEmployedAccomplishment::find()->where(['year' => $model->year])->createCommand()->getRawSql();
        $beneficiariesTargets = ProjectTarget::find()->where(['target_type' => 'Beneficiaries', 'year' => $model->year])->createCommand()->getRawSql();
        $groupBeneficiariesTargets = ProjectTarget::find()->where(['target_type' => 'Group Beneficiaries', 'year' => $model->year])->createCommand()->getRawSql();
        $beneficiariesAccomps = BeneficiariesAccomplishment::find()->where(['year' => $model->year])->createCommand()->getRawSql();
        $groupBeneficiariesAccomps = GroupAccomplishment::find()->where(['year' => $model->year])->createCommand()->getRawSql();
        $accompsSubmitter = Accomplishment::find()->where(['year' => $model->year, 'quarter' => $model->quarter])->createCommand()->getRawSql();
        $accomplishmentPerQuarter = Accomplishment::find()->where(['year' => $model->year, 'quarter' => $model->quarter])->createCommand()->getRawSql();
        $accomps = Accomplishment::find()->select(['project_id', 'IF(sum(COALESCE(action, 0)) > 0, 1, 0) as isCompleted'])->where(['year' => $model->year])->groupBy(['project_id'])->createCommand()->getRawSql();
        $categoryIDs = ProjectCategory::find();

        $categoryTitles = ProjectCategory::find()
            ->select(['project_id', 'GROUP_CONCAT(DISTINCT category.title ORDER BY category.title ASC SEPARATOR ", ") as title'])
            ->leftJoin('category', 'category.id = project_category.category_id')
            ->leftJoin('project', 'project.id = project_category.project_id')
            ->where(['project.draft' => 'No'])
            ->groupBy(['project_category.project_id'])
            ->createCommand()->getRawSql();

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

        $submitterName = Accomplishment::find()
            ->select(['project_id', 'CONCAT(user_info.FIRST_M, " ", user_info.LAST_M) as name'])
            ->leftJoin('user_info', 'user_info.user_id = accomplishment.submitted_by')
            ->leftJoin('project', 'project.id = accomplishment.project_id')
            ->where(['project.draft' => 'No', 'accomplishment.year' => $model->year, 'accomplishment.quarter' => $model->quarter])
            ->groupBy(['accomplishment.project_id'])
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

        $maleEmployedTarget = 'IF("'.$model->quarter.'" = "Q1", COALESCE(maleEmployedTargets.q1, 0),
                                    IF("'.$model->quarter.'" = "Q2", COALESCE(maleEmployedTargets.q2, 0),
                                        IF("'.$model->quarter.'" = "Q3", COALESCE(maleEmployedTargets.q3, 0),
                                        COALESCE(maleEmployedTargets.q4, 0)
                                        )
                                    )
                                )';
            
        $femaleEmployedTarget = 'IF("'.$model->quarter.'" = "Q1", COALESCE(femaleEmployedTargets.q1, 0),
                                    IF("'.$model->quarter.'" = "Q2", COALESCE(femaleEmployedTargets.q2, 0),
                                        IF("'.$model->quarter.'" = "Q3", COALESCE(femaleEmployedTargets.q3, 0),
                                        COALESCE(femaleEmployedTargets.q4, 0)
                                        )
                                    )
                                )';

        $maleEmployedAccomp = 'IF("'.$model->quarter.'" = "Q1", COALESCE(personEmployedAccompsQ1.male, 0),
                                IF("'.$model->quarter.'" = "Q2", COALESCE(personEmployedAccompsQ1.male, 0) + COALESCE(personEmployedAccompsQ2.male, 0),
                                    IF("'.$model->quarter.'" = "Q3", COALESCE(personEmployedAccompsQ1.male, 0) + COALESCE(personEmployedAccompsQ2.male, 0) + COALESCE(personEmployedAccompsQ3.male, 0),
                                    COALESCE(personEmployedAccompsQ1.male, 0) + COALESCE(personEmployedAccompsQ2.male, 0) + COALESCE(personEmployedAccompsQ3.male, 0) + COALESCE(personEmployedAccompsQ4.male, 0)
                                    )
                                )
                            )';

        $femaleEmployedAccomp = 'IF("'.$model->quarter.'" = "Q1", COALESCE(personEmployedAccompsQ1.female, 0),
                                    IF("'.$model->quarter.'" = "Q2", COALESCE(personEmployedAccompsQ1.female, 0) + COALESCE(personEmployedAccompsQ2.female, 0),
                                        IF("'.$model->quarter.'" = "Q3", COALESCE(personEmployedAccompsQ1.female, 0) + COALESCE(personEmployedAccompsQ2.female, 0) + COALESCE(personEmployedAccompsQ3.female, 0),
                                        COALESCE(personEmployedAccompsQ1.female, 0) + COALESCE(personEmployedAccompsQ2.female, 0) + COALESCE(personEmployedAccompsQ3.female, 0) + COALESCE(personEmployedAccompsQ4.female, 0)
                                        )
                                    )
                                )';

        $beneficiaryTarget = 'IF("'.$model->quarter.'" = "Q1", COALESCE(beneficiariesTargets.q1, 0),
                                IF("'.$model->quarter.'" = "Q2", COALESCE(beneficiariesTargets.q2, 0),
                                    IF("'.$model->quarter.'" = "Q3", COALESCE(beneficiariesTargets.q3, 0),
                                    COALESCE(beneficiariesTargets.q4, 0)
                                    )
                                )
                            )';
        
        $groupBeneficiaryTarget = 'IF("'.$model->quarter.'" = "Q1", COALESCE(groupBeneficiariesTargets.q1, 0),
                                IF("'.$model->quarter.'" = "Q2", COALESCE(groupBeneficiariesTargets.q2, 0),
                                    IF("'.$model->quarter.'" = "Q3", COALESCE(groupBeneficiariesTargets.q3, 0),
                                    COALESCE(groupBeneficiariesTargets.q4, 0)
                                    )
                                )
                            )';

        $maleBeneficiaryAccomp = 'IF("'.$model->quarter.'" = "Q1", COALESCE(beneficiariesAccompsQ1.male, 0),
                            IF("'.$model->quarter.'" = "Q2", COALESCE(beneficiariesAccompsQ1.male, 0) + COALESCE(beneficiariesAccompsQ2.male, 0),
                                IF("'.$model->quarter.'" = "Q3", COALESCE(beneficiariesAccompsQ1.male, 0) + COALESCE(beneficiariesAccompsQ2.male, 0) + COALESCE(beneficiariesAccompsQ3.male, 0),
                                COALESCE(beneficiariesAccompsQ1.male, 0) + COALESCE(beneficiariesAccompsQ2.male, 0) + COALESCE(beneficiariesAccompsQ3.male, 0) + COALESCE(beneficiariesAccompsQ4.male, 0)
                                )
                            )
                        )';
    
        $femaleBeneficiaryAccomp = 'IF("'.$model->quarter.'" = "Q1", COALESCE(beneficiariesAccompsQ1.female, 0),
                                IF("'.$model->quarter.'" = "Q2", COALESCE(beneficiariesAccompsQ1.female, 0) + COALESCE(beneficiariesAccompsQ2.female, 0),
                                    IF("'.$model->quarter.'" = "Q3", COALESCE(beneficiariesAccompsQ1.female, 0) + COALESCE(beneficiariesAccompsQ2.female, 0) + COALESCE(beneficiariesAccompsQ3.female, 0),
                                    COALESCE(beneficiariesAccompsQ1.female, 0) + COALESCE(beneficiariesAccompsQ2.female, 0) + COALESCE(beneficiariesAccompsQ3.female, 0) + COALESCE(beneficiariesAccompsQ4.female, 0)
                                    )
                                )
                            )';

        $groupBeneficiaryAccomp = 'IF("'.$model->quarter.'" = "Q1", COALESCE(groupBeneficiariesAccompsQ1.value, 0),
                                IF("'.$model->quarter.'" = "Q2", COALESCE(groupBeneficiariesAccompsQ1.value, 0) + COALESCE(groupBeneficiariesAccompsQ2.value, 0),
                                    IF("'.$model->quarter.'" = "Q3", COALESCE(groupBeneficiariesAccompsQ1.value, 0) + COALESCE(groupBeneficiariesAccompsQ2.value, 0) + COALESCE(groupBeneficiariesAccompsQ3.value, 0),
                                    COALESCE(groupBeneficiariesAccompsQ1.value, 0) + COALESCE(groupBeneficiariesAccompsQ2.value, 0) + COALESCE(groupBeneficiariesAccompsQ3.value, 0) + COALESCE(groupBeneficiariesAccompsQ4.value, 0)
                                    )
                                )
                            )';
        $financialTargetPerQuarter = 'IF("'.$model->quarter.'" = "Q1", COALESCE(financialTargets.q1, 0),
                            IF("'.$model->quarter.'" = "Q2", COALESCE(financialTargets.q2, 0),
                                IF("'.$model->quarter.'" = "Q3", COALESCE(financialTargets.q3, 0),
                                COALESCE(financialTargets.q4, 0)
                                )
                            )
                        )';
        $isCompleted = 'COALESCE(accomps.isCompleted, 0)';

        $categoryIDs = $categoryIDs->all();
        $categoryIDs = ArrayHelper::map($categoryIDs, 'project_id', 'project_id');

        $projects = Project::find()
                    ->select([
                        'project.id',
                        'project.data_type as dataType',
                        'project.project_no as projectNo',
                        'project.title as projectTitle',
                        'IF(barangayTitles.title is null, IF(citymunTitles.title is null, IF(provinceTitles.title is null, IF(regionTitles.title is null, "No location", regionTitles.title), provinceTitles.title), citymunTitles.title), barangayTitles.title) as locationTitle',
                        'submitterName.name as submitterName',
                        'project.start_date as startDate',
                        'project.completion_date as completionDate',
                        'fund_source.title as fundSourceTitle',
                        'categoryTitles.title as categoryTitle',
                        'agency.code as agencyTitle',
                        'sector.title as sectorTitle',
                        'accomplishmentPerQuarter.remarks as remarks',
                        'accomplishmentPerQuarter.date_submitted as date_submitted',
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
                        $maleEmployedTarget.' as malesEmployedTarget',
                        $femaleEmployedTarget.' as femalesEmployedTarget',
                        $maleEmployedAccomp.' as malesEmployedActual',
                        $femaleEmployedAccomp.' as femalesEmployedActual',
                        $beneficiaryTarget.' as beneficiariesTarget',
                        $groupBeneficiaryTarget.' as groupBeneficiariesTarget',
                        $maleBeneficiaryAccomp.' as maleBeneficiariesActual',
                        $femaleBeneficiaryAccomp.' as femaleBeneficiariesActual',
                        $groupBeneficiaryAccomp.' as groupBeneficiariesActual',
                        $isCompleted.' as completed',
                        'LOCATE("%", physicalTargets.indicator) as isPercent',
                    ]);
                    $projects = $projects->leftJoin(['regionTitles' => '('.$regionTitles.')'], 'regionTitles.project_id = project.id');
                    $projects = $projects->leftJoin(['provinceTitles' => '('.$provinceTitles.')'], 'provinceTitles.project_id = project.id');
                    $projects = $projects->leftJoin(['citymunTitles' => '('.$citymunTitles.')'], 'citymunTitles.project_id = project.id');
                    $projects = $projects->leftJoin(['barangayTitles' => '('.$barangayTitles.')'], 'barangayTitles.project_id = project.id');
                    $projects = $projects->leftJoin(['submitterName' => '('.$submitterName.')'], 'submitterName.project_id = project.id');
                    $projects = $projects->leftJoin(['categoryTitles' => '('.$categoryTitles.')'], 'categoryTitles.project_id = project.id');
                    $projects = $projects->leftJoin('fund_source', 'fund_source.id = project.fund_source_id');
                    $projects = $projects->leftJoin('agency', 'agency.id = project.agency_id');
                    //$projects = $projects->leftJoin('accomplishment', 'accomplishment.project_id = project.id');
                    $projects = $projects->leftJoin('sector', 'sector.id = project.sector_id');
                    $projects = $projects->leftJoin(['accomps' => '('.$accomps.')'], 'accomps.project_id = project.id');
                    $projects = $projects->leftJoin(['accomplishmentPerQuarter' => '('.$accomplishmentPerQuarter.')'], 'accomplishmentPerQuarter.project_id = project.id');
                    $projects = $projects->leftJoin('project_category', 'project_category.project_id = project.id');
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
                    $projects = $projects->leftJoin(['personEmployedAccompsQ1' => '('.$personEmployedAccomps.')'], 'personEmployedAccompsQ1.project_id = project.id and personEmployedAccompsQ1.quarter = "Q1"');
                    $projects = $projects->leftJoin(['personEmployedAccompsQ2' => '('.$personEmployedAccomps.')'], 'personEmployedAccompsQ2.project_id = project.id and personEmployedAccompsQ2.quarter = "Q2"');
                    $projects = $projects->leftJoin(['personEmployedAccompsQ3' => '('.$personEmployedAccomps.')'], 'personEmployedAccompsQ3.project_id = project.id and personEmployedAccompsQ3.quarter = "Q3"');
                    $projects = $projects->leftJoin(['personEmployedAccompsQ4' => '('.$personEmployedAccomps.')'], 'personEmployedAccompsQ4.project_id = project.id and personEmployedAccompsQ4.quarter = "Q4"');
                    $projects = $projects->leftJoin(['beneficiariesTargets' => '('.$beneficiariesTargets.')'], 'beneficiariesTargets.project_id = project.id');
                    $projects = $projects->leftJoin(['groupBeneficiariesTargets' => '('.$groupBeneficiariesTargets.')'], 'groupBeneficiariesTargets.project_id = project.id');
                    $projects = $projects->leftJoin(['maleEmployedTargets' => '('.$maleEmployedTargets.')'], 'maleEmployedTargets.project_id = project.id');
                    $projects = $projects->leftJoin(['femaleEmployedTargets' => '('.$femaleEmployedTargets.')'], 'femaleEmployedTargets.project_id = project.id');
                    $projects = $projects->leftJoin(['beneficiariesAccompsQ1' => '('.$beneficiariesAccomps.')'], 'beneficiariesAccompsQ1.project_id = project.id and beneficiariesAccompsQ1.quarter = "Q1"');
                    $projects = $projects->leftJoin(['beneficiariesAccompsQ2' => '('.$beneficiariesAccomps.')'], 'beneficiariesAccompsQ2.project_id = project.id and beneficiariesAccompsQ2.quarter = "Q2"');
                    $projects = $projects->leftJoin(['beneficiariesAccompsQ3' => '('.$beneficiariesAccomps.')'], 'beneficiariesAccompsQ3.project_id = project.id and beneficiariesAccompsQ3.quarter = "Q3"');
                    $projects = $projects->leftJoin(['beneficiariesAccompsQ4' => '('.$beneficiariesAccomps.')'], 'beneficiariesAccompsQ4.project_id = project.id and beneficiariesAccompsQ4.quarter = "Q4"');
                    $projects = $projects->leftJoin(['groupBeneficiariesAccompsQ1' => '('.$groupBeneficiariesAccomps.')'], 'groupBeneficiariesAccompsQ1.project_id = project.id and groupBeneficiariesAccompsQ1.quarter = "Q1"');
                    $projects = $projects->leftJoin(['groupBeneficiariesAccompsQ2' => '('.$groupBeneficiariesAccomps.')'], 'groupBeneficiariesAccompsQ2.project_id = project.id and groupBeneficiariesAccompsQ2.quarter = "Q2"');
                    $projects = $projects->leftJoin(['groupBeneficiariesAccompsQ3' => '('.$groupBeneficiariesAccomps.')'], 'groupBeneficiariesAccompsQ3.project_id = project.id and groupBeneficiariesAccompsQ3.quarter = "Q3"');
                    $projects = $projects->leftJoin(['groupBeneficiariesAccompsQ4' => '('.$groupBeneficiariesAccomps.')'], 'groupBeneficiariesAccompsQ4.project_id = project.id and groupBeneficiariesAccompsQ4.quarter = "Q4"');
                    $projects = $projects->andWhere(['project.year' => $model->year, 'project.draft' => 'No']);
                    $projects = $projects->andWhere(['project.id' => $projectIDs]);

                    $reportName = 'RPMES_'.$model->year.'_'.$model->quarter;
        
                    if(Yii::$app->user->can('AgencyUser'))
                    {
                        $projects = $projects->andWhere(['agency.id' => Yii::$app->user->identity->userinfo->AGENCY_C]);
                    }
        
                    if($model->agency_id != '')
                    {
                        $projects = $projects->andWhere(['agency.id' => $model->agency_id]);
                    }

                    if($model->category_id != '')
                    {
                        $projects = $projects->andWhere(['project_category.category_id' => $model->category_id]);
                    }

                    if($model->sector_id != '')
                    {
                        $projects = $projects->andWhere(['sector.id' => $model->sector_id]);
                    }

        $projects = $projects->asArray()->all();

        //echo '<pre>'; print_r($projects); exit;
                    $i=0;
                    if($model->agency_id != '')
                    {
                        foreach($projects as $project){

                            $reportName = $reportName.'_'.$project['agencyTitle'];
                                $i++;
                            if ($i = 1){ $i=0; break; }
                        }
                    }

                    if($model->category_id != '')
                    {
                        foreach($projects as $project){

                            $reportName = $reportName.'_'.$project['categoryTitle'];
                                $i++;
                            if ($i = 1){ $i=0; break; }
                        }
                    }

                    if($model->sector_id != '')
                    {
                        foreach($projects as $project){

                            $reportName = $reportName.'_'.$project['sectorTitle'];
                                $i++;
                            if ($i = 1){ $i=0; break; }
                        }
                    }

                    $reportName = strtoupper($reportName.'_Summary_Accomplishment');

        //echo $reportName; exit;
            
        $filename = $reportName;

        if($type == 'excel')
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=".$filename.".xls");
            return $this->renderPartial('_report-file', [
                'type' => $type,
                'model' => $model,
                'projects' => $projects,
                'genders' => $genders
            ]);
        }else if($type == 'pdf')
        {
            $content = $this->renderPartial('_report-file', [
                'type' => $type,
                'model' => $model,
                'projects' => $projects,
                'genders' => $genders
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
            return $this->renderAjax('_report-file', [
                'type' => $type,
                'model' => $model,
                'projects' => $projects,
                'genders' => $genders
            ]);
        }
    }
}