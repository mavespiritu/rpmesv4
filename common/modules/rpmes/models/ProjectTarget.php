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
            [['q1', 'q2', 'q3', 'q4'], function ($attribute, $params, $validator) {
                $project = Project::findOne(['id' => $this->project_id]) ? Project::findOne(['id' => $this->project_id]) : new Project();
                $data_type = !$project->isNewRecord ? $project->data_type : '';
                $values = [];
                if($this->q1 > 0) $values[] = $this->q1; 
                if($this->q2 > 0) $values[] = $this->q2; 
                if($this->q3 > 0) $values[] = $this->q3; 
                if($this->q4 > 0) $values[] = $this->q4;

                if ($this->target_type == 'Physical') {
                    if($data_type == 'Cumulative')
                    {
                        $con = false;
                        for($i = 0; $i < count($values) - 1; $i++)
                        {
                            if($values[$i + 1] <= $values[$i])
                            {
                                $con = true;
                            }
                        }

                        if($con == true)
                        {
                            $this->addError($attribute, 'Input must be cumulative');
                        }
                    }else if($data_type == 'Maintained')
                    {
                        $con = false;
                        for($i = 0; $i < count($values) - 1; $i++)
                        {
                            if($values[$i + 1] != $values[$i])
                            {
                                $con = true;
                            }
                        }

                        if($con == true)
                        {
                            $this->addError($attribute, 'Input must be equal to each other');
                        }
                    }
                }else if($this->target_type == 'Financial')
                {
                    if($data_type == 'Cumulative')
                    {
                        $con = false;
                        for($i = 0; $i < count($values) - 1; $i++)
                        {
                            if($values[$i + 1] <= $values[$i])
                            {
                                $con = true;
                            }
                        }

                        if($con == true)
                        {
                            $this->addError($attribute, 'Input must be cumulative');
                        }
                    }
                }
            }],
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
