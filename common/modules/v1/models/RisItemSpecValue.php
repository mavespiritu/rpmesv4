<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_ris_item_spec_value".
 *
 * @property int $id
 * @property int|null $ris_item_spec_id
 * @property string|null $description
 * @property string|null $value
 *
 * @property PpmpRisItemSpec $risItemSpec
 */
class RisItemSpecValue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_ris_item_spec_value';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'value'], 'required'],
            [['ris_item_spec_id'], 'integer'],
            [['description', 'value'], 'string'],
            [['ris_item_spec_id'], 'exist', 'skipOnError' => true, 'targetClass' => RisItemSpec::className(), 'targetAttribute' => ['ris_item_spec_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ris_item_spec_id' => 'Ris Item Spec ID',
            'description' => 'Description',
            'value' => 'Value',
        ];
    }

    /**
     * Gets query for [[RisItemSpec]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRisItemSpec()
    {
        return $this->hasOne(RisItemSpec::className(), ['id' => 'ris_item_spec_id']);
    }
}
