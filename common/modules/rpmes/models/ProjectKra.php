<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_kra".
 *
 * @property int $id
 * @property int|null $project_id
 * @property int|null $year
 * @property int|null $key_result_area_id
 *
 * @property Project $project
 * @property KeyResultArea $keyResultArea
 */
class ProjectKra extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_kra';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'year', 'key_result_area_id'], 'integer'],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
            [['key_result_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => KeyResultArea::className(), 'targetAttribute' => ['key_result_area_id' => 'id']],
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
            'key_result_area_id' => 'KRA/Cluster',
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

    /**
     * Gets query for [[KeyResultArea]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKeyResultArea()
    {
        return $this->hasOne(KeyResultArea::className(), ['id' => 'key_result_area_id']);
    }
}
