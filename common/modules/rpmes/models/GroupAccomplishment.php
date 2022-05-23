<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "group_accomplishment".
 *
 * @property int $id
 * @property int|null $project_id
 * @property int|null $year
 * @property string|null $quarter
 * @property string|null $value
 * @property string|null $remarks
 *
 * @property Project $project
 */
class GroupAccomplishment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group_accomplishment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'year'], 'integer'],
            [['quarter', 'value', 'remarks'], 'string'],
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
            'value' => 'Value',
            'remarks' => 'Remarks',
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
