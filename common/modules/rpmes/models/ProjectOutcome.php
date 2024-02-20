<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_outcome".
 *
 * @property int $id
 * @property int|null $project_id
 * @property int|null $year
 * @property string|null $outcome
 * @property string|null $performance_indicator
 * @property string|null $target
 * @property string|null $timeline
 * @property string|null $remarks
 *
 * @property Project $project
 */
class ProjectOutcome extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_outcome';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'year'], 'integer'],
            [['outcome'], 'required'],
            [['outcome', 'performance_indicator', 'target', 'timeline', 'remarks'], 'string'],
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
            'outcome' => 'Outcome',
            'performance_indicator' => 'Performance Indicator',
            'target' => 'Target',
            'timeline' => 'Timeline',
            'remarks' => 'Remarks',
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

    public function getAccomplishment($year){
        $accomplishment = OutcomeAccomplishment::findOne([
            'project_id' => $this->project_id,
            'outcome_id' => $this->id,
            'year' => $this->year,
        ]);

        return $accomplishment;
    }
}
