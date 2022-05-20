<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_expected_output".
 *
 * @property int $id
 * @property int|null $project_id
 * @property int|null $year
 * @property string|null $indicator
 * @property string|null $target
 *
 * @property Project $project
 */
class ProjectExpectedOutput extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_expected_output';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['indicator', 'target'], 'required'],
            [['project_id', 'year'], 'integer'],
            [['target'], 'string'],
            [['indicator'], 'string', 'max' => 200],
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
            'indicator' => 'Indicator',
            'target' => 'Target',
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
