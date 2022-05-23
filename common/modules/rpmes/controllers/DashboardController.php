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

class DashboardController extends \yii\web\Controller
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
    public function actionIndex()
    {
        $monitoringPlan = DueDate::findOne(['report' => 'Monitoring Plan', 'year' => date("Y")]);
        $accompQ1 = DueDate::findOne(['report' => 'Accomplishment', 'year' => date("Y"), 'quarter' => 'Q1']);
        $accompQ2 = DueDate::findOne(['report' => 'Accomplishment', 'year' => date("Y"), 'quarter' => 'Q2']);
        $accompQ3 = DueDate::findOne(['report' => 'Accomplishment', 'year' => date("Y"), 'quarter' => 'Q3']);
        $accompQ4 = DueDate::findOne(['report' => 'Accomplishment', 'year' => date("Y"), 'quarter' => 'Q4']);
        $exceptionQ1 = DueDate::findOne(['report' => 'Project Exception', 'year' => date("Y"), 'quarter' => 'Q1']);
        $exceptionQ2 = DueDate::findOne(['report' => 'Project Exception', 'year' => date("Y"), 'quarter' => 'Q2']);
        $exceptionQ3 = DueDate::findOne(['report' => 'Project Exception', 'year' => date("Y"), 'quarter' => 'Q3']);
        $exceptionQ4 = DueDate::findOne(['report' => 'Project Exception', 'year' => date("Y"), 'quarter' => 'Q4']);

        return $this->render('index',[
            'monitoringPlan' => $monitoringPlan,
            'accompQ1' => $accompQ1,
            'accompQ2' => $accompQ2,
            'accompQ3' => $accompQ3,
            'accompQ4' => $accompQ4,
            'exceptionQ1' => $exceptionQ1,
            'exceptionQ2' => $exceptionQ2,
            'exceptionQ3' => $exceptionQ3,
            'exceptionQ4' => $exceptionQ4,
        ]);
    }

}
