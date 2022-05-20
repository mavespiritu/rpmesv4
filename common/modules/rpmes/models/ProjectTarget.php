<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_target".
 *
 * @property int|null $project_id
 * @property int|null $year
 * @property string|null $target_type
 * @property string|null $indicator
 * @property float|null $q1
 * @property float|null $q2
 * @property float|null $q3
 * @property float|null $q4
 *
 * @property Project $project
 */
class ProjectTarget extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_target';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['q1', 'q2', 'q3', 'q4'], 'required'],
            [['indicator'], 'required', 'on' => 'physicalTarget'],
            [['project_id', 'year'], 'integer'],
            [['target_type'], 'string'],
            [['q1', 'q2', 'q3', 'q4'], 'safe'],
            [['indicator'], 'string', 'max' => 100],
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
            'target_type' => 'Target Type',
            'indicator' => 'Indicator',
            'q1' => 'Q1',
            'q2' => 'Q2',
            'q3' => 'Q3',
            'q4' => 'Q4',
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

    public function getIndicatorUnitOfMeasure()
    {
        $indicator = $this->indicator;

        return strpos($indicator, '%');
    }
}
