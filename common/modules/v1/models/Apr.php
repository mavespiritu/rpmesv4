<?php

namespace common\modules\v1\models;

use Yii;
use markavespiritu\user\models\Office;
use markavespiritu\user\models\Section;
use markavespiritu\user\models\Unit;
use markavespiritu\user\models\UserInfo;

/**
 * This is the model class for table "ppmp_apr".
 *
 * @property int $id
 * @property int|null $pr_id
 * @property string|null $account_code
 * @property string|null $ps_apr_no
 * @property string|null $agency_ctrl_no
 * @property string|null $date_prepared
 * @property string|null $stock_certified_by
 * @property string|null $fund_certified_by
 * @property string|null $approved_by
 *
 * @property PpmpPr $pr
 * @property PpmpAprItem[] $ppmpAprItems
 */
class Apr extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_apr';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pr_id'], 'integer'],
            [['date_prepared'], 'safe'],
            [['account_code', 'ps_apr_no', 'agency_ctrl_no'], 'string', 'max' => 100],
            [['stock_certified_by', 'fund_certified_by', 'approved_by'], 'string', 'max' => 10],
            [['pr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pr::className(), 'targetAttribute' => ['pr_id' => 'id']],
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
            'account_code' => 'Account Code',
            'ps_apr_no' => 'Ps Apr No',
            'agency_ctrl_no' => 'Agency Ctrl No',
            'date_prepared' => 'Date Prepared',
            'stock_certified_by' => 'Stock Certified By',
            'fund_certified_by' => 'Fund Certified By',
            'approved_by' => 'Approved By',
        ];
    }

    /**
     * Gets query for [[Pr]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPr()
    {
        return $this->hasOne(Pr::className(), ['id' => 'pr_id']);
    }

    /**
     * Gets query for [[PpmpAprItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAprItems()
    {
        return $this->hasMany(AprItem::className(), ['apr_id' => 'id']);
    }

    public function getStockCertifier()
    {
        return $this->hasOne(Signatory::className(), ['emp_id' => 'stock_certified_by']); 
    }

    public function getStockCertifierName()
    {
        return $this->stockCertifier ? $this->stockCertifier->name : '';
    }

    public function getFundsCertifier()
    {
        return $this->hasOne(Signatory::className(), ['emp_id' => 'fund_certified_by']); 
    }

    public function getFundsCertifierName()
    {
        return $this->fundsCertifier ? $this->fundsCertifier->name : '';
    }

    public function getApprover()
    {
        return $this->hasOne(Signatory::className(), ['emp_id' => 'approved_by']); 
    }

    public function getApproverName()
    {
        return $this->approver ? $this->approver->name : '';
    }
}
