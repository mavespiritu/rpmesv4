<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_pr_item_cost".
 *
 * @property int $id
 * @property int|null $pr_id
 * @property int|null $pr_item_id
 * @property int|null $supplier_id
 * @property float|null $cost
 *
 * @property PpmpPrItem $prItem
 * @property PpmpSupplier $supplier
 */
class PrItemCost extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_pr_item_cost';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pr_id', 'pr_item_id', 'supplier_id'], 'integer'],
            [['cost'], 'number'],
            [['pr_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrItem::className(), 'targetAttribute' => ['pr_item_id' => 'id']],
            [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pr_id' => 'Pr ID',
            'pr_item_id' => 'Pr Item ID',
            'supplier_id' => 'Supplier ID',
            'cost' => 'Cost',
        ];
    }

    /**
     * Gets query for [[PrItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrItem()
    {
        return $this->hasOne(PrItem::className(), ['id' => 'pr_item_id']);
    }

    /**
     * Gets query for [[Supplier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }
}
