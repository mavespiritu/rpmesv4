<?php

namespace common\modules\rpmes\controllers;

use Yii;
use common\modules\rpmes\models\Project;
use common\modules\rpmes\models\Submission;
use common\modules\rpmes\models\DueDate;
use common\modules\rpmes\models\DueDateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * DueDateController implements the CRUD actions for DueDate model.
 */
class DueDateController extends Controller
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
                        'roles' => ['Administrator', 'SuperAdministrator'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all DueDate models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new DueDate();
        $model->year = date("Y");

        $years = Submission::find()->select(['distinct(year) as year'])->orderBy(['year' => SORT_DESC])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];

        return $this->render('index', [
            'model' => $model,
            'quarters' => $quarters,
            'years' => $years,
        ]);
    }

    public function actionMonitoringPlanDueDate($year)
    {
        $dueDate = DueDate::findOne([
            'year' => $year,
            'report' => 'Monitoring Plan',
        ]);

        return $this->renderAjax('monitoring-plan',[
            'dueDate' => $dueDate,
            'year' => $year,
            'report' => 'Monitoring Plan',
        ]);
    }
    
    public function actionAccomplishmentDueDate($year, $quarter)
    {
        $dueDate = DueDate::findOne([
            'year' => $year,
            'quarter' => $quarter,
            'report' => 'Accomplishment',
        ]);

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];

        return $this->renderAjax('accomplishment',[
            'dueDate' => $dueDate,
            'year' => $year,
            'quarter' => $quarter,
            'quarters' => $quarters,
            'report' => 'Accomplishment',            
        ]);
    }

    public function actionProjectExceptionDueDate($year, $quarter)
    {
        $dueDate = DueDate::findOne([
            'year' => $year,
            'quarter' => $quarter,
            'report' => 'Project Exception',
        ]);

        $quarters = ['Q1' => '1st Quarter', 'Q2' => '2nd Quarter', 'Q3' => '3rd Quarter', 'Q4' => '4th Quarter'];

        return $this->renderAjax('project-exception',[
            'dueDate' => $dueDate,
            'year' => $year,
            'quarter' => $quarter,
            'quarters' => $quarters,
            'report' => 'Project Exception',
        ]);
    }

    public function actionProjectResultsDueDate($year)
    {
        $dueDate = DueDate::findOne([
            'year' => $year,
            'report' => 'Project Results',
        ]);

        return $this->renderAjax('project-results',[
            'dueDate' => $dueDate,
            'year' => $year,
            'report' => 'Project Results',
        ]);
    }

    public function actionSetMonitoringPlanDueDate($report, $year)
    {
        $model = DueDate::findOne([
            'year' => $year,
            'report' => $report,
        ]) ? DueDate::findOne([
            'year' => $year,
            'report' => $report,
        ]) : new DueDate();

        $model->report = $report;
        $model->year = $year;

        $quarter = '';

        if($model->load(Yii::$app->request->post()))
        {
            $model->save();
        }

        return $this->renderAjax('_form',[
            'model' => $model,
            'report' => $report,
            'year' => $year,
            'quarter' => $quarter,
        ]);
    }

    public function actionSetAccomplishmentDueDate($report, $year, $quarter)
    {
        $model = DueDate::findOne([
            'year' => $year,
            'report' => $report,
            'quarter' => $quarter,
        ]) ? DueDate::findOne([
            'year' => $year,
            'report' => $report,
            'quarter' => $quarter,
        ]) : new DueDate();

        $model->report = $report;
        $model->year = $year;
        $model->quarter = $quarter;

        if($model->load(Yii::$app->request->post()))
        {
            $model->save();
        }

        return $this->renderAjax('_form',[
            'model' => $model,
            'report' => $report,
            'year' => $year,
            'quarter' => $quarter,
        ]);
    }

    public function actionSetProjectExceptionDueDate($report, $year, $quarter)
    {
        $model = DueDate::findOne([
            'year' => $year,
            'report' => $report,
            'quarter' => $quarter,
        ]) ? DueDate::findOne([
            'year' => $year,
            'report' => $report,
            'quarter' => $quarter,
        ]) : new DueDate();

        $model->report = $report;
        $model->year = $year;
        $model->quarter = $quarter;

        if($model->load(Yii::$app->request->post()))
        {
            $model->save();
        }

        return $this->renderAjax('_form',[
            'model' => $model,
            'report' => $report,
            'year' => $year,
            'quarter' => $quarter,
        ]);
    }

    public function actionSetProjectResultsDueDate($report, $year)
    {
        $model = DueDate::findOne([
            'year' => $year,
            'report' => $report,
        ]) ? DueDate::findOne([
            'year' => $year,
            'report' => $report,
        ]) : new DueDate();

        $model->report = $report;
        $model->year = $year;

        $quarter = '';


        if($model->load(Yii::$app->request->post()))
        {
            $model->save();
        }

        return $this->renderAjax('_form',[
            'model' => $model,
            'report' => $report,
            'year' => $year,
            'quarter' => $quarter,
        ]);
    }

    /**
     * Finds the DueDate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DueDate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DueDate::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
