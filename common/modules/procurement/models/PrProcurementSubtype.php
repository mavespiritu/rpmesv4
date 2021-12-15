<?php

namespace common\modules\procurement\models;

use Yii;

/**
 * This is the model class for table "pr_procurement_subtype".
 *
 * @property int $id
 * @property int|null $procurement_type_id
 * @property string|null $title
 * @property string|null $description
 *
 * @property PrProcVerification[] $prProcVerifications
 * @property PrProcurementType $procurementType
 */
class PrProcurementSubtype extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pr_procurement_subtype';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['procurement_type_id'], 'integer'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 100],
            [['procurement_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrProcurementType::className(), 'targetAttribute' => ['procurement_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'procurement_type_id' => 'Procurement Type ID',
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
        return $this->hasMany(PrProcVerification::className(), ['procurement_subtype_id' => 'id']);
    }

    /**
     * Gets query for [[ProcurementType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProcurementType()
    {
        return $this->hasOne(PrProcurementType::className(), ['id' => 'procurement_type_id']);
    }
}
