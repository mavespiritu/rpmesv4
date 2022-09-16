<?php

namespace common\modules\rpmes\models;

use Yii;

/**
 * This is the model class for table "project_problem_solving_session".
 *
 * @property int $id
 * @property int|null $year
 * @property string|null $quarter
 * @property int|null $project_id
 * @property string|null $pss_date
 * @property string|null $agreement_reached
 * @property string|null $next_step
 * @property int|null $submitted_by
 * @property string|null $date_submitted
 */
class ProjectProblemSolvingSession extends \yii\db\ActiveRecord
{
    public $agency_id;
    public $sector_id;
    public $region_id;
    public $province_id;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_problem_solving_session';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['year', 'quarter','project_id', 'pss_date', 'agreement_reached', 'next_step'], 'required'],
            [['year', 'project_id', 'submitted_by','agency_id', 'sector_id', 'region_id', 'province_id'], 'integer'],
            [['quarter', 'agreement_reached', 'next_step'], 'string'],
            [['pss_date', 'date_submitted'], 'safe'],
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
            'projectTitle' => 'Project Title',
            'pss_date' => 'Pss Date',
            'agreement_reached' => 'Agreement Reached',
            'next_step' => 'Next Step',
            'submitted_by' => 'Submitted By',
            'date_submitted' => 'Date Submitted',
            'agency_id' => 'Agency',
            'sector_id' => 'Sector',
            'region_id' => 'Region',
            'province_id' => 'Province',
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
}
