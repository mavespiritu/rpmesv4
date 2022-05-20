<?php

namespace common\modules\rpmes\models;

use Yii;
use markavespiritu\user\models\UserInfo;
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
}
