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
            [['project_id', 'submitted_by','agency_id'], 'integer'],
            [['objective', 'observed_results', 'action', 'quarter'], 'string'],
            [[ 'date_submitted', 'year'], 'safe'],
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
            'quarter' => 'Quarter',
            'year' => 'Year',
            'project_id' => 'Project ID',
            'objective' => 'Objective',
            'observed_results' => 'Observed Results',
            'submitted_by' => 'Submitted By',
            'date_submitted' => 'Date Submitted',
            'action' => '',
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
