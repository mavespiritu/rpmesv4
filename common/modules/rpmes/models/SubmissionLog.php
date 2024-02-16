<?php

namespace common\modules\rpmes\models;

use Yii;
use markavespiritu\user\models\User;
use markavespiritu\user\models\UserInfo;

/**
 * This is the model class for table "submission_log".
 *
 * @property int $id
 * @property int|null $submission_id
 * @property int|null $user_id
 * @property string|null $status
 * @property string|null $datetime
 *
 * @property Submission $submission
 */
class SubmissionLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'submission_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['remarks'], 'required', 'on' => 'forFurtherValidation'],
            [['submission_id', 'user_id'], 'integer'],
            [['datetime'], 'safe'],
            [['status'], 'string', 'max' => 100],
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
            'user_id' => 'User ID',
            'status' => 'Status',
            'datetime' => 'Datetime',
            'remarks' => 'Remarks'
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

    public function getActor()
    {
        $submitter = UserInfo::findOne(['user_id' => $this->user_id]);

        return $submitter ? $submitter->FIRST_M." ".$submitter->LAST_M : '';
    }

    public function getActorPosition()
    {
        $submitter = UserInfo::findOne(['user_id' => $this->user_id]);

        return $submitter ? $submitter->POSITION_C : '';
    }
}
