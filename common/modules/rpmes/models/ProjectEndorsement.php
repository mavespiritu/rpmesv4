<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_endorsement".
 *
 * @property int $id
 * @property int|null $year
 * @property string|null $quarter
 * @property int|null $project_id
 * @property string|null $npmc_action
 *
 * @property Project $project
 */
class ProjectEndorsement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_endorsement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['year', 'quarter', 'project_id', 'npmc_action'], 'required'],
            [[
                'year', 
                'quarter', 
            ], 'required', 'on' => 'generate'],
            [['year', 'project_id'], 'integer'],
            [['quarter', 'npmc_action'], 'string'],
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
            'quarter' => 'Quarter',
            'project_id' => 'Project',
            'npmc_action' => 'Requested NPMC Action',
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

    public function getProjectException()
    {
        return $this->project->getProjectExceptions()->where(['year' => $this->year, 'quarter' => $this->quarter])->one();
    }
}
