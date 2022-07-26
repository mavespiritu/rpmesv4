<?php

namespace common\modules\rpmes\models;

use Yii;
use markavespiritu\user\models\UserInfo;

/**
 * This is the model class for table "training".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $objective
 * @property string|null $office
 * @property string|null $organization
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int|null $male_participant
 * @property int|null $female_participant
 * @property int|null $submitted_by
 * @property string|null $date_submitted
 */
class Training extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'training';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'objective', 'office', 'organization','start_date', 'end_date', 'date_submitted','male_participant', 'female_participant', 'submitted_by','quarter','year'], 'required'],
            [['title', 'objective', 'office', 'organization'], 'string'],
            [['start_date', 'end_date', 'date_submitted'], 'safe'],
            [['male_participant', 'female_participant', 'submitted_by'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title of Training',
            'objective' => 'Objective of Training',
            'office' => 'Lead Office',
            'organization' => 'Participating Offices / Agencies / Organizations',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'male_participant' => 'Male Participant',
            'female_participant' => 'Female Participant',
            'submitted_by' => 'Submitted By',
            'date_submitted' => 'Date Submitted',
            'quarter' => 'Quarter',
            'year' => 'Year'
        ];
    }

    public function getStartDate()
    {
        return $this->start_date != "" ? date("F j, Y", strtotime($this->start_date)) : 'No start date';
    }

    public function getEndDate()
    {
        return $this->end_date != "" ? date("F j, Y", strtotime($this->end_date)) : 'No end date';
    }

    public function getSubmitter()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'submitted_by']);
    }
    public function getSubmitterName()
    {
        return $this->submitter ? $this->submitter->fullName : '';
    }
    public function getTotalParticipant()
    {
        $data = Training::findOne($this->id);

        $total_participant = $data->male_participant + $data->female_participant;

        return $total_participant;
    }
    public function getYearsList() 
    {
        $currentYear = date('Y');
        $leastYear = date('Y') - 3;
        $maxYear = date('Y') + 3;
        $yearRange = range($leastYear, $maxYear);
        return array_combine($yearRange, $yearRange);
    }
}
