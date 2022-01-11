<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_ppmp_item_breakdown".
 *
 * @property int $id
 * @property int|null $ppmp_item_id
 * @property int|null $month_id
 * @property int|null $quantity
 *
 * @property PpmpPpmpItem $ppmpItem
 * @property PpmpMonth $month
 */
class ItemBreakdown extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_ppmp_item_breakdown';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ppmp_item_id', 'month_id', 'quantity'], 'integer'],
            [['ppmp_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => PpmpItem::className(), 'targetAttribute' => ['ppmp_item_id' => 'id']],
            [['month_id'], 'exist', 'skipOnError' => true, 'targetClass' => Month::className(), 'targetAttribute' => ['month_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ppmp_item_id' => 'Ppmp Item ID',
            'month_id' => 'Month',
            'quantity' => 'Quantity',
        ];
    }

    /**
     * Gets query for [[PpmpItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPpmpItem()
    {
        return $this->hasOne(PpmpItem::className(), ['id' => 'ppmp_item_id']);
    }

    /**
     * Gets query for [[Month]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMonth()
    {
        return $this->hasOne(Month::className(), ['id' => 'month_id']);
    }

    public function getQuantityUsed()
    {
        $total = RisSource::find()->select(['sum(quantity) as quantity'])->where(['ppmp_item_id' => $this->ppmp_item_id, 'month_id' => $this->month_id])->asArray()->one();

        return $total ? $total['quantity'] : 0;
    }

    public function getRemaining()
    {
        return $this->quantity - $this->quantityUsed;
    }
}
