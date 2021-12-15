<?php

namespace common\modules\procurement\models;

use Yii;

/**
 * This is the model class for table "pr_procurement_type".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 *
 * @property PrProcVerification[] $prProcVerifications
 * @property PrProcurementSubtype[] $prProcurementSubtypes
 */
class PrProcurementType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pr_procurement_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
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
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[PrProcVerifications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrProcVerifications()
    {
        return $this->hasMany(PrProcVerification::className(), ['procurement_type_id' => 'id']);
    }

    /**
     * Gets query for [[PrProcurementSubtypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrProcurementSubtypes()
    {
        return $this->hasMany(PrProcurementSubtype::className(), ['procurement_type_id' => 'id']);
    }
}
