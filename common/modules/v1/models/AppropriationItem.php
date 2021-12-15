<?php

namespace common\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ppmp_appropriation_item".
 *
 * @property int $id
 * @property int|null $appropriation_id
 * @property int|null $appropriation_obj_id
 * @property int|null $appropriation_pap_id
 * @property float|null $amount
 *
 * @property PpmpAppropriationObj $appropriationObj
 * @property PpmpAppropriationPap $appropriationPap
 */
class AppropriationItem extends \yii\db\ActiveRecord
{
    public $idx;
    public $activity_id;
    public $office_id;
    public $sub_activity_id;
    public $stage;
    public $year;
    public $order;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_appropriation_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stage', 'year'], 'required', 'on' => 'loadAppropriation'],
            [['stage', 'year', 'activity_id'], 'required', 'on' => 'loadBudgetMonitoring'],
            [['stage', 'year'], 'required', 'on' => 'loadPpmpMonitoringUser'],
            [['stage', 'year', 'office_id'], 'required', 'on' => 'loadPpmpMonitoringAdmin'],
            [['activity_id', 'fund_source_id'], 'required', 'on' => 'loadItems'],
            [['activity_id', 'sub_activity_id', 'fund_source_id'], 'required', 'on' => 'loadItemsInRis'],
            [['amount'], 'required'],
            [['appropriation_id', 'obj_id', 'pap_id', 'fund_source_id'], 'integer'],
            [['amount'], 'number'],
            [['appropriation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Appropriation::className(), 'targetAttribute' => ['appropriation' => 'id']],
            [['obj_id'], 'exist', 'skipOnError' => true, 'targetClass' => AppropriationObj::className(), 'targetAttribute' => ['obj_id' => 'id']],
            [['pap_id'], 'exist', 'skipOnError' => true, 'targetClass' => AppropriationPap::className(), 'targetAttribute' => ['pap_id' => 'id']],
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
            'appropriation_id' => 'Source',
            'obj_id' => 'Object ID',
            'pap_id' => 'Program',
            'stage' => 'Stage',
            'type' => 'Type',
            'year' => 'Year',
            'activity_id' => 'Activity',
            'sub_activity_id' => 'PAP',
            'fund_source_id' => 'Fund Source',
            'office_id' => 'Division',
            'amount' => 'Amount',
            'order' => 'Sort (Top to Bottom)'
        ];
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
     * Gets query for [[AppropriationObj]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppropriationObj()
    {
        return $this->hasOne(AppropriationObj::className(), ['appropriation_id' => 'appropriation_id', 'obj_id' => 'obj_id']);
    }

    /**
     * Gets query for [[AppropriationPap]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppropriationPap()
    {
        return $this->hasOne(AppropriationPap::className(), ['appropriation_id' => 'appropriation_id', 'pap_id' => 'pap_id', 'fund_source_id' => 'fund_source_id']);
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
     * Gets query for [[AppropriationAllocation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppropriationAllocations()
    {
        return $this->hasMany(AppropriationAllocation::className(), ['appropriation_item_id' => 'id']);
    }

    public function getRemaining()
    {
        $totalAmount = AppropriationAllocation::find()
                    ->select([
                        'sum(amount) as total'
                    ])
                    ->where(['ppmp_appropriation_allocation.appropriation_item_id' => $this->id])
                    ->asArray()
                    ->one();
        
        return $this->amount - $totalAmount['total'];
    }
}
