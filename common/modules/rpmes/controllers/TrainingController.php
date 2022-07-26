<?php

namespace common\modules\rpmes\controllers;

use Yii;
use common\modules\rpmes\models\Training;
use common\modules\rpmes\models\TrainingSearch;
use common\modules\rpmes\models\UserInfo;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use common\modules\rpmes\models\Project;
use kartik\mpdf\Pdf;

/**
 * TrainingController implements the CRUD actions for Training model.
 */
class TrainingController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'view', 'delete'],
                        'allow' => true,
                        'roles' => ['Administrator','SuperAdministrator'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Training models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrainingSearch();
        $model = new Training();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $years = Training::find()->select(['distinct(year) as year'])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'years' => $years,
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Training model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Training model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Training();

        $currentYear = date('Y');
        $leastYear = date('Y') -5;
        $maxYear = date('Y') + 3;
        $yearsRange = [];

        for ($x = $leastYear; $x <= $currentYear; $x++) {
            $yearsRange = $x;  
        }

        for ($x = $currentYear; $x <= $maxYear; $x++) {
            $yearsRange = $x; 
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->submitted_by = Yii::$app->user->id;
            $model->date_submitted = date('Y-m-d H:i:s');
            $model->save();
            \Yii::$app->getSession()->setFlash('success', 'Record Saved');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'yearsRange' => $yearsRange,
        ]);
    }

    /**
     * Updates an existing Training model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->submitted_by = Yii::$app->user->id;
            $model->date_submitted = date('Y-m-d H:i:s');
            $model->save();
            \Yii::$app->getSession()->setFlash('success', 'Record Updated');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Training model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \Yii::$app->getSession()->setFlash('success', 'Record Deleted');
        return $this->redirect(['index']);
    }

    /**
     * Finds the Training model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Training the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Training::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionPrintFormNine($year, $quarter, $title, $objective, $office, $startDate, $endDate, $maleParticipant, $femaleParticipant)
    {
        $model = [];
        $model['year'] = $year;
        $model['quarter'] = $quarter;
        $model['title'] = $title;
        $model['objective'] = $objective;
        $model['office'] = $office;
        $model['start_date'] = $startDate;
        $model['end_date'] = $endDate;
        $model['male_participant'] = $maleParticipant;
        $model['female_participant'] = $femaleParticipant;

        $trainings = Training::find()
                    ->select(['id','title','objective','office','organization','start_date','end_date','male_participant','female_participant','quarter','year']);

        if($model['year'] != '')
        {
            $trainings = $trainings->andWhere(['training.year' => $model['year']]);
        }

        if($model['quarter'] != '')
        {
            $trainings = $trainings->andWhere(['training.quarter' => $model['quarter']]);
        }

        if($model['title'] != '')
        {
            $trainings = $trainings->andWhere(['training.title' => $model['title']]);
        }

        if($model['objective'] != '')
        {
            $trainings = $trainings->andWhere(['training.objective' => $model['offiobjectivece']]);
        }

        if($model['office'] != '')
        {
            $trainings = $trainings->andWhere(['training.office' => $model['office']]);
        }

        if($model['start_date'] != '')
        {
            $trainings = $trainings->andWhere(['training.start_date' => $model['start_date']]);
        }

        if($model['end_date'] != '')
        {
            $trainings = $trainings->andWhere(['training.end_date' => $model['end_date']]);
        }

        if($model['male_participant'] != '')
        {
            $trainings = $trainings->andWhere(['training.male_participant' => $model['male_participant']]);
        }
        
        if($model['female_participant'] != '')
        {
            $trainings = $trainings->andWhere(['training.female_participant' => $model['female_participant']]);
        }

        $trainings = $trainings->orderBy(['training.title' => SORT_ASC])->asArray()->all();

        //echo "<pre>"; print_r($trainings); exit;

        return $this->renderAjax('form-nine', [
            'model' => $model,
            'type' => 'print',
            'trainings' => $trainings
        ]);
    }
    public function actionDownloadFormNine($type, $year, $quarter, $title, $objective, $office, $organization, $startDate, $endDate, $maleParticipant, $femaleParticipant, $model)
    {
        $model = json_decode($model, true); 
        $model['year'] = $year;
        $model['quarter'] = $quarter;
        $model['title'] = $title;
        $model['objective'] = $objective;
        $model['office'] = $office;
        $model['start_date'] = $startDate;
        $model['end_date'] = $endDate;
        $model['male_participant'] = $maleParticipant;
        $model['female_participant'] = $femaleParticipant;

        $trainings = Training::find()
                    ->select(['id','title','objective','office','organization','start_date','end_date','male_participant','female_participant','quarter','year']);

        if($model['year'] != '')
        {
            $trainings = $trainings->andWhere(['training.year' => $model['year']]);
        }
        
        if($model['quarter'] != '')
        {
            $trainings = $trainings->andWhere(['training.quarter' => $model['quarter']]);
        }

        if($model['title'] != '')
        {
            $trainings = $trainings->andWhere(['training.title' => $model['title']]);
        }

        if($model['objective'] != '')
        {
            $trainings = $trainings->andWhere(['training.objective' => $model['offiobjectivece']]);
        }

        if($model['office'] != '')
        {
            $trainings = $trainings->andWhere(['training.office' => $model['office']]);
        }

        if($model['start_date'] != '')
        {
            $trainings = $trainings->andWhere(['training.start_date' => $model['start_date']]);
        }

        if($model['end_date'] != '')
        {
            $trainings = $trainings->andWhere(['training.end_date' => $model['end_date']]);
        }

        if($model['male_participant'] != '')
        {
            $trainings = $trainings->andWhere(['training.male_participant' => $model['male_participant']]);
        }
        
        if($model['female_participant'] != '')
        {
            $trainings = $trainings->andWhere(['training.female_participant' => $model['female_participant']]);
        }

        $trainings = $trainings->orderBy(['training.title' => SORT_ASC])->asArray()->all();

        $filename = 'RPMES Form 9: LIST OF TRAININGS AND WORKSHOPS';

        if($type == 'excel')
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=".$filename.".xls");
            return $this->renderPartial('form-nine', [
                'model' => $model,
                'type' => $type,
                'trainings' => $trainings,
            ]);
        }else if($type == 'pdf')
        {
            $content = $this->renderPartial('form-nine', [
                'model' => $model,
                'type' => $type,
                'trainings' => $trainings,
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
                'cssInline' => '*{font-family: "Arial";}
                                table{
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
                                }
                                h1,h2,h3,h4,h5,h6{
                                    text-align: center;
                                    font-weight: bolder;
                                }', 
                ]);
        
                $response = Yii::$app->response;
                $response->format = \yii\web\Response::FORMAT_RAW;
                $headers = Yii::$app->response->headers;
                $headers->add('Content-Type', 'application/pdf');
                return $pdf->render();
        }
    }
}
