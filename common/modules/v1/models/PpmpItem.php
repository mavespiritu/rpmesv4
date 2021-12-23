<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_ppmp_item".
 *
 * @property int $id
 * @property int|null $appropriation_item_id
 * @property int|null $activity_id
 * @property int|null $sub_activity_id
 * @property int|null $obj_id
 * @property int|null $ppmp_id
 * @property int|null $item_id
 * @property float|null $cost
 * @property string|null $remarks
 *
 * @property PpmpActivity $activity
 * @property PpmpAppropriationItem $appropriationItem
 * @property PpmpPpmp $ppmp
 * @property PpmpItem $item
 * @property PpmpObj $obj
 * @property PpmpSubActivity $subActivity
 * @property PpmpPpmpItemBreakdown[] $ppmpPpmpItemBreakdowns
 */
class PpmpItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_ppmp_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sub_activity_id', 'obj_id', 'item_id'], 'required'],
            [['appropriation_item_id', 'activity_id', 'sub_activity_id', 'obj_id', 'ppmp_id', 'item_id', 'fund_source_id'], 'integer'],
            [['cost'], 'number'],
            [['remarks', 'type'], 'string'],
            [['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => Activity::className(), 'targetAttribute' => ['activity_id' => 'id']],
            [['appropriation_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => AppropriationItem::className(), 'targetAttribute' => ['appropriation_item_id' => 'id']],
            [['ppmp_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ppmp::className(), 'targetAttribute' => ['ppmp_id' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'id']],
            [['obj_id'], 'exist', 'skipOnError' => true, 'targetClass' => Obj::className(), 'targetAttribute' => ['obj_id' => 'id']],
            [['sub_activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubActivity::className(), 'targetAttribute' => ['sub_activity_id' => 'id']],
            [['fund_source_id'], 'exist', 'skipOnError' => true, 'targetClass' => FundSource::className(), 'targetAttribute' => ['fund_source_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'appropriation_item_id' => 'Appropriation Item ID',
            'fund_source_id' => 'Fund Source ID',
            'activity_id' => 'Activity ID',
            'sub_activity_id' => 'PPA',
            'obj_id' => 'Object',
            'ppmp_id' => 'Ppmp ID',
            'item_id' => 'Item',
            'quantity' => 'Quantity',
            'quantityUsed' => 'Quantity',
            'cost' => 'Cost',
            'remarks' => 'Remarks',
            'type' => 'Type',
        ];
    }

    /**
     * Gets query for [[Activity]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActivity()
    {
        return $this->hasOne(Activity::className(), ['id' => 'activity_id']);
    }

    /**
     * Gets query for [[AppropriationItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppropriationItem()
    {
        return $this->hasOne(AppropriationItem::className(), ['id' => 'appropriation_item_id']);
    }

    /**
     * Gets query for [[Ppmp]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPpmp()
    {
        return $this->hasOne(Ppmp::className(), ['id' => 'ppmp_id']);
    }

    /**
     * Gets query for [[Item]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }

    /**
     * Gets query for [[Obj]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObj()
    {
        return $this->hasOne(Obj::className(), ['id' => 'obj_id']);
    }

    /**
     * Gets query for [[SubActivity]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubActivity()
    {
        return $this->hasOne(SubActivity::className(), ['id' => 'sub_activity_id']);
    }

    /**
     * Gets query for [[FundSource]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFundSource()
    {
        return $this->hasOne(FundSource::className(), ['id' => 'fund_source_id']);
    }

    /**
     * Gets query for [[PpmpPpmpItemBreakdowns]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItemBreakdowns()
    {
        return $this->hasMany(ItemBreakdown::className(), ['ppmp_item_id' => 'id']);
    }

    public static function pageQuantityTotal($provider, $fieldName)
    {
        $total = 0;
        foreach($provider as $item){
            $total+=$item[$fieldName];
        }
        return '<b>'.number_format($total, 2).'</b>';
    }

    public function getQuantity()
    {
        $total = ItemBreakdown::find()->select(['sum(quantity) as quantity'])->where(['ppmp_item_id' => $this->id])->asArray()->one();

        return $total['quantity'];
    }

    public function getQuantityUsed()
    {
        $total = RisSource::find()->select(['sum(quantity) as quantity'])->where(['ppmp_item_id' => $this->id])->asArray()->one();

        return $total['quantity'];
    }

    public function getQuantityUsedPerMonth($month_id)
    {
        $total = RisSource::find()->select(['sum(quantity) as quantity'])->where(['ppmp_item_id' => $this->id, 'month_id' => $month_id])->asArray()->one();

        return $total['quantity'];
    }

    public function getQuantityPerMonth($month_id)
    {
        $quantity = $this->getItemBreakdowns()->where(['month_id' => $month_id])->one();

        return $quantity ? $quantity->quantity : 0;
    }

    public function getRemainingQuantityPerMonth($month_id)
    {
        $maxQuantity = $this->getItemBreakdowns()->where(['month_id' => $month_id])->one();

        return $maxQuantity ? $maxQuantity->quantity - $this->getQuantityUsedPerMonth($month_id) : 0;
    }

    public function getRemainingQuantity()
    {
        return $this->quantity - $this->quantityUsed;
    }

    public function getRemainingQuantityTotalCost()
    {
        return $this->remainingQuantity * $this->cost;
    }

    public function getTotalCost()
    {
        return $this->quantity * $this->cost;
    }

    public static function getTotalPerActivity($ppmp_id, $activity_id, $fund_source_id)
    {
        $quantity = ItemBreakdown::find()
                   ->select([
                       'ppmp_item_id',
                       'sum(quantity) as total'
                   ])
                    ->groupBy(['ppmp_item_id'])
                    ->createCommand()
                    ->getRawSql();

        $total = PpmpItem::find()
                ->select([
                    'sum(quantity.total * cost) as total'
                ])
                ->leftJoin(['quantity' => '('.$quantity.')'], 'quantity.ppmp_item_id = ppmp_ppmp_item.id')
                ->andWhere([
                    'ppmp_id' => $ppmp_id,
                    'activity_id' => $activity_id,
                    'fund_source_id' => $fund_source_id,
                ])
                ->asArray()
                ->one();
        
        return $total['total'];
    }

    public static function getTotalPerSubActivity($ppmp_id, $activity_id, $sub_activity_id, $fund_source_id)
    {
        $quantity = ItemBreakdown::find()
                   ->select([
                       'ppmp_item_id',
                       'sum(quantity) as total'
                   ])
                    ->groupBy(['ppmp_item_id'])
                    ->createCommand()
                    ->getRawSql();

        $total = PpmpItem::find()
                ->select([
                    'sum(quantity.total * cost) as total'
                ])
                ->leftJoin(['quantity' => '('.$quantity.')'], 'quantity.ppmp_item_id = ppmp_ppmp_item.id')
                ->andWhere([
                    'ppmp_id' => $ppmp_id,
                    'activity_id' => $activity_id,
                    'sub_activity_id' => $sub_activity_id,
                    'fund_source_id' => $fund_source_id,
                ])
                ->asArray()
                ->one();
        
        return $total['total'];
    }

    public static function getCountPerSubActivity($ppmp_id, $activity_id, $sub_activity_id, $fund_source_id)
    {
        $total = PpmpItem::find()
                ->andWhere([
                    'ppmp_id' => $ppmp_id,
                    'activity_id' => $activity_id,
                    'sub_activity_id' => $sub_activity_id,
                    'fund_source_id' => $fund_source_id,
                ])
                ->count();
        
        return $total > 1 ? $total.' items' : $total.' item';
    }
}
