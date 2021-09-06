<?php

namespace common\modules\procurement\models;

use Yii;

/**
 * This is the model class for table "pr_item".
 *
 * @property int $id
 * @property int|null $pr_id
 * @property int|null $item_no
 * @property int|null $stock_inventory_id
 * @property string|null $unit
 * @property string|null $item
 * @property string|null $description
 * @property int|null $quantity
 * @property float|null $unit_cost
 *
 * @property PrPr $pr
 * @property PrStockInventory $stockInventory
 * @property PrSupplierQuoteSpec[] $prSupplierQuoteSpecs
 */
class PrItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pr_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stock_inventory_id', 'quantity', 'unit_cost'], 'required', 'on' => 'autoMode'],
            [['item', 'description', 'unit', 'quantity', 'unit_cost'], 'required', 'on' => 'manualMode'],
            [['pr_id', 'item_no', 'stock_inventory_id', 'quantity'], 'integer'],
            [['description'], 'string'],
            [['unit_cost'], 'number'],
            [['unit', 'item'], 'string', 'max' => 100],
            [['pr_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrPr::className(), 'targetAttribute' => ['pr_id' => 'id']],
            [['stock_inventory_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrStockInventory::className(), 'targetAttribute' => ['stock_inventory_id' => 'id']],
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
            'item_no' => 'Item No',
            'stock_inventory_id' => 'Stock Inventory',
            'unit' => 'Unit',
            'item' => 'Item',
            'description' => 'Description',
            'quantity' => 'Quantity',
            'unit_cost' => 'Unit Cost',
        ];
    }

    /**
     * Gets query for [[Pr]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPr()
    {
        return $this->hasOne(PrPr::className(), ['id' => 'pr_id']);
    }

    /**
     * Gets query for [[StockInventory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockInventory()
    {
        return $this->hasOne(PrStockInventory::className(), ['id' => 'stock_inventory_id']);
    }

    /**
     * Gets query for [[PrSupplierQuoteSpecs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrSupplierQuoteSpecs()
    {
        return $this->hasMany(PrSupplierQuoteSpec::className(), ['item_id' => 'id']);
    }

    /**
     * Gets query for [[ItemApproval]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrItemApprovals()
    {
        return $this->hasMany(PrItemApproval::className(), ['item_id' => 'id']);
    }

    public function getApproval()
    {
        $approval = $this->getPrItemApprovals()->orderBy(['id' => SORT_DESC])->one();

        return $approval;
    }

    public function getApprovalStatus()
    {
        $approval = $this->getPrItemApprovals()->orderBy(['id' => SORT_DESC])->one();

        return $approval ? $approval->status : 'FOR PROCUREMENT CHECKING';
    }

    public function getApprovalColor()
    {
        $status = $this->approvalStatus;
        $color = '';

        if($status == 'FOR PROCUREMENT CHECKING')
        {
            $color = 'none';
        }else if($status == 'FOR REVISION')
        {
            $color = '#F39C12';
        }else if($status == 'DISAPPROVED')
        {
            $color = '#DD4B39';
        }

        return $color;
    }
}
