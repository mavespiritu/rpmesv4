<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "person_employed_accomplishment".
 *
 * @property int $id
 * @property int|null $project_id
 * @property int|null $year
 * @property string|null $quarter
 * @property int|null $male
 * @property int|null $female
 * @property string|null $remarks
 *
 * @property Project $project
 */
class PersonEmployedAccomplishment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'person_employed_accomplishment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['male', 'female'], 'required'],
            [['project_id', 'year'], 'integer'],
            [['quarter', 'remarks'], 'string'],
            [['male', 'female'], 'safe'],
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
            'male' => 'Male',
            'female' => 'Female',
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
