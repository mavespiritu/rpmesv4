<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "outcome_accomplishment".
 *
 * @property int $id
 * @property int|null $project_id
 * @property int|null $outcome_id
 * @property int|null $year
 * @property string|null $value
 *
 * @property ProjectOutcome $outcome
 * @property Project $project
 */
class OutcomeAccomplishment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'outcome_accomplishment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value'], 'required'],
            [['project_id', 'outcome_id', 'year'], 'integer'],
            [['value'], 'string'],
            [['outcome_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectOutcome::className(), 'targetAttribute' => ['outcome_id' => 'id']],
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
            'outcome_id' => 'Outcome ID',
            'year' => 'Year',
            'value' => 'Outcome',
        ];
    }

    /**
     * Gets query for [[Outcome]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOutcome()
    {
        return $this->hasOne(ProjectOutcome::className(), ['id' => 'outcome_id']);
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
