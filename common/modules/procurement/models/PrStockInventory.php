<?php

namespace common\modules\procurement\models;

use Yii;

/**
 * This is the model class for table "pr_stock_inventory".
 *
 * @property int $id
 * @property string|null $stock_code
 * @property string|null $article
 * @property string|null $description
 * @property string|null $unit
 *
 * @property PrItem[] $prItems
 */
class PrStockInventory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pr_stock_inventory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['unit'], 'required', 'on' => 'manualMode'],
            [['description'], 'string'],
            [['stock_code', 'article', 'unit'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stock_code' => 'Stock Code',
            'article' => 'Article',
            'description' => 'Description',
            'unit' => 'Unit',
        ];
    }

    /**
     * Gets query for [[PrItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrItems()
    {
        return $this->hasMany(PrItem::className(), ['stock_inventory_id' => 'id']);
    }
}
