<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "expected_output_accomplishment".
 *
 * @property int $id
 * @property int|null $project_id
 * @property int|null $expected_output_id
 * @property int|null $year
 * @property string|null $quarter
 * @property string|null $value
 * @property string|null $remarks
 *
 * @property ProjectExpectedOutput $expectedOutput
 * @property Project $project
 */
class ExpectedOutputAccomplishment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'expected_output_accomplishment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value'], 'required', 'on' => 'notIndividual'],
            [['male', 'female'], 'required', 'on' => 'individual'],
            [['project_id', 'expected_output_id', 'year'], 'integer'],
            [['quarter', 'male', 'female', 'value', 'remarks'], 'string'],
            [['expected_output_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectExpectedOutput::className(), 'targetAttribute' => ['expected_output_id' => 'id']],
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
            'expected_output_id' => 'Expected Output ID',
            'year' => 'Year',
            'quarter' => 'Quarter',
            'value' => 'Value',
            'male' => 'Male',
            'female' => 'Female',
            'remarks' => 'Remarks',
        ];
    }

    /**
     * Gets query for [[ExpectedOutput]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExpectedOutput()
    {
        return $this->hasOne(ProjectExpectedOutput::className(), ['id' => 'expected_output_id']);
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
