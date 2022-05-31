<?php

namespace common\modules\rpmes\models;

use Yii;
use markavespiritu\user\models\UserInfo;
use common\components\helpers\HtmlHelper;
/**
 * This is the model class for table "submission".
 *
 * @property int $id
 * @property int|null $agency_id
 * @property string|null $report
 * @property int|null $year
 * @property string|null $quarter
 * @property string|null $semester
 * @property int|null $submitted_by
 * @property string|null $date_submitted
 * @property string|null $draft
 */
class Submission extends \yii\db\ActiveRecord
{
    public $grouping;
    public $fund_source_id;
    public $region_id;
    public $province_id;
    public $citymun_id;
    public $sector_id;
    public $sub_sector_id;
    public $category_id;
    public $period;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'submission';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agency_id'], 'required', 'on' => 'monitoringPlanAdmin'],
            [['year'], 'required', 'on' => 'acknowledgmentMonitoringPlan'],
            [['year'], 'required', 'on' => 'acknowledgmentMonitoringReport'],
            [['year'], 'required', 'on' => 'generateFormOne'],
            [['year', 'grouping'], 'required', 'on' => 'summaryMonitoringPlan'],
            [['year', 'quarter', 'grouping'], 'required', 'on' => 'summaryMonitoringReport'],
            [['agency_id', 'year', 'submitted_by', 'fund_source_id', 'sector_id', 'sub_sector_id', 'category_id'], 'integer'],
            [['report', 'quarter', 'semester', 'draft', 'region_id', 'province_id', 'citymun_id', 'period'], 'string'],
            [['date_submitted'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agency_id' => 'Agency',
            'report' => 'Report',
            'year' => 'Year',
            'quarter' => 'Quarter',
            'semester' => 'Semester',
            'submitted_by' => 'Submitted By',
            'date_submitted' => 'Date Submitted',
            'draft' => 'Draft',
            'grouping' => 'Group by',
            'quarter' => 'Quarter',
            'agency_id' => 'Agency',
            'fund_source_id' => 'Fund Source',
            'region_id' => 'Region',
            'province_id' => 'Province',
            'citymun_id' => 'City/Municipality',
            'sector_id' =>'Sector',
            'sub_sector_id' =>'Sub-Sector',
            'category_id' => 'Category',
            'period' => 'Period'
        ];
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

    public function getAcknowledgment()
    {
        return $this->hasOne(Acknowledgment::className(), ['submission_id' => 'id']);
    }
    

    public function getSubmitter()
    {
        $submitter = UserInfo::findOne(['user_id' => $this->submitted_by]);

        return $submitter ? $submitter->FIRST_M." ".$submitter->LAST_M : '';
    }

    public function getSubmitterPosition()
    {
        $submitter = UserInfo::findOne(['user_id' => $this->submitted_by]);

        return $submitter ? $submitter->POSITION_C : '';
    }

    public function getProjectCount()
    {
        $projects = Plan::find()
            ->leftJoin('project', 'project.id = plan.project_id')
            ->andWhere([
                'project.agency_id' => $this->agency_id,
                'plan.year' => $this->year
            ])
            ->count();

        return $projects;
    }

    public function getDeadlineStatus()
    {
        $HtmlHelper = new HtmlHelper();
        $dueDate = DueDate::find();

        if($this->report == 'Monitoring Plan')
        {
            $dueDate = $dueDate->andWhere(['year' => $this->year, 'report' => $this->report]);
        }else if($this->report == 'Accomplishment')
        {
            $dueDate = $dueDate->andWhere(['year' => $this->year, 'report' => $this->report, 'quarter' => $this->quarter]);
        }else if($this->report == 'Project Exception')
        {
            $dueDate = $dueDate->andWhere(['year' => $this->year, 'report' => $this->report, 'quarter' => $this->quarter]);
        }

        $dueDate = $dueDate->one();

        $status = $dueDate ? strtotime(date("Y-m-d", strtotime($this->date_submitted))) <= strtotime($dueDate->due_date) ? $HtmlHelper->time_elapsed_string($dueDate->due_date).' before deadline' : $HtmlHelper->time_elapsed_string($dueDate->due_date).' after deadline' : 'no deadline set';

        return $status;
    }
}
