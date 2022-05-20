<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "due_date".
 *
 * @property int $id
 * @property string|null $report
 * @property string|null $quarter
 * @property string|null $semester
 * @property int|null $year
 * @property string|null $due_date
 */
class DueDate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'due_date';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['due_date'], 'required'],
            [['report', 'quarter', 'semester'], 'string'],
            [['year'], 'integer'],
            [['due_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report' => 'Report',
            'quarter' => 'Quarter',
            'semester' => 'Semester',
            'year' => 'Year',
            'due_date' => 'Due Date',
        ];
    }
}
