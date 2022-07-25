<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "agreement".
 *
 * @property int $id
 * @property int|null $year
 * @property string|null $quarter
 * @property int|null $project_id
 * @property string|null $date_of_pss
 * @property string|null $agreement_reached
 * @property string|null $next_step
 * @property int|null $submitted_by
 * @property string|null $date_submitted
 */
class Agreement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agreement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['year', 'project_id', 'submitted_by'], 'integer'],
            [['quarter', 'agreement_reached', 'next_step'], 'string'],
            [['year', 'quarter' ,'project_id', 'date_of_pss', 'agreement_reached', 'next_step'], 'required'],
            [['date_of_pss', 'date_submitted'], 'safe'],
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
            'quarter' => 'Quarter',
            'project_id' => 'Project ID',
            'date_of_pss' => 'Facilitation Meeting Date',
            'agreement_reached' => 'Agreement Reached',
            'next_step' => 'Next Step',
            'submitted_by' => 'Submitted By',
            'date_submitted' => 'Date Submitted',
        ];
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    public function getProjectTitle()
    {
        return $this->project ? $this->project->title : 'No Project';
    }

    public function getYearsList() 
    {
        $currentYear = 2099;
        $yearFrom = 1900;
        $yearsRange = range($yearFrom, $currentYear);
        return array_combine($yearsRange, $yearsRange);
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
