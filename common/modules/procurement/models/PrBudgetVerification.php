<?php

namespace common\modules\procurement\models;

use Yii;

/**
 * This is the model class for table "pr_budget_verification".
 *
 * @property int $id
 * @property int|null $pr_id
 * @property string|null $allotment
 * @property string|null $fund_cluster
 * @property string|null $rc_code
 * @property string|null $source_of_fund
 * @property string|null $charge_to
 * @property string|null $remarks
 *
 * @property PrPr $pr
 */
class PrBudgetVerification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pr_budget_verification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['allotment', 'fund_cluster', 'rc_code', 'source_of_fund', 'charge_to'], 'required'],
            [['pr_id'], 'integer'],
            [['charge_to', 'remarks'], 'string'],
            [['allotment'], 'string', 'max' => 200],
            [['fund_cluster', 'rc_code', 'source_of_fund'], 'string', 'max' => 100],
            [['pr_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrPr::className(), 'targetAttribute' => ['pr_id' => 'id']],
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
            'allotment' => 'Allotment',
            'fund_cluster' => 'Fund Cluster',
            'rc_code' => 'Responsibility Center Code',
            'source_of_fund' => 'Source Of Fund',
            'charge_to' => 'Charge To',
            'remarks' => 'Remarks',
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
}
