<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_result".
 *
 * @property int $id
 * @property int $project_id
 * @property string|null $objective
 * @property string|null $results_indicator
 * @property string|null $observed_results
 * @property string|null $deadline
 * @property int|null $submitted_by
 * @property string|null $date_submitted
 *
 * @property Project $project
 */
class ProjectResult extends \yii\db\ActiveRecord
{
    public $agency_id;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_result';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['year', 'project_id', 'objective', 'results_indicator', 'observed_results', 'deadline', 'action'], 'required'],
            [['project_id', 'submitted_by','agency_id'], 'integer'],
            [['objective', 'results_indicator', 'observed_results', 'action'], 'string'],
            [['year'], 'required', 'on' => 'projectResult'],
            [['deadline', 'date_submitted', 'year'], 'safe'],
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
            'year' => 'Year',
            'project_id' => 'Project ID',
            'objective' => 'Objective',
            'results_indicator' => 'Results Indicator',
            'observed_results' => 'Observed Results',
            'deadline' => 'Deadline',
            'submitted_by' => 'Submitted By',
            'date_submitted' => 'Date Submitted',
            'action' => 'Action',
        ];
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
