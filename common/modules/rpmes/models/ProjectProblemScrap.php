<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_problem".
 *
 * @property int $id
 * @property int|null $project_id
 * @property string|null $nature
 * @property string|null $detail
 * @property string|null $strategy
 * @property string|null $responsible_entity
 * @property string|null $lesson_learned
 * @property int|null $submitted_by
 * @property string|null $date_submitted
 *
 * @property Project $project
 */
class ProjectProblem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_problem';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'submitted_by'], 'integer'],
            [['nature', 'detail', 'strategy', 'responsible_entity', 'lesson_learned'], 'string'],
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
            'nature' => 'Nature',
            'detail' => 'Detail',
            'strategy' => 'Strategy',
            'responsible_entity' => 'Responsible Entity',
            'lesson_learned' => 'Lesson Learned',
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
