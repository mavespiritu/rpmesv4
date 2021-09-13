<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_object_item".
 *
 * @property int $id
 * @property int|null $obj_id
 * @property int|null $item_id
 *
 * @property PpmpItem $item
 * @property PpmpObj $obj
 */
class ObjectItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_object_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obj_id', 'item_id'], 'integer'],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'id']],
            [['obj_id'], 'exist', 'skipOnError' => true, 'targetClass' => Obj::className(), 'targetAttribute' => ['obj_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'obj_id' => 'Obj ID',
            'item_id' => 'Item ID',
        ];
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
}
