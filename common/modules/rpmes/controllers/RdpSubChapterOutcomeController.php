<?php

namespace common\modules\rpmes\controllers;

use Yii;
use common\modules\rpmes\models\RdpChapter;
use common\modules\rpmes\models\RdpChapterOutcome;
use common\modules\rpmes\models\RdpSubChapterOutcome;
use common\modules\rpmes\models\RdpSubChapterOutcomeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * RdpSubChapterOutcomeController implements the CRUD actions for RdpSubChapterOutcome model.
 */
class RdpSubChapterOutcomeController extends Controller
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
                        'roles' => ['Administrator', 'SuperAdministrator'],
                    ],
                ],
            ],
        ];
    }

    public function actionRdpChapterOutcomeList($id)
    {
        $outcomes = RdpChapterOutcome::find()->select(['id', 'concat("Outcome ",level,": ", title) as title'])->where(['rdp_chapter_id' => $id])->all();

        $arr = [];
        $arr[] = ['id'=>'','text'=>''];
        foreach($outcomes as $outcome){
            $arr[] = ['id' => $outcome->id ,'text' => $outcome->title];
        }
        \Yii::$app->response->format = 'json';
        return $arr;
    }

    /**
     * Lists all RdpSubChapterOutcome models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RdpSubChapterOutcomeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RdpSubChapterOutcome model.
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
     * Creates a new RdpSubChapterOutcome model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RdpSubChapterOutcome();

        $chapters = RdpChapter::find()->select(['id', 'concat("Chapter ",chapter_no,": ",title) as title'])->asArray()->all();
        $chapters = ArrayHelper::map($chapters, 'id', 'title');

        $outcomes = [];

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', 'Record Saved');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'chapters' => $chapters,
            'outcomes' => $outcomes,
        ]);
    }

    /**
     * Updates an existing RdpSubChapterOutcome model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $chapters = RdpChapter::find()->select(['id', 'concat("Chapter ",chapter_no,": ",title) as title'])->asArray()->all();
        $chapters = ArrayHelper::map($chapters, 'id', 'title');

        $outcomes = RdpChapterOutcome::find()->select(['id', 'concat("Outcome ",level,": ", title) as title'])->where(['rdp_chapter_id' => $model->rdp_chapter_id])->all();
        $outcomes = ArrayHelper::map($outcomes, 'id', 'title');


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', 'Record Updated');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'chapters' => $chapters,
            'outcomes' => $outcomes,
        ]);
    }

    /**
     * Deletes an existing RdpSubChapterOutcome model.
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
     * Finds the RdpSubChapterOutcome model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RdpSubChapterOutcome the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RdpSubChapterOutcome::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
