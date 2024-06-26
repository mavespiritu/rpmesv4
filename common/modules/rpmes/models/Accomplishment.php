<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "accomplishment".
 *
 * @property int $id
 * @property int|null $project_id
 * @property int|null $year
 * @property string|null $quarter
 * @property string|null $remarks
 * @property string|null $action
 *
 * @property Project $project
 */
class Accomplishment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'accomplishment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'year'], 'integer'],
            [['quarter'], 'string'],
            [['remarks', 'action'], 'safe'],
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
            'quarter' => 'Quarter',
            'remarks' => 'Remarks',
            'action' => 'Action',
            'submitted_by' => 'Submitted By',
            'date_submitted' => 'Date Submitted'
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
    public function getSubmitter()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'submitted_by']);
    }
    public function getSubmitterName()
    {
        return $this->submitter ? $this->submitter->fullName : '';
    }
}
