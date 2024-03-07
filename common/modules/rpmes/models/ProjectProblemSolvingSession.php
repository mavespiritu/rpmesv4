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
            [[
                'year', 
                'quarter',
                'project_id', 
                'issue_details', 
                'issue_typology', 
                'pss_date', 
                'agencies', 
                'agreement_reached'], 'required'],
            [['year', 'project_id', 'submitted_by'], 'integer'],
            [['quarter', 'issue_details', 'issue_typology', 'agencies', 'agreement_reached', 'next_step', 'slippage'], 'string'],
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
            'project_id' => 'Project',
            'issue_details' => 'Issue Details',
            'issue_typology' => 'Issue Typology',
            'pss_date' => 'Date of Meeting',
            'agencies' => 'Concerned Agencies',
            'agreement_reached' => 'Agreements Reached',
            'next_step' => 'Next Step',
            'submitted_by' => 'Submitted By',
            'date_submitted' => 'Date Submitted',
            'slippage' => 'Slippage',
        ];
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }
}
