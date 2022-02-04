<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_pr_item_spec_value".
 *
 * @property int $id
 * @property int|null $pr_item_spec
 * @property string|null $description
 * @property string|null $value
 *
 * @property PpmpPrItemSpec $prItemSpec
 */
class PrItemSpecValue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_pr_item_spec_value';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pr_item_spec'], 'integer'],
            [['description', 'value'], 'string'],
            [['pr_item_spec'], 'exist', 'skipOnError' => true, 'targetClass' => PpmpPrItemSpec::className(), 'targetAttribute' => ['pr_item_spec' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pr_item_spec' => 'Pr Item Spec',
            'description' => 'Description',
            'value' => 'Value',
        ];
    }

    /**
     * Gets query for [[PrItemSpec]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrItemSpec()
    {
        return $this->hasOne(PpmpPrItemSpec::className(), ['id' => 'pr_item_spec']);
    }
}
