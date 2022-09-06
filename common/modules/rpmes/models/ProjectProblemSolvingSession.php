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
 * @property string|null $submitted_date
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
            [['year', 'project_id', 'submitted_by'], 'integer'],
            [['quarter', 'agreement_reached', 'next_step'], 'string'],
            [['pss_date', 'submitted_date'], 'safe'],
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
            'pss_date' => 'Pss Date',
            'agreement_reached' => 'Agreement Reached',
            'next_step' => 'Next Step',
            'submitted_by' => 'Submitted By',
            'submitted_date' => 'Submitted Date',
        ];
    }
}
