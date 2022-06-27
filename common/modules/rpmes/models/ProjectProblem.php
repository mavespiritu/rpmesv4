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
            [['project_id', 'nature', 'detail', 'strategy', 'responsible_entity', 'lesson_learned', 'quarter'], 'required'],
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
            'project_id' => 'Project ID',
            'nature' => 'Nature',
            'detail' => 'Detail',
            'strategy' => 'Strategy',
            'responsible_entity' => 'Responsible Entity',
            'lesson_learned' => 'Lesson Learned',
            'submitted_by' => 'Submitted By',
            'date_submitted' => 'Date Submitted',
        ];
    }

    public function getYearsList() 
    {
        $currentYear = 2099;
        $yearFrom = 1900;
        $yearsRange = range($yearFrom, $currentYear);
        return array_combine($yearsRange, $yearsRange);
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
        return $this->project->sector;
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
        return $this->project->agency ? $this->project->agency->title : 'No Agency';
    }

    public function getAllocationTotal()
    {
        return $this->project->allocationTotal;
    }
}
