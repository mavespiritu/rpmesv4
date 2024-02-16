<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_has_revised_schedules".
 *
 * @property int $id
 * @property int|null $project_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string|null $remarks
 *
 * @property Project $project
 */
class ProjectHasRevisedSchedules extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_has_revised_schedules';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['start_date', 'end_date'], 'required'],
            [['start_date', 'end_date'], 'validateDateRange'],
            [['project_id', 'year'], 'integer'],
            [['start_date', 'end_date'], 'safe'],
            [['remarks'], 'string'],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'year' => 'Year',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'remarks' => 'Remarks',
        ];
    }

    public function validateDateRange($attribute, $params)
    {
        $startDate = strtotime($this->start_date);
        $endDate = strtotime($this->end_date);

        if ($startDate >= $endDate) {
            $this->addError($attribute, 'End date must be greater than start date.');
        }
    }

    /**
     * Gets query for [[Project]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }
}
