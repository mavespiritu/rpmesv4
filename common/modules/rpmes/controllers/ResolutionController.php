<?php

namespace common\modules\rpmes\controllers;

use Yii;
use common\modules\rpmes\models\Resolution;
use common\modules\rpmes\models\ResolutionSearch;
use common\modules\rpmes\models\Project;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use kartik\mpdf\Pdf;

/**
 * ResolutionController implements the CRUD actions for Resolution model.
 */
class ResolutionController extends Controller
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
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Resolution models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Resolution();
        $searchModel = new ResolutionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $years = Resolution::find()->select(['distinct(year) as year'])->asArray()->all();
        $years = [date("Y") => date("Y")] + ArrayHelper::map($years, 'year', 'year');
        array_unique($years);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            'years' => $years,
        ]);
    }

    /**
     * Displays a single Resolution model.
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
     * Creates a new Resolution model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Resolution();

        $model->year = date("Y");

        if ($model->load(Yii::$app->request->post())) {
            $model->submitted_by = Yii::$app->user->id;
            $model->date_submitted = date('Y-m-d H:i:s');
            $model->save();
            \Yii::$app->getSession()->setFlash('success', 'Record Saved');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Resolution model.
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
     * Deletes an existing Resolution model.
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
     * Finds the Resolution model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Resolution the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Resolution::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionPrintFormTen($year,$quarter,$resolutionNumber,$resolution,$dateApproved,$rpmcAction)
    {
        $model = [];
        $model['year'] = $year;
        $model['quarter'] = $quarter;
        $model['resolution_number'] = $resolutionNumber;
        $model['resolution'] = $resolution;
        $model['date_approved'] = $dateApproved;
        $model['rpmc_action'] = $rpmcAction;

        $resolutions = Resolution::find();

        if($model['year'] != '')
        {
            $resolutions = $resolutions->andWhere(['resolution.year' => $model['year']]);
        }

        if($model['quarter'] != '')
        {
            $resolutions = $resolutions->andWhere(['resolution.quarter' => $model['quarter']]);
        }

        if($model['resolution_number'] != '')
        {
            $resolutions = $resolutions->andWhere(['resolution.resolution_number' => $model['resolution_number']]);
        }

        if($model['resolution'] != '')
        {
            $resolutions = $resolutions->andWhere(['resolution.resolution' => $model['resolution']]);
        }

        if($model['date_approved'] != '')
        {
            $resolutions = $resolutions->andWhere(['resolution.date_approved' => $model['date_approved']]);
        }

        if($model['rpmc_action'] != '')
        {
            $resolutions = $resolutions->andWhere(['resolution.rpmc_action' => $model['rpmc_action']]);
        }

        $resolutions = $resolutions->orderBy(['resolution.resolution' => SORT_ASC])->all();

        return $this->renderAjax('form-ten', [
            'model' => $model,
            'type' => 'print',
            'resolutions' => $resolutions
        ]);
    }
    public function actionDownloadFormTen($type, $year, $quarter, $model)
    {
        $model = json_decode($model, true); 
        $model['year'] = $year;
        $model['quarter'] = $quarter;

        $resolutions = Resolution::find()
                    ->select(['id','resolution_number','resolution','date_approved','rpmc_action','quarter','year']);

        if($model['year'] != '')
        {
            $resolutions = $resolutions->andWhere(['resolution.year' => $model['year']]);
        }
        if($model['quarter'] != '')
        {
            $resolutions = $resolutions->andWhere(['resolution.quarter' => $model['quarter']]);
        }

        $resolutions = $resolutions->orderBy(['resolution.quarter' => SORT_ASC])->all();

        $filename = 'RPMES Form 10: LIST OF RESOLUTIONS';

        if($type == 'excel')
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=".$filename.".xls");
            return $this->renderPartial('form-ten', [
                'model' => $model,
                'type' => $type,
                'resolutions' => $resolutions,
            ]);
        }else if($type == 'pdf')
        {
            $content = $this->renderPartial('form-ten', [
                'model' => $model,
                'type' => $type,
                'resolutions' => $resolutions,
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
