<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "agency".
 *
 * @property int $id
 * @property int|null $agency_type_id
 * @property string|null $code
 * @property string|null $title
 * @property string|null $head
 * @property string|null $head_designation
 * @property string|null $address
 *
 * @property AgencyType $agencyType
 * @property Project[] $projects
 */
class Agency extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agency_type_id', 'code', 'title', 'address', 'salutation', 'head', 'head_designation'], 'required'],
            [['code'], 'unique', 'message' => 'The abbreviation has been used already'],
            [['title'], 'unique', 'message' => 'The title has been used already'],
            [['agency_type_id'], 'integer'],
            [['address'], 'string'],
            [['code'], 'string', 'max' => 20],
            [['title'], 'string', 'max' => 200],
            [['head', 'salutation', 'head_designation'], 'string', 'max' => 100],
            [['agency_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => AgencyType::className(), 'targetAttribute' => ['agency_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agency_type_id' => 'Agency Type',
            'agencyTypeTitle' => 'Agency Type',
            'code' => 'Abbreviation',
            'title' => 'Agency Name',
            'head' => 'Agency Head',
            'salutation' => 'Salutation',
            'head_designation' => 'Designation',
            'address' => 'Address',
        ];
    }

    /**
     * Gets query for [[AgencyType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAgencyType()
    {
        return $this->hasOne(AgencyType::className(), ['id' => 'agency_type_id']);
    }

    public function getAgencyTypeTitle()
    {
        return $this->agencyType ? $this->agencyType->title : '';
    }

    /**
     * Gets query for [[Projects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Project::className(), ['agency_id' => 'id']);
    }

    public function getMonitoringPlanSubmission($year)
    {
        return Submission::findOne(['agency_id' => $this->id, 'year' => $year, 'report' => 'Monitoring Plan']);
    }

    public function getMonitoringReportSubmission($year, $quarter)
    {
        return Submission::findOne(['agency_id' => $this->id, 'year' => $year, 'quarter' => $quarter, 'report' => 'Accomplishment']);
    }

    public function getProjectExceptionSubmission($year, $quarter)
    {
        return Submission::findOne(['agency_id' => $this->id, 'year' => $year, 'quarter' => $quarter, 'report' => 'Project Exception']);
    }

    public function getProjectResultsSubmission($year)
    {
        return Submission::findOne(['agency_id' => $this->id, 'year' => $year, 'report' => 'Project Results']);
    }

    public function getMonitoringPlanAcknowledgment($year)
    {
        $submission = $this->getMonitoringPlanSubmission($year);

        return $submission ? $submission->acknowledgment : [];
    }

    public function getMonitoringReportAcknowledgment($year, $quarter)
    {
        $submission = $this->getMonitoringReportSubmission($year, $quarter);

        return $submission ? $submission->acknowledgment : [];
    }

    public function getProjectExceptionAcknowledgment($year, $quarter)
    {
        $submission = $this->getProjectExceptionSubmission($year, $quarter);

        return $submission ? $submission->acknowledgment : [];
    }

    public function getProjectResultsAcknowledgment($year)
    {
        $submission = $this->getProjectResultsSubmission($year);

        return $submission ? $submission->acknowledgment : [];
    }
}
