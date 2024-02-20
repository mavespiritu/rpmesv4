<?php

namespace common\modules\rpmes\models;

use Yii;
use markavespiritu\user\models\User;
use markavespiritu\user\models\UserInfo;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "acknowledgment".
 *
 * @property int $id
 * @property int|null $submission_id
 * @property string|null $control_no
 * @property string|null $recipient_name
 * @property string|null $recipient_designation
 * @property string|null $recipient_address
 * @property string|null $findings
 * @property string|null $action_taken
 * @property int|null $acknowledged_by
 * @property string|null $date_acknowledged
 * @property int|null $prepared_by
 * @property string|null $date_prepared
 *
 * @property Submission $submission
 */
class Acknowledgment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'acknowledgment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['findings', 'action_taken'], 'required'],
            [['submission_id', 'acknowledged_by'], 'integer'],
            [['recipient_office', 'recipient_address', 'findings', 'action_taken'], 'string'],
            [['date_acknowledged'], 'safe'],
            [['control_no'], 'string', 'max' => 50],
            [['recipient_name', 'recipient_designation'], 'string', 'max' => 100],
            [['submission_id'], 'exist', 'skipOnError' => true, 'targetClass' => Submission::className(), 'targetAttribute' => ['submission_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'submission_id' => 'Submission ID',
            'control_no' => 'Control No',
            'recipient_name' => 'Recipient Name',
            'recipient_designation' => 'Recipient Designation',
            'recipient_office' => 'Recipient Office',
            'recipient_address' => 'Recipient Address',
            'findings' => 'Findings',
            'action_taken' => 'Action Taken',
            'acknowledged_by' => 'Acknowledged By',
            'date_acknowledged' => 'Date Acknowledged',
        ];
    }

    /**
     * Gets query for [[Submission]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubmission()
    {
        return $this->hasOne(Submission::className(), ['id' => 'submission_id']);
    }

    public function getAcknowledger()
    {
        $acknowledger = UserInfo::findOne(['user_id' => $this->acknowledged_by]);

        return $acknowledger ? $acknowledger->FIRST_M." ".$acknowledger->LAST_M : '';
    }

    public function getAcknowledgerPosition()
    {
        $acknowledger = UserInfo::findOne(['user_id' => $this->acknowledged_by]);

        return $acknowledger ? $acknowledger->POSITION_C : '';
    }

    public function sendFormOneAcknowledgmentNotification($emails)
    {
        // Your email sending logic here
        // Example using Yii2 mailer component:
        $mailer = Yii::$app->mailer;
        $message = $mailer->compose('acknowledge-form-one-html', [
                'model' => $this
            ])
            ->setFrom('mvespiritu@neda.gov.ph')
            ->setTo($emails)
            ->setSubject('eRPMES Notification: Acknowledgment of '.$this->submission->agency->code.' - Form 1 Submission for CY '.$this->submission->year);

        if ($message->send()) {
            Yii::info('Email sent successfully', 'email');
        } else {
            Yii::error('Failed to send email', 'email');
        }
    }

    public function sendFormTwoAcknowledgmentNotification($emails)
    {
        // Your email sending logic here
        // Example using Yii2 mailer component:
        $mailer = Yii::$app->mailer;
        $message = $mailer->compose('acknowledge-form-two-html', [
                'model' => $this
            ])
            ->setFrom('mvespiritu@neda.gov.ph')
            ->setTo($emails)
            ->setSubject('eRPMES Notification: Acknowledgment of '.$this->submission->agency->code.' - Form 2 Submission for '.$this->submission->quarter.' '.$this->submission->year);

        if ($message->send()) {
            Yii::info('Email sent successfully', 'email');
        } else {
            Yii::error('Failed to send email', 'email');
        }
    }

    public function sendFormThreeAcknowledgmentNotification($emails)
    {
        // Your email sending logic here
        // Example using Yii2 mailer component:
        $mailer = Yii::$app->mailer;
        $message = $mailer->compose('acknowledge-form-three-html', [
                'model' => $this
            ])
            ->setFrom('mvespiritu@neda.gov.ph')
            ->setTo($emails)
            ->setSubject('eRPMES Notification: Acknowledgment of '.$this->submission->agency->code.' - Form 3 Submission for '.$this->submission->quarter.' '.$this->submission->year);

        if ($message->send()) {
            Yii::info('Email sent successfully', 'email');
        } else {
            Yii::error('Failed to send email', 'email');
        }
    }

    public function sendFormFourAcknowledgmentNotification($emails)
    {
        // Your email sending logic here
        // Example using Yii2 mailer component:
        $mailer = Yii::$app->mailer;
        $message = $mailer->compose('acknowledge-form-four-html', [
                'model' => $this
            ])
            ->setFrom('mvespiritu@neda.gov.ph')
            ->setTo($emails)
            ->setSubject('eRPMES Notification: Acknowledgment of '.$this->submission->agency->code.' - Form 4 Submission for '.$this->submission->year);

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
            $adminRole = Yii::$app->authManager->getRole('Administrator');
            //$userRole = Yii::$app->authManager->getRole('AgencyUser');

            //if ($userRole !== null) {
            if ($adminRole !== null) {
           
                //$userIDs = UserInfo::find()->where(['AGENCY_C' => $this->submission->agency_id])->all();
               ///$userIDs = ArrayHelper::map($userIDs, 'user_id', 'user_id');

                $admins = User::find()
                ->innerJoin('auth_assignment', 'auth_assignment.user_id = user.id')
                //->where(['auth_assignment.item_name' => $userRole->name])
                ->where(['auth_assignment.item_name' => $adminRole->name])
                //->andWhere(['id' => $userIDs])
                ->all();

                $emails = [];

                if($admins){
                    foreach($admins as $admin){
                        $emails[] = $admin->email;
                    }
                }

                if($this->submission->report == 'Monitoring Plan')
                {
                    $this->sendFormOneAcknowledgmentNotification($emails);

                }else if($this->submission->report == 'Accomplishment')
                {
                    $this->sendFormTwoAcknowledgmentNotification($emails);

                }else if($this->submission->report == 'Project Exception')
                {
                    $this->sendFormThreeAcknowledgmentNotification($emails);

                }else if($this->submission->report == 'Project Results')
                {
                    $this->sendFormFourAcknowledgmentNotification($emails);
                }
            }

        } else {
            $adminRole = Yii::$app->authManager->getRole('Administrator');
            //$userRole = Yii::$app->authManager->getRole('AgencyUser');

            //if ($userRole !== null) {
            if ($adminRole !== null) {
           
                //$userIDs = UserInfo::find()->where(['AGENCY_C' => $this->submission->agency_id])->all();
               ///$userIDs = ArrayHelper::map($userIDs, 'user_id', 'user_id');

                $admins = User::find()
                ->innerJoin('auth_assignment', 'auth_assignment.user_id = user.id')
                //->where(['auth_assignment.item_name' => $userRole->name])
                ->where(['auth_assignment.item_name' => $adminRole->name])
                //->andWhere(['id' => $userIDs])
                ->all();

                $emails = [];

                if($admins){
                    foreach($admins as $admin){
                        $emails[] = $admin->email;
                    }
                }

                if($this->submission->report == 'Monitoring Plan')
                {
                    $this->sendFormOneAcknowledgmentNotification($emails);

                }else if($this->submission->report == 'Accomplishment')
                {
                    $this->sendFormTwoAcknowledgmentNotification($emails);

                }else if($this->submission->report == 'Project Exception')
                {
                    $this->sendFormThreeAcknowledgmentNotification($emails);

                }else if($this->submission->report == 'Project Results')
                {
                    $this->sendFormFourAcknowledgmentNotification($emails);
                }
            }
        }
    }
}
