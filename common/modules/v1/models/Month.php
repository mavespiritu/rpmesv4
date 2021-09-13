<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_month".
 *
 * @property int $id
 * @property string|null $semester
 * @property string|null $quarter
 * @property string|null $month
 * @property string|null $abbreviation
 *
 * @property PpmpPpmpItemBreakdown[] $ppmpPpmpItemBreakdowns
 */
class Month extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_month';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['semester', 'quarter', 'month'], 'string', 'max' => 100],
            [['abbreviation'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'semester' => 'Semester',
            'quarter' => 'Quarter',
            'month' => 'Month',
            'abbreviation' => 'Abbreviation',
        ];
    }

    /**
     * Gets query for [[PpmpPpmpItemBreakdowns]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPpmpPpmpItemBreakdowns()
    {
        return $this->hasMany(PpmpPpmpItemBreakdown::className(), ['month_id' => 'id']);
    }
}
