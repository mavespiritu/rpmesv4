<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_sdg_goal".
 *
 * @property int|null $project_id
 * @property int|null $year
 * @property int|null $sdg_goal_id
 *
 * @property SdgGoal $sdgGoal
 * @property Project $project
 */
class ProjectSdgGoal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_sdg_goal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sdg_goal_id'], 'required'],
            [['project_id', 'year', 'sdg_goal_id'], 'integer'],
            [['sdg_goal_id'], 'exist', 'skipOnError' => true, 'targetClass' => SdgGoal::className(), 'targetAttribute' => ['sdg_goal_id' => 'id']],
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
            'sdg_goal_id' => 'SDG Goal',
        ];
    }

    /**
     * Gets query for [[SdgGoal]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSdgGoal()
    {
        return $this->hasOne(SdgGoal::className(), ['id' => 'sdg_goal_id']);
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
