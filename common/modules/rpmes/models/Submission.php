<?php

namespace common\modules\rpmes\models;

use Yii;
use markavespiritu\user\models\UserInfo;
use common\components\helpers\HtmlHelper;
use yii\helpers\ArrayHelper;
use markavespiritu\user\models\User;
/**
 * This is the model class for table "submission".
 *
 * @property int $id
 * @property int|null $agency_id
 * @property string|null $report
 * @property int|null $year
 * @property string|null $quarter
 * @property string|null $semester
 * @property int|null $submitted_by
 * @property string|null $date_submitted
 * @property string|null $draft
 */
class Submission extends \yii\db\ActiveRecord
{
    public $grouping;
    public $fund_source_id;
    public $region_id;
    public $province_id;
    public $citymun_id;
    public $sector_id;
    public $sub_sector_id;
    public $category_id;
    public $period;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'submission';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['year'], 'required', 'on' => 'createMonitoringPlan'],
            [['year', 'quarter'], 'required', 'on' => 'createAccomplishmentReport'],
            [['year', 'quarter'], 'required', 'on' => 'createProjectExceptionReport'],
            [['year'], 'required', 'on' => 'createProjectResultsReport'],
            [['year','agency_id'], 'validateMonitoringPlan'],
            [['year', 'quarter', 'agency_id'], 'validateAccomplishmentReport'],
            [['year', 'quarter', 'agency_id'], 'validateProjectExceptionReport'],
            [['year', 'quarter', 'agency_id'], 'validateProjectResultsReport'],
            [['year', 'agency_id'], 'required', 'on' => 'createMonitoringPlanAdmin'],
            [['year', 'quarter', 'agency_id'], 'required', 'on' => 'createAccomplishmentReportAdmin'],
            [['year', 'quarter', 'agency_id'], 'required', 'on' => 'createProjectExceptionReportAdmin'],
            [['year', 'quarter', 'agency_id'], 'required', 'on' => 'createProjectResultsReportAdmin'],
            [['agency_id'], 'required', 'on' => 'monitoringPlanAdmin'],
            [['year'], 'required', 'on' => 'acknowledgmentMonitoringPlan'],
            [['year'], 'required', 'on' => 'acknowledgmentMonitoringReport'],
            [['year'], 'required', 'on' => 'generateFormOne'],
            [['year', 'grouping'], 'required', 'on' => 'summaryMonitoringPlan'],
            [['year', 'quarter', 'grouping'], 'required', 'on' => 'summaryMonitoringReport'],
            [['year', 'quarter','grouping'], 'required', 'on' => 'summaryMonitoringReportSector'],
            [['year', 'quarter'], 'required', 'on' => 'delayedProjects'],
            [['agency_id', 'year', 'submitted_by', 'fund_source_id', 'sector_id', 'sub_sector_id', 'category_id'], 'integer'],
            [['report', 'quarter', 'semester', 'draft', 'region_id', 'province_id', 'citymun_id', 'period'], 'string'],
            [['date_submitted'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agency_id' => 'Agency',
            'report' => 'Report',
            'year' => 'Year',
            'quarter' => 'Quarter',
            'semester' => 'Semester',
            'submitted_by' => 'Submitted By',
            'date_submitted' => 'Date Submitted',
            'draft' => 'Draft',
            'grouping' => 'Group by',
            'quarter' => 'Quarter',
            'agency_id' => 'Agency',
            'fund_source_id' => 'Fund Source',
            'region_id' => 'Region',
            'province_id' => 'Province',
            'citymun_id' => 'City/Municipality',
            'sector_id' =>'Sector',
            'sub_sector_id' =>'Sub-Sector',
            'category_id' => 'Category',
            'period' => 'Period'
        ];
    }

    public function validateMonitoringPlan($attribute, $params, $validator)
    {
        if($this->report == 'Monitoring Plan')
        {
            $model = Submission::findOne(['report' => 'Monitoring Plan', 'year' => $this->year, 'agency_id' => $this->agency_id]);

            if($model)
            {
                $this->addError($attribute, 'This monitoring plan already exists');
            }
        }
    }

    public function validateAccomplishmentReport($attribute, $params, $validator)
    {
        if($this->report == 'Accomplishment')
        {
            $model = Submission::findOne(['report' => 'Accomplishment', 'year' => $this->year, 'quarter' => $this->quarter, 'agency_id' => $this->agency_id]);

            if($model)
            {
                $this->addError($attribute, 'This accomplishment report already exists');
            }
        }
    }

    public function validateProjectExceptionReport($attribute, $params, $validator)
    {
        if($this->report == 'Project Exception')
        {
            $model = Submission::findOne(['report' => 'Project Exception', 'year' => $this->year, 'quarter' => $this->quarter, 'agency_id' => $this->agency_id]);

            if($model)
            {
                $this->addError($attribute, 'This project exception report already exists');
            }
        }
    }

    public function validateProjectResultsReport($attribute, $params, $validator)
    {
        if($this->report == 'Project Results')
        {
            $model = Submission::findOne(['report' => 'Project Results', 'year' => $this->year, 'agency_id' => $this->agency_id]);

            if($model)
            {
                $this->addError($attribute, 'This project results report already exists');
            }
        }
    }

    /**
     * Gets query for [[Agency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlans()
    {
        return $this->hasMany(Plan::className(), ['submission_id' => 'id']);
    }

    public function getSubmissionLogs()
    {
        return $this->hasMany(SubmissionLog::className(), ['submission_id' => 'id']);
    }

    public function getCurrentSubmissionLog()
    {
        return $this->getSubmissionLogs()->orderBy(['id' => SORT_DESC])->one();
    }

    public function getCurrentStatus()
    {
        return $this->currentSubmissionLog ? $this->currentSubmissionLog->status : 'Draft';
    }

    public function getSubmitted()
    {
        return $this->getSubmissionLogs()->where(['status' => 'Submitted'])->orderBy(['id' => SORT_DESC])->one();
    }

    public function getForFurtherValidation()
    {
        return $this->getSubmissionLogs()->where(['status' => 'For further validation'])->orderBy(['id' => SORT_DESC])->one();
    }

    public function getAcknowledged()
    {
        return $this->getSubmissionLogs()->where(['status' => 'Acknowledged'])->orderBy(['id' => SORT_DESC])->one();
    }

    public function getAgency()
    {
        return $this->hasOne(Agency::className(), ['id' => 'agency_id']);
    }

    public function getAcknowledgment()
    {
        return $this->hasOne(Acknowledgment::className(), ['submission_id' => 'id']);
    }
    
    public function getAcknowledger()
    {
        $acknowledgment = $this->acknowledgment; 

        $acknowledger = $acknowledgment ? UserInfo::findOne(['user_id' => $this->acknowledgment->acknowledged_by]) : null;

        return !is_null($acknowledger) ? $acknowledger->FIRST_M." ".$acknowledger->LAST_M : '';
    }

    public function getSubmitter()
    {
        $submitter = UserInfo::findOne(['user_id' => $this->submitted_by]);

        return $submitter ? $submitter->FIRST_M." ".$submitter->LAST_M : '';
    }

    public function getSubmitterPosition()
    {
        $submitter = UserInfo::findOne(['user_id' => $this->submitted_by]);

        return $submitter ? $submitter->POSITION_C : '';
    }

    public function getProjectCount()
    {
        return count($this->plans);
    }

    public function getDeadlineStatus()
    {
        $HtmlHelper = new HtmlHelper();
        $dueDate = DueDate::find();

        if($this->report == 'Monitoring Plan')
        {
            $dueDate = $dueDate->andWhere(['year' => $this->year, 'report' => $this->report]);
        }else if($this->report == 'Accomplishment')
        {
            $dueDate = $dueDate->andWhere(['year' => $this->year, 'report' => $this->report, 'quarter' => $this->quarter]);
        }else if($this->report == 'Project Exception')
        {
            $dueDate = $dueDate->andWhere(['year' => $this->year, 'report' => $this->report, 'quarter' => $this->quarter]);
        }

        $dueDate = $dueDate->one();

        $status = $dueDate ? strtotime(date("Y-m-d", strtotime($this->date_submitted))) <= strtotime($dueDate->due_date) ? $HtmlHelper->time_elapsed_string($dueDate->due_date).' before deadline' : $HtmlHelper->time_elapsed_string($dueDate->due_date).' after deadline' : 'no deadline set';

        return $status;
    }

    public function getLocation()
    {
        return $this->project->location;
    }

    public function sendFormOneSubmissionNotification($emails)
    {
        // Your email sending logic here
        // Example using Yii2 mailer component:
        $mailer = Yii::$app->mailer;
        $message = $mailer->compose('submit-form-one-html', [
                'model' => $this
            ])
            ->setFrom('nro1.mailer@neda.gov.ph')
            ->setTo($emails)
            ->setSubject('eRPMES Notification: '.$this->agency->code.' - Form 1 Submission for CY '.$this->year);

        if ($message->send()) {
            Yii::info('Email sent successfully', 'email');
        } else {
            Yii::error('Failed to send email', 'email');
        }
    }

    public function sendFormTwoSubmissionNotification($emails)
    {
        // Your email sending logic here
        // Example using Yii2 mailer component:
        $mailer = Yii::$app->mailer;
        $message = $mailer->compose('submit-form-two-html', [
                'model' => $this
            ])
            ->setFrom('nro1.mailer@neda.gov.ph')
            ->setTo($emails)
            ->setSubject('eRPMES Notification: '.$this->agency->code.' - Form 2 Submission for '.$this->quarter.' '.$this->year);

        if ($message->send()) {
            Yii::info('Email sent successfully', 'email');
        } else {
            Yii::error('Failed to send email', 'email');
        }
    }

    public function sendFormThreeSubmissionNotification($emails)
    {
        // Your email sending logic here
        // Example using Yii2 mailer component:
        $mailer = Yii::$app->mailer;
        $message = $mailer->compose('submit-form-three-html', [
                'model' => $this
            ])
            ->setFrom('nro1.mailer@neda.gov.ph')
            ->setTo($emails)
            ->setSubject('eRPMES Notification: '.$this->agency->code.' - Form 3 Submission for '.$this->quarter.' '.$this->year);

        if ($message->send()) {
            Yii::info('Email sent successfully', 'email');
        } else {
            Yii::error('Failed to send email', 'email');
        }
    }

    public function sendFormFourSubmissionNotification($emails)
    {
        // Your email sending logic here
        // Example using Yii2 mailer component:
        $mailer = Yii::$app->mailer;
        $message = $mailer->compose('submit-form-four-html', [
                'model' => $this
            ])
            ->setFrom('nro1.mailer@neda.gov.ph')
            ->setTo($emails)
            ->setSubject('eRPMES Notification: '.$this->agency->code.' - Form 4 Submission for CY '.$this->year);

        if ($message->send()) {
            Yii::info('Email sent successfully', 'email');
        } else {
            Yii::error('Failed to send email', 'email');
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            // This code will be executed after a new record is inserted

        } else {
            $adminRole = Yii::$app->authManager->getRole('Administrator');

            if ($adminRole !== null) {
                // Get all users assigned to the 'Administrator' role
                $admins = User::find()
                ->innerJoin('auth_assignment', 'auth_assignment.user_id = user.id')
                ->where(['auth_assignment.item_name' => $adminRole->name])
                ->all();

                $emails = [];

                if($admins){
                    foreach($admins as $admin){
                        $emails[] = $admin->email;
                    }
                }

                if($this->report == 'Monitoring Plan')
                {
                    $this->sendFormOneSubmissionNotification($emails);

                }else if($this->report == 'Accomplishment')
                {
                    $this->sendFormTwoSubmissionNotification($emails);

                }else if($this->report == 'Project Exception')
                {
                    $this->sendFormThreeSubmissionNotification($emails);

                }else if($this->report == 'Project Results')
                {
                    $this->sendFormFourSubmissionNotification($emails);
                }
            }
        }
    }
}
