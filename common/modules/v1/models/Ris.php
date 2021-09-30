<?php

namespace common\modules\v1\models;

use Yii;
use markavespiritu\user\models\Office;
use markavespiritu\user\models\Section;
use markavespiritu\user\models\Unit;
use markavespiritu\user\models\UserInfo;
/**
 * This is the model class for table "ppmp_ris".
 *
 * @property int $id
 * @property string|null $ris_no
 * @property int|null $office_id
 * @property int|null $section_id
 * @property int|null $unit_id
 * @property int|null $ppmp_id
 * @property int|null $fund_cluster_id
 * @property string|null $purpose
 * @property string|null $date_required
 * @property int|null $created_by
 * @property int|null $requested_by
 * @property int|null $approved_by
 * @property int|null $issued_by
 * @property int|null $received_by
 *
 * @property PpmpRisItem[] $ppmpRisItems
 */
class Ris extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_ris';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['office_id', 'fund_cluster_id', 'requested_by', 'date_required', 'purpose'], 'required', 'on' => 'isAdmin'],
            [['fund_cluster_id', 'requested_by', 'date_required', 'purpose'], 'required', 'on' => 'isUser'],
            [['office_id', 'section_id', 'unit_id', 'fund_cluster_id', 'created_by', 'requested_by', 'approved_by', 'issued_by', 'received_by'], 'integer'],
            [['purpose'], 'string'],
            [['date_required', 'date_created', 'date_requested', 'date_approved', 'date_issued', 'date_received'], 'safe'],
            [['ris_no'], 'string', 'max' => 15],
            [['fund_cluster_id'], 'exist', 'skipOnError' => true, 'targetClass' => FundCluster::className(), 'targetAttribute' => ['fund_cluster_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ris_no' => 'RIS No.',
            'office_id' => 'Division',
            'officeName' => 'Division',
            'section_id' => 'Section',
            'unit_id' => 'Unit',
            'fund_cluster_id' => 'Fund Cluster',
            'fundClusterName' => 'Fund Cluster',
            'purpose' => 'Purpose',
            'date_required' => 'Date Required',
            'created_by' => 'Created By',
            'creatorName' => 'Created By',
            'date_created' => 'Date Created',
            'requested_by' => 'Requested By',
            'requesterName' => 'Requested By',
            'date_requested' => 'Date Requested',
            'approved_by' => 'Approved By',
            'date_approved' => 'Date Approved',
            'issued_by' => 'Issued By',
            'date_issued' => 'Date Issued',
            'received_by' => 'Received By',
            'date_received' => 'Date Received',
        ];
    }

    /**
     * Gets query for [[PpmpRisItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRisItems()
    {
        return $this->hasMany(RisItem::className(), ['ris_id' => 'id']);
    }

    public function getFundCluster()
    {
        return $this->hasOne(FundCluster::className(), ['id' => 'fund_cluster_id']);
    }

    public function getFundClusterName()
    {
        return $this->fundCluster ? $this->fundCluster->title : '';
    }

    public function getOffice()
    {
        return $this->hasOne(Office::className(), ['id' => 'office_id']);
    }

    public function getOfficeName()
    {
        return $this->office ? $this->office->abbreviation : '';
    }

    public function getSection()
    {
        return $this->hasOne(Section::className(), ['id' => 'section_id']);
    }

    public function getSectionName()
    {
        return $this->office ? $this->section->abbreviation : '';
    }

    public function getUnit()
    {
        return $this->hasOne(Unit::className(), ['id' => 'unit_id']);
    }

    public function getUnitName()
    {
        return $this->office ? $this->unit->abbreviation : '';
    }

    public function getRequester()
    {
        return $this->hasOne(Signatory::className(), ['id' => 'requested_by']);
    }

    public function getRequesterName()
    {
        return $this->requester ? $this->requester->name : '';
    }

    public function getCreator()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'created_by']); 
    }

    public function getCreatorName()
    {
        return $this->creator ? ucwords(strtolower($this->creator->FIRST_M.' '.$this->creator->LAST_M)) : '';
    }

    public function getApprover()
    {
        return $this->hasOne(Signatory::className(), ['id' => 'approved_by']);
    }

    public function getApproverName()
    {
        return $this->approver ? $this->approver->name : '';
    }

    public function getIssuer()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'issued_by']); 
    }

    public function getIssuerName()
    {
        return $this->issuer ? ucwords(strtolower($this->issuer->FIRST_M.' '.$this->issuer->LAST_M)) : '';
    }

    public function getReceiver()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'received_by']); 
    }

    public function getReceiverName()
    {
        return $this->receiver ? ucwords(strtolower($this->receiver->FIRST_M.' '.$this->receiver->LAST_M)) : '';
    }
}
