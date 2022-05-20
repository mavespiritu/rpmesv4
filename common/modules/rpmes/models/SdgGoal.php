<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "sdg_goal".
 *
 * @property int $id
 * @property int|null $sdg_no
 * @property string|null $title
 * @property string|null $description
 *
 * @property ProjectSdgGoal[] $projectSdgGoals
 */
class SdgGoal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sdg_goal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sdg_no', 'title'], 'required'],
            [['sdg_no'], 'unique', 'message' => 'The number has been used already'],
            [['title'], 'unique', 'message' => 'The title has been used already'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sdg_no' => 'SDG No.',
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[ProjectSdgGoals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectSdgGoals()
    {
        return $this->hasMany(ProjectSdgGoal::className(), ['sdg_goal_id' => 'id']);
    }
}
