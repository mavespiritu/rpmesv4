<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_finding".
 *
 * @property int $id
 * @property string|null $quarter
 * @property int|null $year
 * @property int|null $project_id
 * @property string|null $inspection_date
 * @property string|null $major_finding
 * @property string|null $issues
 * @property string|null $action
 */
class ProjectFinding extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_finding';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quarter', 'major_finding', 'issues', 'action'], 'string'],
            [['year', 'project_id'], 'integer'],
            [['inspection_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'quarter' => 'Quarter',
            'year' => 'Year',
            'project_id' => 'Project ID',
            'inspection_date' => 'Inspection Date',
            'major_finding' => 'Major Finding/s',
            'issues' => 'Issues',
            'action' => 'Action/s Taken/Recommendations',
            'subSectorTitle' => 'Sub Sector',
            'projectCitymuns' => 'Project City/Municipal',
            'projectProvinces' => 'Project Province',
            'projectRegions' => 'Project Region',
        ];
    }

    public function getYearsList() 
    {
        $currentYear = date('Y');
        $leastYear = date('Y') - 3;
        $maxYear = date('Y') + 3;
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

    public function getAgency()
    {
        return $this->project->agency ? $this->project->agency->code : null;
    }

    public function getAllocationTotal()
    {
        return $this->project->allocationTotal;
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
