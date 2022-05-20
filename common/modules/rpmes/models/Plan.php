<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "plan".
 *
 * @property int $id
 * @property int|null $project_id
 * @property string|null $year
 * @property string|null $date_submitted
 * @property int|null $submitted_by
 *
 * @property Project $project
 */
class Plan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'plan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'submitted_by'], 'integer'],
            [['date_submitted'], 'safe'],
            [['year'], 'string', 'max' => 5],
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
            'date_submitted' => 'Date Submitted',
            'submitted_by' => 'Submitted By',
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
