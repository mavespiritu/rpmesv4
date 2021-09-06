<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_default_nep".
 *
 * @property int $id
 * @property int|null $pap_id
 * @property int|null $fund_source_id
 * @property int|null $arrangement
 *
 * @property PpmpPap $pap
 */
class DefaultPap extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_default_pap';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pap_id', 'fund_source_id', 'arrangement'], 'integer'],
            [['pap_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pap::className(), 'targetAttribute' => ['pap_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pap_id' => 'Pap ID',
            'fund_source_id' => 'Fund Source ID',
            'arrangement' => 'Arrangement',
        ];
    }

    /**
     * Gets query for [[Pap]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPap()
    {
        return $this->hasOne(Pap::className(), ['id' => 'pap_id']);
    }
}
