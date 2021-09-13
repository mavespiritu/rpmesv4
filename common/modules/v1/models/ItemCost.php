<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_item_cost".
 *
 * @property int $id
 * @property int|null $ppmp_item_id
 * @property float|null $cost
 * @property string|null $datetime
 *
 * @property PpmpItem $ppmpItem
 */
class ItemCost extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_item_cost';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_id'], 'integer'],
            [['cost'], 'number'],
            [['datetime'], 'safe'],
            [['ppmp_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_id' => 'Ppmp Item ID',
            'cost' => 'Cost',
            'datetime' => 'Datetime',
        ];
    }

    /**
     * Gets query for [[PpmpItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }
}
