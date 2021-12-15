<?php

namespace common\modules\v1\models;

use Yii;
use markavespiritu\user\models\Office;

/**
 * This is the model class for table "ppmp_appropriation_allocation".
 *
 * @property int $id
 * @property int|null $appropriation_item_id
 * @property int|null $office_id
 * @property float|null $amount
 *
 * @property PpmpAppropriationItem $appropriationItem
 */
class AppropriationAllocation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_appropriation_allocation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount'], 'validateAmount'],
            [['amount'], 'required'],
            [['appropriation_item_id', 'office_id'], 'integer'],
            [['appropriation_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => AppropriationItem::className(), 'targetAttribute' => ['appropriation_item_id' => 'id']],
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
            'office_id' => 'Office ID',
            'amount' => 'Amount',
        ];
    }
    
    public function validateAmount($attribute, $params, $validator)
    {
        $item = AppropriationItem::findOne(['id' => $this->appropriation_item_id]);
        $model = AppropriationAllocation::findOne(['appropriation_item_id' => $this->appropriation_item_id, 'office_id' => $this->office_id]);

        if($model)
        {
            if ($this->$attribute > ($item->remaining + $model->amount)) {
                $this->addError($attribute, 'The amount input has exceeded the remaining amount');
            }
        }else{
            if ($this->$attribute > $item->remaining) {
                $this->addError($attribute, 'The amount input has exceeded the remaining amount');
            }
        }
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
}
