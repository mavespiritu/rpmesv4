<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_has_output_indicators".
 *
 * @property int $id
 * @property int|null $project_id
 * @property string|null $indicator
 * @property string|null $target
 *
 * @property Project $project
 */
class ProjectHasOutputIndicators extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_has_output_indicators';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['indicator', 'target'], 'required'],
            [['project_id'], 'integer'],
            [['indicator', 'target'], 'string'],
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
