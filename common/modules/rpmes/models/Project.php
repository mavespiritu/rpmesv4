<?php

namespace common\modules\rpmes\models;

use Yii;
use markavespiritu\user\models\UserInfo;
/**
 * This is the model class for table "project".
 *
 * @property int $id
 * @property int|null $source_id
 * @property string|null $project_no
 * @property int|null $year
 * @property int|null $agency_id
 * @property int|null $program_id
 * @property string|null $title
 * @property string|null $description
 * @property int|null $sector_id
 * @property int|null $sub_sector_id
 * @property int|null $location_scope_id
 * @property int|null $mode_of_implementation_id
 * @property int|null $fund_source_id
 * @property string|null $typhoon
 * @property string|null $data_type
 * @property string|null $period
 * @property string|null $start_date
 * @property string|null $completion_date
 * @property int|null $submitted_by
 * @property string|null $date_submitted
 *
 * @property Agency $agency
 * @property ProjectBarangay[] $projectBarangays
 * @property ProjectCategory[] $projectCategories
 * @property ProjectCitymun[] $projectCitymuns
 * @property ProjectExpectedOutput[] $projectExpectedOutputs
 * @property ProjectOutcome[] $projectOutcomes
 * @property ProjectProvince[] $projectProvinces
 * @property ProjectRdpChapter[] $projectRdpChapters
 * @property ProjectRdpChapterOutcome[] $projectRdpChapterOutcomes
 * @property ProjectRdpSubChapterOutcome[] $projectRdpSubChapterOutcomes
 * @property ProjectRegion[] $projectRegions
 * @property ProjectSdgGoal[] $projectSdgGoals
 * @property ProjectTarget[] $projectTargets
 */
class Project extends \yii\db\ActiveRecord
{
    public $quarter;
    public $status;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['period', 'agency_id', 'title', 'sector_id', 'sub_sector_id', 'mode_of_implementation_id', 'fund_source_id', 'start_date', 'completion_date', 'data_type'], 'required', 'on' => 'projectCreateAdmin'],
            [['period', 'title', 'sector_id', 'sub_sector_id', 'mode_of_implementation_id', 'fund_source_id', 'start_date', 'completion_date', 'data_type'], 'required', 'on' => 'projectCreateUser'],
            [['title', 'sector_id', 'sub_sector_id', 'mode_of_implementation_id', 'fund_source_id', 'start_date', 'completion_date', 'data_type'], 'required', 'on' => 'projectCarryOverUser'],
            [['typhoon'], 'validateTyphoon', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['source_id', 'year', 'agency_id', 'sector_id', 'sub_sector_id', 'location_scope_id', 'mode_of_implementation_id', 'fund_source_id', 'submitted_by'], 'integer'],
            [['title', 'description', 'data_type', 'period', 'other_mode'], 'string'],
            [['start_date', 'completion_date', 'date_submitted', 'program_id', 'draft', 'complete', 'status'], 'safe'],
            [['project_no'], 'string', 'max' => 20],
            [['typhoon'], 'string', 'max' => 100],
            [['agency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agency::className(), 'targetAttribute' => ['agency_id' => 'id']],
            [['year', 'quarter'], 'required', 'on' => 'accomplishmentUser'],
            [['year', 'agency_id','quarter'], 'required', 'on' => 'accomplishmentAdmin'],
            [['year', 'quarter'], 'required', 'on' => 'projectExceptionUser'],
            [['year', 'agency_id','quarter'], 'required', 'on' => 'projectExceptionAdmin'],
            /* [['source_id'], 'required',  'when' => function($model){
                return ($model->period == 'Carry-Over');
            }], */
            [['other_mode'], 'required',  'when' => function($model){
                return ($model->mode_of_implementation_id == 3);
            }],
            ['title', 'unique', 'targetAttribute' => 'year', 'message' => 'The title has been used already'],
        ];
    }

    public function behaviors()
    {
        return [
            'fileBehavior' => [
                'class' => \file\behaviors\FileBehavior::className()
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'source_id' => 'Source Project',
            'project_no' => 'Project No',
            'year' => 'Year',
            'agency_id' => 'Agency',
            'program_id' => 'Program Title',
            'title' => 'Project Title',
            'description' => 'Description',
            'sector_id' => 'Sector',
            'sub_sector_id' => 'Sub-Sector',
            'location_scope_id' => 'Scope of Location',
            'mode_of_implementation_id' => 'Mode of Implementation',
            'other_mode' => 'Others(Please specify)',
            'fund_source_id' => 'Fund Source',
            'typhoon' => 'Typhoon',
            'data_type' => 'Data Type',
            'period' => 'Period',
            'start_date' => 'Start Date',
            'completion_date' => 'Completion Date',
            'submitted_by' => 'Submitted By',
            'date_submitted' => 'Date Submitted',
            'draft' => 'Draft?',
            'complete' => 'Completed?',
            'quarter' => 'Quarter',
            'status' => 'Implementation Status'
        ];
    }

    public function validateTyphoon($attribute, $params, $validator)
    {
        $source = FundSource::findOne(['id' => $this->fund_source_id]);

        if($this->typhoon == "")
        {
            if($source->allow_typhoon == 'Yes')
            {
                $this->addError($attribute, 'Typhoon is required in the selected fund source');
            }
        }
    }

    public function getAllocationTotal()
    {
        $allocation = ProjectTarget::findOne(['project_id' => $this->id, 'target_type' => 'Financial', 'year' => $this->year]);
        $allocations = $allocation ? [$allocation->q1, $allocation->q2, $allocation->q3, $allocation->q4] : [0];
        rsort($allocations);
        $value = 0;
        
        switch($this->data_type){
            case 'Default':
            case 'Maintained':
                $value = $allocation ? floatval($allocation->q1) + floatval($allocation->q2) + floatval($allocation->q3) + floatval($allocation->q4) : 0;
                break;
            case 'Cumulative':
                $value = $allocation ? floatval($allocations[0]) : 0;
                break;
        }

        return $value;
    }

    public function getAllocationAsOfReportingPeriod($quarter)
    {
        $allocation = ProjectTarget::findOne(['project_id' => $this->id, 'target_type' => 'Financial', 'year' => $this->year]);
        $allocationsQ1 = $allocation ? [$allocation->q1] : [0];
        $allocationsQ2 = $allocation ? [$allocation->q1, $allocation->q2] : [0];
        $allocationsQ3 = $allocation ? [$allocation->q1, $allocation->q2, $allocation->q3] : [0];
        $allocationsQ4 = $allocation ? [$allocation->q1, $allocation->q2, $allocation->q3, $allocation->q4] : [0];
        rsort($allocationsQ1);
        rsort($allocationsQ2);
        rsort($allocationsQ3);
        rsort($allocationsQ4);
        $value = 0;
        
        switch($this->data_type){
            case 'Default':
            case 'Maintained':
                     if($quarter == 'Q1'){ $value = $allocation ? floatval($allocation->q1) : 0; }
                else if($quarter == 'Q2'){ $value = $allocation ? floatval($allocation->q1) + floatval($allocation->q2) : 0; }
                else if($quarter == 'Q3'){ $value = $allocation ? floatval($allocation->q1) + floatval($allocation->q2) + floatval($allocation->q3) : 0; }
                else if($quarter == 'Q4'){ $value = $allocation ? floatval($allocation->q1) + floatval($allocation->q2) + floatval($allocation->q3) + floatval($allocation->q4) : 0; }
                break;
            case 'Cumulative':
                     if($quarter == 'Q1'){ $value = $allocation ? floatval($allocation->q1) : 0; }
                else if($quarter == 'Q2'){ $value = $allocation ? $allocation->q2 ? floatval($allocation->q2) : floatval($allocation->q1) : 0;}
                else if($quarter == 'Q3'){ $value = $allocation ? ($allocation->q3 ? floatval($allocation->q3) : ($allocation->q2 ? floatval($allocation->q2) : floatval($allocation->q1))) : 0;}
                else if($quarter == 'Q4'){ $value = $allocation ? ($allocation->q4 ? floatval($allocation->q4) : ($allocation->q3 ? floatval($allocation->q3) : ($allocation->q2 ? floatval($allocation->q2) : floatval($allocation->q1)))) : 0;}
                break;
        }

        return $value;
    }
    
    public function getAllocationForQuarter($quarter)
    {
        $allocation = ProjectTarget::findOne(['project_id' => $this->id, 'target_type' => 'Financial', 'year' => $this->year]);
        $value = 0;
        
             if($quarter == 'Q1'){ $value = $allocation ? floatval($allocation->q1) : 0; }
        else if($quarter == 'Q2'){ $value = $allocation ? floatval($allocation->q2) : 0; }
        else if($quarter == 'Q3'){ $value = $allocation ? floatval($allocation->q3) : 0; }
        else if($quarter == 'Q4'){ $value = $allocation ? floatval($allocation->q4) : 0; }

        return $value;
    }

    public function getReleasesAsOfReportingPeriod($quarter)
    {
        $q1 = FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q1', 'year' => $this->year]) ? FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q1', 'year' => $this->year])->releases : 0;
        $q2 = FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q2', 'year' => $this->year]) ? FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q2', 'year' => $this->year])->releases : 0;
        $q3 = FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q3', 'year' => $this->year]) ? FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q3', 'year' => $this->year])->releases : 0;
        $q4 = FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q4', 'year' => $this->year]) ? FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q4', 'year' => $this->year])->releases : 0;

        $value = 0;
        
        switch($this->data_type){
            case 'Default':
            case 'Maintained':
                     if($quarter == 'Q1'){ $value = floatval($q1); }
                else if($quarter == 'Q2'){ $value = floatval($q1) + floatval($q2); }
                else if($quarter == 'Q3'){ $value = floatval($q1) + floatval($q2) + floatval($q3); }
                else if($quarter == 'Q4'){ $value = floatval($q1) + floatval($q2) + floatval($q3) + floatval($q4); }
                break;
            case 'Cumulative':
                    if($quarter == 'Q1'){ $value = floatval($q1); }
               else if($quarter == 'Q2'){ $value = $q2 ? floatval($q2) : floatval($q1);}
               else if($quarter == 'Q3'){ $value = $q2 ? $q3 ? floatval($q3) : floatval($q2) : floatval($q1);}
               else if($quarter == 'Q4'){ $value = $q2 ? $q3 ? $q4 ? floatval($q4) : floatval($q3) : floatval($q2) : floatval($q1);}
               break;
        }

        return $value;
    }

    public function getObligationsAsOfReportingPeriod($quarter)
    {
        $q1 = FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q1', 'year' => $this->year]) ? FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q1', 'year' => $this->year])->obligation : 0;
        $q2 = FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q2', 'year' => $this->year]) ? FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q2', 'year' => $this->year])->obligation : 0;
        $q3 = FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q3', 'year' => $this->year]) ? FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q3', 'year' => $this->year])->obligation : 0;
        $q4 = FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q4', 'year' => $this->year]) ? FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q4', 'year' => $this->year])->obligation : 0;

        $value = 0;
        
        switch($this->data_type){
            case 'Default':
            case 'Maintained':
                     if($quarter == 'Q1'){ $value = floatval($q1); }
                else if($quarter == 'Q2'){ $value = floatval($q1) + floatval($q2); }
                else if($quarter == 'Q3'){ $value = floatval($q1) + floatval($q2) + floatval($q3); }
                else if($quarter == 'Q4'){ $value = floatval($q1) + floatval($q2) + floatval($q3) + floatval($q4); }
                break;
            case 'Cumulative':
                     if($quarter == 'Q1'){ $value = floatval($q1); }
                else if($quarter == 'Q2'){ $value = $q2 ? floatval($q2) : floatval($q1);}
                else if($quarter == 'Q3'){ $value = $q2 ? $q3 ? floatval($q3) : floatval($q2) : floatval($q1);}
                else if($quarter == 'Q4'){ $value = $q2 ? $q3 ? $q4 ? floatval($q4) : floatval($q3) : floatval($q2) : floatval($q1);}
                break;
        }

        return $value;
    }

    public function getExpendituresAsOfReportingPeriod($quarter)
    {
        $q1 = FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q1', 'year' => $this->year]) ? FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q1', 'year' => $this->year])->expenditures : 0;
        $q2 = FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q2', 'year' => $this->year]) ? FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q2', 'year' => $this->year])->expenditures : 0;
        $q3 = FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q3', 'year' => $this->year]) ? FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q3', 'year' => $this->year])->expenditures : 0;
        $q4 = FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q4', 'year' => $this->year]) ? FinancialAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q4', 'year' => $this->year])->expenditures : 0;

        $value = 0;
        
        switch($this->data_type){
            case 'Default':
            case 'Maintained':
                     if($quarter == 'Q1'){ $value = floatval($q1); }
                else if($quarter == 'Q2'){ $value = floatval($q1) + floatval($q2); }
                else if($quarter == 'Q3'){ $value = floatval($q1) + floatval($q2) + floatval($q3); }
                else if($quarter == 'Q4'){ $value = floatval($q1) + floatval($q2) + floatval($q3) + floatval($q4); }
                break;
            case 'Cumulative':
                    if($quarter == 'Q1'){ $value = floatval($q1); }
               else if($quarter == 'Q2'){ $value = $q2 ? floatval($q2) : floatval($q1);}
               else if($quarter == 'Q3'){ $value = $q2 ? $q3 ? floatval($q3) : floatval($q2) : floatval($q1);}
               else if($quarter == 'Q4'){ $value = $q2 ? $q3 ? $q4 ? floatval($q4) : floatval($q3) : floatval($q2) : floatval($q1);}
               break;
        }

        return $value;
    }

    public function getPhysicalTotal()
    {
        $allocation = ProjectTarget::findOne(['project_id' => $this->id, 'target_type' => 'Physical', 'year' => $this->year]);
        $allocations = $allocation ? [$allocation->q1, $allocation->q2, $allocation->q3, $allocation->q4] : [0];
        rsort($allocations);
        $value = 0;
        
        switch($this->data_type){
            case 'Default':
                $value = $allocation ? intval($allocation->q1) + intval($allocation->q2) + intval($allocation->q3) + intval($allocation->q4) : 0;
                break;
            case 'Cumulative':
                $value = intval($allocations[0]);
                break;
            case 'Maintained':
                $value = intval($allocations[0]);
                break;
        }

        return $value;
    }

    public function getPhysicalTargetAsOfReportingPeriod($quarter)
    {
        $allocation = ProjectTarget::findOne(['project_id' => $this->id, 'target_type' => 'Physical', 'year' => $this->year]);
        $allocationsQ1 = $allocation ? [$allocation->q1] : [0];
        $allocationsQ2 = $allocation ? [$allocation->q1, $allocation->q2] : [0];
        $allocationsQ3 = $allocation ? [$allocation->q1, $allocation->q2, $allocation->q3] : [0];
        $allocationsQ4 = $allocation ? [$allocation->q1, $allocation->q2, $allocation->q3, $allocation->q4] : [0];
        rsort($allocationsQ1);
        rsort($allocationsQ2);
        rsort($allocationsQ3);
        rsort($allocationsQ4);
        $value = 0;
        
        switch($this->data_type){
            case 'Default':
                     if($quarter == 'Q1'){ $value = $allocation ? intval($allocation->q1) : 0; }
                else if($quarter == 'Q2'){ $value = $allocation ? intval($allocation->q1) + intval($allocation->q2) : 0; }
                else if($quarter == 'Q3'){ $value = $allocation ? intval($allocation->q1) + intval($allocation->q2) + intval($allocation->q3) : 0; }
                else if($quarter == 'Q4'){ $value = $allocation ? intval($allocation->q1) + intval($allocation->q2) + intval($allocation->q3) + intval($allocation->q4) : 0; }
                break;
            case 'Cumulative':
                     if($quarter == 'Q1'){ $value = $allocation ? intval($allocation->q1) : 0; }
                else if($quarter == 'Q2'){ $value = $allocation ? $allocation->q2 ? intval($allocation->q2) : intval($allocation->q1) : 0;}
                else if($quarter == 'Q3'){ $value = $allocation ? ($allocation->q3 ? intval($allocation->q3) : ($allocation->q2 ? intval($allocation->q2) : intval($allocation->q1))) : 0;}
                else if($quarter == 'Q4'){ $value = $allocation ? ($allocation->q4 ? intval($allocation->q4) : ($allocation->q3 ? intval($allocation->q3) : ($allocation->q2 ? intval($allocation->q2) : intval($allocation->q1)))) : 0;}
                break;
            case 'Maintained':
                     if($quarter == 'Q1'){ $value = $allocation ? intval($allocation->q1) : 0; }
                else if($quarter == 'Q2'){ $value = $allocation ? intval($allocation->q2) : 0; }
                else if($quarter == 'Q3'){ $value = $allocation ? intval($allocation->q3) : 0; }
                else if($quarter == 'Q4'){ $value = $allocation ? intval($allocation->q4) : 0; }
                break;
        }

        return $value;
    }

    public function getPhysicalTargetForQuarter($quarter)
    {
        $allocation = ProjectTarget::findOne(['project_id' => $this->id, 'target_type' => 'Physical', 'year' => $this->year]);
        $value = 0;
        
             if($quarter == 'Q1'){ $value = $allocation ? intval($allocation->q1) : 0; }
        else if($quarter == 'Q2'){ $value = $allocation ? intval($allocation->q2) : 0; }
        else if($quarter == 'Q3'){ $value = $allocation ? intval($allocation->q3) : 0; }
        else if($quarter == 'Q4'){ $value = $allocation ? intval($allocation->q4) : 0; }

        return $value;
    }

    public function getPhysicalActualToDate($quarter)
    {   
        $target = ProjectTarget::findOne(['project_id' => $this->id, 'target_type' => 'Physical', 'year' => $this->year]);

        $q1 = PhysicalAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q1', 'year' => $this->year]) ? PhysicalAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q1', 'year' => $this->year])->value : 0;
        $q2 = PhysicalAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q2', 'year' => $this->year]) ? PhysicalAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q2', 'year' => $this->year])->value : 0;
        $q3 = PhysicalAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q3', 'year' => $this->year]) ? PhysicalAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q3', 'year' => $this->year])->value : 0;
        $q4 = PhysicalAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q4', 'year' => $this->year]) ? PhysicalAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q4', 'year' => $this->year])->value : 0;

        $value = 0;
        
        switch($this->data_type){
            case 'Default':
                     if($quarter == 'Q1'){ $value = $this->indicatorUnitOfMeasure ? floatval($q1) : intval($q1); }
                else if($quarter == 'Q2'){ $value = $this->indicatorUnitOfMeasure ? floatval($q1) + floatval($q2) : intval($q1) + intval($q2); }
                else if($quarter == 'Q3'){ $value = $this->indicatorUnitOfMeasure ? floatval($q1) + floatval($q2) + floatval($q3) : intval($q1) + intval($q2) + intval($q3); }
                else if($quarter == 'Q4'){ $value = $this->indicatorUnitOfMeasure ? floatval($q1) + floatval($q2) + floatval($q3) + floatval($q4) : intval($q1) + intval($q2) + intval($q3) + intval($q4); }
                break;
            case 'Cumulative':
                    if($quarter == 'Q1'){ $value = $this->indicatorUnitOfMeasure ? floatval($q1) : intval($q1); }
               else if($quarter == 'Q2'){ $value = $this->indicatorUnitOfMeasure ? $q2 ? floatval($q2) : floatval($q1) : 0;}
               else if($quarter == 'Q3'){ $value = $this->indicatorUnitOfMeasure ? $q2 ? $q3 ? floatval($q3) : floatval($q2) : floatval($q1) : 0;}
               else if($quarter == 'Q4'){ $value = $this->indicatorUnitOfMeasure ? $q2 ? $q3 ? $q4 ? floatval($q4) : floatval($q3) : floatval($q2) : floatval($q1) : 0;}
               break;
            case 'Maintained':
                     if($quarter == 'Q1'){ $value = $this->indicatorUnitOfMeasure ? floatval($q1) : intval($q1); }
                else if($quarter == 'Q2'){ $value = $this->indicatorUnitOfMeasure ? floatval($q1) + floatval($q2) : intval($q1) + intval($q2); }
                else if($quarter == 'Q3'){ $value = $this->indicatorUnitOfMeasure ? floatval($q1) + floatval($q2) + floatval($q3) : intval($q1) + intval($q2) + intval($q3); }
                else if($quarter == 'Q4'){ $value = $this->indicatorUnitOfMeasure ? floatval($q1) + floatval($q2) + floatval($q3) + floatval($q4) : intval($q1) + intval($q2) + intval($q3) + intval($q4); }
                break;
        }

        return $value;
    }

    public function getMalesEmployedTarget($quarter)
    {
        $allocation = ProjectTarget::findOne(['project_id' => $this->id, 'target_type' => 'Male Employed', 'year' => $this->year]);
        $value = 0;
        
        if($quarter == 'Q1'){ $value = $allocation ? intval($allocation->q1) : 0; }
        else if($quarter == 'Q2'){ $value = $allocation ? intval($allocation->q1) + intval($allocation->q2) : 0; }
        else if($quarter == 'Q3'){ $value = $allocation ? intval($allocation->q1) + intval($allocation->q2) + intval($allocation->q3) : 0; }
        else if($quarter == 'Q4'){ $value = $allocation ? intval($allocation->q1) + intval($allocation->q2) + intval($allocation->q3) + intval($allocation->q4) : 0; }

        return $value;
    }

    public function getFemalesEmployedTarget($quarter)
    {
        $allocation = ProjectTarget::findOne(['project_id' => $this->id, 'target_type' => 'Female Employed', 'year' => $this->year]);
        $value = 0;
        
        if($quarter == 'Q1'){ $value = $allocation ? intval($allocation->q1) : 0; }
        else if($quarter == 'Q2'){ $value = $allocation ? intval($allocation->q1) + intval($allocation->q2) : 0; }
        else if($quarter == 'Q3'){ $value = $allocation ? intval($allocation->q1) + intval($allocation->q2) + intval($allocation->q3) : 0; }
        else if($quarter == 'Q4'){ $value = $allocation ? intval($allocation->q1) + intval($allocation->q2) + intval($allocation->q3) + intval($allocation->q4) : 0; }

        return $value;
    }

    public function getEmployedTarget($quarter)
    {
        return $this->getMalesEmployedTarget($quarter) + $this->getFemalesEmployedTarget($quarter);
    }

    public function getMalesEmployedActual($quarter)
    {
        $q1 = PersonEmployedAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q1', 'year' => $this->year]) ? PersonEmployedAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q1', 'year' => $this->year])->male : 0;
        $q2 = PersonEmployedAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q2', 'year' => $this->year]) ? PersonEmployedAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q2', 'year' => $this->year])->male : 0;
        $q3 = PersonEmployedAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q3', 'year' => $this->year]) ? PersonEmployedAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q3', 'year' => $this->year])->male : 0;
        $q4 = PersonEmployedAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q4', 'year' => $this->year]) ? PersonEmployedAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q4', 'year' => $this->year])->male : 0;

        $value = 0;
        
             if($quarter == 'Q1'){ $value = intval($q1); }
        else if($quarter == 'Q2'){ $value = intval($q1) + intval($q2); }
        else if($quarter == 'Q3'){ $value = intval($q1) + intval($q2) + intval($q3); }
        else if($quarter == 'Q4'){ $value = intval($q1) + intval($q2) + intval($q3) + intval($q4); }

        return $value;
    }

    public function getFemalesEmployedActual($quarter)
    {
        $q1 = PersonEmployedAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q1', 'year' => $this->year]) ? PersonEmployedAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q1', 'year' => $this->year])->female : 0;
        $q2 = PersonEmployedAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q2', 'year' => $this->year]) ? PersonEmployedAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q2', 'year' => $this->year])->female : 0;
        $q3 = PersonEmployedAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q3', 'year' => $this->year]) ? PersonEmployedAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q3', 'year' => $this->year])->female : 0;
        $q4 = PersonEmployedAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q4', 'year' => $this->year]) ? PersonEmployedAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q4', 'year' => $this->year])->female : 0;

        $value = 0;
        
             if($quarter == 'Q1'){ $value = intval($q1); }
        else if($quarter == 'Q2'){ $value = intval($q1) + intval($q2); }
        else if($quarter == 'Q3'){ $value = intval($q1) + intval($q2) + intval($q3); }
        else if($quarter == 'Q4'){ $value = intval($q1) + intval($q2) + intval($q3) + intval($q4); }

        return $value;
    }

    public function getEmployedActual($quarter)
    {
        return $this->getMalesEmployedActual($quarter) + $this->getFemalesEmployedActual($quarter);
    }

    public function getBeneficiariesTarget($quarter)
    {
        $allocation = ProjectTarget::findOne(['project_id' => $this->id, 'target_type' => 'Beneficiaries', 'year' => $this->year]);
        $value = 0;
        
             if($quarter == 'Q1'){ $value = $allocation ? intval($allocation->q1) : 0; }
        else if($quarter == 'Q2'){ $value = $allocation ? intval($allocation->q1) + intval($allocation->q2) : 0; }
        else if($quarter == 'Q3'){ $value = $allocation ? intval($allocation->q1) + intval($allocation->q2) + intval($allocation->q3) : 0; }
        else if($quarter == 'Q4'){ $value = $allocation ? intval($allocation->q1) + intval($allocation->q2) + intval($allocation->q3) + intval($allocation->q4) : 0; }

        return $value;
    }

    public function getGroupsTarget($quarter)
    {
        $allocation = ProjectTarget::findOne(['project_id' => $this->id, 'target_type' => 'Group Beneficiaries', 'year' => $this->year]);
        $value = 0;
        
             if($quarter == 'Q1'){ $value = $allocation ? intval($allocation->q1) : 0; }
        else if($quarter == 'Q2'){ $value = $allocation ? intval($allocation->q1) + intval($allocation->q2) : 0; }
        else if($quarter == 'Q3'){ $value = $allocation ? intval($allocation->q1) + intval($allocation->q2) + intval($allocation->q3) : 0; }
        else if($quarter == 'Q4'){ $value = $allocation ? intval($allocation->q1) + intval($allocation->q2) + intval($allocation->q3) + intval($allocation->q4) : 0; }

        return $value;
    }

    public function getMaleBeneficiariesActual($quarter)
    {
        $q1 = BeneficiariesAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q1', 'year' => $this->year]) ? BeneficiariesAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q1', 'year' => $this->year])->male : 0;
        $q2 = BeneficiariesAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q2', 'year' => $this->year]) ? BeneficiariesAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q2', 'year' => $this->year])->male : 0;
        $q3 = BeneficiariesAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q3', 'year' => $this->year]) ? BeneficiariesAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q3', 'year' => $this->year])->male : 0;
        $q4 = BeneficiariesAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q4', 'year' => $this->year]) ? BeneficiariesAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q4', 'year' => $this->year])->male : 0;

        $value = 0;
        
             if($quarter == 'Q1'){ $value = intval($q1); }
        else if($quarter == 'Q2'){ $value = intval($q1) + intval($q2); }
        else if($quarter == 'Q3'){ $value = intval($q1) + intval($q2) + intval($q3); }
        else if($quarter == 'Q4'){ $value = intval($q1) + intval($q2) + intval($q3) + intval($q4); }

        return $value;
    }

    public function getFemaleBeneficiariesActual($quarter)
    {
        $q1 = BeneficiariesAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q1', 'year' => $this->year]) ? BeneficiariesAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q1', 'year' => $this->year])->female : 0;
        $q2 = BeneficiariesAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q2', 'year' => $this->year]) ? BeneficiariesAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q2', 'year' => $this->year])->female : 0;
        $q3 = BeneficiariesAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q3', 'year' => $this->year]) ? BeneficiariesAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q3', 'year' => $this->year])->female : 0;
        $q4 = BeneficiariesAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q4', 'year' => $this->year]) ? BeneficiariesAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q4', 'year' => $this->year])->female : 0;

        $value = 0;
        
             if($quarter == 'Q1'){ $value = intval($q1); }
        else if($quarter == 'Q2'){ $value = intval($q1) + intval($q2); }
        else if($quarter == 'Q3'){ $value = intval($q1) + intval($q2) + intval($q3); }
        else if($quarter == 'Q4'){ $value = intval($q1) + intval($q2) + intval($q3) + intval($q4); }

        return $value;
    }

    public function getBeneficiariesActual($quarter)
    {
        return $this->getMaleBeneficiariesActual($quarter) + $this->getFemaleBeneficiariesActual($quarter);
    }

    public function getGroupsActual($quarter)
    {
        $q1 = GroupAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q1', 'year' => $this->year]) ? GroupAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q1', 'year' => $this->year])->value : 0;
        $q2 = GroupAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q2', 'year' => $this->year]) ? GroupAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q2', 'year' => $this->year])->value : 0;
        $q3 = GroupAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q3', 'year' => $this->year]) ? GroupAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q3', 'year' => $this->year])->value : 0;
        $q4 = GroupAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q4', 'year' => $this->year]) ? GroupAccomplishment::findOne(['project_id' => $this->id, 'quarter' => 'Q4', 'year' => $this->year])->value : 0;

        $value = 0;
        
             if($quarter == 'Q1'){ $value = intval($q1); }
        else if($quarter == 'Q2'){ $value = intval($q1) + intval($q2); }
        else if($quarter == 'Q3'){ $value = intval($q1) + intval($q2) + intval($q3); }
        else if($quarter == 'Q4'){ $value = intval($q1) + intval($q2) + intval($q3) + intval($q4); }

        return $value;
    }

    public function getPhysicalSlippage($quarter)
    {
        $unit = $this->indicatorUnitOfMeasure;

        $slippage = 0;

        if($unit == true)
        {
            $slippage = $this->getPhysicalActualToDate($quarter) - $this->getPhysicalTargetAsOfReportingPeriod($quarter);
        }else{
            $slippage = $this->getPhysicalTargetAsOfReportingPeriod($quarter) > 0 ? (($this->getPhysicalActualToDate($quarter)/$this->getPhysicalTargetAsOfReportingPeriod($quarter))*100) - 100 : 0;
        }

        return $slippage;
    }

    public function getImplementationStatus($quarter)
    {
        $slippage = $this->getPhysicalSlippage($quarter);
        $status = '';

             if($slippage <= -15){ $status = 'Behind Schedule'; }
        else if($slippage > -15 && $slippage < 15){ $status = 'On Schedule'; }
        else if($slippage >= 15){ $status = 'Ahead of Schedule'; }

        return $status;
    }

    public function getIsCompleted()
    {
        $isCompleted = false;
        $q1 = Accomplishment::findOne(['project_id' => $this->id, 'year' => $this->year, 'quarter' => 'Q1']) ? Accomplishment::findOne(['project_id' => $this->id, 'year' => $this->year, 'quarter' => 'Q1'])->action == 1 ? true : false : false;
        $q2 = Accomplishment::findOne(['project_id' => $this->id, 'year' => $this->year, 'quarter' => 'Q2']) ? Accomplishment::findOne(['project_id' => $this->id, 'year' => $this->year, 'quarter' => 'Q2'])->action == 1 ? true : false : false;
        $q3 = Accomplishment::findOne(['project_id' => $this->id, 'year' => $this->year, 'quarter' => 'Q3']) ? Accomplishment::findOne(['project_id' => $this->id, 'year' => $this->year, 'quarter' => 'Q3'])->action == 1 ? true : false : false;
        $q4 = Accomplishment::findOne(['project_id' => $this->id, 'year' => $this->year, 'quarter' => 'Q4']) ? Accomplishment::findOne(['project_id' => $this->id, 'year' => $this->year, 'quarter' => 'Q4'])->action == 1 ? true : false : false;
        
        return $q1 || $q2 || $q3 || $q4;
    }

    /**
     * Gets query for [[Agency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAgency()
    {
        return $this->hasOne(Agency::className(), ['id' => 'agency_id']);
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

    public function getFundSourceTitle()
    {
        return $this->fundSource ? $this->fundSource->title : 'No Fund Source';
    }

    /**
     * Gets query for [[ModeOfImplementation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModeOfImplementation()
    {
        return $this->hasOne(ModeOfImplementation::className(), ['id' => 'mode_of_implementation_id']);
    }

    public function getModeOfImplementationTitle()
    {
        return $this->modeOfImplementation ? $this->modeOfImplementation->title : 'No Mode of Implementation';
    }

    /**
     * Gets query for [[Program]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'program_id']);
    }

    /**
     * Gets query for [[Sector]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSector()
    {
        return $this->hasOne(Sector::className(), ['id' => 'sector_id']);
    }

    public function getSectorTitle()
    {
        return $this->sector ? $this->sector->title : 'No Sector';
    }

    /**
     * Gets query for [[SubSector]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubSector()
    {
        return $this->hasOne(SubSector::className(), ['id' => 'sub_sector_id']);
    }

    public function getSubSectorTitle()
    {
        return $this->subSector ? $this->subSector->title : 'No Sub-sector';
    }

    public function getStartDate()
    {
        return $this->start_date != "" ? date("F j, Y", strtotime($this->start_date)) : 'No start date';
    }

    public function getCompletionDate()
    {
        return $this->completion_date != "" ? date("F j, Y", strtotime($this->completion_date)) : 'No completion date';
    }
    /**
     * Gets query for [[ProjectBarangays]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectBarangays()
    {
        return $this->hasMany(ProjectBarangay::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectCategories()
    {
        return $this->hasMany(ProjectCategory::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectKras()
    {
        return $this->hasMany(ProjectKra::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectCitymuns]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectCitymuns()
    {
        return $this->hasMany(ProjectCitymun::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectExpectedOutputs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectExpectedOutputs()
    {
        return $this->hasMany(ProjectExpectedOutput::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectOutcomes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectOutcomes()
    {
        return $this->hasMany(ProjectOutcome::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectProvinces]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProvinces()
    {
        return $this->hasMany(ProjectProvince::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectRdpChapters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectRdpChapters()
    {
        return $this->hasMany(ProjectRdpChapter::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectRdpChapterOutcomes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectRdpChapterOutcomes()
    {
        return $this->hasMany(ProjectRdpChapterOutcome::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectRdpSubChapterOutcomes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectRdpSubChapterOutcomes()
    {
        return $this->hasMany(ProjectRdpSubChapterOutcome::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectRegions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectRegions()
    {
        return $this->hasMany(ProjectRegion::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectSdgGoals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectSdgGoals()
    {
        return $this->hasMany(ProjectSdgGoal::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectTargets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectTargets()
    {
        return $this->hasMany(ProjectTarget::className(), ['project_id' => 'id']);
    }

    public function getUnitofMeasure()
    {
        $target = ProjectTarget::findOne(['project_id' => $this->id, 'year' => $this->year, 'target_type' => 'Physical']);

        return $target ? $target->indicator : '';
    }

    public function getIndicatorUnitOfMeasure()
    {
        $indicator = $this->unitOfMeasure;

        return strpos($indicator, '%') === false ? false : true;
    }

    public function getPhysicalTarget()
    {
        $target = ProjectTarget::findOne(['project_id' => $this->id, 'year' => $this->year, 'target_type' => 'Physical']);

        return $target;
    }

    public function getFinancialTarget()
    {
        $target = ProjectTarget::findOne(['project_id' => $this->id, 'year' => $this->year, 'target_type' => 'Financial']);

        return $target;
    }

    public function getMaleEmployedTarget()
    {
        $target = ProjectTarget::findOne(['project_id' => $this->id, 'year' => $this->year, 'target_type' => 'Male Employed']);

        return $target;
    }

    public function getFemaleEmployedTarget()
    {
        $target = ProjectTarget::findOne(['project_id' => $this->id, 'year' => $this->year, 'target_type' => 'Female Employed']);

        return $target;
    }

    public function getBeneficiaryTarget()
    {
        $target = ProjectTarget::findOne(['project_id' => $this->id, 'year' => $this->year, 'target_type' => 'Beneficiaries']);

        return $target;
    }

    public function getGroupTarget()
    {
        $target = ProjectTarget::findOne(['project_id' => $this->id, 'year' => $this->year, 'target_type' => 'Group Beneficiaries']);

        return $target;
    }

    public function getSubmitter()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'submitted_by']);
    }

    public function getSubmitterName()
    {
        return $this->submitter ? $this->submitter->fullName : '';
    }

    public function getAccomplishmentSubmitter($quarter)
    {
        $accomplishment = Accomplishment::findOne(['project_id' => $this->id, 'year' => $this->year, 'quarter' => $quarter]);
        $submitter = $accomplishment ? !is_null($accomplishment->submitted_by) ? UserInfo::findOne(['user_id' => $accomplishment->submitted_by])->fullName : 'No submitter name' : 'No submitter name';

        return $submitter;
    }

    public function getAccomplishmentDateSubmitted($quarter)
    {
        $accomplishment = Accomplishment::findOne(['project_id' => $this->id, 'year' => $this->year, 'quarter' => $quarter]);
        $dateSubmitted = $accomplishment ? date("F j, Y H:i:s", strtotime($accomplishment->date_submitted)) : 'No submission date';

        return $dateSubmitted;
    }

    public function getLocation()
    {
        $barangays = ProjectBarangay::findAll(['project_id' => $this->id, 'year' => $this->year]);
        $citymuns = ProjectCitymun::findAll(['project_id' => $this->id, 'year' => $this->year]);
        $provinces = ProjectProvince::findAll(['project_id' => $this->id, 'year' => $this->year]);
        $regions = ProjectRegion::findAll(['project_id' => $this->id, 'year' => $this->year]);
        $locations = [];
        if($regions)
        {
            if($provinces)
            {
                if($citymuns)
                {
                    if($barangays)
                    {
                        foreach($barangays as $barangay)
                        {
                            $locations[] = $barangay->barangayName.', '.$barangay->citymunName.', '.$barangay->provinceName;
                        }
                    }else{
                        foreach($citymuns as $citymun)
                        {
                            $locations[] = $citymun->citymunName.', '.$citymun->provinceName;
                        }
                    }
                }else{
                    foreach($provinces as $province)
                    {
                        $locations[] = $province->provinceName;
                    }
                }
            }else{
                foreach($regions as $region)
                {
                    $locations[] = $region->regionName;
                }
            }
        }

        return !empty($locations) ? implode(" &#8226; ", $locations) : 'No location';
    }
    
}
