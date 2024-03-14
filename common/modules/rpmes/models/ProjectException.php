<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_exception".
 *
 * @property int $id
 * @property int|null $project_id
 * @property int|null $year
 * @property string|null $quarter
 * @property string|null $findings
 * @property string|null $causes
 * @property string|null $recommendations
 * @property int|null $submitted_by
 * @property string|null $date_submitted
 *
 * @property Project $project
 */
class ProjectException extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_exception';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'findings',
                'typology_id',
                'issue_status',
                'causes', 
                'action_taken', 
                'recommendations'
            ], 'required'],
            [['requested_action'], 'required', 'on' => 'endorse'],
            [['project_id', 'typology_id', 'for_npmc_action', 'year', 'submitted_by'], 'integer'],
            [['quarter', 'findings', 'causes', 'recommendations', 'requested_action'], 'string'],
            [['date_submitted'], 'safe'],
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
            'typology_id' => 'Typology',
            'other_typology' => 'Other typology',
            'year' => 'Year',
            'quarter' => 'Quarter',
            'findings' => 'Issue Details',
            'issue_status' => 'Issue Status',
            'causes' => 'Reasons',
            'recommendations' => 'Actions to be taken',
            'action_taken' => 'Actions taken',
            'for_npmc_action' => 'For NPMC Action',
            'requested_action' => 'Requested Action',
            'submitted_by' => 'Submitted By',
            'date_submitted' => 'Date Submitted',
            'reviewed_by' => 'Reviewed By',
            'date_reviewed' => 'Date Reviewed',
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

    /**
     * Gets query for [[Typology]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTypology()
    {
        return $this->hasOne(Typology::className(), ['id' => 'typology_id']);
    }
}
