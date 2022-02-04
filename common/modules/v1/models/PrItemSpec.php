<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_pr_item_spec".
 *
 * @property int $id
 * @property int|null $pr_id
 * @property int|null $item_id
 * @property float|null $cost
 *
 * @property PpmpPrItemSpecValue[] $ppmpPrItemSpecValues
 */
class PrItemSpec extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_pr_item_spec';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pr_id', 'item_id'], 'integer'],
            [['cost'], 'number'],
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
            'item_id' => 'Item ID',
            'cost' => 'Cost',
        ];
    }

    /**
     * Gets query for [[PpmpPrItemSpecValues]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrItemSpecValues()
    {
        return $this->hasMany(PrItemSpecValue::className(), ['pr_item_spec' => 'id']);
    }

    public function getPrItemSpecValueString()
    {
        $details = '';
        
        if($this->prItemSpecValues)
        {
            $i = 0;
            foreach($this->prItemSpecValues as $value)
            {
                $details .= $i == count($this->prItemSpecValues) - 1 ? $value->description.': '.$value->value : $value->description.': '.$value->value.', '; 

                $i++;
            }
        }

        return $details;
    }
}
