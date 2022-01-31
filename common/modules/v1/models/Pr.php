<?php

namespace common\modules\v1\models;

use Yii;
use markavespiritu\user\models\Office;
use markavespiritu\user\models\Section;
use markavespiritu\user\models\Unit;
use markavespiritu\user\models\UserInfo;
/**
 * This is the model class for table "ppmp_pr".
 *
 * @property int $id
 * @property string|null $pr_no
 * @property string|null $office_id
 * @property string|null $section_id
 * @property string|null $unit_id
 * @property int|null $fund_source_id
 * @property int|null $fund_cluster_id
 * @property string|null $purpose
 * @property string|null $requested_by
 * @property string|null $date_requested
 * @property string|null $approved_by
 * @property string|null $date_approved
 * @property string|null $type
 */
class Pr extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppmp_pr';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'office_id', 'year', 'fund_source_id', 'fund_cluster_id', 'purpose', 'date_requested', 'requested_by', 'procurement_mode_id'], 'required'],
            [['fund_source_id', 'fund_cluster_id'], 'integer'],
            [['purpose', 'type'], 'string'],
            [['year'], 'integer'],
            [['date_requested', 'date_approved', 'date_created'], 'safe'],
            [['pr_no', 'office_id', 'section_id', 'unit_id', 'requested_by', 'approved_by', 'created_by'], 'string', 'max' => 100],
            [['fund_source_id'], 'exist', 'skipOnError' => true, 'targetClass' => FundSource::className(), 'targetAttribute' => ['fund_source_id' => 'id']],
            [['fund_cluster_id'], 'exist', 'skipOnError' => true, 'targetClass' => FundCluster::className(), 'targetAttribute' => ['fund_cluster_id' => 'id']],
            [['procurement_mode_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProcurementMode::className(), 'targetAttribute' => ['procurement_mode_id' => 'id']],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pr_no' => 'PR No.',
            'office_id' => 'Division',
            'officeName' => 'Division',
            'section_id' => 'Section',
            'unit_id' => 'Unit',
            'fund_source_id' => 'Fund Source',
            'fundSourceName' => 'Fund Source',
            'fund_cluster_id' => 'Fund Cluster',
            'fundClusterName' => 'Fund Cluster',
            'purpose' => 'Purpose',
            'created_by' => 'Created By',
            'creatorName' => 'Created By',
            'date_created' => 'Date Created',
            'requested_by' => 'Requested By',
            'requesterName' => 'Requested By',
            'date_requested' => 'Date Requested',
            'approved_by' => 'Approved By',
            'approverName' => 'Approved By',
            'date_approved' => 'Date Approved',
            'disapproved_by' => 'Disapproved By',
            'date_disapproved' => 'Date Disapproved',
            'year' => 'Year',
            'type' => 'Type',
            'procurement_mode_id' => 'Mode of Procurement',
            'procurementModeName' => 'Mode of Procurement',
        ];
    }

    public function getStatus()
    {
        $status = Transaction::find()->where(['model' => 'Pr', 'model_id' => $this->id])->orderBy(['datetime' => SORT_DESC])->one();

        return $status;
    }

    public function getFundSource()
    {
        return $this->hasOne(FundSource::className(), ['id' => 'fund_source_id']);
    }

    public function getFundSourceName()
    {
        return $this->fundSource ? $this->fundSource->code : '';
    }

    public function getFundCluster()
    {
        return $this->hasOne(FundCluster::className(), ['id' => 'fund_cluster_id']);
    }

    public function getFundClusterName()
    {
        return $this->fundCluster ? $this->fundCluster->title : '';
    }

    public function getProcurementMode()
    {
        return $this->hasOne(ProcurementMode::className(), ['id' => 'procurement_mode_id']);
    }

    public function getProcurementModeName()
    {
        return $this->procurementMode ? $this->procurementMode->title : '';
    }

    public function getOffice()
    {
        return $this->hasOne(Office::className(), ['abbreviation' => 'office_id']);
    }

    public function getOfficeName()
    {
        return $this->office ? $this->office->abbreviation : '';
    }

    public function getSection()
    {
        return $this->hasOne(Section::className(), ['abbreviation' => 'section_id']);
    }

    public function getSectionName()
    {
        return $this->office ? $this->section->abbreviation : '';
    }

    public function getUnit()
    {
        return $this->hasOne(Unit::className(), ['abbreviation' => 'unit_id']);
    }

    public function getUnitName()
    {
        return $this->office ? $this->unit->abbreviation : '';
    }

    public function getRequester()
    {
        return $this->hasOne(Signatory::className(), ['emp_id' => 'requested_by']);
    }

    public function getRequesterName()
    {
        return $this->requester ? $this->requester->name : '';
    }

    public function getCreator()
    {
        return $this->hasOne(UserInfo::className(), ['EMP_N' => 'created_by']); 
    }

    public function getCreatorName()
    {
        return $this->creator ? ucwords(strtolower($this->creator->FIRST_M.' '.$this->creator->LAST_M)) : '';
    }

    public function getApprover()
    {
        return $this->hasOne(Signatory::className(), ['emp_id' => 'approved_by']);
    }

    public function getApproverName() 
    {
        return $this->approver ? $this->approver->name : '';
    }

    public function getDisapprover()
    {
        return $this->hasOne(Signatory::className(), ['emp_id' => 'disapproved_by']);
    }

    public function getDisapproverName()
    {
        return $this->disapprover ? $this->disapprover->name : '';
    }

    public function afterSave($insert, $changedAttributes){

        if($insert)
        {
            $status = new Transaction();
            $status->actor = Yii::$app->user->identity->userinfo->EMP_N;
            $status->model = 'Pr';
            $status->model_id = $this->id;
            $status->status = 'Draft';
            $status->save();
        }

        parent::afterSave($insert, $changedAttributes);
    }
}
