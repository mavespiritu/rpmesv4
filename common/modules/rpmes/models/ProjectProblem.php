<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_problem".
 *
 * @property int $id
 * @property int|null $project_id
 * @property string|null $nature
 * @property string|null $detail
 * @property string|null $strategy
 * @property string|null $responsible_entity
 * @property string|null $lesson_learned
 * @property int|null $submitted_by
 * @property string|null $date_submitted
 *
 * @property Project $project
 */
class ProjectProblem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_problem';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'nature', 'detail', 'strategy', 'responsible_entity', 'lesson_learned', 'year'], 'required'],
            [['project_id', 'submitted_by', 'year'], 'integer'],
            [['nature', 'detail', 'strategy', 'responsible_entity', 'lesson_learned'], 'string'],
            [['date_submitted'], 'safe'],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'year' => 'Year',
            'quarter' => 'quarter',
            'project_id' => 'Project',
            'projectTitle' => 'Project',
            'sectorTitle' => 'Sector',
            'nature' => 'Nature of Problem',
            'detail' => 'Detail of Problem',
            'strategy' => 'Strategies / Actions Taken to Resolve the Problem / Issue',
            'responsible_entity' => 'Responsible Entities / Key Actors and Their Specific Assistance',
            'lesson_learned' => 'Lessons Learned and Good Practices that could be Shared to the NPMC / Other PMCs',
            'submitted_by' => 'Submitted By',
            'date_submitted' => 'Date Submitted',
            'subSectorTitle' => 'Sub Sector',
            'projectCitymuns' => 'Project City/Municipal',
            'projectProvinces' => 'Project Province',
            'projectRegions' => 'Project Region',
        ];
    }

    public function getYearsList() 
    {
        $currentYear = date('Y');
        $leastYear = date('Y') - 1;
        $maxYear = date('Y');
        $yearRange = range($leastYear, $maxYear);
        return array_combine($yearRange, $yearRange);
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    public function getProjectTitle()
    {
        return $this->project ? $this->project->title : 'No Project';
    }

    public function getSector()
    {
        return $this->project->sector ? $this->project->sector->title : null;
    }

    public function getSectorTitle()
    {
        return $this->project->sector ? $this->project->sector->title : 'No Sector';
    }

    public function getSubSector()
    {
        return $this->project->subSector;
    }

    public function getSubSectorTitle()
    {
        return $this->project->subSector ? $this->project->subSector->title : 'No Sub Sector';
    }

    public function getLocation()
    {
        return $this->project->location;
    }

    public function getAgency()
    {
        return $this->project->agency ? $this->project->agency->code : null;
    }

    public function getAllocationTotal()
    {
        return number_format($this->project->allocationTotal, 2);
    }

    public function getProjectBarangays()
    {
        $brangayLocations = [];
        $barangays = $this->project->projectBarangays;

            foreach($barangays as $barangay)
            {
                $brangayLocations[] = $barangay->barangayName;
            }

        return !empty($brangayLocations) ? implode(" ; ", $brangayLocations) : 'All Barangay';
    }

    public function getProjectCitymuns()
    {
        $citymunsLocations = [];
        $citymuns = $this->project->projectCitymuns;

            foreach($citymuns as $citymun)
            {
                $citymunsLocations[] = $citymun->citymunName;
            }

        return !empty($citymunsLocations) ? implode(" ; ", $citymunsLocations) : 'All City/Municipality';
    }

    public function getProjectProvinces()
    {
        $provincesLocations = [];
        $provinces = $this->project->projectProvinces;

            foreach($provinces as $province)
            {
                $provincesLocations[] = $province->provinceName;
            }

        return !empty($provincesLocations) ? implode(" ; ", $provincesLocations) : 'All Province';
    }
    
    public function getProjectRegions()
    {
        $regionsLocations = [];
        $regions = $this->project->projectRegions;

            foreach($regions as $region)
            {
                $regionsLocations[] = $region->regionName;
            }

        return !empty($regionsLocations) ? implode(" ; ", $regionsLocations) : 'No Region';
    }
}
