<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_finding".
 *
 * @property int $id
 * @property string|null $quarter
 * @property int|null $year
 * @property int|null $project_id
 * @property string|null $inspection_date
 * @property string|null $major_finding
 * @property string|null $issues
 * @property string|null $action
 */
class ProjectFinding extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_finding';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['year', 'quarter', 'project_id', 'inspection_date', 'site_details', 'major_finding', 'issues', 'action', 'action_to_be_taken'], 'required'],
            [['quarter', 'site_details', 'major_finding', 'issues', 'action', 'action_to_be_taken'], 'string'],
            [['year', 'project_id'], 'integer'],
            [['inspection_date'], 'safe'],
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
            'project_id' => 'Project',
            'inspection_date' => 'Date of Project Inspection',
            'site_details' => 'Details on Site(s) Inspected',
            'major_finding' => 'Findings',
            'issues' => 'Issues',
            'action' => 'Actions Taken',
            'action_to_be_taken' => 'Actions to be Taken',
        ];
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }
}
