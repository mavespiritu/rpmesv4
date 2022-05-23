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
            [['causes', 'recommendations'], 'required'],
            [['project_id', 'year', 'submitted_by'], 'integer'],
            [['quarter', 'findings', 'causes', 'recommendations'], 'string'],
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
            'year' => 'Year',
            'quarter' => 'Quarter',
            'findings' => 'Findings',
            'causes' => 'Causes',
            'recommendations' => 'Recommendations',
            'submitted_by' => 'Submitted By',
            'date_submitted' => 'Date Submitted',
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
