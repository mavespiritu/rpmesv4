<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_appropriation_pap".
 *
 * @property int $id
 * @property int|null $appropriation_id
 * @property int|null $pap_id
 * @property int|null $fund_source_id
 *
 * @property PpmpAppropriationItem[] $ppmpAppropriationItems
 * @property PpmpAppropriation $appropriation
 * @property PpmpFundSource $fundSource
 * @property PpmpPap $pap
 */
class AppropriationPap extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_appropriation_pap';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pap_id', 'fund_source_id'], 'required'],
            [['appropriation_id', 'pap_id', 'fund_source_id'], 'integer'],
            [['appropriation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Appropriation::className(), 'targetAttribute' => ['appropriation_id' => 'id']],
            [['fund_source_id'], 'exist', 'skipOnError' => true, 'targetClass' => FundSource::className(), 'targetAttribute' => ['fund_source_id' => 'id']],
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
            'appropriation_id' => 'Appropriation ID',
            'pap_id' => 'Program',
            'fund_source_id' => 'Fund Source',
            'arrangement' => 'Arrangement',
        ];
    }

    /**
     * Gets query for [[PpmpAppropriationItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppropriationItems()
    {
        return $this->hasMany(AppropriationItem::className(), ['appropriation_id' => 'appropriation_id', 'pap_id' => 'pap_id']);
    }

    /**
     * Gets query for [[Appropriation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppropriation()
    {
        return $this->hasOne(Appropriation::className(), ['id' => 'appropriation_id']);
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
     * Gets query for [[Pap]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPap()
    {
        return $this->hasOne(Pap::className(), ['id' => 'pap_id']);
    }
}
