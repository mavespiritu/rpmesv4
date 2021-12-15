<?php

namespace common\modules\procurement\models;
use markavespiritu\user\models\UserInfo;

use Yii;

/**
 * This is the model class for table "pr_pr".
 *
 * @property int $id
 * @property int|null $entity_id
 * @property string|null $dts_no
 * @property string|null $rc_code
 * @property string|null $date_requested
 * @property string|null $fund_cluster
 * @property string|null $purpose
 * @property string|null $requester
 * @property string|null $requester_designation
 * @property string|null $approver
 * @property string|null $approver_designation
 * @property string|null $source_of_fund
 * @property string|null $charge_to
 *
 * @property PrActivityTimeline[] $prActivityTimelines
 * @property PrBudgetVerification[] $prBudgetVerifications
 * @property PrItem[] $prItems
 * @property PrEntity $entity
 * @property PrProcVerification[] $prProcVerifications
 * @property PrRfq[] $prRfqs
 * @property PrTransactionHistory[] $prTransactionHistories
 */
class PrPr extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pr_pr';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dts_no'], 'unique', 'message' => 'The DTS No. has been used already'],
            [['dts_no', 'entity_name', 'purpose', 'requester', 'requester_designation', 'approver', 'approver_designation', 'date_requested'], 'required'],
            [['date_requested'], 'safe'],
            [['purpose', 'charge_to'], 'string'],
            [['dts_no', 'entity_name', 'rc_code', 'fund_cluster', 'requester', 'requester_designation', 'approver', 'approver_designation', 'source_of_fund'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'entity_name' => 'Entity Name',
            'dts_no' => 'DTS No.',
            'rc_code' => 'Responsibility Center Code',
            'date_requested' => 'Date Requested',
            'fund_cluster' => 'Fund Cluster',
            'purpose' => 'Purpose',
            'requester' => 'Requested By',
            'requester_designation' => 'Designation',
            'approver' => 'Approved By',
            'approver_designation' => 'Designation',
            'source_of_fund' => 'Source Of Fund',
            'charge_to' => 'Charge To',
        ];
    }

    /**
     * Gets query for [[PrActivityTimelines]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrActivityTimelines()
    {
        return $this->hasMany(PrActivityTimeline::className(), ['pr_id' => 'id']);
    }

    /**
     * Gets query for [[PrBudgetVerifications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrBudgetVerification()
    {
        return $this->hasOne(PrBudgetVerification::className(), ['pr_id' => 'id']);
    }

    /**
     * Gets query for [[PrItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrItems()
    {
        return $this->hasMany(PrItem::className(), ['pr_id' => 'id']);
    }

    /**
     * Gets query for [[Ppmp]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrPpmp()
    {
        return $this->hasOne(PrPpmp::className(), ['pr_id' => 'id']);
    }

    /**
     * Gets query for [[PrProcVerifications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrProcVerification()
    {
        return $this->hasOne(PrProcVerification::className(), ['pr_id' => 'id']);
    }

    /**
     * Gets query for [[PrRfqs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrRfqs()
    {
        return $this->hasMany(PrRfq::className(), ['pr_id' => 'id']);
    }

    /**
     * Gets query for [[PrTransactionHistories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrTransactionHistories()
    {
        return $this->hasMany(PrTransactionHistory::className(), ['pr_id' => 'id']);
    }

    public function getProcurementFocalPerson()
    {
        $transaction = PrTransactionHistory::find()->where(['pr_id' => $this->id, 'group' => 'END-USER', 'status' => 'PENDING', 'action_type' => 'FOR ADDITION OF ITEM'])->orderBy(['date_of_action' => SORT_DESC])->one() ? PrTransactionHistory::find()->where(['pr_id' => $this->id, 'group' => 'END-USER', 'status' => 'PENDING', 'action_type' => 'FOR ADDITION OF ITEM'])->orderBy(['date_of_action' => SORT_DESC])->one() : new PrTransactionHistory();

        $verifier = '';

        if(!$transaction->isNewRecord)
        {
            $name = UserInfo::findOne(['user_id' => $transaction->action_taken_by]);
            $verifier = $name ? ucwords(strtolower($name->FIRST_M)).' '.strtoupper(substr($name->MIDDLE_M, 0, 1)).'. '.ucwords(strtolower($name->LAST_M)) : '';
        }

        return $verifier;
    }

    public function getBudgetVerifier()
    {
        $transaction = PrTransactionHistory::find()->where(['pr_id' => $this->id, 'group' => 'BUDGET', 'status' => 'APPROVED', 'action_type' => 'BUDGET VERIFIED'])->orderBy(['date_of_action' => SORT_DESC])->one() ? PrTransactionHistory::find()->where(['pr_id' => $this->id, 'group' => 'BUDGET', 'status' => 'APPROVED', 'action_type' => 'BUDGET VERIFIED'])->orderBy(['date_of_action' => SORT_DESC])->one() : new PrTransactionHistory();

        $verifier = '';

        if(!$transaction->isNewRecord)
        {
            $name = UserInfo::findOne(['user_id' => $transaction->action_taken_by]);
            $verifier = $name ? ucwords(strtolower($name->FIRST_M)).' '.strtoupper(substr($name->MIDDLE_M, 0, 1)).'. '.ucwords(strtolower($name->LAST_M)) : '';
        }

        return $verifier;
    }

    public function getProcurementVerifier()
    {
        $transaction = PrTransactionHistory::find()->where(['pr_id' => $this->id, 'group' => 'PROCUREMENT', 'status' => 'APPROVED', 'action_type' => 'PROCUREMENT VERIFIED'])->orderBy(['date_of_action' => SORT_DESC])->one() ? PrTransactionHistory::find()->where(['pr_id' => $this->id, 'group' => 'PROCUREMENT', 'status' => 'APPROVED', 'action_type' => 'PROCUREMENT VERIFIED'])->orderBy(['date_of_action' => SORT_DESC])->one() : new PrTransactionHistory();

        $verifier = '';

        if(!$transaction->isNewRecord)
        {
            $name = UserInfo::findOne(['user_id' => $transaction->action_taken_by]);
            $verifier = $name ? ucwords(strtolower($name->FIRST_M)).' '.strtoupper(substr($name->MIDDLE_M, 0, 1)).'. '.ucwords(strtolower($name->LAST_M)) : '';
        }

        return $verifier;
    }
}
